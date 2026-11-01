<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlanController extends Controller
{
    public function index(): View
    {
        $plans = Plan::withCount(['invitations', 'transactions'])->orderBy('price')->get();
        return view('admin.plans.index', compact('plans'));
    }

    public function update(Request $request, Plan $plan): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'price' => ['required', 'numeric', 'min:0'],
            'duration_days' => ['required', 'integer', 'min:1'],
            'max_photos' => ['required', 'integer', 'min:1'],
            'max_events' => ['required', 'integer', 'min:1'],
            'features_text' => ['required', 'string'],
        ]);

        $features = array_values(array_filter(array_map('trim', explode("\n", $validated['features_text']))));

        $plan->update([
            'name' => $validated['name'],
            'price' => $validated['price'],
            'duration_days' => $validated['duration_days'],
            'max_photos' => $validated['max_photos'],
            'max_events' => $validated['max_events'],
            'features' => $features,
        ]);

        return back()->with('status', "Paket {$plan->name} berhasil diperbarui!");
    }
}
