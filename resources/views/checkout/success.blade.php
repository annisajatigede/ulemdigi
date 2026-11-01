<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-stone-800 leading-tight">
            {{ __('Pembayaran Berhasil') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="bg-white rounded-3xl p-8 sm:p-10 shadow-sm border border-stone-200">
                <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-inner">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                </div>

                <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Transaksi Lunas</span>
                <h3 class="text-3xl font-extrabold text-stone-900 mt-1 mb-2">Pembayaran Berhasil!</h3>
                <p class="text-xs sm:text-sm text-stone-500 max-w-sm mx-auto leading-relaxed">
                    Terima kasih! Undangan digital Anda telah aktif secara otomatis dan siap untuk diedit.
                </p>

                <div class="bg-stone-50 rounded-2xl p-5 border border-stone-200 text-left my-8 space-y-2 text-xs">
                    <div class="flex justify-between">
                        <span class="text-stone-500">Nomor Pesanan:</span>
                        <span class="font-mono font-bold text-stone-800">{{ $transaction->order_id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-stone-500">Paket Layanan:</span>
                        <span class="font-bold text-stone-800">{{ $transaction->plan->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-stone-500">Masa Aktif Hingga:</span>
                        <span class="font-bold text-stone-800">{{ $invitation?->expires_at?->translatedFormat('d F Y') ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-stone-500">Status Undangan:</span>
                        <span class="inline-block px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 font-bold uppercase text-[10px]">
                            {{ $invitation?->status ?? 'Draft' }}
                        </span>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    @if($invitation)
                        <a href="{{ route('editor.edit', ['invitation' => $invitation->id]) }}"
                           class="flex-1 bg-rose-600 hover:bg-rose-700 text-white font-bold py-3.5 px-6 rounded-2xl shadow-lg shadow-rose-600/25 transition-all text-xs sm:text-sm">
                            Buka Editor Undangan →
                        </a>
                    @endif
                    <a href="{{ route('dashboard') }}"
                       class="bg-stone-100 hover:bg-stone-200 text-stone-800 font-semibold py-3.5 px-6 rounded-2xl transition-colors text-xs sm:text-sm">
                        Ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
