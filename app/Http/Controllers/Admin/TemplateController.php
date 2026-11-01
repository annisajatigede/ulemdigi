<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TemplateController extends Controller
{
    public function index(): View
    {
        $templates = Template::withCount('invitations')->orderBy('id')->get();
        return view('admin.templates.index', compact('templates'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'category' => ['required', 'in:wedding,khitan,general'],
            'style' => ['required', 'string', 'max:50'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'blade_file' => ['nullable', 'file', 'max:2048'],
            'blade_view' => ['nullable', 'string', 'max:100'],
        ]);

        $bladeView = $validated['blade_view'] ?? '';

        // Handle uploaded Blade file if provided
        if ($request->hasFile('blade_file')) {
            $file = $request->file('blade_file');
            $safeName = Str::slug($validated['name'], '_');
            $fileName = $safeName . '.blade.php';
            $destinationDir = resource_path('views/templates');
            if (! is_dir($destinationDir)) {
                mkdir($destinationDir, 0755, true);
            }
            $file->move($destinationDir, $fileName);
            $bladeView = 'templates.' . $safeName;
        }

        if (empty($bladeView)) {
            $bladeView = 'templates.' . Str::slug($validated['name'], '_');
        }

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbFile = $request->file('thumbnail');
            $thumbName = 'thumb_' . time() . '_' . Str::slug($validated['name']) . '.' . $thumbFile->getClientOriginalExtension();
            $thumbDir = public_path('templates/thumbnails');
            if (! is_dir($thumbDir)) {
                mkdir($thumbDir, 0755, true);
            }
            $thumbFile->move($thumbDir, $thumbName);
            $thumbnailPath = '/templates/thumbnails/' . $thumbName;
        }

        Template::create([
            'name' => $validated['name'],
            'category' => $validated['category'],
            'style' => $validated['style'],
            'blade_view' => $bladeView,
            'thumbnail_path' => $thumbnailPath,
            'is_active' => true,
        ]);

        return back()->with('status', 'Template baru berhasil ditambahkan!');
    }

    public function update(Request $request, Template $template): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'category' => ['required', 'in:wedding,khitan,general'],
            'style' => ['required', 'string', 'max:50'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'blade_file' => ['nullable', 'file', 'max:2048'],
            'blade_view' => ['required', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $template->name = $validated['name'];
        $template->category = $validated['category'];
        $template->style = $validated['style'];
        $template->blade_view = $validated['blade_view'];
        $template->is_active = $request->boolean('is_active', true);

        if ($request->hasFile('blade_file')) {
            $file = $request->file('blade_file');
            $safeName = Str::slug($validated['name'], '_');
            $fileName = $safeName . '.blade.php';
            $destinationDir = resource_path('views/templates');
            $file->move($destinationDir, $fileName);
            $template->blade_view = 'templates.' . $safeName;
        }

        if ($request->hasFile('thumbnail')) {
            $thumbFile = $request->file('thumbnail');
            $thumbName = 'thumb_' . time() . '_' . Str::slug($validated['name']) . '.' . $thumbFile->getClientOriginalExtension();
            $thumbDir = public_path('templates/thumbnails');
            if (! is_dir($thumbDir)) {
                mkdir($thumbDir, 0755, true);
            }
            $thumbFile->move($thumbDir, $thumbName);
            $template->thumbnail_path = '/templates/thumbnails/' . $thumbName;
        }

        $template->save();

        return back()->with('status', 'Template berhasil diperbarui!');
    }

    public function destroy(Template $template): RedirectResponse
    {
        if ($template->invitations()->exists()) {
            return back()->with('error', 'Template ini sedang digunakan oleh undangan aktif dan tidak dapat dihapus.');
        }

        $template->delete();

        return back()->with('status', 'Template berhasil dihapus.');
    }
}
