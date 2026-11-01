<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-2xl text-stone-900 leading-tight">
            {{ __('Pustaka Musik Latar Undangan') }}
        </h2>
    </x-slot>

    <div class="py-10" x-data="{ addMusicModal: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold rounded-2xl flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm font-semibold rounded-2xl">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- STATS CARDS -->
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                <div class="bg-white rounded-2xl border border-stone-200 p-4 shadow-sm">
                    <div class="text-[11px] font-semibold text-stone-500 uppercase tracking-wider">Total Musik</div>
                    <div class="text-2xl font-black text-stone-900 mt-1">{{ $stats['total'] }}</div>
                </div>
                <div class="bg-white rounded-2xl border border-stone-200 p-4 shadow-sm">
                    <div class="text-[11px] font-semibold text-stone-500 uppercase tracking-wider">Musik Aktif</div>
                    <div class="text-2xl font-black text-emerald-600 mt-1">{{ $stats['active'] }}</div>
                </div>
                <div class="bg-white rounded-2xl border border-stone-200 p-4 shadow-sm">
                    <div class="text-[11px] font-semibold text-stone-500 uppercase tracking-wider">Pernikahan</div>
                    <div class="text-2xl font-black text-rose-600 mt-1">{{ $stats['wedding'] }}</div>
                </div>
                <div class="bg-white rounded-2xl border border-stone-200 p-4 shadow-sm">
                    <div class="text-[11px] font-semibold text-stone-500 uppercase tracking-wider">Khitanan</div>
                    <div class="text-2xl font-black text-amber-600 mt-1">{{ $stats['khitan'] }}</div>
                </div>
                <div class="bg-white rounded-2xl border border-stone-200 p-4 shadow-sm col-span-2 sm:col-span-1">
                    <div class="text-[11px] font-semibold text-stone-500 uppercase tracking-wider">Acara Umum</div>
                    <div class="text-2xl font-black text-indigo-600 mt-1">{{ $stats['general'] }}</div>
                </div>
            </div>

            <!-- MAIN CARD -->
            <div class="bg-white rounded-3xl border border-stone-200 shadow-sm p-6 sm:p-8">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
                    <div>
                        <h3 class="text-base font-extrabold text-stone-900 flex items-center gap-2">
                            <span>🎵</span>
                            <span>Koleksi Musik Latar Rekomendasi</span>
                        </h3>
                        <p class="text-xs text-stone-500">Disediakan oleh Admin untuk langsung dipilih pengguna saat mendesain undangan.</p>
                    </div>

                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <!-- Filter Kategori -->
                        <div class="flex items-center bg-stone-100 p-1 rounded-xl text-xs">
                            <a href="{{ route('admin.musics.index') }}"
                               class="px-3 py-1.5 rounded-lg font-semibold transition-colors {{ !request('category') || request('category') === 'all' ? 'bg-white shadow text-stone-900' : 'text-stone-600 hover:text-stone-900' }}">
                                Semua
                            </a>
                            <a href="{{ route('admin.musics.index', ['category' => 'wedding']) }}"
                               class="px-3 py-1.5 rounded-lg font-semibold transition-colors {{ request('category') === 'wedding' ? 'bg-white shadow text-stone-900' : 'text-stone-600 hover:text-stone-900' }}">
                                Wedding
                            </a>
                            <a href="{{ route('admin.musics.index', ['category' => 'khitan']) }}"
                               class="px-3 py-1.5 rounded-lg font-semibold transition-colors {{ request('category') === 'khitan' ? 'bg-white shadow text-stone-900' : 'text-stone-600 hover:text-stone-900' }}">
                                Khitan
                            </a>
                            <a href="{{ route('admin.musics.index', ['category' => 'general']) }}"
                               class="px-3 py-1.5 rounded-lg font-semibold transition-colors {{ request('category') === 'general' ? 'bg-white shadow text-stone-900' : 'text-stone-600 hover:text-stone-900' }}">
                                Umum
                            </a>
                        </div>

                        <button @click="addMusicModal = true"
                                class="bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-md transition-colors shrink-0">
                            + Tambah Musik
                        </button>
                    </div>
                </div>

                <!-- TABLE LISTING -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-stone-200 text-[11px] font-bold text-stone-400 uppercase tracking-wider">
                                <th class="py-3 px-4">Urutan</th>
                                <th class="py-3 px-4">Judul Lagu / Musik</th>
                                <th class="py-3 px-4">Kategori</th>
                                <th class="py-3 px-4">Putar Preview</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100 text-xs">
                            @forelse ($musics as $music)
                                <tr class="hover:bg-stone-50/80 transition-colors">
                                    <td class="py-4 px-4 font-mono text-stone-400 font-semibold">
                                        #{{ $music->sort_order }}
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="font-bold text-stone-900">{{ $music->title }}</div>
                                        <div class="text-[11px] text-stone-500 font-medium">{{ $music->artist ?? 'Tanpa Nama Artis' }}</div>
                                        <div class="text-[10px] text-stone-400 font-mono mt-0.5 truncate max-w-xs">{{ $music->file_path }}</div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider
                                            {{ $music->category === 'wedding' ? 'bg-rose-50 text-rose-700 border border-rose-200' :
                                               ($music->category === 'khitan' ? 'bg-amber-50 text-amber-700 border border-amber-200' :
                                               ($music->category === 'general' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-stone-100 text-stone-700')) }}">
                                            {{ $music->category_label }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4">
                                        <audio controls preload="none" class="h-9 w-48 sm:w-64 rounded-lg">
                                            <source src="{{ $music->audio_url }}" type="audio/mpeg">
                                            Browser Anda tidak mendukung elemen audio.
                                        </audio>
                                    </td>
                                    <td class="py-4 px-4">
                                        <form action="{{ route('admin.musics.toggle', $music) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold transition-colors
                                                        {{ $music->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100' : 'bg-stone-100 text-stone-500 hover:bg-stone-200' }}"
                                                    title="Klik untuk ubah status">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $music->is_active ? 'bg-emerald-500' : 'bg-stone-400' }}"></span>
                                                {{ $music->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td class="py-4 px-4 text-right">
                                        <form action="{{ route('admin.musics.destroy', $music) }}" method="POST"
                                              onsubmit="return confirm('Yakin ingin menghapus musik \'{{ $music->title }}\' ini dari pustaka?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-600 hover:text-rose-800 font-bold text-xs p-1">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-stone-400 text-xs">
                                        Belum ada musik dalam kategori ini. Silakan tambahkan melalui tombol di atas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($musics->hasPages())
                    <div class="mt-6 border-t border-stone-100 pt-4">
                        {{ $musics->links() }}
                    </div>
                @endif
            </div>

            <!-- MODAL TAMBAH MUSIK -->
            <div x-show="addMusicModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-stone-900/50 p-4">
                <div @click.away="addMusicModal = false" class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-base font-bold text-stone-900">Tambah Musik Latar Baru</h4>
                        <button @click="addMusicModal = false" class="text-stone-400 hover:text-stone-600 text-sm font-bold">✕</button>
                    </div>

                    <form action="{{ route('admin.musics.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-stone-700 mb-1">Judul Lagu / Musik *</label>
                            <input type="text" name="title" required class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200 focus:ring-1 focus:ring-rose-500" placeholder="Contoh: Romantic Acoustic Waltz">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-stone-700 mb-1">Artis / Pembuat</label>
                                <input type="text" name="artist" class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200 focus:ring-1 focus:ring-rose-500" placeholder="Contoh: Kawinan Studio">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-stone-700 mb-1">Kategori Acara *</label>
                                <select name="category" required class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200 focus:ring-1 focus:ring-rose-500">
                                    <option value="wedding">Pernikahan (Wedding)</option>
                                    <option value="khitan">Khitanan (Khitan)</option>
                                    <option value="general">Acara Umum (General)</option>
                                    <option value="all">Semua Jenis Acara</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-stone-700 mb-1">Urutan Prioritas (Sort Order)</label>
                            <input type="number" name="sort_order" value="1" min="0" class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200 focus:ring-1 focus:ring-rose-500">
                        </div>

                        <div class="border-t border-stone-100 pt-3 space-y-3">
                            <div>
                                <label class="block text-xs font-bold text-stone-700 mb-1">Unggah Berkas Audio (MP3 / WAV / OGG)</label>
                                <input type="file" name="music_file" accept="audio/*" class="w-full text-xs text-stone-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-rose-50 file:text-rose-700 hover:file:bg-rose-100">
                                <p class="text-[11px] text-stone-400 mt-1">Maksimal 15MB.</p>
                            </div>

                            <div class="text-center text-[11px] text-stone-400 font-semibold">— ATAU —</div>

                            <div>
                                <label class="block text-xs font-bold text-stone-700 mb-1">Tautan / URL Audio Langsung</label>
                                <input type="text" name="file_url" class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200 focus:ring-1 focus:ring-rose-500" placeholder="https://... atau /audio/nama_file.mp3">
                            </div>
                        </div>

                        <div class="flex justify-end gap-2 pt-3 border-t border-stone-100">
                            <button type="button" @click="addMusicModal = false" class="px-4 py-2 text-xs font-bold text-stone-600 hover:bg-stone-100 rounded-xl">Batal</button>
                            <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-md">Simpan Musik</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
