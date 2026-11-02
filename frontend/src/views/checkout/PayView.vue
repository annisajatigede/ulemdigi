<template>
  <div class="py-12">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
      <div v-if="loading" class="text-center py-12 text-stone-500 text-sm">
        Memuat data pembayaran...
      </div>

      <div v-else-if="transaction" class="bg-white rounded-3xl shadow-sm border border-stone-200 overflow-hidden">
        <div class="p-6 sm:p-8 text-center border-b border-stone-100 bg-gradient-to-b from-stone-50 to-white">
          <span class="inline-block px-3 py-1 bg-amber-100 text-amber-800 font-bold text-xs rounded-full uppercase tracking-wider mb-2">
            Menunggu Pembayaran
          </span>
          <h3 class="text-2xl font-extrabold text-stone-900">Selesaikan Pembayaran Anda</h3>
          <p class="text-xs text-stone-500 mt-1">
            Order ID: <code class="font-mono text-stone-700 bg-stone-100 px-2 py-0.5 rounded">{{ transaction.order_id }}</code>
          </p>
        </div>

        <div class="p-6 sm:p-8 space-y-6">
          <div class="bg-rose-50/50 border border-rose-100 rounded-2xl p-6 text-center">
            <span class="text-xs text-stone-500 uppercase tracking-wider font-semibold">Total Tagihan</span>
            <div class="text-4xl font-extrabold text-rose-600 mt-1 mb-2">
              Rp {{ Number(transaction.amount).toLocaleString('id-ID') }}
            </div>
            <p class="text-xs text-stone-600 font-medium">
              {{ transaction.plans?.name }} • Aktif {{ transaction.plans?.duration_days }} Hari
            </p>
          </div>

          <!-- Tombol Snap Resmi -->
          <button
            @click="payWithSnap"
            class="w-full inline-flex items-center justify-center gap-2 bg-gradient-to-r from-rose-600 to-rose-700 hover:from-rose-700 hover:to-rose-800 text-white font-bold py-4 rounded-2xl shadow-xl shadow-rose-600/25 transition-all transform hover:-translate-y-0.5 text-base"
          >
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
              <button
                @click="simulateSuccess"
                :disabled="isSimulating"
                class="w-full bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold py-3 rounded-xl shadow-md transition-colors disabled:opacity-50"
              >
                <span v-if="!isSimulating">⚡ Simulasi Pembayaran Berhasil (Auto Active)</span>
                <span v-else>Memproses Webhook Aktivasi...</span>
              </button>
            </div>
          </div>
        </div>

        <div class="p-6 bg-stone-50 border-t border-stone-100 text-center">
          <router-link to="/dashboard" class="text-xs font-semibold text-stone-500 hover:text-stone-700">
            Kembali ke Dashboard Saya
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '../../api/client';
import { alertError } from '../../utils/alert';

const route = useRoute();
const router = useRouter();

const transaction = ref(null);
const snapToken = ref(null);
const loading = ref(true);
const isSimulating = ref(false);

async function loadTransaction() {
  loading.value = true;
  try {
    const res = await api.get(`/checkout/transactions/${route.params.id}`);
    transaction.value = res.data.transaction;
    snapToken.value = res.data.snap_token;

    if (transaction.value.status === 'paid') {
      router.push(`/checkout/success/${transaction.value.id}`);
    }
  } catch (err) {
    await alertError('Gagal Memuat Transaksi', err.response?.data?.message || 'Gagal memuat transaksi.');
    router.push('/dashboard');
  } finally {
    loading.value = false;
  }
}

function payWithSnap() {
  if (window.snap && snapToken.value && !snapToken.value.startsWith('MOCK_SNAP_')) {
    window.snap.pay(snapToken.value, {
      onSuccess: () => {
        router.push(`/checkout/success/${transaction.value.id}`);
      },
      onPending: () => {
        router.push(`/checkout/pay/${transaction.value.id}`);
      },
      onError: () => {
        alertError('Pembayaran Gagal', 'Pembayaran gagal diproses atau dibatalkan.');
      },
      onClose: () => {
        // Closed without completing
      },
    });
  } else {
    // If mock or no real key, suggest simulator
    simulateSuccess();
  }
}

async function simulateSuccess() {
  isSimulating.value = true;
  try {
    await api.post('/payment/simulator/callback', {
      order_id: transaction.value.order_id,
      status: 'paid',
    });
    router.push(`/checkout/success/${transaction.value.id}`);
  } catch (err) {
    await alertError('Simulasi Gagal', err.response?.data?.message || 'Gagal menjalankan simulasi.');
  } finally {
    isSimulating.value = false;
  }
}

onMounted(() => {
  loadTransaction();
});
</script>
