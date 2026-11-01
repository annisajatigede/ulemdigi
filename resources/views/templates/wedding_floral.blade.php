<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $data['opening']['title'] ?? 'The Wedding of' }} {{ $data['party1']['name'] ?? 'Fulanah' }} & {{ $data['party2']['name'] ?? 'Fulan' }}</title>
    <meta name="description" content="Undangan Pernikahan Digital {{ $data['party1']['name'] ?? '' }} & {{ $data['party2']['name'] ?? '' }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (Vite build) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .font-serif-title { font-family: 'Playfair Display', serif; }
        .font-cursive { font-family: 'Great Vibes', cursive; }
        .font-body { font-family: 'Plus Jakarta Sans', sans-serif; }
        [x-cloak] { display: none !important; }
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-8px) rotate(2deg); }
        }
        .animate-float { animation: float 4s ease-in-out infinite; }
        .spin-slow { animation: spin 8s linear infinite; }
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    </style>
</head>
<body class="font-body bg-[#fff9fa] text-stone-700 antialiased selection:bg-rose-100 selection:text-rose-600 overflow-x-hidden"
      x-data="invitationApp({
          musicUrl: '{{ $data['music_url'] ?? '' }}',
          eventDate: '{{ isset($invitation->events[0]) ? $invitation->events[0]->start_at->toIso8601String() : now()->addDays(14)->toIso8601String() }}'
      })">

    @if(!empty($data['music_path']))
    <!-- Audio Player Element -->
    <audio x-ref="audioPlayer" loop preload="none">
        <source src="{{ $data['music_url'] ?? (str_starts_with($data['music_path'], 'http') ? $data['music_path'] : asset(ltrim($data['music_path'], '/'))) }}" type="audio/mpeg">
    </audio>

    <!-- Floating Audio Toggle Button -->
    <button @click="toggleMusic()"
            x-show="opened"
            x-transition
            class="fixed bottom-6 right-6 z-40 bg-white/90 backdrop-blur border border-rose-200 text-rose-600 p-3.5 rounded-full shadow-xl hover:scale-110 transition-transform active:scale-95 focus:outline-none"
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
         class="fixed inset-0 z-50 flex items-center justify-center bg-gradient-to-b from-[#fff1f2] via-[#ffe4e6] to-[#fff1f2] p-6 text-center select-none">
        
        <div class="max-w-md w-full bg-white/80 backdrop-blur-md p-8 sm:p-10 rounded-3xl shadow-2xl border border-rose-100/80 relative overflow-hidden">
            <!-- Floral Decorative corners -->
            <div class="absolute -top-10 -left-10 w-28 h-28 bg-rose-200/40 rounded-full blur-xl"></div>
            <div class="absolute -bottom-10 -right-10 w-28 h-28 bg-rose-200/40 rounded-full blur-xl"></div>

            <p class="text-xs font-semibold tracking-widest text-rose-500 uppercase mb-3">{{ $data['opening']['title'] ?? 'The Wedding of' }}</p>
            <h1 class="font-serif-title text-4xl sm:text-5xl font-bold text-stone-800 tracking-tight mb-2">
                {{ $data['party1']['name'] ?? 'Fulanah' }} <span class="font-cursive text-rose-500 font-normal text-3xl">&</span> {{ $data['party2']['name'] ?? 'Fulan' }}
            </h1>
            <div class="w-16 h-0.5 bg-rose-300 mx-auto my-4"></div>

            <p class="text-xs text-stone-500 mb-1">Kepada Yth. Bapak/Ibu/Saudara/i:</p>
            <div class="inline-block bg-rose-50 border border-rose-200/80 px-4 py-2 rounded-xl my-2">
                <span class="text-base sm:text-lg font-semibold text-rose-800">{{ $recipientName ?? 'Tamu Undangan' }}</span>
                @if(!empty($isPersonal))
                    <span class="block text-[11px] text-rose-600 font-medium">Link Tamu Spesial</span>
                @endif
            </div>
            <p class="text-xs text-stone-500 mt-2 mb-6 italic">*Mohon maaf bila ada kesalahan penulisan nama/gelar</p>

            <button @click="openInvitation()"
                    class="w-full inline-flex items-center justify-center gap-2 bg-gradient-to-r from-rose-500 to-rose-600 hover:from-rose-600 hover:to-rose-700 text-white font-medium px-6 py-3.5 rounded-2xl shadow-lg shadow-rose-500/25 transition-all transform hover:-translate-y-0.5 active:translate-y-0 text-sm tracking-wide">
                <svg class="w-5 h-5 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76" />
                </svg>
                <span>Buka Undangan</span>
            </button>
        </div>
    </div>

    <!-- MAIN INVITATION CONTENT -->
    <main class="max-w-xl mx-auto bg-white min-h-screen shadow-2xl relative border-x border-rose-100">

        <!-- 1. HERO SECTION -->
        <header class="relative pt-16 pb-20 px-6 text-center bg-gradient-to-b from-[#fff1f2]/80 via-white to-white overflow-hidden">
            <div class="inline-block px-3 py-1 bg-rose-100/70 text-rose-700 text-xs font-semibold rounded-full tracking-wider uppercase mb-4">
                Walimatul 'Urs
            </div>
            <h1 class="font-cursive text-5xl sm:text-6xl text-rose-600 mb-2">
                {{ $data['party1']['name'] ?? 'Fulanah' }} & {{ $data['party2']['name'] ?? 'Fulan' }}
            </h1>
            <p class="font-serif-title text-stone-600 text-sm tracking-widest uppercase mb-6">
                {{ isset($invitation->events[0]) ? $invitation->events[0]->start_at->translatedFormat('l, d F Y') : 'Segera Hadir' }}
            </p>

            <!-- COUNTDOWN TIMER -->
            <div class="grid grid-cols-4 gap-2.5 max-w-xs mx-auto text-center mt-6">
                <div class="bg-rose-50/80 border border-rose-100 rounded-2xl p-2.5">
                    <span class="block text-xl sm:text-2xl font-bold text-rose-700" x-text="countdown.days">0</span>
                    <span class="text-[10px] text-stone-500 uppercase tracking-wider">Hari</span>
                </div>
                <div class="bg-rose-50/80 border border-rose-100 rounded-2xl p-2.5">
                    <span class="block text-xl sm:text-2xl font-bold text-rose-700" x-text="countdown.hours">0</span>
                    <span class="text-[10px] text-stone-500 uppercase tracking-wider">Jam</span>
                </div>
                <div class="bg-rose-50/80 border border-rose-100 rounded-2xl p-2.5">
                    <span class="block text-xl sm:text-2xl font-bold text-rose-700" x-text="countdown.minutes">0</span>
                    <span class="text-[10px] text-stone-500 uppercase tracking-wider">Menit</span>
                </div>
                <div class="bg-rose-50/80 border border-rose-100 rounded-2xl p-2.5">
                    <span class="block text-xl sm:text-2xl font-bold text-rose-700" x-text="countdown.seconds">0</span>
                    <span class="text-[10px] text-stone-500 uppercase tracking-wider">Detik</span>
                </div>
            </div>
        </header>

        <!-- 2. OPENING & AYAT SUCI -->
        <section class="py-12 px-6 sm:px-10 text-center border-t border-rose-50">
            <p class="font-serif-title italic text-stone-600 text-xs sm:text-sm max-w-md mx-auto leading-relaxed">
                "{{ $data['opening']['quote'] ?? 'Dan di antara tanda-tanda kebesaran-Nya ialah Dia menciptakan pasangan-pasangan untukmu dari jenismu sendiri...' }}"
            </p>
            @if(!empty($data['opening']['quote_source']))
                <p class="text-xs font-semibold text-rose-600 mt-3 tracking-wider">{{ $data['opening']['quote_source'] }}</p>
            @endif

            <div class="mt-8">
                <p class="text-xs text-rose-500 font-semibold tracking-wide uppercase">{{ $data['opening']['greeting'] ?? 'Assalamu’alaikum Warahmatullahi Wabarakatuh' }}</p>
                <p class="text-xs sm:text-sm text-stone-600 mt-2 leading-relaxed max-w-md mx-auto">
                    Dengan memohon rahmat dan ridho Allah SWT, kami bermaksud menyelenggarakan syukuran pernikahan putra-putri kami:
                </p>
            </div>
        </section>

        <!-- 3. PROFILE MEMPELAI -->
        <section class="py-10 px-6 sm:px-10 bg-rose-50/30">
            <!-- Mempelai Wanita -->
            <div class="text-center mb-10">
                <div class="w-32 h-32 mx-auto rounded-full p-1 border-2 border-dashed border-rose-300 shadow-md overflow-hidden bg-white mb-4">
                    @if(!empty($data['party1']['photo_path']))
                        <img src="{{ $data['party1']['photo_path'] }}" alt="{{ $data['party1']['name'] }}" class="w-full h-full object-cover rounded-full">
                    @else
                        <div class="w-full h-full rounded-full bg-rose-100 flex items-center justify-center text-rose-400 font-serif-title text-4xl">
                            {{ substr($data['party1']['name'] ?? 'W', 0, 1) }}
                        </div>
                    @endif
                </div>
                <h3 class="font-serif-title text-2xl font-bold text-stone-800">{{ $data['party1']['full_name'] ?? 'Siti Fulanah, S.Kom' }}</h3>
                <p class="text-xs text-rose-600 font-medium mt-1">{{ $data['party1']['role_label'] ?? 'Mempelai Wanita' }}</p>
                <p class="text-xs text-stone-500 mt-1">{{ $data['party1']['child_order'] ?? 'Putri pertama dari' }} {{ $data['party1']['parents'] ?? 'Bpk. Ahmad & Ibu Fatimah' }}</p>
                @if(!empty($data['party1']['instagram']))
                    <a href="https://instagram.com/{{ ltrim($data['party1']['instagram'], '@') }}" target="_blank" class="inline-flex items-center gap-1 text-xs text-rose-500 hover:text-rose-700 mt-2 font-medium">
                        <span>{{ $data['party1']['instagram'] }}</span>
                    </a>
                @endif
            </div>

            <div class="text-center my-4 font-cursive text-4xl text-rose-400">&</div>

            <!-- Mempelai Pria -->
            <div class="text-center">
                <div class="w-32 h-32 mx-auto rounded-full p-1 border-2 border-dashed border-rose-300 shadow-md overflow-hidden bg-white mb-4">
                    @if(!empty($data['party2']['photo_path']))
                        <img src="{{ $data['party2']['photo_path'] }}" alt="{{ $data['party2']['name'] }}" class="w-full h-full object-cover rounded-full">
                    @else
                        <div class="w-full h-full rounded-full bg-rose-100 flex items-center justify-center text-rose-400 font-serif-title text-4xl">
                            {{ substr($data['party2']['name'] ?? 'P', 0, 1) }}
                        </div>
                    @endif
                </div>
                <h3 class="font-serif-title text-2xl font-bold text-stone-800">{{ $data['party2']['full_name'] ?? 'Muhammad Fulan, S.T' }}</h3>
                <p class="text-xs text-rose-600 font-medium mt-1">{{ $data['party2']['role_label'] ?? 'Mempelai Pria' }}</p>
                <p class="text-xs text-stone-500 mt-1">{{ $data['party2']['child_order'] ?? 'Putra kedua dari' }} {{ $data['party2']['parents'] ?? 'Bpk. Hasan & Ibu Aminah' }}</p>
                @if(!empty($data['party2']['instagram']))
                    <a href="https://instagram.com/{{ ltrim($data['party2']['instagram'], '@') }}" target="_blank" class="inline-flex items-center gap-1 text-xs text-rose-500 hover:text-rose-700 mt-2 font-medium">
                        <span>{{ $data['party2']['instagram'] }}</span>
                    </a>
                @endif
            </div>
        </section>

        <!-- 4. RANGKAIAN ACARA -->
        <section class="py-14 px-6 sm:px-10">
            <div class="text-center mb-8">
                <span class="text-xs font-semibold text-rose-500 uppercase tracking-widest">Informasi Acara</span>
                <h2 class="font-serif-title text-3xl font-bold text-stone-800 mt-1">Waktu & Tempat</h2>
                <div class="w-12 h-0.5 bg-rose-300 mx-auto mt-3"></div>
            </div>

            <div class="space-y-6">
                @forelse($invitation->events as $event)
                    <div class="bg-gradient-to-br from-white to-rose-50/50 border border-rose-100 rounded-3xl p-6 sm:p-8 shadow-sm text-center relative overflow-hidden">
                        <h3 class="font-serif-title text-xl font-bold text-rose-900 mb-2">{{ $event->title }}</h3>
                        
                        <div class="my-4 py-3 border-y border-rose-100">
                            <p class="font-semibold text-stone-800 text-sm sm:text-base">{{ $event->start_at->translatedFormat('l, d F Y') }}</p>
                            <p class="text-xs text-rose-600 font-medium mt-1">
                                Pukul {{ $event->start_at->format('H:i') }} @if($event->end_at) - {{ $event->end_at->format('H:i') }} @endif {{ $event->timezone }}
                            </p>
                        </div>

                        <p class="font-semibold text-stone-800 text-sm">{{ $event->venue_name }}</p>
                        @if($event->address)
                            <p class="text-xs text-stone-500 mt-1 leading-relaxed">{{ $event->address }}</p>
                        @endif

                        @if(!empty($event->maps_url))
                            <a href="{{ $event->maps_url }}" target="_blank"
                               class="inline-flex items-center gap-1.5 mt-5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-semibold px-4 py-2.5 rounded-xl transition-colors">
                                <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>Petunjuk Lokasi (Google Maps)</span>
                            </a>
                        @endif
                    </div>
                @empty
                    <p class="text-center text-xs text-stone-400">Jadwal acara belum diatur.</p>
                @endforelse
            </div>
        </section>

        <!-- 5. KISAH CINTA / STORY -->
        @if(!empty($data['story']) && count($data['story']) > 0)
        <section class="py-12 px-6 sm:px-10 bg-rose-50/20">
            <div class="text-center mb-8">
                <span class="text-xs font-semibold text-rose-500 uppercase tracking-widest">Our Journey</span>
                <h2 class="font-serif-title text-3xl font-bold text-stone-800 mt-1">Kisah Cinta Kami</h2>
                <div class="w-12 h-0.5 bg-rose-300 mx-auto mt-3"></div>
            </div>

            <div class="relative border-l-2 border-rose-200 ml-4 sm:ml-8 space-y-6">
                @foreach($data['story'] as $story)
                    <div class="relative pl-6">
                        <div class="absolute -left-2.5 top-1.5 w-4 h-4 rounded-full bg-rose-500 border-4 border-white shadow"></div>
                        <span class="text-xs font-bold text-rose-600 bg-rose-100/80 px-2.5 py-0.5 rounded-full">{{ $story['year'] ?? '' }}</span>
                        <h4 class="font-serif-title text-base font-bold text-stone-800 mt-1.5">{{ $story['title'] ?? '' }}</h4>
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
                <span class="text-xs font-semibold text-rose-500 uppercase tracking-widest">Momen Indah</span>
                <h2 class="font-serif-title text-3xl font-bold text-stone-800 mt-1">Galeri Foto</h2>
                <div class="w-12 h-0.5 bg-rose-300 mx-auto mt-3"></div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @foreach($data['gallery'] as $photo)
                    <div class="aspect-square rounded-2xl overflow-hidden shadow-sm border border-rose-100 group">
                        <img src="{{ $photo['url'] ?? $photo }}" alt="Galeri" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    </div>
                @endforeach
            </div>
        </section>
        @endif

        <!-- 7. AMPLOP DIGITAL / HADIAH -->
        @if(!empty($data['gifts']) && count($data['gifts']) > 0)
        <section class="py-12 px-6 sm:px-10 bg-rose-50/40" x-data="{ copiedIdx: null }">
            <div class="text-center mb-8">
                <span class="text-xs font-semibold text-rose-500 uppercase tracking-widest">Tanda Kasih</span>
                <h2 class="font-serif-title text-3xl font-bold text-stone-800 mt-1">Amplop Digital</h2>
                <div class="w-12 h-0.5 bg-rose-300 mx-auto mt-3"></div>
                <p class="text-xs text-stone-500 mt-3 max-w-sm mx-auto leading-relaxed">
                    Doa restu Anda merupakan karunia terindah bagi kami. Namun jika ingin memberikan tanda kasih secara cashless, silakan melalui rekening di bawah ini:
                </p>
            </div>

            <div class="space-y-4 max-w-sm mx-auto">
                @foreach($data['gifts'] as $idx => $gift)
                    <div class="bg-white border border-rose-200 rounded-2xl p-5 shadow-sm relative overflow-hidden">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-rose-800 tracking-wider text-sm">{{ $gift['bank_name'] ?? 'BANK' }}</span>
                            <span class="text-[10px] text-stone-400 uppercase tracking-wider">Transfer Bank</span>
                        </div>
                        <div class="font-mono text-lg font-bold text-stone-800 tracking-wider my-1">
                            {{ $gift['account_number'] ?? '' }}
                        </div>
                        <p class="text-xs text-stone-500">a.n. <span class="font-medium text-stone-700">{{ $gift['account_holder'] ?? '' }}</span></p>

                        <button @click="navigator.clipboard.writeText('{{ $gift['account_number'] ?? '' }}'); copiedIdx = {{ $idx }}; setTimeout(() => copiedIdx = null, 2500)"
                                class="mt-4 w-full inline-flex items-center justify-center gap-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-semibold py-2 rounded-xl border border-rose-200 transition-colors">
                            <span x-show="copiedIdx !== {{ $idx }}">Salin Nomor Rekening</span>
                            <span x-show="copiedIdx === {{ $idx }}" class="text-emerald-600 font-bold">✓ Tersalin ke Clipboard</span>
                        </button>
                    </div>
                @endforeach
            </div>
        </section>
        @endif

        <!-- 8. FORM RSVP & BUKU TAMU -->
        @if(($data['rsvp_enabled'] ?? true) || ($data['wishes_enabled'] ?? true))
        <section class="py-14 px-6 sm:px-10" id="rsvp-section" x-data="rsvpFormHandler('{{ route('invitation.rsvp', ['slug' => $invitation->slug]) }}')">
            <div class="text-center mb-8">
                <span class="text-xs font-semibold text-rose-500 uppercase tracking-widest">Konfirmasi & Doa</span>
                <h2 class="font-serif-title text-3xl font-bold text-stone-800 mt-1">RSVP & Ucapan</h2>
                <div class="w-12 h-0.5 bg-rose-300 mx-auto mt-3"></div>
            </div>

            <!-- Toast Alert -->
            <div x-show="successMessage" x-cloak class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-medium text-center">
                <span x-text="successMessage"></span>
            </div>

            <form @submit.prevent="submitForm()" class="bg-white border border-rose-100 rounded-3xl p-6 sm:p-8 shadow-md space-y-4">
                <input type="hidden" name="personal_token" value="{{ $guest->personal_token ?? '' }}">

                <div>
                    <label class="block text-xs font-semibold text-stone-700 mb-1">Nama Lengkap *</label>
                    <input type="text" x-model="form.name" required
                           class="w-full text-xs sm:text-sm px-4 py-2.5 rounded-xl border border-rose-200 focus:ring-2 focus:ring-rose-400 focus:border-rose-400"
                           placeholder="Tulis nama Anda">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-stone-700 mb-1">No. WhatsApp / HP</label>
                    <input type="text" x-model="form.phone"
                           class="w-full text-xs sm:text-sm px-4 py-2.5 rounded-xl border border-rose-200 focus:ring-2 focus:ring-rose-400 focus:border-rose-400"
                           placeholder="Contoh: 08123456789">
                </div>

                @if($data['rsvp_enabled'] ?? true)
                <div>
                    <label class="block text-xs font-semibold text-stone-700 mb-1">Konfirmasi Kehadiran *</label>
                    <select x-model="form.rsvp_status" required
                            class="w-full text-xs sm:text-sm px-4 py-2.5 rounded-xl border border-rose-200 focus:ring-2 focus:ring-rose-400 focus:border-rose-400">
                        <option value="attended">Ya, Saya akan Hadir</option>
                        <option value="maybe">Masih Ragu / Belum Pasti</option>
                        <option value="not_attended">Maaf, Tidak Dapat Hadir</option>
                    </select>
                </div>

                <div x-show="form.rsvp_status === 'attended'">
                    <label class="block text-xs font-semibold text-stone-700 mb-1">Jumlah Orang yang Hadir</label>
                    <input type="number" min="1" max="10" x-model="form.guest_count"
                           class="w-full text-xs sm:text-sm px-4 py-2.5 rounded-xl border border-rose-200 focus:ring-2 focus:ring-rose-400 focus:border-rose-400">
                </div>
                @endif

                @if($data['wishes_enabled'] ?? true)
                <div>
                    <label class="block text-xs font-semibold text-stone-700 mb-1">Doa Restu & Ucapan</label>
                    <textarea x-model="form.message" rows="3"
                              class="w-full text-xs sm:text-sm px-4 py-2.5 rounded-xl border border-rose-200 focus:ring-2 focus:ring-rose-400 focus:border-rose-400"
                              placeholder="Tulis ucapan dan doa terbaik untuk kami..."></textarea>
                </div>
                @endif

                <button type="submit" :disabled="submitting"
                        class="w-full bg-gradient-to-r from-rose-500 to-rose-600 hover:from-rose-600 hover:to-rose-700 text-white font-semibold py-3 rounded-xl shadow-md transition-all text-xs sm:text-sm disabled:opacity-50">
                    <span x-show="!submitting">Kirim Konfirmasi & Ucapan</span>
                    <span x-show="submitting">Mengirimkan...</span>
                </button>
            </form>

            <!-- FEED UCAPAN TERBARU -->
            <div class="mt-8 space-y-3">
                <h4 class="text-xs font-bold text-stone-700 uppercase tracking-wider mb-2">Ucapan & Doa Teman/Kerabat ({{ $invitation->wishes->count() }})</h4>
                <div class="max-h-72 overflow-y-auto space-y-2.5 pr-1">
                    <template x-for="wish in localWishes" :key="wish.id">
                        <div class="bg-rose-50/50 border border-rose-100 p-3.5 rounded-2xl">
                            <span class="font-bold text-rose-900 text-xs sm:text-sm block" x-text="wish.name"></span>
                            <p class="text-xs text-stone-600 mt-1 leading-relaxed" x-text="wish.message"></p>
                        </div>
                    </template>
                    @foreach($invitation->wishes as $w)
                        <div class="bg-rose-50/50 border border-rose-100 p-3.5 rounded-2xl">
                            <span class="font-bold text-rose-900 text-xs sm:text-sm block">{{ $w->name }}</span>
                            <p class="text-xs text-stone-600 mt-1 leading-relaxed">{{ $w->message }}</p>
                            <span class="text-[10px] text-stone-400 mt-1 block">{{ $w->created_at->diffForHumans() }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        <!-- 9. CLOSING SECTION -->
        <section class="py-14 px-6 sm:px-10 text-center bg-gradient-to-b from-white to-[#fff1f2]/60 border-t border-rose-100">
            <p class="text-xs sm:text-sm text-stone-600 max-w-md mx-auto leading-relaxed">
                {{ $data['closing']['text'] ?? 'Merupakan suatu kehormatan dan kebahagiaan bagi kami apabila Bapak/Ibu/Saudara/i berkenan hadir untuk memberikan doa restu.' }}
            </p>
            <h3 class="font-cursive text-4xl text-rose-600 mt-6">{{ $data['closing']['signature'] ?? ($data['party1']['name'] . ' & ' . $data['party2']['name']) }}</h3>

            <!-- Tombol Bagikan WhatsApp -->
            <div class="mt-8 pt-6 border-t border-rose-100">
                <a href="https://api.whatsapp.com/send?text={{ urlencode('Halo! Berikut undangan pernikahan digital ' . ($data['party1']['name'] ?? '') . ' & ' . ($data['party2']['name'] ?? '') . ': ' . url()->current()) }}"
                   target="_blank"
                   class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-4 py-2.5 rounded-xl shadow-md transition-colors">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
                    </svg>
                    <span>Bagikan Undangan via WhatsApp</span>
                </a>
            </div>

            <p class="text-[10px] text-stone-400 mt-8">Dibuat dengan segenap cinta di <a href="{{ route('home') }}" class="font-semibold text-rose-500 hover:underline">Kawinan.id</a></p>
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
                        audio.play().then(() => {
                            this.isPlaying = true;
                        }).catch(() => {
                            this.isPlaying = false;
                        });
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
                            alert(data.message || 'Terjadi kesalahan saat menyimpan RSVP.');
                        }
                    } catch (e) {
                        alert('Gagal mengirim RSVP. Mohon periksa koneksi internet Anda.');
                    } finally {
                        this.submitting = false;
                    }
                }
            };
        }
    </script>
</body>
</html>
