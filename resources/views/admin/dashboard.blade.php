<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-rose-600 uppercase tracking-wider">Super Administrator</span>
                <h2 class="font-extrabold text-2xl text-stone-900 leading-tight">
                    {{ __('Admin Dashboard') }}
                </h2>
            </div>
            <span class="px-3 py-1 bg-rose-100 text-rose-800 text-xs font-bold rounded-full">Admin Mode</span>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            <!-- STATISTIK UTAMA -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <div class="bg-white p-5 rounded-3xl border border-stone-200 shadow-sm">
                    <span class="text-[10px] font-bold text-stone-400 uppercase tracking-wider block">Total Pendapatan</span>
                    <span class="text-xl font-extrabold text-emerald-600 mt-1 block">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
                </div>
                <div class="bg-white p-5 rounded-3xl border border-stone-200 shadow-sm">
                    <span class="text-[10px] font-bold text-stone-400 uppercase tracking-wider block">Total Transaksi</span>
                    <span class="text-xl font-extrabold text-stone-900 mt-1 block">{{ $totalTransactions }}</span>
                </div>
                <div class="bg-white p-5 rounded-3xl border border-stone-200 shadow-sm">
                    <span class="text-[10px] font-bold text-stone-400 uppercase tracking-wider block">Transaksi Lunas</span>
                    <span class="text-xl font-extrabold text-emerald-600 mt-1 block">{{ $paidTransactions }}</span>
                </div>
                <div class="bg-white p-5 rounded-3xl border border-stone-200 shadow-sm">
                    <span class="text-[10px] font-bold text-stone-400 uppercase tracking-wider block">Total Undangan</span>
                    <span class="text-xl font-extrabold text-stone-900 mt-1 block">{{ $totalInvitations }}</span>
                </div>
                <div class="bg-white p-5 rounded-3xl border border-stone-200 shadow-sm">
                    <span class="text-[10px] font-bold text-stone-400 uppercase tracking-wider block">Total Pengguna</span>
                    <span class="text-xl font-extrabold text-stone-900 mt-1 block">{{ $totalUsers }}</span>
                </div>
                <div class="bg-white p-5 rounded-3xl border border-stone-200 shadow-sm">
                    <span class="text-[10px] font-bold text-stone-400 uppercase tracking-wider block">Template Aktif</span>
                    <span class="text-xl font-extrabold text-stone-900 mt-1 block">{{ $activeTemplates }}</span>
                </div>
            </div>

            <!-- SHORTCUT MENU -->
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                <a href="{{ route('admin.templates.index') }}" class="p-4 bg-white hover:bg-stone-50 border border-stone-200 rounded-2xl shadow-sm text-center transition-all">
                    <span class="text-xl block mb-1">🎨</span>
                    <span class="text-xs font-bold text-stone-800">Kelola Template</span>
                </a>
                <a href="{{ route('admin.plans.index') }}" class="p-4 bg-white hover:bg-stone-50 border border-stone-200 rounded-2xl shadow-sm text-center transition-all">
                    <span class="text-xl block mb-1">🏷️</span>
                    <span class="text-xs font-bold text-stone-800">Paket & Harga</span>
                </a>
                <a href="{{ route('admin.transactions.index') }}" class="p-4 bg-white hover:bg-stone-50 border border-stone-200 rounded-2xl shadow-sm text-center transition-all">
                    <span class="text-xl block mb-1">💳</span>
                    <span class="text-xs font-bold text-stone-800">Data Transaksi</span>
                </a>
                <a href="{{ route('admin.users.index') }}" class="p-4 bg-white hover:bg-stone-50 border border-stone-200 rounded-2xl shadow-sm text-center transition-all">
                    <span class="text-xl block mb-1">👥</span>
                    <span class="text-xs font-bold text-stone-800">Data Pengguna</span>
                </a>
                <a href="{{ route('admin.settings.index') }}" class="p-4 bg-white hover:bg-stone-50 border border-stone-200 rounded-2xl shadow-sm text-center transition-all">
                    <span class="text-xl block mb-1">⚙️</span>
                    <span class="text-xs font-bold text-stone-800">Pengaturan Web</span>
                </a>
            </div>

            <!-- TRANSAKSI TERBARU -->
            <div class="bg-white rounded-3xl border border-stone-200 shadow-sm p-6 sm:p-8">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-extrabold text-stone-900">Transaksi Terbaru Sistem</h3>
                    <a href="{{ route('admin.transactions.index') }}" class="text-xs text-rose-600 font-bold hover:underline">Lihat Semua →</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-stone-50 text-stone-500 uppercase tracking-wider border-b border-stone-200">
                            <tr>
                                <th class="py-3 px-4">Order ID</th>
                                <th class="py-3 px-4">Pengguna</th>
                                <th class="py-3 px-4">Paket</th>
                                <th class="py-3 px-4">Nominal</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            @forelse($recentTransactions as $tx)
                                <tr>
                                    <td class="py-3 px-4 font-mono font-bold text-stone-800">{{ $tx->order_id }}</td>
                                    <td class="py-3 px-4">{{ $tx->user->name ?? '-' }} ({{ $tx->user->email ?? '-' }})</td>
                                    <td class="py-3 px-4">{{ $tx->plan->name ?? '-' }}</td>
                                    <td class="py-3 px-4 font-semibold text-stone-900">{{ $tx->formattedAmount() }}</td>
                                    <td class="py-3 px-4">
                                        @if($tx->status === 'paid')
                                            <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px]">PAID</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 font-bold text-[10px]">{{ strtoupper($tx->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-stone-500">{{ $tx->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-4 text-center text-stone-400">Belum ada data transaksi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
