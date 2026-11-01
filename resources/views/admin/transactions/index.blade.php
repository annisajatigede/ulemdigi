<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h2 class="font-extrabold text-2xl text-stone-900 leading-tight">
                {{ __('Data Transaksi Pembayaran') }}
            </h2>

            <a href="{{ route('admin.transactions.export', request()->query()) }}"
               class="bg-white hover:bg-stone-50 border border-stone-200 text-stone-800 text-xs font-bold px-4 py-2.5 rounded-xl shadow-sm transition-all flex items-center gap-1.5 self-start">
                <span>📥 Unduh Laporan (CSV)</span>
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- STATISTIK KEUANGAN -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-sm">
                    <span class="text-[10px] font-bold text-stone-400 uppercase tracking-wider block">Total Terbayar</span>
                    <span class="text-2xl font-extrabold text-emerald-600 mt-1 block">Rp {{ number_format($stats['total_amount'], 0, ',', '.') }}</span>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-sm">
                    <span class="text-[10px] font-bold text-stone-400 uppercase tracking-wider block">Total Transaksi</span>
                    <span class="text-2xl font-extrabold text-stone-900 mt-1 block">{{ $stats['total_count'] }}</span>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-sm">
                    <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider block">Transaksi Lunas</span>
                    <span class="text-2xl font-extrabold text-emerald-600 mt-1 block">{{ $stats['paid_count'] }}</span>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-sm">
                    <span class="text-[10px] font-bold text-amber-600 uppercase tracking-wider block">Pending / Belum Bayar</span>
                    <span class="text-2xl font-extrabold text-amber-600 mt-1 block">{{ $stats['pending_count'] }}</span>
                </div>
            </div>

            <!-- FILTER TRANSAKSI -->
            <div class="bg-white rounded-3xl border border-stone-200 shadow-sm p-6 sm:p-8">
                <form action="{{ route('admin.transactions.index') }}" method="GET" class="flex flex-wrap items-end gap-3 mb-6">
                    <div>
                        <label class="block text-[11px] font-bold text-stone-600 mb-1">Cari Order ID / Pelanggan</label>
                        <input type="text" name="q" value="{{ $search }}" placeholder="INV-... / email..."
                               class="text-xs px-3 py-2 rounded-xl border border-stone-200 w-56">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-stone-600 mb-1">Status</label>
                        <select name="status" class="text-xs px-3 py-2 rounded-xl border border-stone-200">
                            <option value="">Semua Status</option>
                            <option value="paid" {{ $status === 'paid' ? 'selected' : '' }}>Paid (Lunas)</option>
                            <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="failed" {{ $status === 'failed' ? 'selected' : '' }}>Failed</option>
                            <option value="expired" {{ $status === 'expired' ? 'selected' : '' }}>Expired</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-stone-600 mb-1">Dari Tanggal</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="text-xs px-3 py-2 rounded-xl border border-stone-200">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-stone-600 mb-1">Sampai Tanggal</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="text-xs px-3 py-2 rounded-xl border border-stone-200">
                    </div>

                    <button type="submit" class="bg-stone-900 text-white text-xs font-bold px-4 py-2 rounded-xl">Terapkan Filter</button>
                    @if($search || $status || $startDate || $endDate)
                        <a href="{{ route('admin.transactions.index') }}" class="text-xs text-rose-600 hover:underline self-center ml-2">Reset</a>
                    @endif
                </form>

                <!-- TABEL TRANSAKSI -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-stone-50 text-stone-500 uppercase tracking-wider border-b border-stone-200">
                            <tr>
                                <th class="py-3 px-4">Order ID</th>
                                <th class="py-3 px-4">Pelanggan</th>
                                <th class="py-3 px-4">Paket</th>
                                <th class="py-3 px-4">Nominal</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4">Gateway</th>
                                <th class="py-3 px-4">Tanggal Order</th>
                                <th class="py-3 px-4">Waktu Bayar</th>
                                <th class="py-3 px-4">Undangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            @forelse($transactions as $t)
                                <tr>
                                    <td class="py-3 px-4 font-mono font-bold text-stone-800">{{ $t->order_id }}</td>
                                    <td class="py-3 px-4 font-semibold text-stone-900">
                                        {{ $t->user->name ?? '-' }}
                                        <span class="block text-[11px] text-stone-500 font-normal">{{ $t->user->email ?? '-' }}</span>
                                    </td>
                                    <td class="py-3 px-4">{{ $t->plan->name ?? '-' }}</td>
                                    <td class="py-3 px-4 font-bold text-stone-900">{{ $t->formattedAmount() }}</td>
                                    <td class="py-3 px-4">
                                        @if($t->status === 'paid')
                                            <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px]">LUNAS</span>
                                        @elseif($t->status === 'pending')
                                            <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 font-bold text-[10px]">PENDING</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 font-bold text-[10px]">{{ strtoupper($t->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 uppercase font-semibold text-stone-600">{{ $t->gateway }}</td>
                                    <td class="py-3 px-4 text-stone-500">{{ $t->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="py-3 px-4 text-stone-500">{{ $t->paid_at ? $t->paid_at->format('d/m/Y H:i') : '-' }}</td>
                                    <td class="py-3 px-4">
                                        @if($t->invitation)
                                            <a href="{{ route('invitation.show', ['slug' => $t->invitation->slug]) }}" target="_blank" class="text-rose-600 hover:underline font-mono">
                                                /u/{{ $t->invitation->slug }}
                                            </a>
                                        @else
                                            <span class="text-stone-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="py-6 text-center text-stone-400">Tidak ada riwayat transaksi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $transactions->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
