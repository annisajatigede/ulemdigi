<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $data['opening']['title'] ?? 'Walimatul \'Urs' }} {{ $data['party1']['name'] ?? 'Fulanah' }} & {{ $data['party2']['name'] ?? 'Fulan' }}</title>
    <meta name="description" content="Undangan Pernikahan Islami {{ $data['party1']['name'] ?? '' }} & {{ $data['party2']['name'] ?? '' }}">

    <!-- Google Fonts: Amiri (Arabic Calligraphy), Playfair Display, Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Playfair+Display:ital,wght@0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (Vite build) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .font-arabic { font-family: 'Amiri', serif; }
        .font-serif-title { font-family: 'Playfair Display', serif; }
        .font-body { font-family: 'Plus Jakarta Sans', sans-serif; }
        [x-cloak] { display: none !important; }
        .spin-slow { animation: spin 8s linear infinite; }
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    </style>
</head>
<body class="font-body bg-[#fbfbfa] text-stone-700 antialiased selection:bg-emerald-100 selection:text-emerald-800 overflow-x-hidden"
      x-data="invitationApp({
          musicUrl: '{{ $data['music_url'] ?? '' }}',
          eventDate: '{{ isset($invitation->events[0]) ? $invitation->events[0]->start_at->toIso8601String() : now()->addDays(14)->toIso8601String() }}'
      })">

    @if(!empty($data['music_path']))
    <!-- Audio Player Element -->
    <audio x-ref="audioPlayer" loop preload="none">
        <source src="{{ $data['music_url'] ?? (str_starts_with($data['music_path'], 'http') ? $data['music_path'] : asset(ltrim($data['music_path'], '/'))) }}" type="audio/mpeg">
    </audio>

    <!-- Floating Audio Button -->
    <button @click="toggleMusic()"
            x-show="opened"
            x-transition
            class="fixed bottom-6 right-6 z-40 bg-white/95 backdrop-blur border border-emerald-300 text-emerald-800 p-3.5 rounded-full shadow-2xl hover:scale-110 transition-transform active:scale-95 focus:outline-none"
            :class="{ 'spin-slow': isPlaying }"
            title="Musik Latar">
        <svg x-show="isPlaying" class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/>
        </svg>
        <svg x-show="!isPlaying" class="w-6 h-6 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2" />
        </svg>
    </button>
    @endif

    <!-- COVER / OPENING MODAL -->
    <div x-show="!opened"
         x-transition:leave="transition ease-in-out duration-700 transform"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-full"
         class="fixed inset-0 z-50 flex items-center justify-center bg-gradient-to-b from-[#f0fdf4] via-[#ecfdf5] to-[#f0fdf4] p-6 text-center select-none">
        
        <div class="max-w-md w-full bg-white/95 backdrop-blur-md p-8 sm:p-10 rounded-3xl shadow-2xl border border-emerald-200/80 relative overflow-hidden">
            <!-- Islamic Arch Geometric Accent -->
            <div class="w-16 h-16 mx-auto mb-3 text-emerald-700/80 flex items-center justify-center">
                <svg viewBox="0 0 100 100" class="w-12 h-12 fill-current">
                    <path d="M50 0 C65 25 80 40 100 50 C80 60 65 75 50 100 C35 75 20 60 0 50 C20 40 35 25 50 0 Z" opacity="0.8"/>
                </svg>
            </div>

            <p class="font-arabic text-2xl text-emerald-800 mb-1">بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</p>
            <p class="text-xs font-semibold tracking-widest text-emerald-700 uppercase mb-2">Walimatul 'Urs</p>

            <h1 class="font-serif-title text-3xl sm:text-4xl font-bold text-stone-900 mb-2">
                {{ $data['party1']['name'] ?? 'Fulanah' }} & {{ $data['party2']['name'] ?? 'Fulan' }}
            </h1>
            
            <div class="w-16 h-0.5 bg-emerald-400 mx-auto my-3"></div>

            <p class="text-xs text-stone-500 mb-1">Kepada Yth. Bapak/Ibu/Saudara/i:</p>
            <div class="inline-block bg-emerald-50 border border-emerald-200 px-5 py-2.5 rounded-2xl my-2">
                <span class="text-base sm:text-lg font-bold text-emerald-900 font-serif-title">{{ $recipientName ?? 'Tamu Undangan' }}</span>
                @if(!empty($isPersonal))
                    <span class="block text-[11px] text-emerald-700 font-medium">Tamu Kehormatan</span>
                @endif
            </div>

            <button @click="openInvitation()"
                    class="mt-6 w-full inline-flex items-center justify-center gap-2 bg-gradient-to-r from-emerald-700 to-emerald-800 hover:from-emerald-800 hover:to-emerald-900 text-white font-semibold px-6 py-3.5 rounded-2xl shadow-xl shadow-emerald-800/25 transition-all transform hover:-translate-y-0.5 text-xs sm:text-sm tracking-wide">
                <span>Buka Undangan</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </button>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <main class="max-w-xl mx-auto bg-white min-h-screen shadow-2xl relative border-x border-stone-200">

        <!-- 1. HERO SECTION -->
        <header class="relative pt-16 pb-16 px-6 text-center bg-gradient-to-b from-[#f0fdf4]/70 via-white to-white">
            <p class="font-arabic text-3xl text-emerald-800 mb-3 leading-relaxed">بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</p>
            <span class="inline-block px-3 py-1 bg-emerald-100 text-emerald-800 text-[11px] font-bold rounded-full uppercase tracking-wider mb-3">
                Undangan Pernikahan
            </span>

            <h1 class="font-serif-title text-4xl sm:text-5xl text-stone-900 font-bold mb-2">
                {{ $data['party1']['name'] ?? 'Fulanah' }} <span class="text-emerald-700 font-normal">&</span> {{ $data['party2']['name'] ?? 'Fulan' }}
            </h1>
            <p class="text-xs text-stone-500 font-medium tracking-widest uppercase mb-6">
                {{ isset($invitation->events[0]) ? $invitation->events[0]->start_at->translatedFormat('l, d F Y') : 'Segera Hadir' }}
            </p>

            <!-- COUNTDOWN -->
            <div class="grid grid-cols-4 gap-2.5 max-w-xs mx-auto text-center mt-6">
                <div class="bg-emerald-50/70 border border-emerald-200/80 rounded-2xl p-2.5">
                    <span class="block text-xl font-bold font-serif-title text-emerald-800" x-text="countdown.days">0</span>
                    <span class="text-[9px] text-stone-500 uppercase tracking-widest font-semibold">Hari</span>
                </div>
                <div class="bg-emerald-50/70 border border-emerald-200/80 rounded-2xl p-2.5">
                    <span class="block text-xl font-bold font-serif-title text-emerald-800" x-text="countdown.hours">0</span>
                    <span class="text-[9px] text-stone-500 uppercase tracking-widest font-semibold">Jam</span>
                </div>
                <div class="bg-emerald-50/70 border border-emerald-200/80 rounded-2xl p-2.5">
                    <span class="block text-xl font-bold font-serif-title text-emerald-800" x-text="countdown.minutes">0</span>
                    <span class="text-[9px] text-stone-500 uppercase tracking-widest font-semibold">Menit</span>
                </div>
                <div class="bg-emerald-50/70 border border-emerald-200/80 rounded-2xl p-2.5">
                    <span class="block text-xl font-bold font-serif-title text-emerald-800" x-text="countdown.seconds">0</span>
                    <span class="text-[9px] text-stone-500 uppercase tracking-widest font-semibold">Detik</span>
                </div>
            </div>
        </header>

        <!-- 2. AYAT SUCI AL-QUR'AN -->
        <section class="py-12 px-6 sm:px-10 text-center border-t border-stone-100">
            <div class="max-w-md mx-auto bg-stone-50/80 border border-emerald-100 p-6 sm:p-8 rounded-3xl relative">
                <p class="font-arabic text-2xl text-emerald-900 mb-4 leading-loose dir-rtl">
                    وَمِنْ آيَاتِهِ أَنْ خَلَقَ لَكُم مِّنْ أَنفُسِكُمْ أَزْوَاجًا لِّتَسْكُنُوا إِلَيْهَا وَجَعَلَ بَيْنَكُم مَّوَدَّةً وَرَحْمَةً
                </p>
                <p class="text-xs sm:text-sm text-stone-600 italic leading-relaxed">
                    "{{ $data['opening']['quote'] ?? 'Dan di antara tanda-tanda kebesaran-Nya ialah Dia menciptakan pasangan-pasangan untukmu dari jenismu sendiri, agar kamu cenderung dan merasa tenteram kepadanya, dan Dia menjadikan di antaramu rasa kasih dan sayang.' }}"
                </p>
                <p class="text-xs font-bold text-emerald-700 mt-3 tracking-wider uppercase">
                    {{ $data['opening']['quote_source'] ?? 'QS. Ar-Rum: 21' }}
                </p>
            </div>

            <div class="mt-8">
                <p class="text-xs font-bold text-emerald-800 uppercase tracking-wide">{{ $data['opening']['greeting'] ?? 'Assalamu’alaikum Warahmatullahi Wabarakatuh' }}</p>
                <p class="text-xs sm:text-sm text-stone-600 mt-2 leading-relaxed max-w-md mx-auto">
                    Dengan memohon rahmat dan ridho Allah Subhanahu Wa Ta'ala, kami mengundang Bapak/Ibu/Saudara/i untuk menghadiri syukuran pernikahan kami:
                </p>
            </div>
        </section>

        <!-- 3. PROFIL MEMPELAI (ISLAMI & ELEGAN) -->
        <section class="py-12 px-6 sm:px-10 bg-[#f7f9f7]">
            <!-- Mempelai Wanita -->
            <div class="text-center mb-8">
                <div class="w-32 h-32 mx-auto rounded-full p-1 border-2 border-emerald-300 shadow-sm overflow-hidden bg-white mb-4">
                    @if(!empty($data['party1']['photo_path']))
                        <img src="{{ $data['party1']['photo_path'] }}" alt="{{ $data['party1']['name'] }}" class="w-full h-full object-cover rounded-full">
                    @else
                        <div class="w-full h-full rounded-full bg-emerald-50 flex items-center justify-center text-emerald-700 font-serif-title text-4xl">
                            {{ substr($data['party1']['name'] ?? 'W', 0, 1) }}
                        </div>
                    @endif
                </div>
                <h3 class="font-serif-title text-2xl font-bold text-stone-900">{{ $data['party1']['full_name'] ?? 'Siti Fulanah, S.Kom' }}</h3>
                <p class="text-xs text-emerald-700 font-semibold mt-1">{{ $data['party1']['role_label'] ?? 'Mempelai Wanita' }}</p>
                <p class="text-xs text-stone-500 mt-1">{{ $data['party1']['child_order'] ?? 'Putri pertama dari' }} {{ $data['party1']['parents'] ?? 'Bpk. Ahmad & Ibu Fatimah' }}</p>
            </div>

            <div class="text-center my-3 font-arabic text-3xl text-emerald-600">و</div>

            <!-- Mempelai Pria -->
            <div class="text-center">
                <div class="w-32 h-32 mx-auto rounded-full p-1 border-2 border-emerald-300 shadow-sm overflow-hidden bg-white mb-4">
                    @if(!empty($data['party2']['photo_path']))
                        <img src="{{ $data['party2']['photo_path'] }}" alt="{{ $data['party2']['name'] }}" class="w-full h-full object-cover rounded-full">
                    @else
                        <div class="w-full h-full rounded-full bg-emerald-50 flex items-center justify-center text-emerald-700 font-serif-title text-4xl">
                            {{ substr($data['party2']['name'] ?? 'P', 0, 1) }}
                        </div>
                    @endif
                </div>
                <h3 class="font-serif-title text-2xl font-bold text-stone-900">{{ $data['party2']['full_name'] ?? 'Muhammad Fulan, S.T' }}</h3>
                <p class="text-xs text-emerald-700 font-semibold mt-1">{{ $data['party2']['role_label'] ?? 'Mempelai Pria' }}</p>
                <p class="text-xs text-stone-500 mt-1">{{ $data['party2']['child_order'] ?? 'Putra kedua dari' }} {{ $data['party2']['parents'] ?? 'Bpk. Hasan & Ibu Aminah' }}</p>
            </div>
        </section>

        <!-- 4. RANGKAIAN ACARA -->
        <section class="py-14 px-6 sm:px-10">
            <div class="text-center mb-8">
                <span class="text-xs font-bold text-emerald-700 uppercase tracking-widest">Agenda Acara</span>
                <h2 class="font-serif-title text-3xl font-bold text-stone-900 mt-1">Waktu & Tempat</h2>
                <div class="w-12 h-0.5 bg-emerald-500 mx-auto mt-2"></div>
            </div>

            <div class="space-y-6">
                @forelse($invitation->events as $event)
                    <div class="bg-gradient-to-br from-white to-emerald-50/40 border border-emerald-100 rounded-3xl p-6 sm:p-8 shadow-sm text-center">
                        <span class="inline-block px-3 py-1 bg-emerald-100/80 text-emerald-900 text-xs font-bold rounded-full mb-2">
                            {{ $event->title }}
                        </span>

                        <div class="my-4 py-3 border-y border-emerald-100">
                            <p class="font-serif-title font-bold text-stone-900 text-base sm:text-lg">{{ $event->start_at->translatedFormat('l, d F Y') }}</p>
                            <p class="text-xs text-emerald-700 font-semibold mt-1">
                                Pukul {{ $event->start_at->format('H:i') }} @if($event->end_at) - {{ $event->end_at->format('H:i') }} @endif {{ $event->timezone }}
                            </p>
                        </div>

                        <p class="font-semibold text-stone-800 text-sm">{{ $event->venue_name }}</p>
                        @if($event->address)
                            <p class="text-xs text-stone-500 mt-1 leading-relaxed">{{ $event->address }}</p>
                        @endif

                        @if(!empty($event->maps_url))
                            <a href="{{ $event->maps_url }}" target="_blank"
                               class="inline-flex items-center gap-1.5 mt-5 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-semibold px-4 py-2.5 rounded-xl shadow-md transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                </svg>
                                <span>Navigasi Lokasi Google Maps</span>
                            </a>
                        @endif
                    </div>
                @empty
                    <p class="text-center text-xs text-stone-400">Jadwal acara belum diatur.</p>
                @endforelse
            </div>
        </section>

        <!-- 5. KISAH MENUJU HALAL / TIMELINE -->
        @if(!empty($data['story']) && count($data['story']) > 0)
        <section class="py-12 px-6 sm:px-10 bg-[#f7f9f7]">
            <div class="text-center mb-8">
                <span class="text-xs font-bold text-emerald-700 uppercase tracking-widest">Kisah Kami</span>
                <h2 class="font-serif-title text-3xl font-bold text-stone-900 mt-1">Menuju Ikatan Halal</h2>
                <div class="w-12 h-0.5 bg-emerald-500 mx-auto mt-2"></div>
            </div>

            <div class="relative border-l-2 border-emerald-200 ml-4 sm:ml-8 space-y-6">
                @foreach($data['story'] as $story)
                    <div class="relative pl-6">
                        <div class="absolute -left-2.5 top-1.5 w-4 h-4 rounded-full bg-emerald-600 border-4 border-white shadow"></div>
                        <span class="text-xs font-bold text-emerald-700 bg-emerald-100 px-2.5 py-0.5 rounded-full">{{ $story['year'] ?? '' }}</span>
                        <h4 class="font-serif-title text-base font-bold text-stone-900 mt-1.5">{{ $story['title'] ?? '' }}</h4>
                        <p class="text-xs text-stone-600 mt-1 leading-relaxed">{{ $story['description'] ?? '' }}</p>
                    </div>
                @endforeach
            </div>
        </section>
        @endif

        <!-- 6. GALERI FOTO -->
        @if(!empty($data['gallery']) && count($data['gallery']) > 0)
        <section class="py-12 px-6 sm:px-10">
            <div class="text-center mb-8">
                <span class="text-xs font-bold text-emerald-700 uppercase tracking-widest">Momen Bahagia</span>
                <h2 class="font-serif-title text-3xl font-bold text-stone-900 mt-1">Galeri Foto</h2>
                <div class="w-12 h-0.5 bg-emerald-500 mx-auto mt-2"></div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @foreach($data['gallery'] as $photo)
                    <div class="aspect-square rounded-2xl overflow-hidden border border-emerald-100 shadow-sm group">
                        <img src="{{ $photo['url'] ?? $photo }}" alt="Foto" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    </div>
                @endforeach
            </div>
        </section>
        @endif

        <!-- 7. AMPLOP DIGITAL / TANDA KASIH -->
        @if(!empty($data['gifts']) && count($data['gifts']) > 0)
        <section class="py-12 px-6 sm:px-10 bg-emerald-50/50" x-data="{ copiedIdx: null }">
            <div class="text-center mb-8">
                <span class="text-xs font-bold text-emerald-700 uppercase tracking-widest">Tanda Kasih</span>
                <h2 class="font-serif-title text-3xl font-bold text-stone-900 mt-1">Amplop Digital</h2>
                <div class="w-12 h-0.5 bg-emerald-500 mx-auto mt-2"></div>
                <p class="text-xs text-stone-500 mt-3 max-w-sm mx-auto leading-relaxed">
                    Doa restu Anda adalah berkah terbaik bagi kami. Namun apabila berkehendak memberikan tanda kasih, dapat disalurkan melalui:
                </p>
            </div>

            <div class="space-y-4 max-w-sm mx-auto">
                @foreach($data['gifts'] as $idx => $gift)
                    <div class="bg-white border border-emerald-200 rounded-2xl p-5 shadow-sm">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-emerald-900 text-sm">{{ $gift['bank_name'] ?? 'BANK' }}</span>
                            <span class="text-[10px] text-stone-400 uppercase">Transfer</span>
                        </div>
                        <div class="font-mono text-lg font-bold text-emerald-800 tracking-wider my-1">
                            {{ $gift['account_number'] ?? '' }}
                        </div>
                        <p class="text-xs text-stone-500">a.n. <span class="font-bold text-stone-700">{{ $gift['account_holder'] ?? '' }}</span></p>

                        <button @click="navigator.clipboard.writeText('{{ $gift['account_number'] ?? '' }}'); copiedIdx = {{ $idx }}; setTimeout(() => copiedIdx = null, 2500)"
                                class="mt-4 w-full bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-semibold py-2 rounded-xl border border-emerald-200 transition-colors">
                            <span x-show="copiedIdx !== {{ $idx }}">Salin Nomor Rekening</span>
                            <span x-show="copiedIdx === {{ $idx }}" class="text-emerald-700 font-bold">✓ Tersalin ke Clipboard</span>
                        </button>
                    </div>
                @endforeach
            </div>
        </section>
        @endif

        <!-- 8. RSVP & DOA RESTU -->
        @if(($data['rsvp_enabled'] ?? true) || ($data['wishes_enabled'] ?? true))
        <section class="py-14 px-6 sm:px-10" id="rsvp-section" x-data="rsvpFormHandler('{{ route('invitation.rsvp', ['slug' => $invitation->slug]) }}')">
            <div class="text-center mb-8">
                <span class="text-xs font-bold text-emerald-700 uppercase tracking-widest">Konfirmasi & Doa</span>
                <h2 class="font-serif-title text-3xl font-bold text-stone-900 mt-1">RSVP & Doa Restu</h2>
                <div class="w-12 h-0.5 bg-emerald-500 mx-auto mt-2"></div>
            </div>

            <!-- Toast Alert -->
            <div x-show="successMessage" x-cloak class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium text-center">
                <span x-text="successMessage"></span>
            </div>

            <form @submit.prevent="submitForm()" class="bg-white border border-emerald-100 rounded-3xl p-6 sm:p-8 shadow-sm space-y-4">
                <input type="hidden" name="personal_token" value="{{ $guest->personal_token ?? '' }}">

                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">Nama Lengkap *</label>
                    <input type="text" x-model="form.name" required
                           class="w-full text-xs sm:text-sm px-4 py-2.5 rounded-xl border border-emerald-200 focus:ring-1 focus:ring-emerald-500"
                           placeholder="Nama Anda">
                </div>

                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">No. WhatsApp / HP</label>
                    <input type="text" x-model="form.phone"
                           class="w-full text-xs sm:text-sm px-4 py-2.5 rounded-xl border border-emerald-200 focus:ring-1 focus:ring-emerald-500"
                           placeholder="08123456789">
                </div>

                @if($data['rsvp_enabled'] ?? true)
                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">Konfirmasi Kehadiran *</label>
                    <select x-model="form.rsvp_status" required
                            class="w-full text-xs sm:text-sm px-4 py-2.5 rounded-xl border border-emerald-200 focus:ring-1 focus:ring-emerald-500">
                        <option value="attended">Insya Allah Hadir</option>
                        <option value="maybe">Masih Ragu / Belum Pasti</option>
                        <option value="not_attended">Berhalangan Hadir</option>
                    </select>
                </div>

                <div x-show="form.rsvp_status === 'attended'">
                    <label class="block text-xs font-bold text-stone-700 mb-1">Jumlah Tamu yang Hadir</label>
                    <input type="number" min="1" max="10" x-model="form.guest_count"
                           class="w-full text-xs sm:text-sm px-4 py-2.5 rounded-xl border border-emerald-200 focus:ring-1 focus:ring-emerald-500">
                </div>
                @endif

                @if($data['wishes_enabled'] ?? true)
                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">Doa untuk Kedua Mempelai</label>
                    <textarea x-model="form.message" rows="3"
                              class="w-full text-xs sm:text-sm px-4 py-2.5 rounded-xl border border-emerald-200 focus:ring-1 focus:ring-emerald-500"
                              placeholder="Tuliskan doa kebaikan dan keberkahan..."></textarea>
                </div>
                @endif

                <button type="submit" :disabled="submitting"
                        class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-semibold py-3 rounded-xl shadow-md transition-all text-xs sm:text-sm disabled:opacity-50">
                    <span x-show="!submitting">Kirim Konfirmasi & Doa</span>
                    <span x-show="submitting">Mengirim...</span>
                </button>
            </form>

            <!-- FEED DOA -->
            <div class="mt-8 space-y-3">
                <h4 class="text-xs font-bold text-stone-700 uppercase tracking-wider mb-2">Untaian Doa & Ucapan ({{ $invitation->wishes->count() }})</h4>
                <div class="max-h-72 overflow-y-auto space-y-2.5 pr-1">
                    <template x-for="wish in localWishes" :key="wish.id">
                        <div class="bg-emerald-50/50 border border-emerald-100 p-3.5 rounded-2xl">
                            <span class="font-bold text-emerald-900 text-xs sm:text-sm block" x-text="wish.name"></span>
                            <p class="text-xs text-stone-600 mt-1 leading-relaxed" x-text="wish.message"></p>
                        </div>
                    </template>
                    @foreach($invitation->wishes as $w)
                        <div class="bg-emerald-50/50 border border-emerald-100 p-3.5 rounded-2xl">
                            <span class="font-bold text-emerald-900 text-xs sm:text-sm block">{{ $w->name }}</span>
                            <p class="text-xs text-stone-600 mt-1 leading-relaxed">{{ $w->message }}</p>
                            <span class="text-[10px] text-stone-400 mt-1 block">{{ $w->created_at->diffForHumans() }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        <!-- 9. PENUTUP & DOA PENGANTIN -->
        <section class="py-14 px-6 sm:px-10 text-center bg-gradient-to-b from-white to-emerald-50/60 border-t border-emerald-100">
            <p class="font-arabic text-xl text-emerald-800 mb-2">
                بَارَكَ اللَّهُ لَكَ وَبَارَكَ عَلَيْكَ وَجَمَعَ بَيْنَكُمَا فِي خَيْرٍ
            </p>
            <p class="text-xs text-stone-500 italic mb-6">
                "Semoga Allah memberkahi engkau di waktu lapang maupun sempit, dan menghimpun kalian berdua dalam kebaikan."
            </p>

            <p class="text-xs sm:text-sm text-stone-600 max-w-md mx-auto leading-relaxed">
                {{ $data['closing']['text'] ?? 'Merupakan suatu kehormatan dan kebahagiaan bagi kami apabila Bapak/Ibu/Saudara/i berkenan hadir untuk memberikan doa restu.' }}
            </p>
            <h3 class="font-serif-title text-2xl text-emerald-900 font-bold mt-4">{{ $data['closing']['signature'] ?? ($data['party1']['name'] . ' & ' . $data['party2']['name']) }}</h3>

            <div class="mt-8 pt-6 border-t border-emerald-100">
                <a href="https://api.whatsapp.com/send?text={{ urlencode('Assalamu’alaikum! Berikut undangan pernikahan kami ' . ($data['party1']['name'] ?? '') . ' & ' . ($data['party2']['name'] ?? '') . ': ' . url()->current()) }}"
                   target="_blank"
                   class="inline-flex items-center gap-2 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-semibold px-4 py-2.5 rounded-xl shadow-md transition-colors">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
                    </svg>
                    <span>Bagikan ke WhatsApp</span>
                </a>
            </div>

            <p class="text-[10px] text-stone-400 mt-8">Dibuat dengan penuh berkah di <a href="{{ route('home') }}" class="font-semibold text-emerald-700 hover:underline">Kawinan.id</a></p>
        </section>
    </main>

    <!-- Alpine.js Application Script -->
    <script>
        function invitationApp(config) {
            return {
                opened: {{ !empty($previewMode) ? 'true' : 'false' }},
                isPlaying: false,
                countdown: { days: 0, hours: 0, minutes: 0, seconds: 0 },
                init() {
                    this.startCountdown(config.eventDate);
                },
                openInvitation() {
                    this.opened = true;
                    this.playMusic();
                },
                playMusic() {
                    const audio = this.$refs.audioPlayer;
                    if (audio) {
                        audio.play().then(() => this.isPlaying = true).catch(() => this.isPlaying = false);
                    }
                },
                toggleMusic() {
                    const audio = this.$refs.audioPlayer;
                    if (!audio) return;
                    if (this.isPlaying) {
                        audio.pause();
                        this.isPlaying = false;
                    } else {
                        audio.play().then(() => this.isPlaying = true);
                    }
                },
                startCountdown(targetDateStr) {
                    const target = new Date(targetDateStr).getTime();
                    const update = () => {
                        const now = new Date().getTime();
                        const diff = target - now;
                        if (diff > 0) {
                            this.countdown.days = Math.floor(diff / (1000 * 60 * 60 * 24));
                            this.countdown.hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                            this.countdown.minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                            this.countdown.seconds = Math.floor((diff % (1000 * 60)) / 1000);
                        } else {
                            this.countdown = { days: 0, hours: 0, minutes: 0, seconds: 0 };
                        }
                    };
                    update();
                    setInterval(update, 1000);
                }
            };
        }

        function rsvpFormHandler(endpoint) {
            return {
                submitting: false,
                successMessage: '',
                localWishes: [],
                form: {
                    name: '{{ $guest->name ?? ($recipientName !== "Tamu Undangan" ? $recipientName : "") }}',
                    phone: '{{ $guest->phone ?? "" }}',
                    rsvp_status: '{{ $guest->rsvp_status ?? "attended" }}',
                    guest_count: {{ $guest->guest_count ?? 1 }},
                    message: ''
                },
                async submitForm() {
                    this.submitting = true;
                    this.successMessage = '';
                    try {
                        const res = await fetch(endpoint, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(this.form)
                        });
                        const data = await res.json();
                        if (res.ok && data.success) {
                            this.successMessage = data.message;
                            if (data.wish) {
                                this.localWishes.unshift(data.wish);
                            }
                            this.form.message = '';
                        } else {
                            alert(data.message || 'Terjadi kesalahan.');
                        }
                    } catch (e) {
                        alert('Gagal mengirim RSVP.');
                    } finally {
                        this.submitting = false;
                    }
                }
            };
        }
    </script>
</body>
</html>
