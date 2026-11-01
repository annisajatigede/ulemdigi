<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-2xl text-stone-900 leading-tight">
                    {{ __('Dashboard Undangan Saya') }}
                </h2>
                <p class="text-xs text-stone-500 mt-0.5">Kelola dan pantau seluruh undangan digital Anda di sini.</p>
            </div>
            <a href="{{ route('home') }}#template"
               class="inline-flex items-center gap-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs sm:text-sm px-5 py-2.5 rounded-2xl shadow-md shadow-rose-600/20 transition-all">
                <span>+ Buat Undangan Baru</span>
            </a>
        </div>
    </x-slot>

    <div class="py-10" x-data="{ copyFeedback: null }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            <!-- Flash Alert -->
            @if (session('status'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold rounded-2xl">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm font-semibold rounded-2xl">
                    {{ session('error') }}
                </div>
            @endif

            <!-- STATISTIK RINGKASAN -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white p-5 rounded-3xl border border-stone-200 shadow-sm">
                    <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Total Undangan</span>
                    <div class="text-3xl font-extrabold text-stone-900 mt-2">{{ $stats['total'] }}</div>
                </div>
                <div class="bg-white p-5 rounded-3xl border border-stone-200 shadow-sm">
                    <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Dipublikasikan</span>
                    <div class="text-3xl font-extrabold text-emerald-600 mt-2">{{ $stats['published'] }}</div>
                </div>
                <div class="bg-white p-5 rounded-3xl border border-stone-200 shadow-sm">
                    <span class="text-xs font-bold text-amber-600 uppercase tracking-wider">Draft / Belum Rilis</span>
                    <div class="text-3xl font-extrabold text-amber-600 mt-2">{{ $stats['draft'] }}</div>
                </div>
                <div class="bg-white p-5 rounded-3xl border border-stone-200 shadow-sm">
                    <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Total Respon RSVP</span>
                    <div class="text-3xl font-extrabold text-rose-600 mt-2">{{ $stats['total_guests'] }}</div>
                </div>
            </div>

            <!-- DAFTAR UNDANGAN USER -->
            <div class="bg-white rounded-3xl border border-stone-200 shadow-sm p-6 sm:p-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-extrabold text-stone-900">Daftar Undangan Digital</h3>
                        <p class="text-xs text-stone-500">Pilih undangan untuk diedit atau disebarkan ke tamu.</p>
                    </div>
                </div>

                @if($invitations->isEmpty())
                    <div class="text-center py-12 bg-stone-50 rounded-2xl border-2 border-dashed border-stone-200">
                        <div class="w-14 h-14 bg-rose-100 text-rose-600 rounded-2xl flex items-center justify-center mx-auto mb-3 text-2xl">
                            💌
                        </div>
                        <h4 class="text-base font-bold text-stone-800">Belum Ada Undangan</h4>
                        <p class="text-xs text-stone-500 max-w-sm mx-auto mt-1 mb-5">
                            Mulai pilih desain template favorit dan paket yang Anda inginkan untuk membuat undangan pertama.
                        </p>
                        <a href="{{ route('home') }}#template" class="inline-block bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs px-6 py-3 rounded-xl shadow-md">
                            Pilih Template Sekarang
                        </a>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($invitations as $inv)
                            @php
                                $data = $inv->getSafeData();
                                $isExpired = $inv->isExpired();
                                $daysLeft = $inv->daysRemaining();
                                $publicUrl = route('invitation.show', ['slug' => $inv->slug]);
                            @endphp
                            <div class="bg-stone-50 rounded-3xl border border-stone-200 overflow-hidden flex flex-col justify-between hover:shadow-lg transition-all duration-300">
                                <div>
                                    <!-- Header image / template thumbnail -->
                                    <div class="relative aspect-[16/9] bg-stone-200 overflow-hidden">
                                        <img src="{{ $inv->template->thumbnail_path ?? '/templates/thumbnails/' . $inv->template->blade_view . '.svg' }}"
                                             alt="{{ $inv->template->name }}"
                                             class="w-full h-full object-cover">

                                        <!-- Status Badge -->
                                        <div class="absolute top-3 left-3 flex gap-2">
                                            @if($isExpired)
                                                <span class="bg-rose-600 text-white text-[10px] font-extrabold px-2.5 py-1 rounded-full shadow">
                                                    Kedaluwarsa
                                                </span>
                                            @elseif($inv->status === 'published')
                                                <span class="bg-emerald-600 text-white text-[10px] font-extrabold px-2.5 py-1 rounded-full shadow">
                                                    ● Publik
                                                </span>
                                            @else
                                                <span class="bg-amber-500 text-white text-[10px] font-extrabold px-2.5 py-1 rounded-full shadow">
                                                    Draft
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Sisa Masa Aktif Badge -->
                                        <div class="absolute top-3 right-3">
                                            <span class="bg-stone-900/80 backdrop-blur text-white text-[10px] font-semibold px-2.5 py-1 rounded-full">
                                                @if($isExpired)
                                                    Habis
                                                @else
                                                    Sisa {{ $daysLeft }} Hari
                                                @endif
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Content -->
                                    <div class="p-5">
                                        <span class="text-[10px] uppercase font-bold text-stone-500 tracking-wider">
                                            {{ $inv->template->name }} • {{ $inv->plan->name }}
                                        </span>
                                        <h4 class="font-extrabold text-stone-900 text-lg mt-0.5 line-clamp-1">
                                            {{ $data['party1']['name'] ?? 'Pihak 1' }} @if(!empty($data['party2']['name'])) & {{ $data['party2']['name'] }} @endif
                                        </h4>
                                        <p class="text-xs text-stone-500 font-mono mt-1 truncate">
                                            /u/{{ $inv->slug }}
                                        </p>

                                        <div class="grid grid-cols-2 gap-2 mt-4 pt-3 border-t border-stone-200/80 text-[11px] text-stone-600">
                                            <div>
                                                <span class="text-stone-400 block">Tamu / RSVP:</span>
                                                <span class="font-bold text-stone-800">{{ $inv->guests_count }} Tamu</span>
                                            </div>
                                            <div>
                                                <span class="text-stone-400 block">Ucapan:</span>
                                                <span class="font-bold text-stone-800">{{ $inv->wishes_count }} Doa</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="p-5 pt-0 space-y-2">
                                    <div class="grid grid-cols-2 gap-2">
                                        <a href="{{ route('editor.edit', $inv) }}"
                                           class="bg-stone-900 hover:bg-stone-800 text-white text-xs font-bold py-2.5 rounded-xl text-center transition-colors">
                                            ✏️ Edit Undangan
                                        </a>
                                        <a href="{{ route('guests.index', $inv) }}"
                                           class="bg-white hover:bg-stone-100 text-stone-800 border border-stone-300 text-xs font-bold py-2.5 rounded-xl text-center transition-colors">
                                            👥 Tamu & RSVP
                                        </a>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <!-- Toggle Publish Button -->
                                        @if(!$isExpired)
                                            <form action="{{ route('editor.toggle-publish', $inv) }}" method="POST" class="flex-1">
                                                @csrf
                                                <button type="submit"
                                                        class="w-full text-xs font-bold py-2 rounded-xl transition-colors {{ $inv->status === 'published' ? 'bg-amber-100 hover:bg-amber-200 text-amber-900' : 'bg-emerald-600 hover:bg-emerald-700 text-white' }}">
                                                    {{ $inv->status === 'published' ? 'Tarik ke Draft' : '🚀 Publikasikan' }}
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ route('checkout.select', ['template_id' => $inv->template_id, 'invitation_id' => $inv->id]) }}"
                                               class="flex-1 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold py-2 rounded-xl text-center transition-colors">
                                                🔄 Perpanjang Paket
                                            </a>
                                        @endif

                                        <!-- Copy URL Button -->
                                        <button @click="navigator.clipboard.writeText('{{ $publicUrl }}'); copyFeedback = {{ $inv->id }}; setTimeout(() => copyFeedback = null, 2500)"
                                                class="px-3 py-2 bg-white hover:bg-stone-100 border border-stone-300 rounded-xl text-xs font-semibold text-stone-700 transition-colors"
                                                title="Salin Tautan Publik">
                                            <span x-show="copyFeedback !== {{ $inv->id }}">🔗 Salin</span>
                                            <span x-show="copyFeedback === {{ $inv->id }}" class="text-emerald-600 font-bold">✓ Tersalin</span>
                                        </button>

                                        <!-- Preview Link -->
                                        <a href="{{ route('editor.preview', $inv) }}" target="_blank"
                                           class="px-3 py-2 bg-white hover:bg-stone-100 border border-stone-300 rounded-xl text-xs font-semibold text-stone-700 transition-colors"
                                           title="Lihat Pratinjau">
                                            👁️
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- RIWAYAT TRANSAKSI TERAKHIR -->
            <div class="bg-white rounded-3xl border border-stone-200 shadow-sm p-6 sm:p-8">
                <h3 class="text-base font-extrabold text-stone-900 mb-4">Riwayat Pembayaran Terbaru</h3>
                @if($transactions->isEmpty())
                    <p class="text-xs text-stone-400">Belum ada riwayat transaksi.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-stone-50 text-stone-500 uppercase tracking-wider border-b border-stone-200">
                                <tr>
                                    <th class="py-3 px-4">Order ID</th>
                                    <th class="py-3 px-4">Paket</th>
                                    <th class="py-3 px-4">Nominal</th>
                                    <th class="py-3 px-4">Status</th>
                                    <th class="py-3 px-4">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-100">
                                @foreach($transactions as $trx)
                                    <tr>
                                        <td class="py-3 px-4 font-mono font-bold text-stone-800">{{ $trx->order_id }}</td>
                                        <td class="py-3 px-4">{{ $trx->plan->name }}</td>
                                        <td class="py-3 px-4 font-semibold text-stone-900">{{ $trx->formattedAmount() }}</td>
                                        <td class="py-3 px-4">
                                            @if($trx->status === 'paid')
                                                <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold uppercase text-[10px]">Lunas</span>
                                            @elseif($trx->status === 'pending')
                                                <a href="{{ route('checkout.pay.page', $trx) }}" class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 font-bold uppercase text-[10px] hover:underline">Bayar Sekarang →</a>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full bg-stone-100 text-stone-600 font-bold uppercase text-[10px]">{{ $trx->status }}</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-stone-500">{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
