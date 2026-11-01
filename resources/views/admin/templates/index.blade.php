<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-2xl text-stone-900 leading-tight">
            {{ __('Manajemen Template Undangan') }}
        </h2>
    </x-slot>

    <div class="py-10" x-data="{ addTemplateModal: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold rounded-2xl">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm font-semibold rounded-2xl">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white rounded-3xl border border-stone-200 shadow-sm p-6 sm:p-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-base font-extrabold text-stone-900">Koleksi Desain Template Undangan</h3>
                        <p class="text-xs text-stone-500">Unggah berkas Blade view baru dan gambar thumbnail.</p>
                    </div>

                    <button @click="addTemplateModal = true"
                            class="bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-md">
                        + Tambah Template Baru
                    </button>
                </div>

                <!-- MODAL TAMBAH TEMPLATE -->
                <div x-show="addTemplateModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-stone-900/50 p-4">
                    <div @click.away="addTemplateModal = false" class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl">
                        <h4 class="text-base font-bold text-stone-900 mb-4">Unggah Template Desain Baru</h4>
                        <form action="{{ route('admin.templates.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-stone-700 mb-1">Nama Template *</label>
                                <input type="text" name="name" required class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200" placeholder="Contoh: Royal Gold Wedding">
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-stone-700 mb-1">Kategori *</label>
                                    <select name="category" required class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200">
                                        <option value="wedding">Pernikahan (Wedding)</option>
                                        <option value="khitan">Khitanan (Khitan)</option>
                                        <option value="general">Acara Umum (General)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-stone-700 mb-1">Gaya / Style *</label>
                                    <input type="text" name="style" required class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200" placeholder="classic, floral, modern, etc.">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-stone-700 mb-1">Upload Berkas Blade View (.blade.php)</label>
                                <input type="file" name="blade_file" class="w-full text-xs text-stone-500">
                                <p class="text-[11px] text-stone-400 mt-1">Otomatis tersimpan ke <code>resources/views/templates/</code></p>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-stone-700 mb-1">Upload Gambar Thumbnail</label>
                                <input type="file" name="thumbnail" accept="image/*" class="w-full text-xs text-stone-500">
                                <p class="text-[11px] text-stone-400 mt-1">Format: JPG, PNG, WEBP, SVG. Maks 2MB.</p>
                            </div>

                            <div class="flex justify-end gap-2 pt-2">
                                <button type="button" @click="addTemplateModal = false" class="px-4 py-2 text-xs font-bold text-stone-500">Batal</button>
                                <button type="submit" class="bg-rose-600 text-white text-xs font-bold px-5 py-2.5 rounded-xl shadow-md">Simpan & Terbitkan</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- DAFTAR TEMPLATE CARDS -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($templates as $t)
                        <div class="bg-stone-50 rounded-2xl border border-stone-200 overflow-hidden flex flex-col justify-between">
                            <div>
                                <div class="aspect-video bg-stone-200 relative overflow-hidden">
                                    <img src="{{ $t->thumbnail_path ?? '/templates/thumbnails/' . $t->blade_view . '.svg' }}" class="w-full h-full object-cover">
                                    <span class="absolute top-3 left-3 px-2 py-0.5 rounded-full text-[10px] font-bold text-white {{ $t->category === 'wedding' ? 'bg-rose-600' : ($t->category === 'khitan' ? 'bg-sky-600' : 'bg-emerald-600') }}">
                                        {{ strtoupper($t->category) }}
                                    </span>
                                </div>
                                <div class="p-4">
                                    <h4 class="font-bold text-stone-900 text-base">{{ $t->name }}</h4>
                                    <p class="text-xs text-stone-500 font-mono mt-1">View: {{ $t->blade_view }}</p>
                                    <div class="mt-3 flex items-center justify-between text-xs text-stone-600">
                                        <span>Digunakan: <strong>{{ $t->invitations_count }}x</strong></span>
                                        <span class="font-bold {{ $t->is_active ? 'text-emerald-600' : 'text-stone-400' }}">
                                            {{ $t->is_active ? '● Aktif' : 'Non-aktif' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 pt-0 border-t border-stone-200/60 mt-3 flex justify-between items-center">
                                <form action="{{ route('admin.templates.destroy', $t) }}" method="POST" onsubmit="return confirm('Hapus template ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:text-rose-800 text-xs font-bold" {{ $t->invitations_count > 0 ? 'disabled title="Sedang digunakan"' : '' }}>
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
