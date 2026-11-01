<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Music;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MusicController extends Controller
{
    public function index(Request $request): View
    {
        $query = Music::query();

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        $musics = $query->orderBy('sort_order')->latest('id')->paginate(15)->withQueryString();

        $stats = [
            'total' => Music::count(),
            'active' => Music::where('is_active', true)->count(),
            'wedding' => Music::where('category', 'wedding')->count(),
            'khitan' => Music::where('category', 'khitan')->count(),
            'general' => Music::where('category', 'general')->count(),
        ];

        return view('admin.musics.index', compact('musics', 'stats'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'artist' => ['nullable', 'string', 'max:150'],
            'category' => ['required', 'in:wedding,khitan,general,all'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'music_file' => ['nullable', 'file', 'mimes:mp3,wav,ogg,m4a', 'max:15360'],
            'file_url' => ['nullable', 'string', 'max:255'],
        ]);

        if (! $request->hasFile('music_file') && empty($validated['file_url'])) {
            return back()->withErrors(['music_file' => 'Harap unggah berkas audio (MP3/WAV/OGG) atau cantumkan URL audio langsung.'])->withInput();
        }

        $filePath = null;

        if ($request->hasFile('music_file')) {
            $file = $request->file('music_file');
            $safeSlug = Str::slug($validated['title'], '_');
            $fileName = 'preset_' . time() . '_' . $safeSlug . '.' . $file->getClientOriginalExtension();
            $destinationDir = public_path('audio');
            if (! is_dir($destinationDir)) {
                mkdir($destinationDir, 0755, true);
            }
            $file->move($destinationDir, $fileName);
            $filePath = '/audio/' . $fileName;
        } else {
            $filePath = $validated['file_url'];
        }

        Music::create([
            'title' => $validated['title'],
            'artist' => $validated['artist'] ?? 'Kawinan Studio',
            'category' => $validated['category'],
            'file_path' => $filePath,
            'is_active' => true,
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        return back()->with('status', 'Musik latar baru berhasil ditambahkan ke pustaka!');
    }

    public function toggle(Music $music): RedirectResponse
    {
        $music->is_active = ! $music->is_active;
        $music->save();

        return back()->with('status', 'Status aktif musik "' . $music->title . '" berhasil diubah.');
    }

    public function destroy(Music $music): RedirectResponse
    {
        $filePath = public_path(ltrim($music->file_path, '/'));
        if (str_starts_with($music->file_path, '/audio/preset_') && file_exists($filePath)) {
            @unlink($filePath);
        }

        $music->delete();

        return back()->with('status', 'Musik latar berhasil dihapus dari pustaka.');
    }
}
