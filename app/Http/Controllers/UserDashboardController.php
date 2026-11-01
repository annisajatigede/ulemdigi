<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserDashboardController extends Controller
{
    /**
     * Show the user dashboard.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $invitations = Invitation::with(['template', 'plan', 'events'])
            ->where('user_id', $user->id)
            ->withCount(['guests', 'wishes'])
            ->latest()
            ->get();

        $transactions = $user->transactions()
            ->with(['plan', 'invitation'])
            ->latest()
            ->take(5)
            ->get();

        $stats = [
            'total' => $invitations->count(),
            'published' => $invitations->where('status', 'published')->count(),
            'draft' => $invitations->where('status', 'draft')->count(),
            'expired' => $invitations->filter->isExpired()->count(),
            'total_guests' => $invitations->sum('guests_count'),
            'total_wishes' => $invitations->sum('wishes_count'),
        ];

        return view('dashboard', compact('invitations', 'transactions', 'stats'));
    }
}
