<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $data['opening']['title'] ?? 'Special Celebration' }}</title>
    <meta name="description" content="Undangan Acara {{ $data['opening']['title'] ?? '' }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (Vite build) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .font-cinzel { font-family: 'Cinzel', serif; }
        .font-montserrat { font-family: 'Montserrat', sans-serif; }
        [x-cloak] { display: none !important; }
        .spin-slow { animation: spin 8s linear infinite; }
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    </style>
</head>
<body class="font-montserrat bg-[#0f172a] text-slate-300 antialiased selection:bg-emerald-500/20 selection:text-emerald-400 overflow-x-hidden"
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
            class="fixed bottom-6 right-6 z-40 bg-slate-900/90 border border-slate-700 text-emerald-400 p-3.5 rounded-full shadow-2xl hover:scale-110 transition-transform active:scale-95 focus:outline-none"
            :class="{ 'spin-slow': isPlaying }"
            title="Audio">
        <svg x-show="isPlaying" class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/>
        </svg>
        <svg x-show="!isPlaying" class="w-6 h-6 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2" />
        </svg>
    </button>
    @endif

    <!-- COVER / BUKA UNDANGAN -->
    <div x-show="!opened"
         x-transition:leave="transition ease-in-out duration-700 transform"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-full"
         class="fixed inset-0 z-50 flex items-center justify-center bg-gradient-to-b from-[#090d16] via-[#0f172a] to-[#090d16] p-6 text-center select-none">
        
        <div class="max-w-md w-full bg-slate-900/90 backdrop-blur-md p-8 sm:p-10 rounded-3xl shadow-2xl border border-slate-800 relative overflow-hidden">
            <div class="w-12 h-12 rounded-full border border-emerald-500/40 flex items-center justify-center mx-auto mb-4 text-emerald-400">
                ✦
            </div>

            <p class="text-xs uppercase tracking-[0.3em] text-emerald-400 mb-2">OFFICIAL INVITATION</p>
            <h1 class="font-cinzel text-3xl sm:text-4xl font-bold text-white tracking-wide mb-3">
                {{ $data['opening']['title'] ?? 'Special Celebration' }}
            </h1>
            <p class="text-xs text-slate-400 max-w-xs mx-auto mb-6">
                {{ $data['party1']['full_name'] ?? 'Penyelenggara Acara' }}
            </p>

            <div class="w-16 h-px bg-slate-700 mx-auto my-4"></div>

            <p class="text-[11px] text-slate-400 uppercase tracking-widest mb-1">TERUNTUK:</p>
            <div class="inline-block bg-slate-800/80 border border-slate-700 px-5 py-2.5 rounded-xl my-2">
                <span class="text-base font-semibold text-emerald-300 font-cinzel">{{ $recipientName ?? 'Tamu Undangan' }}</span>
                @if(!empty($isPersonal))
                    <span class="block text-[10px] text-emerald-500 uppercase tracking-widest mt-0.5">VIP Guest</span>
                @endif
            </div>

            <button @click="openInvitation()"
                    class="mt-6 w-full inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-500 text-white font-cinzel font-semibold px-6 py-3.5 rounded-xl shadow-lg shadow-emerald-900/40 transition-all text-xs tracking-[0.2em] uppercase">
                <span>Buka Undangan</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </button>
        </div>
    </div>

    <!-- MAIN INVITATION -->
    <main class="max-w-xl mx-auto bg-slate-900 min-h-screen shadow-2xl relative border-x border-slate-800">

        <!-- 1. HERO -->
        <header class="relative pt-16 pb-16 px-6 text-center border-b border-slate-800 bg-gradient-to-b from-slate-950 to-slate-900">
            <span class="text-xs font-semibold tracking-[0.3em] text-emerald-400 uppercase mb-3 inline-block">ACARA RESMI</span>
            <h1 class="font-cinzel text-3xl sm:text-4xl text-white font-bold tracking-wide mb-3">
                {{ $data['opening']['title'] ?? 'Special Celebration' }}
            </h1>
            <p class="text-xs text-slate-400 tracking-widest uppercase mb-8">
                {{ isset($invitation->events[0]) ? $invitation->events[0]->start_at->translatedFormat('l, d F Y') : 'Segera' }}
            </p>

            <!-- COUNTDOWN -->
            <div class="grid grid-cols-4 gap-2.5 max-w-xs mx-auto text-center">
                <div class="bg-slate-800/80 border border-slate-700/80 rounded-xl p-2.5">
                    <span class="block text-xl font-bold font-cinzel text-emerald-400" x-text="countdown.days">0</span>
                    <span class="text-[9px] text-slate-400 uppercase tracking-widest">Hari</span>
                </div>
                <div class="bg-slate-800/80 border border-slate-700/80 rounded-xl p-2.5">
                    <span class="block text-xl font-bold font-cinzel text-emerald-400" x-text="countdown.hours">0</span>
                    <span class="text-[9px] text-slate-400 uppercase tracking-widest">Jam</span>
                </div>
                <div class="bg-slate-800/80 border border-slate-700/80 rounded-xl p-2.5">
                    <span class="block text-xl font-bold font-cinzel text-emerald-400" x-text="countdown.minutes">0</span>
                    <span class="text-[9px] text-slate-400 uppercase tracking-widest">Menit</span>
                </div>
                <div class="bg-slate-800/80 border border-slate-700/80 rounded-xl p-2.5">
                    <span class="block text-xl font-bold font-cinzel text-emerald-400" x-text="countdown.seconds">0</span>
                    <span class="text-[9px] text-slate-400 uppercase tracking-widest">Detik</span>
                </div>
            </div>
        </header>

        <!-- 2. GREETINGS & QUOTE -->
        <section class="py-12 px-6 sm:px-10 text-center border-b border-slate-800">
            <p class="italic text-slate-300 text-xs sm:text-sm max-w-md mx-auto leading-relaxed">
                "{{ $data['opening']['quote'] ?? 'Kebahagiaan terasa sempurna ketika dapat berbagi momen istimewa bersama orang-orang tercinta.' }}"
            </p>
            <div class="mt-6 text-xs text-slate-400 max-w-md mx-auto leading-relaxed">
                {{ $data['opening']['greeting'] ?? 'Salam Sejahtera / Assalamu’alaikum Warahmatullahi Wabarakatuh' }}
            </div>
        </section>

        <!-- 3. RANGKAIAN ACARA -->
        <section class="py-14 px-6 sm:px-10 border-b border-slate-800">
            <div class="text-center mb-8">
                <span class="text-[10px] font-semibold text-emerald-400 uppercase tracking-[0.25em]">SCHEDULE</span>
                <h2 class="font-cinzel text-2xl font-bold text-white mt-1">Waktu & Tempat</h2>
                <div class="w-12 h-0.5 bg-emerald-500/50 mx-auto mt-2"></div>
            </div>

            <div class="space-y-6">
                @forelse($invitation->events as $event)
                    <div class="bg-slate-800/40 border border-slate-700/70 rounded-2xl p-6 text-center">
                        <h3 class="font-cinzel text-lg font-bold text-emerald-300 mb-2">{{ $event->title }}</h3>
                        
                        <div class="my-4 py-3 border-y border-slate-700/60">
                            <p class="font-semibold text-white text-sm">{{ $event->start_at->translatedFormat('l, d F Y') }}</p>
                            <p class="text-xs text-slate-400 mt-1">
                                {{ $event->start_at->format('H:i') }} @if($event->end_at) - {{ $event->end_at->format('H:i') }} @endif {{ $event->timezone }}
                            </p>
                        </div>

                        <p class="font-medium text-slate-200 text-xs sm:text-sm">{{ $event->venue_name }}</p>
                        @if($event->address)
                            <p class="text-xs text-slate-400 mt-1">{{ $event->address }}</p>
                        @endif

                        @if(!empty($event->maps_url))
                            <a href="{{ $event->maps_url }}" target="_blank"
                               class="inline-flex items-center gap-1.5 mt-4 bg-slate-700/60 hover:bg-slate-700 text-emerald-300 text-xs px-4 py-2 rounded-xl transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                </svg>
                                <span>Petunjuk Lokasi Google Maps</span>
                            </a>
                        @endif
                    </div>
                @empty
                    <p class="text-center text-xs text-slate-500">Jadwal belum ditentukan.</p>
                @endforelse
            </div>
        </section>

        <!-- 4. GALERI FOTO -->
        @if(!empty($data['gallery']) && count($data['gallery']) > 0)
        <section class="py-12 px-6 sm:px-10 border-b border-slate-800">
            <div class="text-center mb-8">
                <span class="text-[10px] font-semibold text-emerald-400 uppercase tracking-[0.25em]">GALLERY</span>
                <h2 class="font-cinzel text-2xl font-bold text-white mt-1">Galeri Foto</h2>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @foreach($data['gallery'] as $photo)
                    <div class="aspect-square rounded-xl overflow-hidden border border-slate-700 group">
                        <img src="{{ $photo['url'] ?? $photo }}" alt="Foto" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    </div>
                @endforeach
            </div>
        </section>
        @endif

        <!-- 5. AMPLOP DIGITAL / TANDA TERIMA KASIH -->
        @if(!empty($data['gifts']) && count($data['gifts']) > 0)
        <section class="py-12 px-6 sm:px-10 border-b border-slate-800" x-data="{ copiedIdx: null }">
            <div class="text-center mb-8">
                <span class="text-[10px] font-semibold text-emerald-400 uppercase tracking-[0.25em]">GIFTS</span>
                <h2 class="font-cinzel text-2xl font-bold text-white mt-1">Amplop Digital</h2>
            </div>

            <div class="space-y-4 max-w-sm mx-auto">
                @foreach($data['gifts'] as $idx => $gift)
                    <div class="bg-slate-800/60 border border-slate-700 rounded-2xl p-5 shadow-sm">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-white text-sm">{{ $gift['bank_name'] ?? 'BANK' }}</span>
                            <span class="text-[10px] text-slate-400 uppercase tracking-widest">Digital Transfer</span>
                        </div>
                        <div class="font-mono text-lg font-bold text-emerald-400 tracking-wider my-1">
                            {{ $gift['account_number'] ?? '' }}
                        </div>
                        <p class="text-xs text-slate-400">a.n. <span class="text-slate-200">{{ $gift['account_holder'] ?? '' }}</span></p>

                        <button @click="navigator.clipboard.writeText('{{ $gift['account_number'] ?? '' }}'); copiedIdx = {{ $idx }}; setTimeout(() => copiedIdx = null, 2500)"
                                class="mt-4 w-full bg-slate-700/50 hover:bg-slate-700 text-xs font-semibold py-2 rounded-xl border border-slate-600 transition-colors">
                            <span x-show="copiedIdx !== {{ $idx }}">Salin Nomor</span>
                            <span x-show="copiedIdx === {{ $idx }}" class="text-emerald-400 font-bold">✓ Tersalin</span>
                        </button>
                    </div>
                @endforeach
            </div>
        </section>
        @endif

        <!-- 6. RSVP & BUKU TAMU -->
        @if(($data['rsvp_enabled'] ?? true) || ($data['wishes_enabled'] ?? true))
        <section class="py-14 px-6 sm:px-10 border-b border-slate-800" id="rsvp-section" x-data="rsvpFormHandler('{{ route('invitation.rsvp', ['slug' => $invitation->slug]) }}')">
            <div class="text-center mb-8">
                <span class="text-[10px] font-semibold text-emerald-400 uppercase tracking-[0.25em]">CONFIRMATION</span>
                <h2 class="font-cinzel text-2xl font-bold text-white mt-1">Konfirmasi & Ucapan</h2>
            </div>

            <div x-show="successMessage" x-cloak class="mb-6 p-4 rounded-xl bg-emerald-950/60 border border-emerald-800 text-emerald-300 text-xs text-center">
                <span x-text="successMessage"></span>
            </div>

            <form @submit.prevent="submitForm()" class="bg-slate-800/40 border border-slate-700 rounded-2xl p-6 sm:p-8 space-y-4">
                <input type="hidden" name="personal_token" value="{{ $guest->personal_token ?? '' }}">

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Lengkap *</label>
                    <input type="text" x-model="form.name" required
                           class="w-full text-xs sm:text-sm px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white focus:ring-1 focus:ring-emerald-400 focus:border-emerald-400"
                           placeholder="Nama Anda">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">No. WhatsApp / HP</label>
                    <input type="text" x-model="form.phone"
                           class="w-full text-xs sm:text-sm px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white focus:ring-1 focus:ring-emerald-400 focus:border-emerald-400"
                           placeholder="08123456789">
                </div>

                @if($data['rsvp_enabled'] ?? true)
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Konfirmasi Kehadiran *</label>
                    <select x-model="form.rsvp_status" required
                            class="w-full text-xs sm:text-sm px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white focus:ring-1 focus:ring-emerald-400 focus:border-emerald-400">
                        <option value="attended">Hadir</option>
                        <option value="maybe">Belum Pasti</option>
                        <option value="not_attended">Tidak Dapat Hadir</option>
                    </select>
                </div>
                @endif

                @if($data['wishes_enabled'] ?? true)
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Pesan / Ucapan</label>
                    <textarea x-model="form.message" rows="3"
                              class="w-full text-xs sm:text-sm px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white focus:ring-1 focus:ring-emerald-400 focus:border-emerald-400"
                              placeholder="Tulis pesan atau ucapan..."></textarea>
                </div>
                @endif

                <button type="submit" :disabled="submitting"
                        class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-cinzel font-semibold py-3 rounded-xl transition-all text-xs tracking-wider uppercase disabled:opacity-50">
                    <span x-show="!submitting">Kirim RSVP</span>
                    <span x-show="submitting">Mengirimkan...</span>
                </button>
            </form>

            <div class="mt-8 space-y-3">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Buku Ucapan ({{ $invitation->wishes->count() }})</h4>
                <div class="max-h-64 overflow-y-auto space-y-2.5 pr-1">
                    <template x-for="wish in localWishes" :key="wish.id">
                        <div class="bg-slate-800/40 border border-slate-700/60 p-3.5 rounded-xl">
                            <span class="font-bold text-white text-xs block" x-text="wish.name"></span>
                            <p class="text-xs text-slate-400 mt-1 leading-relaxed" x-text="wish.message"></p>
                        </div>
                    </template>
                    @foreach($invitation->wishes as $w)
                        <div class="bg-slate-800/40 border border-slate-700/60 p-3.5 rounded-xl">
                            <span class="font-bold text-white text-xs block">{{ $w->name }}</span>
                            <p class="text-xs text-slate-400 mt-1 leading-relaxed">{{ $w->message }}</p>
                            <span class="text-[10px] text-slate-500 mt-1 block">{{ $w->created_at->diffForHumans() }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        <!-- 7. PENUTUP -->
        <section class="py-14 px-6 sm:px-10 text-center bg-slate-950">
            <p class="text-xs text-slate-400 max-w-md mx-auto leading-relaxed">
                {{ $data['closing']['text'] ?? 'Kehadiran dan perhatian Anda merupakan suatu kehormatan yang tak ternilai bagi kami.' }}
            </p>
            <h3 class="font-cinzel text-xl text-emerald-400 font-bold mt-4">{{ $data['closing']['signature'] ?? 'Penyelenggara Acara' }}</h3>

            <div class="mt-8 pt-6 border-t border-slate-800">
                <a href="https://api.whatsapp.com/send?text={{ urlencode('Halo! Berikut undangan: ' . url()->current()) }}"
                   target="_blank"
                   class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold px-4 py-2.5 rounded-xl transition-colors">
                    <span>Bagikan ke WhatsApp</span>
                </a>
            </div>

            <p class="text-[10px] text-slate-600 mt-8">Powered by <a href="{{ route('home') }}" class="font-semibold text-emerald-400 hover:underline">Kawinan.id</a></p>
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
