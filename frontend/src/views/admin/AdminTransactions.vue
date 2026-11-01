<template>
  <div class="min-h-screen bg-stone-50 pb-20">
    <!-- Header -->
    <header class="bg-white border-b border-stone-200 sticky top-16 z-30 shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <div>
            <div class="flex items-center gap-2">
              <router-link to="/admin" class="text-xs text-stone-500 hover:text-stone-800 font-semibold">← Admin Dashboard</router-link>
            </div>
            <h2 class="font-extrabold text-2xl text-stone-900 leading-tight mt-1">
              Data Transaksi Pembayaran
            </h2>
          </div>

          <button @click="exportCsv"
                  class="bg-white hover:bg-stone-50 border border-stone-200 text-stone-800 text-xs font-bold px-4 py-2.5 rounded-xl shadow-sm transition-all flex items-center gap-1.5 self-start">
            <span>📥 Unduh Laporan (CSV)</span>
          </button>
        </div>
      </div>
    </header>

    <div v-if="loading" class="py-20 text-center text-stone-400 text-sm">
      Memuat data transaksi...
    </div>

    <div v-else class="py-10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- STATISTIK KEUANGAN -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
          <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-sm">
            <span class="text-[10px] font-bold text-stone-400 uppercase tracking-wider block">Total Terbayar</span>
            <span class="text-2xl font-extrabold text-emerald-600 mt-1 block">Rp {{ formatNumber(stats.total_amount) }}</span>
          </div>
          <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-sm">
            <span class="text-[10px] font-bold text-stone-400 uppercase tracking-wider block">Total Transaksi</span>
            <span class="text-2xl font-extrabold text-stone-900 mt-1 block">{{ stats.total_count }}</span>
          </div>
          <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-sm">
            <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider block">Transaksi Lunas</span>
            <span class="text-2xl font-extrabold text-emerald-600 mt-1 block">{{ stats.paid_count }}</span>
          </div>
          <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-sm">
            <span class="text-[10px] font-bold text-amber-600 uppercase tracking-wider block">Pending / Belum Bayar</span>
            <span class="text-2xl font-extrabold text-amber-600 mt-1 block">{{ stats.pending_count }}</span>
          </div>
        </div>

        <!-- FILTER TRANSAKSI -->
        <div class="bg-white rounded-3xl border border-stone-200 shadow-sm p-6 sm:p-8">
          <div class="flex flex-wrap items-end gap-3 mb-6">
            <div>
              <label class="block text-[11px] font-bold text-stone-600 mb-1">Cari Order ID / Pelanggan</label>
              <input type="text" v-model="filterQ" @keyup.enter="applyFilter" placeholder="INV-... / email..."
                     class="text-xs px-3 py-2 rounded-xl border border-stone-200 w-56 focus:ring-1 focus:ring-rose-500">
            </div>

            <div>
              <label class="block text-[11px] font-bold text-stone-600 mb-1">Status</label>
              <select v-model="filterStatus" class="text-xs px-3 py-2 rounded-xl border border-stone-200 focus:ring-1 focus:ring-rose-500">
                <option value="">Semua Status</option>
                <option value="paid">Paid (Lunas)</option>
                <option value="pending">Pending</option>
                <option value="failed">Failed</option>
                <option value="expired">Expired</option>
              </select>
            </div>

            <div>
              <label class="block text-[11px] font-bold text-stone-600 mb-1">Dari Tanggal</label>
              <input type="date" v-model="filterStartDate" class="text-xs px-3 py-2 rounded-xl border border-stone-200">
            </div>

            <div>
              <label class="block text-[11px] font-bold text-stone-600 mb-1">Sampai Tanggal</label>
              <input type="date" v-model="filterEndDate" class="text-xs px-3 py-2 rounded-xl border border-stone-200">
            </div>

            <button type="button" @click="applyFilter" class="bg-stone-900 text-white text-xs font-bold px-4 py-2 rounded-xl">Terapkan Filter</button>
            <button v-if="filterQ || filterStatus || filterStartDate || filterEndDate" type="button" @click="resetFilter" class="text-xs text-rose-600 hover:underline self-center ml-2">Reset</button>
          </div>

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
                <tr v-for="t in transactions" :key="t.id">
                  <td class="py-3 px-4 font-mono font-bold text-stone-800">{{ t.order_id }}</td>
                  <td class="py-3 px-4 font-semibold text-stone-900">
                    {{ t.users?.name || '-' }}
                    <span class="block text-[11px] text-stone-500 font-normal">{{ t.users?.email || '-' }}</span>
                  </td>
                  <td class="py-3 px-4">{{ t.plans?.name || '-' }}</td>
                  <td class="py-3 px-4 font-bold text-stone-900">Rp {{ formatNumber(t.amount) }}</td>
                  <td class="py-3 px-4">
                    <span v-if="t.status === 'paid'" class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px]">LUNAS</span>
                    <span v-else-if="t.status === 'pending'" class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 font-bold text-[10px]">PENDING</span>
                    <span v-else class="px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 font-bold text-[10px]">{{ (t.status || '').toUpperCase() }}</span>
                  </td>
                  <td class="py-3 px-4 uppercase font-semibold text-stone-600">{{ t.gateway }}</td>
                  <td class="py-3 px-4 text-stone-500">{{ formatDate(t.created_at) }}</td>
                  <td class="py-3 px-4 text-stone-500">{{ t.paid_at ? formatDate(t.paid_at) : '-' }}</td>
                  <td class="py-3 px-4">
                    <a v-if="t.invitations" :href="`/u/${t.invitations.slug}`" target="_blank" class="text-rose-600 hover:underline font-mono">
                      /u/{{ t.invitations.slug }}
                    </a>
                    <span v-else class="text-stone-400">-</span>
                  </td>
                </tr>
                <tr v-if="transactions.length === 0">
                  <td colspan="9" class="py-6 text-center text-stone-400">Tidak ada riwayat transaksi.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../api/client';

const loading = ref(true);
const transactions = ref([]);
const stats = ref({ total_amount: 0, total_count: 0, paid_count: 0, pending_count: 0 });

const filterQ = ref('');
const filterStatus = ref('');
const filterStartDate = ref('');
const filterEndDate = ref('');

const formatNumber = (num) => Number(num || 0).toLocaleString('id-ID');
const formatDate = (dateStr) => {
  if (!dateStr) return '-';
  const d = new Date(dateStr);
  return d.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
};

const fetchTransactions = async () => {
  try {
    loading.value = true;
    const res = await api.get('/admin/transactions', {
      params: {
        q: filterQ.value || undefined,
        status: filterStatus.value || undefined,
        start_date: filterStartDate.value || undefined,
        end_date: filterEndDate.value || undefined
      }
    });
    transactions.value = res.data.transactions || [];
    stats.value = res.data.stats || {};
  } catch (err) {
    console.error('Failed to load transactions:', err);
  } finally {
    loading.value = false;
  }
};

const applyFilter = () => fetchTransactions();
const resetFilter = () => {
  filterQ.value = '';
  filterStatus.value = '';
  filterStartDate.value = '';
  filterEndDate.value = '';
  fetchTransactions();
};

const exportCsv = () => {
  const params = new URLSearchParams();
  if (filterStatus.value) params.append('status', filterStatus.value);
  if (filterStartDate.value) params.append('start_date', filterStartDate.value);
  if (filterEndDate.value) params.append('end_date', filterEndDate.value);
  window.open(`/api/admin/transactions/export?${params.toString()}`, '_blank');
};

onMounted(() => {
  fetchTransactions();
});
</script>
