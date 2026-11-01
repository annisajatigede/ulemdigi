<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-stone-800 leading-tight">
            {{ __('Konfirmasi Pembelian Paket Undangan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <form action="{{ route('checkout.pay') }}" method="POST" class="bg-white rounded-3xl shadow-sm border border-stone-200 overflow-hidden">
                @csrf
                @if($existingInvitation)
                    <input type="hidden" name="invitation_id" value="{{ $existingInvitation->id }}">
                @endif

                <div class="p-6 sm:p-8 border-b border-stone-100">
                    <span class="text-xs font-bold text-rose-600 uppercase tracking-wider">Langkah 1 dari 2</span>
                    <h3 class="text-2xl font-extrabold text-stone-900 mt-1">Review Pilihan Undangan & Paket</h3>
                    @if($existingInvitation)
                        <div class="mt-2 p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800">
                            <strong>Perpanjangan / Upgrade:</strong> Anda sedang memperpanjang paket untuk undangan <code>{{ $existingInvitation->slug }}</code>.
                        </div>
                    @endif
                </div>

                <div class="p-6 sm:p-8 space-y-8">
                    <!-- 1. Pilihan Template -->
                    <div>
                        <label class="block text-sm font-bold text-stone-900 mb-3">1. Pilih Desain Template</label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            @foreach($allTemplates as $tmpl)
                                <label class="cursor-pointer">
                                    <input type="radio" name="template_id" value="{{ $tmpl->id }}" {{ $template->id === $tmpl->id ? 'checked' : '' }} class="peer sr-only">
                                    <div class="p-4 rounded-2xl border-2 peer-checked:border-rose-600 peer-checked:bg-rose-50/30 transition-all border-stone-200 hover:border-stone-300">
                                        <div class="aspect-video bg-stone-100 rounded-xl overflow-hidden mb-3">
                                            <img src="{{ $tmpl->thumbnail_path ?? '/templates/thumbnails/' . $tmpl->blade_view . '.svg' }}" alt="{{ $tmpl->name }}" class="w-full h-full object-cover">
                                        </div>
                                        <div class="font-bold text-stone-900 text-sm">{{ $tmpl->name }}</div>
                                        <span class="text-[11px] text-stone-500 capitalize">{{ $tmpl->category }} • {{ $tmpl->style }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- 2. Pilihan Paket -->
                    <div>
                        <label class="block text-sm font-bold text-stone-900 mb-3">2. Pilih Paket Layanan</label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            @foreach($allPlans as $p)
                                <label class="cursor-pointer">
                                    <input type="radio" name="plan_id" value="{{ $p->id }}" {{ $plan->id === $p->id ? 'checked' : '' }} class="peer sr-only">
                                    <div class="p-5 rounded-2xl border-2 peer-checked:border-rose-600 peer-checked:bg-rose-50/30 transition-all border-stone-200 hover:border-stone-300 h-full flex flex-col justify-between">
                                        <div>
                                            <div class="font-extrabold text-stone-900 text-base">{{ $p->name }}</div>
                                            <div class="text-2xl font-extrabold text-rose-600 my-2">{{ $p->formattedPrice() }}</div>
                                            <span class="inline-block px-2.5 py-0.5 rounded-full bg-stone-100 text-stone-600 text-xs font-semibold mb-3">Aktif {{ $p->duration_days }} hari</span>
                                            <ul class="text-xs text-stone-600 space-y-1.5">
                                                <li>✓ {{ $p->max_events }} Rangkaian Acara</li>
                                                <li>✓ Maks. {{ $p->max_photos }} Foto Galeri</li>
                                                <li>✓ RSVP & Amplop Digital</li>
                                            </ul>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Ringkasan Biaya -->
                    <div class="bg-stone-50 rounded-2xl p-6 border border-stone-200">
                        <h4 class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-4">Rincian Pembayaran</h4>
                        <div class="flex items-center justify-between text-sm py-1">
                            <span class="text-stone-600">Model Pembayaran</span>
                            <span class="font-semibold text-stone-900">Sekali Bayar (Tanpa Langganan)</span>
                        </div>
                        <div class="flex items-center justify-between text-sm py-1">
                            <span class="text-stone-600">Metode Pembayaran</span>
                            <span class="font-semibold text-stone-900">Midtrans Snap (QRIS, VA Bank, E-Wallet)</span>
                        </div>
                        <div class="border-t border-stone-200 my-3"></div>
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-stone-900">Total Pembayaran</span>
                            <span class="font-extrabold text-2xl text-rose-600">{{ $plan->formattedPrice() }}</span>
                        </div>
                    </div>
                </div>

                <div class="p-6 sm:p-8 bg-stone-50 border-t border-stone-100 flex items-center justify-between">
                    <a href="{{ route('home') }}" class="text-xs font-bold text-stone-500 hover:text-stone-700">← Batal</a>
                    <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white font-bold text-sm px-8 py-3.5 rounded-2xl shadow-lg shadow-rose-600/30 transition-all transform hover:-translate-y-0.5">
                        Lanjut ke Pembayaran Snap →
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
