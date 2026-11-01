<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGuestRequest;
use App\Models\Guest;
use App\Models\Invitation;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GuestController extends Controller
{
    /**
     * Display guest list and RSVP analytics for an invitation.
     */
    public function index(Request $request, Invitation $invitation): View
    {
        $this->authorize('update', $invitation);

        $invitation->load(['plan', 'wishes' => fn ($q) => $q->latest()]);

        $search = $request->query('q');
        $statusFilter = $request->query('status');

        $guestsQuery = $invitation->guests()->latest();
        if ($search) {
            $guestsQuery->where('name', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%");
        }
        if ($statusFilter && in_array($statusFilter, ['attended', 'not_attended', 'maybe', 'pending'])) {
            $guestsQuery->where('rsvp_status', $statusFilter);
        }

        $guests = $guestsQuery->paginate(20)->withQueryString();

        $allGuests = $invitation->guests()->get();
        $analytics = [
            'total_invitations' => $allGuests->count(),
            'total_guests_estimated' => $allGuests->sum('guest_count'),
            'attended' => $allGuests->where('rsvp_status', 'attended')->count(),
            'not_attended' => $allGuests->where('rsvp_status', 'not_attended')->count(),
            'maybe' => $allGuests->where('rsvp_status', 'maybe')->count(),
            'pending' => $allGuests->where('rsvp_status', 'pending')->count(),
            'checked_in' => $allGuests->whereNotNull('checked_in_at')->count(),
            'total_wishes' => $invitation->wishes()->count(),
        ];

        return view('guests.index', compact('invitation', 'guests', 'analytics', 'search', 'statusFilter'));
    }

    /**
     * Store new guest.
     */
    public function store(StoreGuestRequest $request, Invitation $invitation): RedirectResponse
    {
        $this->authorize('update', $invitation);

        $validated = $request->validated();
        $invitation->guests()->create([
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? null,
            'guest_count' => $validated['guest_count'] ?? 1,
            'personal_token' => Str::random(10),
            'rsvp_status' => 'pending',
        ]);

        return back()->with('status', 'Tamu undangan berhasil ditambahkan!');
    }

    /**
     * Update guest details.
     */
    public function update(StoreGuestRequest $request, Invitation $invitation, Guest $guest): RedirectResponse
    {
        $this->authorize('update', $invitation);

        $guest->update($request->validated());

        return back()->with('status', 'Data tamu berhasil diperbarui!');
    }

    /**
     * Check-in guest at the venue.
     */
    public function checkIn(Invitation $invitation, Guest $guest): RedirectResponse
    {
        $this->authorize('update', $invitation);

        if ($guest->checked_in_at) {
            $guest->update(['checked_in_at' => null]);
            $msg = "Check-in dibatalkan untuk {$guest->name}.";
        } else {
            $guest->update([
                'checked_in_at' => Carbon::now(),
                'rsvp_status' => 'attended',
            ]);
            $msg = "{$guest->name} berhasil check-in di lokasi acara!";
        }

        return back()->with('status', $msg);
    }

    /**
     * Delete guest.
     */
    public function destroy(Invitation $invitation, Guest $guest): RedirectResponse
    {
        $this->authorize('update', $invitation);

        $guest->delete();

        return back()->with('status', 'Tamu berhasil dihapus dari daftar.');
    }

    /**
     * Export guest list to CSV.
     */
    public function exportCsv(Invitation $invitation): StreamedResponse
    {
        $this->authorize('update', $invitation);

        $guests = $invitation->guests()->orderBy('name')->get();
        $filename = "tamu-{$invitation->slug}-" . date('Ymd-His') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($guests, $invitation) {
            $output = fopen('php://output', 'w');
            // UTF-8 BOM for Microsoft Excel compatibility
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($output, ['No', 'Nama Tamu', 'No WhatsApp/HP', 'Status RSVP', 'Jumlah Tamu', 'Waktu Check-in', 'Link Personal']);

            $no = 1;
            foreach ($guests as $g) {
                $statusIndo = match ($g->rsvp_status) {
                    'attended' => 'Hadir',
                    'not_attended' => 'Tidak Hadir',
                    'maybe' => 'Ragu-ragu / Belum Pasti',
                    default => 'Belum Merespons',
                };

                $personalUrl = url("/u/{$invitation->slug}/{$g->personal_token}");

                fputcsv($output, [
                    $no++,
                    $g->name,
                    $g->phone ?? '-',
                    $statusIndo,
                    $g->guest_count,
                    $g->checked_in_at ? $g->checked_in_at->format('d/m/Y H:i') : 'Belum Check-in',
                    $personalUrl,
                ]);
            }

            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }
}
