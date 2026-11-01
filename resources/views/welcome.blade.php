<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $settings['site_name'] }} — {{ $settings['site_tagline'] }}</title>
    <meta name="description" content="{{ $settings['site_description'] }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,400&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-stone-50 font-sans text-stone-800 antialiased selection:bg-rose-500 selection:text-white">

    <!-- NAVBAR -->
    <nav class="sticky top-0 z-40 bg-white/80 backdrop-blur-md border-b border-stone-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-xl bg-gradient-to-tr from-rose-500 to-amber-500 flex items-center justify-center text-white font-bold text-lg shadow-md shadow-rose-500/20">K</span>
                <span class="font-extrabold text-xl tracking-tight text-stone-900">{{ $settings['site_name'] }}</span>
            </a>

            <div class="hidden md:flex items-center gap-8 text-sm font-semibold text-stone-600">
                <a href="#kategori" class="hover:text-rose-600 transition-colors">Kategori</a>
                <a href="#template" class="hover:text-rose-600 transition-colors">Koleksi Desain</a>
                <a href="#harga" class="hover:text-rose-600 transition-colors">Pilihan Paket</a>
                <a href="#fitur" class="hover:text-rose-600 transition-colors">Fitur</a>
            </div>

            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="text-xs sm:text-sm font-semibold bg-stone-900 hover:bg-stone-800 text-white px-4 py-2 rounded-xl transition-all shadow-sm">
                        Dashboard Saya
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-xs sm:text-sm font-semibold text-stone-700 hover:text-stone-900 px-3 py-2">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="text-xs sm:text-sm font-semibold bg-rose-600 hover:bg-rose-700 text-white px-4 py-2 rounded-xl shadow-md shadow-rose-600/25 transition-all">
                        Daftar Gratis
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <header class="relative pt-16 pb-24 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto text-center overflow-hidden">
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-rose-100 text-rose-800 text-xs font-semibold mb-6">
            <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
            <span>Platform Undangan Digital Berbayar #1 di Indonesia</span>
        </div>

        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-stone-900 tracking-tight max-w-4xl mx-auto leading-[1.15]">
            Bagikan Momen Bahagiamu dengan Undangan Online <span class="bg-gradient-to-r from-rose-600 via-amber-600 to-rose-600 bg-clip-text text-transparent">Elegan & Modern</span>
        </h1>

        <p class="mt-6 text-base sm:text-lg text-stone-600 max-w-2xl mx-auto leading-relaxed">
            Cocok untuk Pernikahan, Khitanan, dan Acara Spesial. Sekali bayar, aktif instan dengan Midtrans Snap, fitur RSVP real-time, amplop digital, dan personalisasi nama tamu.
        </p>

        <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
            <a href="#template" class="bg-rose-600 hover:bg-rose-700 text-white font-bold text-sm px-6 py-3.5 rounded-2xl shadow-xl shadow-rose-600/30 transition-all transform hover:-translate-y-0.5">
                Pilih Desain Undangan
            </a>
            <a href="#harga" class="bg-white hover:bg-stone-100 text-stone-800 border border-stone-200 font-bold text-sm px-6 py-3.5 rounded-2xl shadow-sm transition-all">
                Lihat Paket Harga
            </a>
        </div>

        <div class="mt-12 flex flex-wrap items-center justify-center gap-6 text-xs font-medium text-stone-500">
            <div class="flex items-center gap-1.5">
                <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span>Aktif Otomatis Tanpa Konfirmasi</span>
            </div>
            <div class="flex items-center gap-1.5">
                <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span>Responsif & Ringan di Mobile</span>
            </div>
            <div class="flex items-center gap-1.5">
                <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span>Midtrans Snap Multi-Payment</span>
            </div>
        </div>
    </header>

    <!-- SECTION KATEGORI & FILTER TEMPLATE -->
    <section id="kategori" class="py-16 bg-white border-y border-stone-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-10">
                <span class="text-xs font-bold uppercase tracking-wider text-rose-600">Pilih Kategori Acara</span>
                <h2 class="text-3xl font-extrabold text-stone-900 mt-1">Desain Sesuai Kebutuhan Momen Anda</h2>
            </div>

            <!-- Category Pills Filter -->
            <div class="flex flex-wrap items-center justify-center gap-2 mb-12">
                <a href="{{ route('home', ['category' => 'all']) }}#template"
                   class="px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-bold transition-all {{ $selectedCategory === 'all' ? 'bg-stone-900 text-white shadow-md' : 'bg-stone-100 hover:bg-stone-200 text-stone-700' }}">
                    Semua Kategori ({{ \App\Models\Template::where('is_active', true)->count() }})
                </a>
                <a href="{{ route('home', ['category' => 'wedding']) }}#template"
                   class="px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-bold transition-all {{ $selectedCategory === 'wedding' ? 'bg-rose-600 text-white shadow-md' : 'bg-stone-100 hover:bg-stone-200 text-stone-700' }}">
                    💍 Pernikahan
                </a>
                <a href="{{ route('home', ['category' => 'khitan']) }}#template"
                   class="px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-bold transition-all {{ $selectedCategory === 'khitan' ? 'bg-sky-600 text-white shadow-md' : 'bg-stone-100 hover:bg-stone-200 text-stone-700' }}">
                    🎈 Khitanan Anak
                </a>
                <a href="{{ route('home', ['category' => 'general']) }}#template"
                   class="px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-bold transition-all {{ $selectedCategory === 'general' ? 'bg-emerald-600 text-white shadow-md' : 'bg-stone-100 hover:bg-stone-200 text-stone-700' }}">
                    🎉 Acara Umum / Syukuran
                </a>
            </div>

            <!-- TEMPLATE CARDS GRID -->
            <div id="template" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($templates as $tmpl)
                    <div class="bg-white rounded-3xl border border-stone-200 overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col group">
                        <!-- Thumbnail Preview -->
                        <div class="relative aspect-[16/10] bg-stone-100 overflow-hidden">
                            <img src="{{ $tmpl->thumbnail_path ?? '/templates/thumbnails/' . $tmpl->blade_view . '.svg' }}"
                                 alt="{{ $tmpl->name }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">

                            <!-- Category Badge -->
                            <div class="absolute top-4 left-4">
                                @if($tmpl->category === 'wedding')
                                    <span class="bg-rose-600 text-white text-[11px] font-bold px-3 py-1 rounded-full shadow">Pernikahan</span>
                                @elseif($tmpl->category === 'khitan')
                                    <span class="bg-sky-600 text-white text-[11px] font-bold px-3 py-1 rounded-full shadow">Khitanan</span>
                                @else
                                    <span class="bg-emerald-600 text-white text-[11px] font-bold px-3 py-1 rounded-full shadow">Umum</span>
                                @endif
                            </div>
                        </div>

                        <!-- Card Info -->
                        <div class="p-6 flex-1 flex flex-col justify-between">
                            <div>
                                <h3 class="text-xl font-bold text-stone-900">{{ $tmpl->name }}</h3>
                                <p class="text-xs text-stone-500 mt-1 capitalize">Gaya: {{ $tmpl->style }} • Mobile-First Responsive</p>
                            </div>

                            <div class="mt-6 pt-4 border-t border-stone-100 flex items-center gap-3">
                                <a href="{{ route('checkout.select', ['template_id' => $tmpl->id]) }}"
                                   class="flex-1 text-center bg-rose-600 hover:bg-rose-700 text-white text-xs sm:text-sm font-bold py-2.5 rounded-xl shadow-md shadow-rose-600/20 transition-all">
                                    Gunakan Desain Ini
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- SECTION PAKET HARGA -->
    <section id="harga" class="py-20 bg-stone-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-xs font-bold uppercase tracking-wider text-rose-600">Pilihan Paket Sekali Bayar</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-stone-900 mt-1">Transparan, Tanpa Biaya Tersembunyi</h2>
                <p class="text-xs sm:text-sm text-stone-500 mt-2">Pilih paket terbaik yang sesuai dengan kebutuhan acara istimewa Anda.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-stretch">
                @foreach($plans as $plan)
                    @php
                        $isPopular = ($plan->code === 'standard');
                    @endphp
                    <div class="bg-white rounded-3xl p-8 border-2 {{ $isPopular ? 'border-rose-500 shadow-2xl relative scale-105 z-10' : 'border-stone-200 shadow-sm' }} flex flex-col justify-between">
                        @if($isPopular)
                            <div class="absolute -top-4 left-1/2 transform -translate-x-1/2 bg-gradient-to-r from-rose-500 to-amber-500 text-white text-[11px] font-extrabold uppercase px-4 py-1 rounded-full shadow-md">
                                Paling Populer ★
                            </div>
                        @endif

                        <div>
                            <h3 class="text-xl font-extrabold text-stone-900">{{ $plan->name }}</h3>
                            <p class="text-xs text-stone-500 mt-1">Masa aktif {{ $plan->duration_days }} hari penuh</p>

                            <div class="mt-6 mb-8">
                                <span class="text-4xl font-extrabold text-stone-900">{{ $plan->formattedPrice() }}</span>
                                <span class="text-xs text-stone-500 font-medium">/ acara</span>
                            </div>

                            <ul class="space-y-3.5 text-xs sm:text-sm text-stone-600">
                                @foreach($plan->features ?? [] as $feat)
                                    <li class="flex items-start gap-2.5">
                                        <svg class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>{{ $feat }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="mt-8 pt-6 border-t border-stone-100">
                            <a href="{{ route('checkout.select', ['plan_code' => $plan->code]) }}"
                               class="w-full block text-center py-3 rounded-2xl font-bold text-xs sm:text-sm transition-all {{ $isPopular ? 'bg-rose-600 hover:bg-rose-700 text-white shadow-lg shadow-rose-600/30' : 'bg-stone-900 hover:bg-stone-800 text-white' }}">
                                Pilih {{ $plan->name }}
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- SECTION FITUR UTAMA -->
    <section id="fitur" class="py-20 bg-white border-t border-stone-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-xs font-bold uppercase tracking-wider text-rose-600">Keunggulan Aplikasi</span>
                <h2 class="text-3xl font-extrabold text-stone-900 mt-1">Mengapa Memilih {{ $settings['site_name'] }}?</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="p-6 rounded-3xl bg-stone-50 border border-stone-200/80">
                    <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center font-bold text-xl mb-4">💳</div>
                    <h4 class="text-base font-bold text-stone-900 mb-1">Aktivasi Instan Tanpa Menunggu</h4>
                    <p class="text-xs text-stone-500 leading-relaxed">Terhubung langsung dengan Midtrans Snap (QRIS, VA Bank, GoPay). Undangan aktif otomatis begitu pembayaran terkonfirmasi.</p>
                </div>

                <div class="p-6 rounded-3xl bg-stone-50 border border-stone-200/80">
                    <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center font-bold text-xl mb-4">📱</div>
                    <h4 class="text-base font-bold text-stone-900 mb-1">Mobile First & Sangat Cepat</h4>
                    <p class="text-xs text-stone-500 leading-relaxed">Dibuat dengan Tailwind CSS & Alpine.js murni tanpa beban library berat. Waktu muat kilat di HP tamu undangan.</p>
                </div>

                <div class="p-6 rounded-3xl bg-stone-50 border border-stone-200/80">
                    <div class="w-12 h-12 rounded-2xl bg-sky-100 text-sky-600 flex items-center justify-center font-bold text-xl mb-4">✍️</div>
                    <h4 class="text-base font-bold text-stone-900 mb-1">Personalisasi Nama Tamu</h4>
                    <p class="text-xs text-stone-500 leading-relaxed">Dukung parameter <code>?to=Nama</code> dan tautan unik per tamu dengan token personal, lengkap dengan generator pesan WhatsApp.</p>
                </div>

                <div class="p-6 rounded-3xl bg-stone-50 border border-stone-200/80">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-xl mb-4">🎁</div>
                    <h4 class="text-base font-bold text-stone-900 mb-1">Amplop Digital Multi-Rekening</h4>
                    <p class="text-xs text-stone-500 leading-relaxed">Mendukung banyak rekening bank dan e-wallet sekaligus dengan fitur 1-klik salin nomor rekening yang praktis.</p>
                </div>

                <div class="p-6 rounded-3xl bg-stone-50 border border-stone-200/80">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-xl mb-4">📊</div>
                    <h4 class="text-base font-bold text-stone-900 mb-1">Dashboard Analitik RSVP</h4>
                    <p class="text-xs text-stone-500 leading-relaxed">Pantau jumlah tamu yang pasti hadir, tidak hadir, atau ragu-ragu secara real-time lengkap dengan fitur ekspor ke Excel/CSV.</p>
                </div>

                <div class="p-6 rounded-3xl bg-stone-50 border border-stone-200/80">
                    <div class="w-12 h-12 rounded-2xl bg-purple-100 text-purple-600 flex items-center justify-center font-bold text-xl mb-4">🎵</div>
                    <h4 class="text-base font-bold text-stone-900 mb-1">Musik Latar Otomatis</h4>
                    <p class="text-xs text-stone-500 leading-relaxed">Tombol pembuka memicu audio autoplay yang ramah browser modern lengkap dengan pengontrol mengambang play/pause.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-stone-900 text-stone-400 py-12 border-t border-stone-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center sm:text-left flex flex-col sm:flex-row items-center justify-between gap-6">
            <div>
                <span class="text-white font-extrabold text-lg">{{ $settings['site_name'] }}</span>
                <p class="text-xs text-stone-400 mt-1">{{ $settings['site_tagline'] }}</p>
            </div>
            <div class="flex items-center gap-6 text-xs">
                <a href="{{ route('home') }}#harga" class="hover:text-white transition-colors">Paket Harga</a>
                <a href="{{ route('home') }}#template" class="hover:text-white transition-colors">Desain Undangan</a>
                <a href="https://api.whatsapp.com/send?phone={{ $settings['contact_whatsapp'] }}" target="_blank" class="hover:text-white transition-colors">WhatsApp Support</a>
            </div>
            <p class="text-[11px] text-stone-400">© {{ date('Y') }} {{ $settings['site_name'] }}. Hak cipta dilindungi.</p>
        </div>
    </footer>

</body>
</html>
