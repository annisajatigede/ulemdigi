<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-stone-800 leading-tight">
            {{ __('Pembayaran Undangan Digital') }}
        </h2>
    </x-slot>

    <!-- Midtrans Snap JS Integration -->
    @if(!empty($clientKey))
        <script type="text/javascript"
                src="{{ $isProduction ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
                data-client-key="{{ $clientKey }}"></script>
    @endif

    <div class="py-12" x-data="paymentPage('{{ $transaction->order_id }}', '{{ $snapToken }}')">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl shadow-sm border border-stone-200 overflow-hidden">
                <div class="p-6 sm:p-8 text-center border-b border-stone-100 bg-gradient-to-b from-stone-50 to-white">
                    <span class="inline-block px-3 py-1 bg-amber-100 text-amber-800 font-bold text-xs rounded-full uppercase tracking-wider mb-2">
                        Menunggu Pembayaran
                    </span>
                    <h3 class="text-2xl font-extrabold text-stone-900">Selesaikan Pembayaran Anda</h3>
                    <p class="text-xs text-stone-500 mt-1">Order ID: <code class="font-mono text-stone-700 bg-stone-100 px-2 py-0.5 rounded">{{ $transaction->order_id }}</code></p>
                </div>

                <div class="p-6 sm:p-8 space-y-6">
                    <div class="bg-rose-50/50 border border-rose-100 rounded-2xl p-6 text-center">
                        <span class="text-xs text-stone-500 uppercase tracking-wider font-semibold">Total Tagihan</span>
                        <div class="text-4xl font-extrabold text-rose-600 mt-1 mb-2">{{ $transaction->formattedAmount() }}</div>
                        <p class="text-xs text-stone-600 font-medium">{{ $transaction->plan->name }} • Aktif {{ $transaction->plan->duration_days }} Hari</p>
                    </div>

                    <!-- Tombol Snap Resmi -->
                    <button @click="payWithSnap()"
                            class="w-full inline-flex items-center justify-center gap-2 bg-gradient-to-r from-rose-600 to-rose-700 hover:from-rose-700 hover:to-rose-800 text-white font-bold py-4 rounded-2xl shadow-xl shadow-rose-600/25 transition-all transform hover:-translate-y-0.5 text-base">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        <span>Bayar Sekarang (Midtrans Snap)</span>
                    </button>

                    <!-- SIMULATOR PEMBAYARAN SANDBOX/LOKAL -->
                    <div class="mt-8 pt-6 border-t border-stone-200">
                        <div class="bg-stone-50 border border-stone-200 rounded-2xl p-5 text-center">
                            <div class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-700 uppercase tracking-wider mb-1">
                                <span>🛠️ Mode Pengujian Sandbox / Lokal</span>
                            </div>
                            <p class="text-xs text-stone-500 mb-4 leading-relaxed">
                                Anda dapat mengklik tombol simulasi di bawah untuk menguji webhook otomatis Midtrans secara instan dan mengaktifkan undangan Anda tanpa perlu transfer nyata.
                            </p>
                            <button @click="simulateSuccess()"
                                    :disabled="isSimulating"
                                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold py-3 rounded-xl shadow-md transition-colors disabled:opacity-50">
                                <span x-show="!isSimulating">⚡ Simulasi Pembayaran Berhasil (Auto Active)</span>
                                <span x-show="isSimulating">Memproses Webhook Aktivasi...</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="p-6 bg-stone-50 border-t border-stone-100 text-center">
                    <a href="{{ route('dashboard') }}" class="text-xs font-semibold text-stone-500 hover:text-stone-700">
                        Kembali ke Dashboard Saya
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function paymentPage(orderId, snapToken) {
            return {
                isSimulating: false,
                payWithSnap() {
                    if (window.snap && snapToken && !snapToken.startsWith('SNAP-MOCK-')) {
                        window.snap.pay(snapToken, {
                            onSuccess: (result) => {
                                window.location.href = "{{ route('checkout.success', ['transaction' => $transaction->id]) }}";
                            },
                            onPending: (result) => {
                                alert('Pembayaran sedang diproses.');
                                window.location.reload();
                            },
                            onError: (result) => {
                                alert('Terjadi kesalahan pada transaksi.');
                            },
                            onClose: () => {
                                console.log('Customer closed the popup without finishing payment.');
                            }
                        });
                    } else {
                        // Fallback jika mode simulator atau token mock
                        this.simulateSuccess();
                    }
                },
                async simulateSuccess() {
                    this.isSimulating = true;
                    try {
                        const res = await fetch("{{ route('payment.simulator.callback') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                order_id: orderId,
                                status: 'paid'
                            })
                        });

                        const data = await res.json();
                        if (res.ok && data.status === 'success') {
                            window.location.href = "{{ route('checkout.success', ['transaction' => $transaction->id]) }}";
                        } else {
                            alert(data.message || 'Gagal memproses simulasi.');
                        }
                    } catch (e) {
                        alert('Koneksi gagal saat menghubungi endpoint simulasi.');
                    } finally {
                        this.isSimulating = false;
                    }
                }
            };
        }
    </script>
</x-app-layout>
