<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('dashboard') }}" class="text-xs text-stone-500 hover:text-stone-800 font-semibold">← Dashboard</a>
                    <span class="text-stone-300">/</span>
                    <span class="text-xs font-bold text-rose-600 uppercase">{{ $invitation->slug }}</span>
                </div>
                <h2 class="font-extrabold text-2xl text-stone-900 leading-tight mt-1">
                    Kelola Tamu Undangan & RSVP
                </h2>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('guests.export', $invitation) }}"
                   class="bg-white hover:bg-stone-50 border border-stone-200 text-stone-800 text-xs font-bold px-4 py-2.5 rounded-xl shadow-sm transition-all flex items-center gap-1.5">
                    <span>📥 Ekspor Data (CSV)</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10" x-data="{ copyFeedback: null, addGuestModal: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            @if (session('status'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold rounded-2xl">
                    {{ session('status') }}
                </div>
            @endif

            <!-- 1. ANALITIK RSVP -->
            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3">
                <div class="bg-white p-4 rounded-2xl border border-stone-200 shadow-sm text-center">
                    <span class="text-[10px] font-bold text-stone-400 uppercase tracking-wider block">Total Undangan</span>
                    <span class="text-2xl font-extrabold text-stone-900 mt-1 block">{{ $analytics['total_invitations'] }}</span>
                </div>
                <div class="bg-white p-4 rounded-2xl border border-stone-200 shadow-sm text-center">
                    <span class="text-[10px] font-bold text-stone-400 uppercase tracking-wider block">Estimasi Orang</span>
                    <span class="text-2xl font-extrabold text-indigo-600 mt-1 block">{{ $analytics['total_guests_estimated'] }}</span>
                </div>
                <div class="bg-white p-4 rounded-2xl border border-stone-200 shadow-sm text-center">
                    <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider block">Pasti Hadir</span>
                    <span class="text-2xl font-extrabold text-emerald-600 mt-1 block">{{ $analytics['attended'] }}</span>
                </div>
                <div class="bg-white p-4 rounded-2xl border border-stone-200 shadow-sm text-center">
                    <span class="text-[10px] font-bold text-rose-600 uppercase tracking-wider block">Tidak Hadir</span>
                    <span class="text-2xl font-extrabold text-rose-600 mt-1 block">{{ $analytics['not_attended'] }}</span>
                </div>
                <div class="bg-white p-4 rounded-2xl border border-stone-200 shadow-sm text-center">
                    <span class="text-[10px] font-bold text-amber-600 uppercase tracking-wider block">Masih Ragu</span>
                    <span class="text-2xl font-extrabold text-amber-600 mt-1 block">{{ $analytics['maybe'] }}</span>
                </div>
                <div class="bg-white p-4 rounded-2xl border border-stone-200 shadow-sm text-center">
                    <span class="text-[10px] font-bold text-stone-400 uppercase tracking-wider block">Belum Respon</span>
                    <span class="text-2xl font-extrabold text-stone-500 mt-1 block">{{ $analytics['pending'] }}</span>
                </div>
                <div class="bg-white p-4 rounded-2xl border border-stone-200 shadow-sm text-center">
                    <span class="text-[10px] font-bold text-sky-600 uppercase tracking-wider block">Check-in Hadir</span>
                    <span class="text-2xl font-extrabold text-sky-600 mt-1 block">{{ $analytics['checked_in'] }}</span>
                </div>
            </div>

            <!-- Visual Attendance Progress Bar -->
            @php
                $totalCount = max($analytics['total_invitations'], 1);
                $pctAttended = round(($analytics['attended'] / $totalCount) * 100);
                $pctNot = round(($analytics['not_attended'] / $totalCount) * 100);
                $pctMaybe = round(($analytics['maybe'] / $totalCount) * 100);
            @endphp
            <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-sm">
                <div class="flex items-center justify-between text-xs font-semibold text-stone-600 mb-2">
                    <span>Persentase Kehadiran Tamu</span>
                    <span>{{ $pctAttended }}% Konfirmasi Hadir</span>
                </div>
                <div class="h-3 w-full bg-stone-100 rounded-full overflow-hidden flex">
                    <div style="width: {{ $pctAttended }}%" class="bg-emerald-500 h-full" title="Hadir: {{ $pctAttended }}%"></div>
                    <div style="width: {{ $pctMaybe }}%" class="bg-amber-400 h-full" title="Ragu: {{ $pctMaybe }}%"></div>
                    <div style="width: {{ $pctNot }}%" class="bg-rose-500 h-full" title="Tidak Hadir: {{ $pctNot }}%"></div>
                </div>
                <div class="flex gap-4 text-[11px] text-stone-500 mt-2.5">
                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Hadir</span>
                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span> Ragu-ragu</span>
                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span> Tidak Hadir</span>
                </div>
            </div>

            <!-- 2. TAMBAH TAMU BARU FORM & PENCARIAN -->
            <div class="bg-white rounded-3xl border border-stone-200 shadow-sm p-6 sm:p-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                    <div>
                        <h3 class="text-base font-extrabold text-stone-900">Daftar Tamu & Tautan Personal</h3>
                        <p class="text-xs text-stone-500">Kirim link undangan spesifik yang mencantumkan nama masing-masing tamu.</p>
                    </div>

                    <button type="button" @click="addGuestModal = true"
                            class="bg-stone-900 hover:bg-stone-800 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-sm transition-all flex items-center gap-1.5 self-start">
                        <span>+ Tambah Tamu Baru</span>
                    </button>
                </div>

                <!-- MODAL TAMBAH TAMU -->
                <div x-show="addGuestModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-stone-900/50 p-4">
                    <div @click.away="addGuestModal = false" class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl">
                        <h4 class="text-base font-bold text-stone-900 mb-4">Tambah Tamu Undangan</h4>
                        <form action="{{ route('guests.store', $invitation) }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-stone-700 mb-1">Nama Tamu Undangan *</label>
                                <input type="text" name="name" required placeholder="Contoh: Bpk. Joko Widodo & Rekan"
                                       class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-stone-200 focus:ring-1 focus:ring-rose-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-stone-700 mb-1">No. WhatsApp / HP</label>
                                <input type="text" name="phone" placeholder="Contoh: 08123456789 (opsional)"
                                       class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-stone-200 focus:ring-1 focus:ring-rose-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-stone-700 mb-1">Perkiraan Jumlah Orang (Pax)</label>
                                <input type="number" name="guest_count" value="1" min="1" max="20"
                                       class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-stone-200 focus:ring-1 focus:ring-rose-500">
                            </div>
                            <div class="flex justify-end gap-2 pt-2">
                                <button type="button" @click="addGuestModal = false" class="px-4 py-2 text-xs font-bold text-stone-500 hover:text-stone-700">Batal</button>
                                <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs px-5 py-2.5 rounded-xl shadow-md">Simpan Tamu</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- FILTER & SEARCH -->
                <form action="{{ route('guests.index', $invitation) }}" method="GET" class="flex flex-wrap gap-2 mb-6">
                    <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama atau nomor HP..."
                           class="text-xs px-3.5 py-2 rounded-xl border border-stone-200 w-64 focus:ring-1 focus:ring-rose-500">

                    <select name="status" class="text-xs px-3 py-2 rounded-xl border border-stone-200 focus:ring-1 focus:ring-rose-500">
                        <option value="">Semua Status RSVP</option>
                        <option value="attended" {{ $statusFilter === 'attended' ? 'selected' : '' }}>Hadir</option>
                        <option value="maybe" {{ $statusFilter === 'maybe' ? 'selected' : '' }}>Ragu-ragu</option>
                        <option value="not_attended" {{ $statusFilter === 'not_attended' ? 'selected' : '' }}>Tidak Hadir</option>
                        <option value="pending" {{ $statusFilter === 'pending' ? 'selected' : '' }}>Belum Merespons</option>
                    </select>

                    <button type="submit" class="bg-stone-900 text-white text-xs font-bold px-4 py-2 rounded-xl">Filter</button>
                    @if($search || $statusFilter)
                        <a href="{{ route('guests.index', $invitation) }}" class="text-xs text-rose-600 hover:underline self-center ml-2">Reset</a>
                    @endif
                </form>

                <!-- TABEL TAMU -->
                @if($guests->isEmpty())
                    <p class="text-xs text-stone-400 py-8 text-center">Belum ada daftar tamu yang cocok dengan pencarian.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-stone-50 text-stone-500 uppercase tracking-wider border-b border-stone-200">
                                <tr>
                                    <th class="py-3 px-4">Nama Tamu</th>
                                    <th class="py-3 px-4">Kontak / HP</th>
                                    <th class="py-3 px-4">Status RSVP</th>
                                    <th class="py-3 px-4">Pax</th>
                                    <th class="py-3 px-4">Check-in</th>
                                    <th class="py-3 px-4">Link & Bagikan</th>
                                    <th class="py-3 px-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-100">
                                @foreach($guests as $g)
                                    @php
                                        $guestUrl = url("/u/{$invitation->slug}/{$g->personal_token}");
                                        $phoneClean = preg_replace('/[^0-9]/', '', $g->phone ?? '');
                                        if (str_starts_with($phoneClean, '0')) {
                                            $phoneClean = '62' . substr($phoneClean, 1);
                                        }
                                        $waText = urlencode("Kepada Yth. {$g->name},\n\nTanpa mengurangi rasa hormat, perkenankan kami mengundang Bapak/Ibu/Saudara/i untuk menghadiri acara kami.\n\nBerikut tautan undangan online lengkap:\n{$guestUrl}\n\nMerupakan suatu kehormatan dan kebahagiaan bagi kami apabila berkenan hadir dan memberikan doa restu. Terima kasih.");
                                    @endphp
                                    <tr>
                                        <td class="py-3 px-4 font-bold text-stone-900">{{ $g->name }}</td>
                                        <td class="py-3 px-4 text-stone-500 font-mono">{{ $g->phone ?? '-' }}</td>
                                        <td class="py-3 px-4">
                                            @if($g->rsvp_status === 'attended')
                                                <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px]">Hadir</span>
                                            @elseif($g->rsvp_status === 'not_attended')
                                                <span class="px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 font-bold text-[10px]">Tidak Hadir</span>
                                            @elseif($g->rsvp_status === 'maybe')
                                                <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 font-bold text-[10px]">Ragu-ragu</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full bg-stone-100 text-stone-500 font-bold text-[10px]">Belum Respon</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 font-semibold text-stone-700">{{ $g->guest_count }} orang</td>
                                        <td class="py-3 px-4">
                                            <form action="{{ route('guests.checkin', ['invitation' => $invitation->id, 'guest' => $g->id]) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                        class="px-2.5 py-1 rounded-lg text-[10px] font-bold transition-colors {{ $g->checked_in_at ? 'bg-sky-100 text-sky-800' : 'bg-stone-100 hover:bg-stone-200 text-stone-600' }}">
                                                    {{ $g->checked_in_at ? '✓ ' . $g->checked_in_at->format('H:i') : 'Check-in' }}
                                                </button>
                                            </form>
                                        </td>
                                        <td class="py-3 px-4">
                                            <div class="flex items-center gap-1.5">
                                                <button @click="navigator.clipboard.writeText('{{ $guestUrl }}'); copyFeedback = {{ $g->id }}; setTimeout(() => copyFeedback = null, 2500)"
                                                        class="bg-stone-100 hover:bg-stone-200 text-stone-700 px-2 py-1 rounded-lg text-[10px] font-semibold transition-colors">
                                                    <span x-show="copyFeedback !== {{ $g->id }}">Salin Link</span>
                                                    <span x-show="copyFeedback === {{ $g->id }}" class="text-emerald-600 font-bold">✓ Tersalin</span>
                                                </button>

                                                @if(!empty($phoneClean))
                                                    <a href="https://api.whatsapp.com/send?phone={{ $phoneClean }}&text={{ $waText }}"
                                                       target="_blank"
                                                       class="bg-emerald-600 hover:bg-emerald-700 text-white px-2 py-1 rounded-lg text-[10px] font-bold transition-colors">
                                                        Kirim WA
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="py-3 px-4 text-right">
                                            <form action="{{ route('guests.destroy', ['invitation' => $invitation->id, 'guest' => $g->id]) }}" method="POST"
                                                  onsubmit="return confirm('Hapus tamu ini dari daftar?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-rose-500 hover:text-rose-700 font-bold text-xs">
                                                    Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $guests->links() }}
                    </div>
                @endif
            </div>

            <!-- 3. FEED DOA & BUKU TAMU TERBARU -->
            <div class="bg-white rounded-3xl border border-stone-200 shadow-sm p-6 sm:p-8">
                <h3 class="text-base font-extrabold text-stone-900 mb-4">Doa & Ucapan Masuk ({{ $invitation->wishes->count() }})</h3>
                <div class="space-y-3 max-h-80 overflow-y-auto">
                    @forelse($invitation->wishes as $wish)
                        <div class="p-3.5 bg-stone-50 rounded-2xl border border-stone-100 flex items-start justify-between">
                            <div>
                                <span class="font-bold text-xs text-stone-900 block">{{ $wish->name }}</span>
                                <p class="text-xs text-stone-600 mt-1 leading-relaxed">{{ $wish->message }}</p>
                            </div>
                            <span class="text-[10px] text-stone-400 shrink-0 ml-4">{{ $wish->created_at->diffForHumans() }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-stone-400">Belum ada ucapan masuk dari tamu.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
