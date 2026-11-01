<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $data['opening']['title'] ?? 'Walimatul Khitan' }} {{ $data['party1']['name'] ?? 'Rayyan' }}</title>
    <meta name="description" content="Undangan Walimatul Khitan {{ $data['party1']['full_name'] ?? 'Rayyan' }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (Vite build) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .font-fredoka { font-family: 'Fredoka', cursive, sans-serif; }
        .font-body { font-family: 'Plus Jakarta Sans', sans-serif; }
        [x-cloak] { display: none !important; }
        .spin-slow { animation: spin 8s linear infinite; }
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    </style>
</head>
<body class="font-body bg-[#f0f9ff] text-slate-700 antialiased selection:bg-sky-100 selection:text-sky-600 overflow-x-hidden"
      x-data="invitationApp({
          musicUrl: '{{ $data['music_path'] ?? '' }}',
          eventDate: '{{ isset($invitation->events[0]) ? $invitation->events[0]->start_at->toIso8601String() : now()->addDays(14)->toIso8601String() }}'
      })">

    <!-- Audio Player Element -->
    <audio x-ref="audioPlayer" loop preload="none">
        <source src="{{ $data['music_path'] ?? 'https://assets.mixkit.co/music/preview/mixkit-game-level-music-689.mp3' }}" type="audio/mpeg">
    </audio>

    <!-- Floating Audio Button -->
    <button @click="toggleMusic()"
            x-show="opened"
            x-transition
            class="fixed bottom-6 right-6 z-40 bg-white border-2 border-sky-300 text-sky-600 p-3.5 rounded-full shadow-xl hover:scale-110 transition-transform active:scale-95 focus:outline-none"
            :class="{ 'spin-slow': isPlaying }"
            title="Musik Latar">
        <svg x-show="isPlaying" class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/>
        </svg>
        <svg x-show="!isPlaying" class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2" />
        </svg>
    </button>

    <!-- COVER / BUKA UNDANGAN -->
    <div x-show="!opened"
         x-transition:leave="transition ease-in-out duration-700 transform"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-full"
         class="fixed inset-0 z-50 flex items-center justify-center bg-gradient-to-b from-[#e0f2fe] via-[#bae6fd] to-[#e0f2fe] p-6 text-center select-none">
        
        <div class="max-w-md w-full bg-white/95 backdrop-blur p-8 sm:p-10 rounded-3xl shadow-2xl border-4 border-sky-200 relative overflow-hidden">
            <span class="inline-block bg-amber-100 text-amber-700 font-fredoka font-semibold text-xs px-3 py-1 rounded-full uppercase tracking-wider mb-3">
                🎈 Syukuran Khitanan 🎈
            </span>

            <h1 class="font-fredoka text-3xl sm:text-4xl font-bold text-sky-800 tracking-tight mb-2">
                {{ $data['opening']['title'] ?? 'Walimatul Khitan' }}
            </h1>
            <p class="font-fredoka text-2xl text-amber-500 font-bold mb-4">
                {{ $data['party1']['name'] ?? 'Rayyan' }}
            </p>

            <div class="w-16 h-1 bg-sky-300 rounded-full mx-auto my-3"></div>

            <p class="text-xs text-slate-500 mb-1">Kepada Yth. Bapak/Ibu/Saudara/i:</p>
            <div class="inline-block bg-sky-50 border-2 border-dashed border-sky-300 px-5 py-2.5 rounded-2xl my-2">
                <span class="text-base sm:text-lg font-bold text-sky-900 font-fredoka">{{ $recipientName ?? 'Tamu Undangan' }}</span>
                @if(!empty($isPersonal))
                    <span class="block text-[11px] text-sky-600 font-medium">Tamu Terhormat</span>
                @endif
            </div>

            <button @click="openInvitation()"
                    class="mt-6 w-full inline-flex items-center justify-center gap-2 bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-600 hover:to-blue-700 text-white font-fredoka font-bold px-6 py-3.5 rounded-2xl shadow-lg shadow-sky-500/30 transition-all transform hover:-translate-y-0.5 text-base tracking-wide">
                <span>Buka Undangan Ceria</span>
                <svg class="w-5 h-5 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </button>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <main class="max-w-xl mx-auto bg-white min-h-screen shadow-2xl relative border-x border-sky-100">

        <!-- 1. HERO SECTION -->
        <header class="relative pt-16 pb-16 px-6 text-center bg-gradient-to-b from-[#e0f2fe]/60 via-white to-white">
            <div class="inline-flex items-center gap-1.5 px-3.5 py-1 bg-amber-400 text-amber-950 font-fredoka text-xs font-bold rounded-full mb-4 shadow-sm">
                <span>✨ Walimatul Khitan ✨</span>
            </div>
            <h1 class="font-fredoka text-4xl sm:text-5xl text-sky-600 font-bold mb-2">
                {{ $data['party1']['name'] ?? 'Rayyan' }}
            </h1>
            <p class="font-fredoka text-slate-500 text-sm tracking-wider uppercase mb-6">
                {{ isset($invitation->events[0]) ? $invitation->events[0]->start_at->translatedFormat('l, d F Y') : 'Segera Hadir' }}
            </p>

            <!-- COUNTDOWN -->
            <div class="grid grid-cols-4 gap-2.5 max-w-xs mx-auto text-center mt-6">
                <div class="bg-sky-50 border-2 border-sky-200 rounded-2xl p-2.5">
                    <span class="block text-xl sm:text-2xl font-fredoka font-bold text-sky-700" x-text="countdown.days">0</span>
                    <span class="text-[10px] text-slate-500 font-semibold uppercase">Hari</span>
                </div>
                <div class="bg-sky-50 border-2 border-sky-200 rounded-2xl p-2.5">
                    <span class="block text-xl sm:text-2xl font-fredoka font-bold text-sky-700" x-text="countdown.hours">0</span>
                    <span class="text-[10px] text-slate-500 font-semibold uppercase">Jam</span>
                </div>
                <div class="bg-sky-50 border-2 border-sky-200 rounded-2xl p-2.5">
                    <span class="block text-xl sm:text-2xl font-fredoka font-bold text-sky-700" x-text="countdown.minutes">0</span>
                    <span class="text-[10px] text-slate-500 font-semibold uppercase">Menit</span>
                </div>
                <div class="bg-sky-50 border-2 border-sky-200 rounded-2xl p-2.5">
                    <span class="block text-xl sm:text-2xl font-fredoka font-bold text-sky-700" x-text="countdown.seconds">0</span>
                    <span class="text-[10px] text-slate-500 font-semibold uppercase">Detik</span>
                </div>
            </div>
        </header>

        <!-- 2. DOA KHITAN SECTION (No wedding verses!) -->
        <section class="py-10 px-6 sm:px-10 text-center border-t border-sky-50">
            <div class="bg-sky-50/70 border-2 border-dashed border-sky-200 p-6 rounded-3xl max-w-md mx-auto">
                <p class="font-fredoka text-xs font-semibold text-sky-700 uppercase tracking-wider mb-2">Doa Syukuran Khitan</p>
                <p class="italic text-slate-700 text-xs sm:text-sm leading-relaxed">
                    "{{ $data['opening']['quote'] ?? 'Semoga ananda menjadi anak yang sholeh, berbakti kepada kedua orang tua, agama, nusa, dan bangsa. Serta dikaruniai kesehatan dan kecerdasan.' }}"
                </p>
            </div>

            <div class="mt-8">
                <p class="text-xs text-sky-600 font-bold uppercase tracking-wider">{{ $data['opening']['greeting'] ?? 'Assalamu’alaikum Warahmatullahi Wabarakatuh' }}</p>
                <p class="text-xs sm:text-sm text-slate-600 mt-2 leading-relaxed max-w-md mx-auto">
                    Dengan memohon ridho dan rahmat Allah SWT, kami sekeluarga bermaksud menyelenggarakan syukuran khitanan putra kami tersayang:
                </p>
            </div>
        </section>

        <!-- 3. PROFILE ANAK YANG DIKHITAN & ORANG TUA -->
        <section class="py-12 px-6 sm:px-10 bg-sky-50/40">
            <div class="text-center">
                <div class="w-36 h-36 mx-auto rounded-3xl p-1.5 border-4 border-sky-300 shadow-lg overflow-hidden bg-white mb-4 transform -rotate-1">
                    @if(!empty($data['party1']['photo_path']))
                        <img src="{{ $data['party1']['photo_path'] }}" alt="{{ $data['party1']['name'] }}" class="w-full h-full object-cover rounded-2xl">
                    @else
                        <div class="w-full h-full rounded-2xl bg-sky-100 flex items-center justify-center text-sky-500 font-fredoka text-5xl">
                            👦
                        </div>
                    @endif
                </div>

                <h3 class="font-fredoka text-2xl sm:text-3xl font-bold text-sky-900">{{ $data['party1']['full_name'] ?? 'Muhammad Rayyan Al-Ghifari' }}</h3>
                <p class="text-xs font-bold text-amber-600 uppercase tracking-wider mt-1">{{ $data['party1']['role_label'] ?? 'Anak yang Dikhitan' }}</p>
                <p class="text-xs sm:text-sm text-slate-600 mt-2 font-medium">
                    {{ $data['party1']['child_order'] ?? 'Putra tercinta dari' }}
                    <span class="font-bold text-slate-800">{{ $data['party1']['parents'] ?? 'Bpk. Ahmad & Ibu Fatimah' }}</span>
                </p>
            </div>
        </section>

        <!-- 4. RANGKAIAN ACARA -->
        <section class="py-14 px-6 sm:px-10">
            <div class="text-center mb-8">
                <span class="text-xs font-bold text-sky-600 uppercase tracking-widest">Informasi Acara</span>
                <h2 class="font-fredoka text-3xl font-bold text-slate-800 mt-1">Waktu & Tempat Syukuran</h2>
                <div class="w-12 h-1 bg-sky-400 rounded-full mx-auto mt-2"></div>
            </div>

            <div class="space-y-6">
                @forelse($invitation->events as $event)
                    <div class="bg-gradient-to-br from-white to-sky-50/60 border-2 border-sky-100 rounded-3xl p-6 sm:p-8 shadow-sm text-center relative overflow-hidden">
                        <div class="inline-block bg-sky-100 text-sky-800 font-fredoka font-semibold text-xs px-3 py-1 rounded-full mb-2">
                            {{ $event->title }}
                        </div>
                        
                        <div class="my-4 py-3 border-y-2 border-dashed border-sky-100">
                            <p class="font-fredoka font-bold text-slate-800 text-base sm:text-lg">{{ $event->start_at->translatedFormat('l, d F Y') }}</p>
                            <p class="text-xs text-sky-600 font-bold mt-1">
                                Pukul {{ $event->start_at->format('H:i') }} @if($event->end_at) - {{ $event->end_at->format('H:i') }} @endif {{ $event->timezone }}
                            </p>
                        </div>

                        <p class="font-bold text-slate-800 text-sm">{{ $event->venue_name }}</p>
                        @if($event->address)
                            <p class="text-xs text-slate-500 mt-1 leading-relaxed">{{ $event->address }}</p>
                        @endif

                        @if(!empty($event->maps_url))
                            <a href="{{ $event->maps_url }}" target="_blank"
                               class="inline-flex items-center gap-1.5 mt-5 bg-sky-500 hover:bg-sky-600 text-white text-xs font-fredoka font-bold px-4 py-2.5 rounded-xl shadow-md transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>Buka Petunjuk Peta Google Maps</span>
                            </a>
                        @endif
                    </div>
                @empty
                    <p class="text-center text-xs text-slate-400">Jadwal acara belum diatur.</p>
                @endforelse
            </div>
        </section>

        <!-- 5. GALERI FOTO KECERIAAN -->
        @if(!empty($data['gallery']) && count($data['gallery']) > 0)
        <section class="py-12 px-6 sm:px-10 bg-sky-50/20">
            <div class="text-center mb-8">
                <span class="text-xs font-bold text-sky-600 uppercase tracking-widest">Momen Ceria</span>
                <h2 class="font-fredoka text-3xl font-bold text-slate-800 mt-1">Galeri Foto</h2>
                <div class="w-12 h-1 bg-sky-400 rounded-full mx-auto mt-2"></div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @foreach($data['gallery'] as $photo)
                    <div class="aspect-square rounded-2xl overflow-hidden shadow-sm border-2 border-sky-100 group">
                        <img src="{{ $photo['url'] ?? $photo }}" alt="Galeri Khitan" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    </div>
                @endforeach
            </div>
        </section>
        @endif

        <!-- 6. AMPLOP DIGITAL / TANDA KASIH -->
        @if(!empty($data['gifts']) && count($data['gifts']) > 0)
        <section class="py-12 px-6 sm:px-10 bg-sky-50/50" x-data="{ copiedIdx: null }">
            <div class="text-center mb-8">
                <span class="text-xs font-bold text-sky-600 uppercase tracking-widest">Tanda Kasih</span>
                <h2 class="font-fredoka text-3xl font-bold text-slate-800 mt-1">Amplop Digital</h2>
                <div class="w-12 h-1 bg-sky-400 rounded-full mx-auto mt-2"></div>
                <p class="text-xs text-slate-500 mt-3 max-w-sm mx-auto leading-relaxed">
                    Bagi kerabat yang ingin memberikan kado/tanda kasih secara cashless kepada ananda tercinta, dapat melalui rekening berikut:
                </p>
            </div>

            <div class="space-y-4 max-w-sm mx-auto">
                @foreach($data['gifts'] as $idx => $gift)
                    <div class="bg-white border-2 border-sky-200 rounded-2xl p-5 shadow-sm relative overflow-hidden">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-fredoka font-bold text-sky-800 tracking-wider text-base">{{ $gift['bank_name'] ?? 'BANK' }}</span>
                            <span class="text-[10px] text-slate-400 uppercase font-semibold">Transfer / E-Wallet</span>
                        </div>
                        <div class="font-mono text-lg font-bold text-slate-800 tracking-wider my-1">
                            {{ $gift['account_number'] ?? '' }}
                        </div>
                        <p class="text-xs text-slate-500">a.n. <span class="font-bold text-slate-700">{{ $gift['account_holder'] ?? '' }}</span></p>

                        <button @click="navigator.clipboard.writeText('{{ $gift['account_number'] ?? '' }}'); copiedIdx = {{ $idx }}; setTimeout(() => copiedIdx = null, 2500)"
                                class="mt-4 w-full inline-flex items-center justify-center gap-1.5 bg-sky-100 hover:bg-sky-200 text-sky-800 text-xs font-fredoka font-bold py-2 rounded-xl transition-colors">
                            <span x-show="copiedIdx !== {{ $idx }}">Salin Nomor Rekening</span>
                            <span x-show="copiedIdx === {{ $idx }}" class="text-emerald-600 font-bold">✓ Tersalin ke Clipboard</span>
                        </button>
                    </div>
                @endforeach
            </div>
        </section>
        @endif

        <!-- 7. RSVP & UCAPAN -->
        @if(($data['rsvp_enabled'] ?? true) || ($data['wishes_enabled'] ?? true))
        <section class="py-14 px-6 sm:px-10" id="rsvp-section" x-data="rsvpFormHandler('{{ route('invitation.rsvp', ['slug' => $invitation->slug]) }}')">
            <div class="text-center mb-8">
                <span class="text-xs font-bold text-sky-600 uppercase tracking-widest">Konfirmasi & Doa</span>
                <h2 class="font-fredoka text-3xl font-bold text-slate-800 mt-1">RSVP & Doa untuk Ananda</h2>
                <div class="w-12 h-1 bg-sky-400 rounded-full mx-auto mt-2"></div>
            </div>

            <!-- Toast Alert -->
            <div x-show="successMessage" x-cloak class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-medium text-center">
                <span x-text="successMessage"></span>
            </div>

            <form @submit.prevent="submitForm()" class="bg-white border-2 border-sky-100 rounded-3xl p-6 sm:p-8 shadow-md space-y-4">
                <input type="hidden" name="personal_token" value="{{ $guest->personal_token ?? '' }}">

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap *</label>
                    <input type="text" x-model="form.name" required
                           class="w-full text-xs sm:text-sm px-4 py-2.5 rounded-xl border border-sky-200 focus:ring-2 focus:ring-sky-400 focus:border-sky-400"
                           placeholder="Nama Anda">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">No. WhatsApp / HP</label>
                    <input type="text" x-model="form.phone"
                           class="w-full text-xs sm:text-sm px-4 py-2.5 rounded-xl border border-sky-200 focus:ring-2 focus:ring-sky-400 focus:border-sky-400"
                           placeholder="08123456789">
                </div>

                @if($data['rsvp_enabled'] ?? true)
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Konfirmasi Kehadiran *</label>
                    <select x-model="form.rsvp_status" required
                            class="w-full text-xs sm:text-sm px-4 py-2.5 rounded-xl border border-sky-200 focus:ring-2 focus:ring-sky-400 focus:border-sky-400">
                        <option value="attended">Hadir</option>
                        <option value="maybe">Masih Ragu / Belum Pasti</option>
                        <option value="not_attended">Tidak Dapat Hadir</option>
                    </select>
                </div>

                <div x-show="form.rsvp_status === 'attended'">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Jumlah Orang yang Hadir</label>
                    <input type="number" min="1" max="10" x-model="form.guest_count"
                           class="w-full text-xs sm:text-sm px-4 py-2.5 rounded-xl border border-sky-200 focus:ring-2 focus:ring-sky-400 focus:border-sky-400">
                </div>
                @endif

                @if($data['wishes_enabled'] ?? true)
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Ucapan & Doa</label>
                    <textarea x-model="form.message" rows="3"
                              class="w-full text-xs sm:text-sm px-4 py-2.5 rounded-xl border border-sky-200 focus:ring-2 focus:ring-sky-400 focus:border-sky-400"
                              placeholder="Tuliskan doa terbaik untuk anak yang dikhitan..."></textarea>
                </div>
                @endif

                <button type="submit" :disabled="submitting"
                        class="w-full bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-600 hover:to-blue-700 text-white font-fredoka font-bold py-3 rounded-xl shadow-md transition-all text-sm disabled:opacity-50">
                    <span x-show="!submitting">Kirim Konfirmasi & Doa</span>
                    <span x-show="submitting">Mengirim...</span>
                </button>
            </form>

            <!-- FEED UCAPAN -->
            <div class="mt-8 space-y-3">
                <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Doa Restu Sahabat & Keluarga ({{ $invitation->wishes->count() }})</h4>
                <div class="max-h-72 overflow-y-auto space-y-2.5 pr-1">
                    <template x-for="wish in localWishes" :key="wish.id">
                        <div class="bg-sky-50 border border-sky-100 p-3.5 rounded-2xl">
                            <span class="font-fredoka font-bold text-sky-900 text-sm block" x-text="wish.name"></span>
                            <p class="text-xs text-slate-600 mt-1 leading-relaxed" x-text="wish.message"></p>
                        </div>
                    </template>
                    @foreach($invitation->wishes as $w)
                        <div class="bg-sky-50 border border-sky-100 p-3.5 rounded-2xl">
                            <span class="font-fredoka font-bold text-sky-900 text-sm block">{{ $w->name }}</span>
                            <p class="text-xs text-slate-600 mt-1 leading-relaxed">{{ $w->message }}</p>
                            <span class="text-[10px] text-slate-400 mt-1 block">{{ $w->created_at->diffForHumans() }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        <!-- 8. PENUTUP -->
        <section class="py-14 px-6 sm:px-10 text-center bg-gradient-to-b from-white to-[#e0f2fe]/60 border-t border-sky-100">
            <p class="text-xs sm:text-sm text-slate-600 max-w-md mx-auto leading-relaxed">
                {{ $data['closing']['text'] ?? 'Merupakan suatu kehormatan dan kebahagiaan bagi kami sekeluarga apabila Bapak/Ibu/Saudara/i berkenan hadir serta memberikan doa restu.' }}
            </p>
            <h3 class="font-fredoka font-bold text-2xl text-sky-700 mt-6">{{ $data['closing']['signature'] ?? 'Keluarga Besar' }}</h3>

            <div class="mt-8 pt-6 border-t border-sky-100">
                <a href="https://api.whatsapp.com/send?text={{ urlencode('Halo! Berikut undangan Walimatul Khitan ananda ' . ($data['party1']['name'] ?? '') . ': ' . url()->current()) }}"
                   target="_blank"
                   class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-4 py-2.5 rounded-xl shadow-md transition-colors">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
                    </svg>
                    <span>Bagikan Undangan ke WhatsApp</span>
                </a>
            </div>

            <p class="text-[10px] text-slate-400 mt-8">Dibuat dengan suka cita di <a href="{{ route('home') }}" class="font-semibold text-sky-600 hover:underline">Kawinan.id</a></p>
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
