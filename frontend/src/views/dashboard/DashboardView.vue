<template>
  <div class="py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

      <!-- Header Section -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h2 class="font-extrabold text-2xl text-stone-900 leading-tight">
            Dashboard Undangan Saya
          </h2>
          <p class="text-xs text-stone-500 mt-0.5">Kelola dan pantau seluruh undangan digital Anda di sini.</p>
        </div>
        <router-link
          to="/#template"
          class="inline-flex items-center gap-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs sm:text-sm px-5 py-2.5 rounded-2xl shadow-md shadow-rose-600/20 transition-all self-start sm:self-auto"
        >
          <span>+ Buat Undangan Baru</span>
        </router-link>
      </div>

      <!-- Flash Status -->
      <div v-if="statusMessage" class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold rounded-2xl flex items-center justify-between">
        <span>{{ statusMessage }}</span>
        <button @click="statusMessage = ''" class="text-emerald-600 font-bold">✕</button>
      </div>

      <!-- STATISTIK RINGKASAN -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-3xl border border-stone-200 shadow-sm">
          <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Total Undangan</span>
          <div class="text-3xl font-extrabold text-stone-900 mt-2">{{ stats.total }}</div>
        </div>
        <div class="bg-white p-5 rounded-3xl border border-stone-200 shadow-sm">
          <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Dipublikasikan</span>
          <div class="text-3xl font-extrabold text-emerald-600 mt-2">{{ stats.published }}</div>
        </div>
        <div class="bg-white p-5 rounded-3xl border border-stone-200 shadow-sm">
          <span class="text-xs font-bold text-amber-600 uppercase tracking-wider">Draft / Belum Rilis</span>
          <div class="text-3xl font-extrabold text-amber-600 mt-2">{{ stats.draft }}</div>
        </div>
        <div class="bg-white p-5 rounded-3xl border border-stone-200 shadow-sm">
          <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Total Respon RSVP</span>
          <div class="text-3xl font-extrabold text-rose-600 mt-2">{{ stats.total_guests }}</div>
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

        <div v-if="invitations.length === 0" class="text-center py-12 bg-stone-50 rounded-2xl border-2 border-dashed border-stone-200">
          <div class="w-14 h-14 bg-rose-100 text-rose-600 rounded-2xl flex items-center justify-center mx-auto mb-3 text-2xl">
            💌
          </div>
          <h4 class="text-base font-bold text-stone-800">Belum Ada Undangan</h4>
          <p class="text-xs text-stone-500 max-w-sm mx-auto mt-1 mb-5">
            Mulai pilih desain template favorit dan paket yang Anda inginkan untuk membuat undangan pertama.
          </p>
          <router-link to="/#template" class="inline-block bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs px-6 py-3 rounded-xl shadow-md">
            Pilih Template Sekarang
          </router-link>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="inv in invitations"
            :key="inv.id"
            class="bg-stone-50 rounded-3xl border border-stone-200 overflow-hidden flex flex-col justify-between hover:shadow-lg transition-all duration-300"
          >
            <div>
              <!-- Header image / template thumbnail -->
              <div class="relative aspect-[16/9] bg-stone-200 overflow-hidden">
                <img
                  :src="inv.templates?.thumbnail_path || '/templates/thumbnails/wedding_floral.svg'"
                  :alt="inv.templates?.name"
                  class="w-full h-full object-cover"
                />

                <!-- Status Badge -->
                <div class="absolute top-3 left-3 flex gap-2">
                  <span v-if="inv.is_expired" class="bg-rose-600 text-white text-[10px] font-extrabold px-2.5 py-1 rounded-full shadow">
                    Kedaluwarsa
                  </span>
                  <span v-else-if="inv.status === 'published'" class="bg-emerald-600 text-white text-[10px] font-extrabold px-2.5 py-1 rounded-full shadow">
                    ● Publik
                  </span>
                  <span v-else class="bg-amber-500 text-white text-[10px] font-extrabold px-2.5 py-1 rounded-full shadow">
                    Draft
                  </span>
                </div>

                <!-- Sisa Masa Aktif Badge -->
                <div class="absolute top-3 right-3">
                  <span class="bg-stone-900/80 backdrop-blur text-white text-[10px] font-semibold px-2.5 py-1 rounded-full">
                    <span v-if="inv.is_expired">Habis</span>
                    <span v-else>Sisa {{ inv.days_remaining }} Hari</span>
                  </span>
                </div>
              </div>

              <!-- Content -->
              <div class="p-5">
                <span class="text-[10px] uppercase font-bold text-stone-500 tracking-wider">
                  {{ inv.templates?.name }} • {{ inv.plans?.name }}
                </span>
                <h4 class="font-extrabold text-stone-900 text-lg mt-0.5 line-clamp-1">
                  {{ inv.safeData?.party1?.name || 'Pihak 1' }}
                  <span v-if="inv.safeData?.party2?.name"> & {{ inv.safeData?.party2?.name }}</span>
                </h4>
                <p class="text-xs text-stone-500 font-mono mt-1 truncate">
                  /u/{{ inv.slug }}
                </p>

                <div class="grid grid-cols-2 gap-2 mt-4 pt-3 border-t border-stone-200/80 text-[11px] text-stone-600">
                  <div>
                    <span class="text-stone-400 block">Tamu / RSVP:</span>
                    <span class="font-bold text-stone-800">{{ inv.guests_count }} Tamu</span>
                  </div>
                  <div>
                    <span class="text-stone-400 block">Ucapan:</span>
                    <span class="font-bold text-stone-800">{{ inv.wishes_count }} Doa</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div class="p-5 pt-0 space-y-2">
              <div class="grid grid-cols-2 gap-2">
                <router-link
                  :to="`/editor/${inv.id}`"
                  class="bg-stone-900 hover:bg-stone-800 text-white text-xs font-bold py-2.5 rounded-xl text-center transition-colors"
                >
                  ✏️ Edit Undangan
                </router-link>
                <router-link
                  :to="`/invitations/${inv.id}/guests`"
                  class="bg-white hover:bg-stone-100 text-stone-800 border border-stone-300 text-xs font-bold py-2.5 rounded-xl text-center transition-colors"
                >
                  👥 Tamu & RSVP
                </router-link>
              </div>

              <div class="flex items-center gap-2">
                <!-- Toggle Publish Button -->
                <button
                  v-if="!inv.is_expired"
                  @click="handleTogglePublish(inv)"
                  class="flex-1 text-xs font-bold py-2 rounded-xl transition-colors"
                  :class="inv.status === 'published' ? 'bg-amber-100 hover:bg-amber-200 text-amber-900' : 'bg-emerald-600 hover:bg-emerald-700 text-white'"
                >
                  {{ inv.status === 'published' ? 'Tarik ke Draft' : '🚀 Publikasikan' }}
                </button>
                <router-link
                  v-else
                  :to="`/checkout/select?template_id=${inv.template_id}&invitation_id=${inv.id}`"
                  class="flex-1 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold py-2 rounded-xl text-center transition-colors"
                >
                  🔄 Perpanjang Paket
                </router-link>

                <!-- Copy URL Button -->
                <button
                  @click="copyUrl(inv)"
                  class="px-3 py-2 bg-white hover:bg-stone-100 border border-stone-300 rounded-xl text-xs font-semibold text-stone-700 transition-colors"
                  title="Salin Tautan Publik"
                >
                  <span v-if="copiedId !== inv.id">🔗 Salin</span>
                  <span v-else class="text-emerald-600 font-bold">✓ Tersalin</span>
                </button>

                <!-- Preview Link -->
                <a
                  :href="`/u/${inv.slug}`"
                  target="_blank"
                  class="px-3 py-2 bg-white hover:bg-stone-100 border border-stone-300 rounded-xl text-xs font-semibold text-stone-700 transition-colors"
                  title="Lihat Pratinjau Publik"
                >
                  👁️
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- RIWAYAT TRANSAKSI TERAKHIR -->
      <div class="bg-white rounded-3xl border border-stone-200 shadow-sm p-6 sm:p-8">
        <h3 class="text-base font-extrabold text-stone-900 mb-4">Riwayat Pembayaran Terbaru</h3>

        <div v-if="transactions.length === 0" class="text-xs text-stone-400 py-4 text-center">
          Belum ada catatan riwayat transaksi.
        </div>

        <div v-else class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead class="bg-stone-50 text-stone-500 uppercase tracking-wider border-b border-stone-200">
              <tr>
                <th class="py-3 px-4">Order ID</th>
                <th class="py-3 px-4">Paket</th>
                <th class="py-3 px-4">Jumlah</th>
                <th class="py-3 px-4">Status</th>
                <th class="py-3 px-4">Tanggal</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
              <tr v-for="tx in transactions" :key="tx.id" class="hover:bg-stone-50">
                <td class="py-3 px-4 font-mono font-bold text-stone-900">{{ tx.order_id }}</td>
                <td class="py-3 px-4 font-semibold text-stone-800">{{ tx.plans?.name || 'Paket' }}</td>
                <td class="py-3 px-4 font-bold text-stone-900">Rp {{ Number(tx.amount).toLocaleString('id-ID') }}</td>
                <td class="py-3 px-4">
                  <span
                    class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase"
                    :class="tx.status === 'paid' ? 'bg-emerald-100 text-emerald-800' : (tx.status === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800')"
                  >
                    {{ tx.status }}
                  </span>
                </td>
                <td class="py-3 px-4 text-stone-500">{{ new Date(tx.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../api/client';
import { notifySuccess, alertError } from '../../utils/alert';

const invitations = ref([]);
const transactions = ref([]);
const stats = ref({
  total: 0,
  published: 0,
  draft: 0,
  total_guests: 0,
});
const statusMessage = ref('');
const copiedId = ref(null);

async function loadDashboard() {
  try {
    const res = await api.get('/dashboard');
    invitations.value = res.data.invitations || [];
    transactions.value = res.data.transactions || [];
    stats.value = res.data.stats || stats.value;
  } catch (err) {
    console.error('Failed to load dashboard:', err);
  }
}

async function handleTogglePublish(inv) {
  try {
    const res = await api.post(`/editor/${inv.id}/toggle-publish`);
    notifySuccess(res.data.message || 'Status publikasi berhasil diubah');
    await loadDashboard();
  } catch (err) {
    await alertError('Gagal!', err.response?.data?.message || 'Gagal mengubah status publish.');
  }
}

function copyUrl(inv) {
  const url = `${window.location.origin}/u/${inv.slug}`;
  navigator.clipboard.writeText(url);
  copiedId.value = inv.id;
  setTimeout(() => {
    copiedId.value = null;
  }, 2500);
}

onMounted(() => {
  loadDashboard();
});
</script>
