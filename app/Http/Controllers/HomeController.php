<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Setting;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the SaaS landing page.
     */
    public function index(Request $request): View
    {
        $selectedCategory = $request->query('category', 'all');

        $templatesQuery = Template::where('is_active', true);
        if ($selectedCategory !== 'all' && in_array($selectedCategory, ['wedding', 'khitan', 'general'])) {
            $templatesQuery->where('category', $selectedCategory);
        }
        $templates = $templatesQuery->orderBy('id')->get();

        $plans = Plan::orderBy('price')->get();

        $settings = [
            'site_name' => Setting::get('site_name', 'Kawinan.id'),
            'site_tagline' => Setting::get('site_tagline', 'Platform Undangan Digital Berbayar Modern & Elegan'),
            'site_description' => Setting::get('site_description', 'Buat undangan pernikahan, khitanan, dan momen spesial dalam hitungan menit.'),
            'contact_whatsapp' => Setting::get('contact_whatsapp', '6281234567890'),
        ];

        return view('welcome', compact('templates', 'plans', 'settings', 'selectedCategory'));
    }
}
