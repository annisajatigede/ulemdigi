<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Undangan Belum Dipublikasikan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-stone-50 min-h-screen flex items-center justify-center p-6 text-center antialiased">
    <div class="max-w-md w-full bg-white p-8 rounded-3xl shadow-xl border border-stone-200">
        <div class="w-16 h-16 bg-amber-100 text-amber-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <h2 class="text-xl font-bold text-stone-800 mb-2">Undangan Belum Dipublikasikan</h2>
        <p class="text-xs text-stone-500 mb-6 leading-relaxed">
            Undangan ini masih dalam tahap penyusunan oleh penyelenggara acara. Silakan kembali lagi nanti atau hubungi pihak yang mengundang Anda.
        </p>
        <a href="{{ route('home') }}" class="inline-block bg-stone-900 hover:bg-stone-800 text-white text-xs font-semibold px-5 py-2.5 rounded-xl shadow-md transition-colors">
            Kembali ke Beranda
        </a>
    </div>
</body>
</html>
