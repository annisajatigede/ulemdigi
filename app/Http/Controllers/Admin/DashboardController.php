<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\Plan;
use App\Models\Template;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalRevenue = Transaction::where('status', 'paid')->sum('amount');
        $totalTransactions = Transaction::count();
        $paidTransactions = Transaction::where('status', 'paid')->count();
        $totalUsers = User::count();
        $totalInvitations = Invitation::count();
        $activeTemplates = Template::where('is_active', true)->count();

        $recentTransactions = Transaction::with(['user', 'plan', 'invitation'])
            ->latest()
            ->take(8)
            ->get();

        $recentUsers = User::latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalRevenue',
            'totalTransactions',
            'paidTransactions',
            'totalUsers',
            'totalInvitations',
            'activeTemplates',
            'recentTransactions',
            'recentUsers'
        ));
    }
}
