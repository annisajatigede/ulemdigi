<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRsvpRequest;
use App\Models\Guest;
use App\Models\Invitation;
use App\Models\Wish;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicInvitationController extends Controller
{
    /**
     * Render public digital invitation by slug.
     */
    public function show(Request $request, string $slug): View
    {
        $invitation = Invitation::with(['template', 'plan', 'events', 'wishes' => fn ($q) => $q->latest()->take(30)])
            ->where('slug', $slug)
            ->firstOrFail();

        // Access check: only published invitations are visible to general public
        $isOwnerOrAdmin = auth()->check() && (auth()->id() === $invitation->user_id || auth()->user()->isAdmin());

        if (! $isOwnerOrAdmin) {
            if ($invitation->status === 'draft') {
                return view('invitation.not_published', compact('invitation'));
            }

            if ($invitation->isExpired()) {
                return view('invitation.expired', compact('invitation'));
            }
        }

        $recipientName = $request->query('to', 'Tamu Undangan');
        $isPersonal = false;
        $guest = null;

        $bladeView = $invitation->template->blade_view ?? 'templates.wedding_floral';
        $data = $invitation->getSafeData();
        $previewMode = false;

        return view($bladeView, compact('invitation', 'data', 'recipientName', 'isPersonal', 'guest', 'previewMode'));
    }

    /**
     * Render personalized invitation using personal guest token.
     */
    public function showGuest(Request $request, string $slug, string $token): View
    {
        $invitation = Invitation::with(['template', 'plan', 'events', 'wishes' => fn ($q) => $q->latest()->take(30)])
            ->where('slug', $slug)
            ->firstOrFail();

        $guest = Guest::where('invitation_id', $invitation->id)
            ->where('personal_token', $token)
            ->first();

        $recipientName = $guest ? $guest->name : $request->query('to', 'Tamu Undangan');
        $isPersonal = (bool) $guest;

        $isOwnerOrAdmin = auth()->check() && (auth()->id() === $invitation->user_id || auth()->user()->isAdmin());

        if (! $isOwnerOrAdmin) {
            if ($invitation->status === 'draft') {
                return view('invitation.not_published', compact('invitation'));
            }

            if ($invitation->isExpired()) {
                return view('invitation.expired', compact('invitation'));
            }
        }

        $bladeView = $invitation->template->blade_view ?? 'templates.wedding_floral';
        $data = $invitation->getSafeData();
        $previewMode = false;

        return view($bladeView, compact('invitation', 'data', 'recipientName', 'isPersonal', 'guest', 'previewMode'));
    }

    /**
     * Submit RSVP and congratulations message.
     */
    public function submitRsvp(StoreRsvpRequest $request, string $slug): JsonResponse|RedirectResponse
    {
        $invitation = Invitation::where('slug', $slug)->firstOrFail();

        $validated = $request->validated();
        $personalToken = $validated['personal_token'] ?? null;

        $guest = null;
        if ($personalToken) {
            $guest = Guest::where('invitation_id', $invitation->id)
                ->where('personal_token', $personalToken)
                ->first();
        }

        if (! $guest && ! empty($validated['phone'])) {
            $guest = Guest::where('invitation_id', $invitation->id)
                ->where('phone', $validated['phone'])
                ->first();
        }

        if ($guest) {
            $guest->update([
                'name' => $validated['name'],
                'phone' => $validated['phone'] ?? $guest->phone,
                'rsvp_status' => $validated['rsvp_status'],
                'guest_count' => $validated['guest_count'] ?? 1,
            ]);
        } else {
            $guest = Guest::create([
                'invitation_id' => $invitation->id,
                'name' => $validated['name'],
                'phone' => $validated['phone'] ?? null,
                'rsvp_status' => $validated['rsvp_status'],
                'guest_count' => $validated['guest_count'] ?? 1,
            ]);
        }

        $newWish = null;
        if (! empty($validated['message'])) {
            $newWish = Wish::create([
                'invitation_id' => $invitation->id,
                'guest_id' => $guest->id,
                'name' => $validated['name'],
                'message' => $validated['message'],
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Terima kasih atas konfirmasi kehadiran dan doa restu Anda!',
                'guest' => $guest,
                'wish' => $newWish,
            ]);
        }

        return back()->with('status', 'Terima kasih atas konfirmasi kehadiran dan doa restu Anda!');
    }
}
