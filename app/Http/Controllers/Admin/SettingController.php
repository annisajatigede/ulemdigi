<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        $settings = [
            'site_name' => Setting::get('site_name', 'Kawinan.id'),
            'site_tagline' => Setting::get('site_tagline', 'Platform Undangan Digital Berbayar Modern & Elegan'),
            'site_description' => Setting::get('site_description', 'Solusi undangan online terbaik untuk momen spesial Anda.'),
            'contact_whatsapp' => Setting::get('contact_whatsapp', '6281234567890'),
            'contact_email' => Setting::get('contact_email', 'support@kawinan.id'),
            'logo_url' => Setting::get('logo_url'),
            'favicon_url' => Setting::get('favicon_url'),
            'midtrans_server_key' => Setting::get('midtrans_server_key', env('MIDTRANS_SERVER_KEY')),
            'midtrans_client_key' => Setting::get('midtrans_client_key', env('MIDTRANS_CLIENT_KEY')),
            'midtrans_is_production' => Setting::get('midtrans_is_production', '0'),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'site_name' => ['required', 'string', 'max:100'],
            'site_tagline' => ['required', 'string', 'max:200'],
            'site_description' => ['required', 'string', 'max:500'],
            'contact_whatsapp' => ['nullable', 'string', 'max:30'],
            'contact_email' => ['nullable', 'email', 'max:100'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,svg,webp', 'max:1024'],
            'favicon' => ['nullable', 'file', 'mimes:ico,png,svg', 'max:512'],
            'midtrans_server_key' => ['nullable', 'string', 'max:255'],
            'midtrans_client_key' => ['nullable', 'string', 'max:255'],
            'midtrans_is_production' => ['nullable', 'in:0,1'],
        ]);

        Setting::set('site_name', $validated['site_name']);
        Setting::set('site_tagline', $validated['site_tagline']);
        Setting::set('site_description', $validated['site_description']);
        Setting::set('contact_whatsapp', $validated['contact_whatsapp'] ?? '');
        Setting::set('contact_email', $validated['contact_email'] ?? '');
        Setting::set('midtrans_server_key', $validated['midtrans_server_key'] ?? '');
        Setting::set('midtrans_client_key', $validated['midtrans_client_key'] ?? '');
        Setting::set('midtrans_is_production', $request->input('midtrans_is_production', '0'));

        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoName = 'logo_' . time() . '.' . $logo->getClientOriginalExtension();
            $logo->move(public_path('uploads'), $logoName);
            Setting::set('logo_url', '/uploads/' . $logoName);
        }

        if ($request->hasFile('favicon')) {
            $fav = $request->file('favicon');
            $favName = 'favicon_' . time() . '.' . $fav->getClientOriginalExtension();
            $fav->move(public_path('uploads'), $favName);
            Setting::set('favicon_url', '/uploads/' . $favName);
        }

        return back()->with('status', 'Pengaturan situs berhasil disimpan!');
    }
}
