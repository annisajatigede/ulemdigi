<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('dashboard') }}" class="text-xs text-stone-500 hover:text-stone-800 font-semibold">← Dashboard</a>
                    <span class="text-stone-300">/</span>
                    <span class="text-xs font-bold text-rose-600 uppercase">{{ $invitation->category }}</span>
                </div>
                <h2 class="font-extrabold text-2xl text-stone-900 leading-tight mt-1">
                    Editor Undangan: <span class="text-rose-600">{{ $safeData['party1']['name'] ?? 'Momen' }}</span>
                </h2>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('editor.preview', $invitation) }}" target="_blank"
                   class="bg-white hover:bg-stone-50 text-stone-700 border border-stone-200 text-xs font-bold px-4 py-2.5 rounded-xl shadow-sm transition-all flex items-center gap-1.5">
                    <span>Lihat Tab Baru ↗</span>
                </a>
                <button type="submit" form="invitation-editor-form"
                        class="bg-rose-600 hover:bg-rose-700 text-white text-xs sm:text-sm font-bold px-6 py-2.5 rounded-xl shadow-md shadow-rose-600/25 transition-all">
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </x-slot>

    @php
        $isKhitan = ($invitation->category === 'khitan');
        $isGeneral = ($invitation->category === 'general');
        $maxPhotos = $invitation->plan->max_photos ?? 15;
        $maxEvents = $invitation->plan->max_events ?? 3;
    @endphp

    <div class="py-8" x-data="invitationEditor()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold rounded-2xl">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm font-semibold rounded-2xl">
                    <p class="font-bold mb-1">Terdapat kesalahan pengisian formulir:</p>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                <!-- LEFT COLUMN: SECTION FORMS -->
                <div class="lg:col-span-7 bg-white rounded-3xl border border-stone-200 shadow-sm p-6 sm:p-8">
                    <!-- SECTION TABS -->
                    <div class="flex overflow-x-auto space-x-2 border-b border-stone-200 pb-3 mb-6 scrollbar-none">
                        <button type="button" @click="activeTab = 'dasar'"
                                :class="activeTab === 'dasar' ? 'bg-stone-900 text-white' : 'bg-stone-100 text-stone-600 hover:bg-stone-200'"
                                class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-colors">
                            1. Dasar & Tema
                        </button>
                        <button type="button" @click="activeTab = 'pembuka'"
                                :class="activeTab === 'pembuka' ? 'bg-stone-900 text-white' : 'bg-stone-100 text-stone-600 hover:bg-stone-200'"
                                class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-colors">
                            2. Pembuka
                        </button>
                        <button type="button" @click="activeTab = 'mempelai'"
                                :class="activeTab === 'mempelai' ? 'bg-stone-900 text-white' : 'bg-stone-100 text-stone-600 hover:bg-stone-200'"
                                class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-colors">
                            3. {{ $isKhitan ? 'Anak & Orang Tua' : ($isGeneral ? 'Penyelenggara' : 'Mempelai') }}
                        </button>
                        <button type="button" @click="activeTab = 'acara'"
                                :class="activeTab === 'acara' ? 'bg-stone-900 text-white' : 'bg-stone-100 text-stone-600 hover:bg-stone-200'"
                                class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-colors">
                            4. Acara
                        </button>
                        <button type="button" @click="activeTab = 'kisah'"
                                :class="activeTab === 'kisah' ? 'bg-stone-900 text-white' : 'bg-stone-100 text-stone-600 hover:bg-stone-200'"
                                class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-colors">
                            5. Kisah / Cerita
                        </button>
                        <button type="button" @click="activeTab = 'galeri'"
                                :class="activeTab === 'galeri' ? 'bg-stone-900 text-white' : 'bg-stone-100 text-stone-600 hover:bg-stone-200'"
                                class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-colors">
                            6. Galeri ({{ count($safeData['gallery'] ?? []) }}/{{ $maxPhotos }})
                        </button>
                        <button type="button" @click="activeTab = 'amplop'"
                                :class="activeTab === 'amplop' ? 'bg-stone-900 text-white' : 'bg-stone-100 text-stone-600 hover:bg-stone-200'"
                                class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-colors">
                            7. Amplop Digital
                        </button>
                        <button type="button" @click="activeTab = 'penutup'"
                                :class="activeTab === 'penutup' ? 'bg-stone-900 text-white' : 'bg-stone-100 text-stone-600 hover:bg-stone-200'"
                                class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-colors">
                            8. RSVP & Penutup
                        </button>
                    </div>

                    <!-- THE EDIT FORM -->
                    <form id="invitation-editor-form" action="{{ route('editor.update', $invitation) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- 1. TAB DASAR & TEMA -->
                        <div x-show="activeTab === 'dasar'" class="space-y-6">
                            <h3 class="text-base font-bold text-stone-900">Pengaturan Dasar & Tema</h3>

                            <div>
                                <label class="block text-xs font-bold text-stone-700 mb-1">Tautan / Slug Undangan *</label>
                                <div class="flex items-center">
                                    <span class="bg-stone-100 text-stone-500 text-xs px-3 py-2.5 rounded-l-xl border border-r-0 border-stone-200 font-mono">
                                        {{ url('/u/') }}/
                                    </span>
                                    <input type="text" name="slug" value="{{ old('slug', $invitation->slug) }}" required
                                           class="w-full text-xs font-mono px-3 py-2.5 rounded-r-xl border border-stone-200 focus:ring-1 focus:ring-rose-500">
                                </div>
                                <p class="text-[11px] text-stone-400 mt-1">Hanya huruf kecil, angka, dan tanda hubung (-).</p>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-stone-700 mb-2">Desain Template Digunakan</label>
                                <div class="grid grid-cols-3 gap-3">
                                    @foreach($templates as $tmpl)
                                        <label class="cursor-pointer">
                                            <input type="radio" name="template_id" value="{{ $tmpl->id }}" {{ $invitation->template_id === $tmpl->id ? 'checked' : '' }} class="peer sr-only">
                                            <div class="p-3 rounded-xl border-2 peer-checked:border-rose-600 peer-checked:bg-rose-50/40 text-center transition-all border-stone-200 hover:border-stone-300">
                                                <div class="font-bold text-xs text-stone-800">{{ $tmpl->name }}</div>
                                                <span class="text-[10px] text-stone-500 capitalize">{{ $tmpl->category }}</span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-stone-700 mb-1">Warna Aksen Tema</label>
                                <div class="flex items-center gap-3">
                                    <input type="color" name="theme_color" value="{{ old('theme_color', $safeData['theme_color'] ?? '#e11d48') }}"
                                           class="w-10 h-10 p-0.5 rounded-xl border border-stone-200 cursor-pointer">
                                    <span class="text-xs font-mono text-stone-600">{{ $safeData['theme_color'] ?? '#e11d48' }}</span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-stone-700 mb-1">Musik Latar Belakang</label>
                                <div class="space-y-2">
                                    <input type="file" name="custom_music" accept="audio/*"
                                           class="block w-full text-xs text-stone-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-rose-50 file:text-rose-700 hover:file:bg-rose-100">
                                    <p class="text-[11px] text-stone-400">Format: MP3, M4A, OGG. Maksimal 8MB.</p>
                                    @if(!empty($safeData['music_path']))
                                        <div class="text-[11px] text-emerald-600 font-medium">✓ Musik saat ini aktif: {{ basename($safeData['music_path']) }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- 2. TAB PEMBUKA -->
                        <div x-show="activeTab === 'pembuka'" x-cloak class="space-y-5">
                            <h3 class="text-base font-bold text-stone-900">Bagian Pembuka (Opening)</h3>

                            <div>
                                <label class="block text-xs font-bold text-stone-700 mb-1">Judul Undangan / Acara *</label>
                                <input type="text" name="opening[title]" value="{{ old('opening.title', $safeData['opening']['title'] ?? '') }}" required
                                       class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200 focus:ring-1 focus:ring-rose-500">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-stone-700 mb-1">Salam Pembuka</label>
                                <input type="text" name="opening[greeting]" value="{{ old('opening.greeting', $safeData['opening']['greeting'] ?? '') }}"
                                       class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200 focus:ring-1 focus:ring-rose-500">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-stone-700 mb-1">
                                    {{ $isKhitan ? 'Kutipan / Doa Khitanan' : ($isGeneral ? 'Kutipan / Kata Mutiara' : 'Kutipan Ayat / Doa Pernikahan') }}
                                </label>
                                <textarea name="opening[quote]" rows="3"
                                          class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200 focus:ring-1 focus:ring-rose-500">{{ old('opening.quote', $safeData['opening']['quote'] ?? '') }}</textarea>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-stone-700 mb-1">Sumber Kutipan / Dalil (Opsional)</label>
                                <input type="text" name="opening[quote_source]" value="{{ old('opening.quote_source', $safeData['opening']['quote_source'] ?? '') }}"
                                       class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200 focus:ring-1 focus:ring-rose-500"
                                       placeholder="Contoh: QS. Ar-Rum: 21">
                            </div>
                        </div>

                        <!-- 3. TAB TOKOH UTAMA / MEMPELAI -->
                        <div x-show="activeTab === 'mempelai'" x-cloak class="space-y-6">
                            <h3 class="text-base font-bold text-stone-900">
                                {{ $isKhitan ? 'Informasi Anak yang Dikhitan & Orang Tua' : ($isGeneral ? 'Informasi Penyelenggara' : 'Informasi Kedua Mempelai') }}
                            </h3>

                            <!-- Pihak 1 -->
                            <div class="p-5 bg-stone-50 rounded-2xl border border-stone-200 space-y-4">
                                <h4 class="text-xs font-extrabold uppercase tracking-wider text-rose-600">
                                    {{ $isKhitan ? 'Profil Anak' : ($isGeneral ? 'Profil Penyelenggara' : 'Mempelai Wanita') }}
                                </h4>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-bold text-stone-700 mb-1">Nama Panggilan *</label>
                                        <input type="text" name="party1[name]" value="{{ old('party1.name', $safeData['party1']['name'] ?? '') }}" required
                                               class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-stone-700 mb-1">Nama Lengkap & Gelar *</label>
                                        <input type="text" name="party1[full_name]" value="{{ old('party1.full_name', $safeData['party1']['full_name'] ?? '') }}" required
                                               class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-bold text-stone-700 mb-1">Urutan Kelahiran</label>
                                        <input type="text" name="party1[child_order]" value="{{ old('party1.child_order', $safeData['party1']['child_order'] ?? '') }}"
                                               class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200"
                                               placeholder="Contoh: Putri pertama dari">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-stone-700 mb-1">Nama Orang Tua</label>
                                        <input type="text" name="party1[parents]" value="{{ old('party1.parents', $safeData['party1']['parents'] ?? '') }}"
                                               class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200"
                                               placeholder="Contoh: Bpk. Ahmad & Ibu Siti">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-stone-700 mb-1">Foto Profil</label>
                                    <input type="file" name="party1_photo" accept="image/*" class="text-xs text-stone-500">
                                    @if(!empty($safeData['party1']['photo_path']))
                                        <div class="mt-2 flex items-center gap-2">
                                            <img src="{{ $safeData['party1']['photo_path'] }}" class="w-10 h-10 object-cover rounded-full border">
                                            <span class="text-[11px] text-stone-500">Foto terpasang</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Pihak 2 (Hanya jika bukan general) -->
                            @if(!$isGeneral)
                            <div class="p-5 bg-stone-50 rounded-2xl border border-stone-200 space-y-4">
                                <h4 class="text-xs font-extrabold uppercase tracking-wider text-rose-600">
                                    {{ $isKhitan ? 'Keluarga / Orang Tua' : 'Mempelai Pria' }}
                                </h4>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-bold text-stone-700 mb-1">Nama Panggilan</label>
                                        <input type="text" name="party2[name]" value="{{ old('party2.name', $safeData['party2']['name'] ?? '') }}"
                                               class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-stone-700 mb-1">Nama Lengkap & Gelar</label>
                                        <input type="text" name="party2[full_name]" value="{{ old('party2.full_name', $safeData['party2']['full_name'] ?? '') }}"
                                               class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-bold text-stone-700 mb-1">Urutan Kelahiran</label>
                                        <input type="text" name="party2[child_order]" value="{{ old('party2.child_order', $safeData['party2']['child_order'] ?? '') }}"
                                               class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-stone-700 mb-1">Nama Orang Tua</label>
                                        <input type="text" name="party2[parents]" value="{{ old('party2.parents', $safeData['party2']['parents'] ?? '') }}"
                                               class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-stone-700 mb-1">Foto Profil</label>
                                    <input type="file" name="party2_photo" accept="image/*" class="text-xs text-stone-500">
                                    @if(!empty($safeData['party2']['photo_path']))
                                        <div class="mt-2 flex items-center gap-2">
                                            <img src="{{ $safeData['party2']['photo_path'] }}" class="w-10 h-10 object-cover rounded-full border">
                                            <span class="text-[11px] text-stone-500">Foto terpasang</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- 4. TAB ACARA (MULTI ACARA) -->
                        <div x-show="activeTab === 'acara'" x-cloak class="space-y-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-base font-bold text-stone-900">Rangkaian Acara</h3>
                                    <p class="text-xs text-stone-500">Maksimal {{ $maxEvents }} acara sesuai paket Anda.</p>
                                </div>
                                <button type="button" @click="addEvent()" :disabled="events.length >= {{ $maxEvents }}"
                                        class="text-xs bg-stone-900 hover:bg-stone-800 disabled:opacity-40 text-white font-bold px-3 py-1.5 rounded-xl">
                                    + Tambah Acara
                                </button>
                            </div>

                            <template x-for="(event, index) in events" :key="index">
                                <div class="p-5 bg-stone-50 rounded-2xl border border-stone-200 space-y-3 relative">
                                    <div class="flex justify-between items-center">
                                        <span class="font-bold text-xs uppercase tracking-wider text-rose-600" x-text="'Acara #' + (index + 1)"></span>
                                        <button type="button" @click="removeEvent(index)" x-show="events.length > 1"
                                                class="text-rose-600 hover:text-rose-800 text-xs font-bold">Hapus</button>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold text-stone-700 mb-1">Nama Acara *</label>
                                        <input type="text" :name="'events[' + index + '][title]'" x-model="event.title" required
                                               class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200"
                                               placeholder="Contoh: Akad Nikah / Resepsi">
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                        <div class="sm:col-span-2">
                                            <label class="block text-xs font-semibold text-stone-700 mb-1">Waktu Mulai *</label>
                                            <input type="datetime-local" :name="'events[' + index + '][start_at]'" x-model="event.start_at" required
                                                   class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-stone-700 mb-1">Zona Waktu</label>
                                            <select :name="'events[' + index + '][timezone]'" x-model="event.timezone"
                                                    class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200">
                                                <option value="WIB">WIB</option>
                                                <option value="WITA">WITA</option>
                                                <option value="WIT">WIT</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold text-stone-700 mb-1">Nama Gedung / Tempat *</label>
                                        <input type="text" :name="'events[' + index + '][venue_name]'" x-model="event.venue_name" required
                                               class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200"
                                               placeholder="Contoh: Masjid Agung / Hotel Grand Santika">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold text-stone-700 mb-1">Alamat Lengkap</label>
                                        <input type="text" :name="'events[' + index + '][address]'" x-model="event.address"
                                               class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200"
                                               placeholder="Jl. Merdeka No. 45, Jakarta Selatan">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold text-stone-700 mb-1">Link Google Maps (URL)</label>
                                        <input type="url" :name="'events[' + index + '][maps_url]'" x-model="event.maps_url"
                                               class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200"
                                               placeholder="https://maps.app.goo.gl/...">
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- 5. TAB KISAH / TIMELINE -->
                        <div x-show="activeTab === 'kisah'" x-cloak class="space-y-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-base font-bold text-stone-900">Perjalanan & Kisah Cinta / Timeline</h3>
                                    <p class="text-xs text-stone-500">Momen berharga yang ingin dibagikan.</p>
                                </div>
                                <button type="button" @click="addStory()"
                                        class="text-xs bg-stone-900 hover:bg-stone-800 text-white font-bold px-3 py-1.5 rounded-xl">
                                    + Tambah Momen
                                </button>
                            </div>

                            <template x-for="(story, index) in stories" :key="index">
                                <div class="p-4 bg-stone-50 rounded-2xl border border-stone-200 space-y-2">
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs font-bold text-rose-600" x-text="'Momen #' + (index + 1)"></span>
                                        <button type="button" @click="removeStory(index)" class="text-rose-600 text-xs font-bold">Hapus</button>
                                    </div>
                                    <div class="grid grid-cols-3 gap-2">
                                        <input type="text" :name="'story[' + index + '][year]'" x-model="story.year" placeholder="Tahun / Momen" class="text-xs px-3 py-2 rounded-xl border border-stone-200">
                                        <input type="text" :name="'story[' + index + '][title]'" x-model="story.title" placeholder="Judul Momen" class="col-span-2 text-xs px-3 py-2 rounded-xl border border-stone-200">
                                    </div>
                                    <textarea :name="'story[' + index + '][description]'" x-model="story.description" rows="2" placeholder="Ceritakan kisah singkatnya..." class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200"></textarea>
                                </div>
                            </template>
                        </div>

                        <!-- 6. TAB GALERI FOTO -->
                        <div x-show="activeTab === 'galeri'" x-cloak class="space-y-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-base font-bold text-stone-900">Galeri Foto</h3>
                                    <p class="text-xs text-stone-500">Batas paket: {{ $maxPhotos }} foto.</p>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-stone-700 mb-1">Unggah Foto Baru (Bisa Multi-Pilih)</label>
                                <input type="file" name="gallery_photos[]" multiple accept="image/*" class="text-xs text-stone-500">
                                <p class="text-[11px] text-stone-400 mt-1">Maksimal 3MB per foto. Format: JPG, PNG, WEBP.</p>
                            </div>

                            <div class="grid grid-cols-3 sm:grid-cols-4 gap-3 pt-3">
                                @foreach($safeData['gallery'] ?? [] as $gIdx => $gItem)
                                    <div class="aspect-square rounded-2xl overflow-hidden border border-stone-200 relative group">
                                        <img src="{{ $gItem['url'] ?? $gItem }}" class="w-full h-full object-cover">
                                        <label class="absolute inset-0 bg-stone-900/60 opacity-0 group-hover:opacity-100 flex items-center justify-center cursor-pointer transition-opacity text-white text-xs font-bold">
                                            <input type="checkbox" name="remove_gallery_index[]" value="{{ $gIdx }}" class="mr-1">
                                            Hapus
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- 7. TAB AMPLOP DIGITAL -->
                        <div x-show="activeTab === 'amplop'" x-cloak class="space-y-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-base font-bold text-stone-900">Amplop Digital & Rekening</h3>
                                    <p class="text-xs text-stone-500">Tamu dapat menyalin nomor rekening untuk mengirim kado.</p>
                                </div>
                                <button type="button" @click="addGift()"
                                        class="text-xs bg-stone-900 hover:bg-stone-800 text-white font-bold px-3 py-1.5 rounded-xl">
                                    + Tambah Rekening
                                </button>
                            </div>

                            <template x-for="(gift, index) in gifts" :key="index">
                                <div class="p-4 bg-stone-50 rounded-2xl border border-stone-200 space-y-2">
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs font-bold text-rose-600" x-text="'Rekening #' + (index + 1)"></span>
                                        <button type="button" @click="removeGift(index)" class="text-rose-600 text-xs font-bold">Hapus</button>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                        <input type="text" :name="'gifts[' + index + '][bank_name]'" x-model="gift.bank_name" placeholder="Bank (BCA / Mandiri / GoPay)" class="text-xs px-3 py-2 rounded-xl border border-stone-200">
                                        <input type="text" :name="'gifts[' + index + '][account_number]'" x-model="gift.account_number" placeholder="Nomor Rekening / No HP" class="text-xs px-3 py-2 rounded-xl border border-stone-200 font-mono">
                                        <input type="text" :name="'gifts[' + index + '][account_holder]'" x-model="gift.account_holder" placeholder="Atas Nama (Pemilik)" class="text-xs px-3 py-2 rounded-xl border border-stone-200">
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- 8. TAB RSVP & PENUTUP -->
                        <div x-show="activeTab === 'penutup'" x-cloak class="space-y-6">
                            <h3 class="text-base font-bold text-stone-900">Fitur RSVP & Teks Penutup</h3>

                            <div class="space-y-3">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="rsvp_enabled" value="1" {{ ($safeData['rsvp_enabled'] ?? true) ? 'checked' : '' }} class="rounded border-stone-300 text-rose-600 focus:ring-rose-500">
                                    <span class="text-xs font-semibold text-stone-700">Aktifkan Formulir Konfirmasi Kehadiran (RSVP)</span>
                                </label>

                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="wishes_enabled" value="1" {{ ($safeData['wishes_enabled'] ?? true) ? 'checked' : '' }} class="rounded border-stone-300 text-rose-600 focus:ring-rose-500">
                                    <span class="text-xs font-semibold text-stone-700">Aktifkan Buku Tamu & Doa Restu Publik</span>
                                </label>
                            </div>

                            <div class="pt-4 border-t border-stone-100">
                                <label class="block text-xs font-bold text-stone-700 mb-1">Teks Penutup</label>
                                <textarea name="closing[text]" rows="3"
                                          class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200">{{ old('closing.text', $safeData['closing']['text'] ?? '') }}</textarea>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-stone-700 mb-1">Nama Penutup / Tanda Tangan</label>
                                <input type="text" name="closing[signature]" value="{{ old('closing.signature', $safeData['closing']['signature'] ?? '') }}"
                                       class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200"
                                       placeholder="Contoh: Keluarga Besar Kami">
                            </div>
                        </div>

                        <div class="mt-8 pt-6 border-t border-stone-200 flex justify-end">
                            <button type="submit"
                                    class="bg-rose-600 hover:bg-rose-700 text-white text-xs sm:text-sm font-bold px-8 py-3 rounded-2xl shadow-lg shadow-rose-600/30 transition-all">
                                Simpan Semua Perubahan Undangan
                            </button>
                        </div>
                    </form>
                </div>

                <!-- RIGHT COLUMN: MOBILE FRAME LIVE PREVIEW -->
                <div class="lg:col-span-5 sticky top-20">
                    <div class="text-center mb-3 flex items-center justify-between">
                        <span class="text-xs font-extrabold uppercase text-stone-500 tracking-wider">Live Mobile Preview</span>
                        <button type="button" @click="reloadIframe()"
                                class="text-xs text-rose-600 hover:text-rose-800 font-bold flex items-center gap-1">
                            <span>🔄 Muat Ulang Pratinjau</span>
                        </button>
                    </div>

                    <!-- Smartphone Bezel Mockup -->
                    <div class="mx-auto w-[320px] sm:w-[360px] h-[640px] bg-stone-900 rounded-[45px] p-3 shadow-2xl border-4 border-stone-800 relative flex flex-col overflow-hidden">
                        <!-- Speaker & Camera notch -->
                        <div class="absolute top-5 left-1/2 -translate-x-1/2 w-28 h-4 bg-stone-950 rounded-full z-20 flex items-center justify-center">
                            <div class="w-2.5 h-2.5 rounded-full bg-stone-800 mr-2"></div>
                            <div class="w-8 h-1 rounded-full bg-stone-800"></div>
                        </div>

                        <!-- Iframe Screen -->
                        <div class="w-full h-full rounded-[35px] overflow-hidden bg-white relative pt-5">
                            <iframe id="previewIframe"
                                    src="{{ route('editor.preview', $invitation) }}"
                                    class="w-full h-full border-0"></iframe>
                        </div>

                        <!-- Home bar indicator -->
                        <div class="absolute bottom-2 left-1/2 -translate-x-1/2 w-28 h-1 bg-stone-600 rounded-full z-20"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Alpine.js script for dynamic forms -->
    <script>
        function invitationEditor() {
            return {
                activeTab: 'dasar',
                events: @json($invitation->events->map(fn($e) => [
                    'title' => $e->title,
                    'start_at' => $e->start_at->format('Y-m-d\TH:i'),
                    'end_at' => $e->end_at ? $e->end_at->format('Y-m-d\TH:i') : '',
                    'timezone' => $e->timezone,
                    'venue_name' => $e->venue_name,
                    'address' => $e->address,
                    'maps_url' => $e->maps_url,
                ])->toArray()),
                stories: @json($safeData['story'] ?? []),
                gifts: @json($safeData['gifts'] ?? []),

                addEvent() {
                    if (this.events.length < {{ $maxEvents }}) {
                        this.events.push({
                            title: 'Resepsi Pernikahan',
                            start_at: '{{ now()->addDays(14)->format("Y-m-d\TH:i") }}',
                            end_at: '',
                            timezone: 'WIB',
                            venue_name: '',
                            address: '',
                            maps_url: ''
                        });
                    }
                },
                removeEvent(idx) {
                    if (this.events.length > 1) {
                        this.events.splice(idx, 1);
                    }
                },
                addStory() {
                    this.stories.push({ year: '', title: '', description: '' });
                },
                removeStory(idx) {
                    this.stories.splice(idx, 1);
                },
                addGift() {
                    this.gifts.push({ bank_name: 'BCA', account_number: '', account_holder: '' });
                },
                removeGift(idx) {
                    this.gifts.splice(idx, 1);
                },
                reloadIframe() {
                    const iframe = document.getElementById('previewIframe');
                    if (iframe) {
                        iframe.src = iframe.src;
                    }
                }
            };
        }
    </script>
</x-app-layout>
