<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateInvitationRequest;
use App\Models\Invitation;
use App\Models\InvitationEvent;
use App\Models\Template;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class InvitationEditorController extends Controller
{
    /**
     * Show the section-based invitation editor.
     */
    public function edit(Invitation $invitation): View
    {
        $this->authorize('update', $invitation);

        $invitation->load(['template', 'plan', 'events']);
        $templates = Template::where('is_active', true)->get();
        $safeData = $invitation->getSafeData();

        return view('editor.index', compact('invitation', 'templates', 'safeData'));
    }

    /**
     * Update invitation data and sections.
     */
    public function update(UpdateInvitationRequest $request, Invitation $invitation): RedirectResponse
    {
        $this->authorize('update', $invitation);

        $validated = $request->validated();
        $plan = $invitation->plan;
        $currentData = $invitation->getSafeData();

        // 1. Basic information
        $invitation->slug = $validated['slug'];
        $invitation->template_id = $validated['template_id'];

        // 2. Handle Party 1 Photo
        if ($request->hasFile('party1_photo')) {
            $path = $request->file('party1_photo')->store('invitations/' . $invitation->id, 'public');
            $currentData['party1']['photo_path'] = '/storage/' . $path;
        }

        // 3. Handle Party 2 Photo
        if ($request->hasFile('party2_photo')) {
            $path = $request->file('party2_photo')->store('invitations/' . $invitation->id, 'public');
            $currentData['party2']['photo_path'] = '/storage/' . $path;
        }

        // 4. Handle Background Music
        if ($request->hasFile('custom_music')) {
            $path = $request->file('custom_music')->store('invitations/' . $invitation->id . '/music', 'public');
            $currentData['music_path'] = '/storage/' . $path;
        } elseif (! empty($request->input('preset_music'))) {
            $currentData['music_path'] = $request->input('preset_music');
        }

        // 5. Handle Gallery Photos Upload
        $existingGallery = $currentData['gallery'] ?? [];
        if ($request->hasFile('gallery_photos')) {
            $uploadedPhotos = $request->file('gallery_photos');
            $maxAllowed = $plan->max_photos ?? 15;

            foreach ($uploadedPhotos as $photo) {
                if (count($existingGallery) >= $maxAllowed) {
                    break;
                }
                $path = $photo->store('invitations/' . $invitation->id . '/gallery', 'public');
                $existingGallery[] = [
                    'url' => '/storage/' . $path,
                    'caption' => '',
                ];
            }
        }

        // Remove gallery photos if requested
        if ($request->has('remove_gallery_index')) {
            $indexesToRemove = (array) $request->input('remove_gallery_index');
            foreach ($indexesToRemove as $idx) {
                unset($existingGallery[$idx]);
            }
            $existingGallery = array_values($existingGallery);
        }
        $currentData['gallery'] = $existingGallery;

        // 6. Merge section contents
        $currentData['theme_color'] = $validated['theme_color'] ?? $currentData['theme_color'];

        $currentData['opening'] = array_merge($currentData['opening'], $request->input('opening', []));
        $currentData['party1'] = array_merge($currentData['party1'], $request->input('party1', []));
        $currentData['party2'] = array_merge($currentData['party2'], $request->input('party2', []));

        $currentData['story'] = array_values(array_filter($request->input('story', []), function ($item) {
            return ! empty($item['title']) || ! empty($item['description']);
        }));

        $currentData['gifts'] = array_values(array_filter($request->input('gifts', []), function ($item) {
            return ! empty($item['account_number']) || ! empty($item['bank_name']);
        }));

        $currentData['closing'] = array_merge($currentData['closing'], $request->input('closing', []));
        $currentData['rsvp_enabled'] = $request->boolean('rsvp_enabled', true);
        $currentData['wishes_enabled'] = $request->boolean('wishes_enabled', true);

        $invitation->data = $currentData;
        $invitation->save();

        // 7. Sync Events
        $eventsData = $request->input('events', []);
        $maxEvents = $plan->max_events ?? 3;
        $eventsData = array_slice($eventsData, 0, $maxEvents);

        $invitation->events()->delete();
        foreach ($eventsData as $eventItem) {
            if (! empty($eventItem['title']) && ! empty($eventItem['start_at'])) {
                InvitationEvent::create([
                    'invitation_id' => $invitation->id,
                    'title' => $eventItem['title'],
                    'start_at' => Carbon::parse($eventItem['start_at']),
                    'end_at' => ! empty($eventItem['end_at']) ? Carbon::parse($eventItem['end_at']) : null,
                    'timezone' => $eventItem['timezone'] ?? 'WIB',
                    'venue_name' => $eventItem['venue_name'],
                    'address' => $eventItem['address'] ?? '',
                    'maps_url' => $eventItem['maps_url'] ?? '',
                ]);
            }
        }

        return redirect()->route('editor.edit', $invitation)
            ->with('status', 'Undangan berhasil disimpan dan diperbarui!');
    }

    /**
     * Toggle publish / draft status of invitation.
     */
    public function togglePublish(Invitation $invitation): RedirectResponse
    {
        $this->authorize('publish', $invitation);

        if ($invitation->isExpired()) {
            return back()->with('error', 'Masa aktif undangan sudah berakhir. Silakan lakukan perpanjangan paket terlebih dahulu.');
        }

        if ($invitation->status === 'published') {
            $invitation->status = 'draft';
            $message = 'Undangan dikembalikan ke status Draft (tidak publik).';
        } else {
            $invitation->status = 'published';
            if (! $invitation->published_at) {
                $invitation->published_at = Carbon::now();
            }
            $message = 'Selamat! Undangan Anda telah resmi dipublikasikan.';
        }

        $invitation->save();

        return back()->with('status', $message);
    }

    /**
     * Live Preview frame rendered inside the mobile viewport.
     */
    public function livePreview(Invitation $invitation): View
    {
        $this->authorize('update', $invitation);

        $invitation->load(['template', 'plan', 'events', 'wishes' => fn ($q) => $q->take(10)]);
        $bladeView = $invitation->template->blade_view ?? 'templates.wedding_floral';

        $data = $invitation->getSafeData();
        $recipientName = 'Tamu Undangan (Preview)';
        $isPersonal = false;
        $previewMode = true;

        return view($bladeView, compact('invitation', 'data', 'recipientName', 'isPersonal', 'previewMode'));
    }
}
