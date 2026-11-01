<template>
  <div class="min-h-screen bg-stone-50 pb-20">
    <!-- Header -->
    <header class="bg-white border-b border-stone-200 sticky top-16 z-30 shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex items-center justify-between">
          <div>
            <span class="text-xs font-bold text-rose-600 uppercase tracking-wider">Super Administrator</span>
            <h2 class="font-extrabold text-2xl text-stone-900 leading-tight">
              Admin Dashboard
            </h2>
          </div>
          <span class="px-3 py-1 bg-rose-100 text-rose-800 text-xs font-bold rounded-full">Admin Mode</span>
        </div>
      </div>
    </header>

    <div v-if="loading" class="py-20 text-center text-stone-400 text-sm">
      Memuat data dashboard admin...
    </div>

    <div v-else class="py-10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        <!-- STATISTIK UTAMA -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
          <div class="bg-white p-5 rounded-3xl border border-stone-200 shadow-sm">
            <span class="text-[10px] font-bold text-stone-400 uppercase tracking-wider block">Total Pendapatan</span>
            <span class="text-xl font-extrabold text-emerald-600 mt-1 block">Rp {{ formatNumber(stats.totalRevenue) }}</span>
          </div>
          <div class="bg-white p-5 rounded-3xl border border-stone-200 shadow-sm">
            <span class="text-[10px] font-bold text-stone-400 uppercase tracking-wider block">Total Transaksi</span>
            <span class="text-xl font-extrabold text-stone-900 mt-1 block">{{ stats.totalTransactions }}</span>
          </div>
          <div class="bg-white p-5 rounded-3xl border border-stone-200 shadow-sm">
            <span class="text-[10px] font-bold text-stone-400 uppercase tracking-wider block">Transaksi Lunas</span>
            <span class="text-xl font-extrabold text-emerald-600 mt-1 block">{{ stats.paidTransactions }}</span>
          </div>
          <div class="bg-white p-5 rounded-3xl border border-stone-200 shadow-sm">
            <span class="text-[10px] font-bold text-stone-400 uppercase tracking-wider block">Total Undangan</span>
            <span class="text-xl font-extrabold text-stone-900 mt-1 block">{{ stats.totalInvitations }}</span>
          </div>
          <div class="bg-white p-5 rounded-3xl border border-stone-200 shadow-sm">
            <span class="text-[10px] font-bold text-stone-400 uppercase tracking-wider block">Total Pengguna</span>
            <span class="text-xl font-extrabold text-stone-900 mt-1 block">{{ stats.totalUsers }}</span>
          </div>
          <div class="bg-white p-5 rounded-3xl border border-stone-200 shadow-sm">
            <span class="text-[10px] font-bold text-stone-400 uppercase tracking-wider block">Template Aktif</span>
            <span class="text-xl font-extrabold text-stone-900 mt-1 block">{{ stats.activeTemplates }}</span>
          </div>
        </div>

        <!-- SHORTCUT MENU -->
        <div class="grid grid-cols-2 sm:grid-cols-6 gap-3">
          <router-link to="/admin/templates" class="p-4 bg-white hover:bg-stone-50 border border-stone-200 rounded-2xl shadow-sm text-center transition-all">
            <span class="text-xl block mb-1">🎨</span>
            <span class="text-xs font-bold text-stone-800">Kelola Template</span>
          </router-link>
          <router-link to="/admin/musics" class="p-4 bg-white hover:bg-stone-50 border border-stone-200 rounded-2xl shadow-sm text-center transition-all">
            <span class="text-xl block mb-1">🎵</span>
            <span class="text-xs font-bold text-stone-800">Pustaka Musik</span>
          </router-link>
          <router-link to="/admin/plans" class="p-4 bg-white hover:bg-stone-50 border border-stone-200 rounded-2xl shadow-sm text-center transition-all">
            <span class="text-xl block mb-1">🏷️</span>
            <span class="text-xs font-bold text-stone-800">Paket & Harga</span>
          </router-link>
          <router-link to="/admin/transactions" class="p-4 bg-white hover:bg-stone-50 border border-stone-200 rounded-2xl shadow-sm text-center transition-all">
            <span class="text-xl block mb-1">💳</span>
            <span class="text-xs font-bold text-stone-800">Data Transaksi</span>
          </router-link>
          <router-link to="/admin/users" class="p-4 bg-white hover:bg-stone-50 border border-stone-200 rounded-2xl shadow-sm text-center transition-all">
            <span class="text-xl block mb-1">👥</span>
            <span class="text-xs font-bold text-stone-800">Data Pengguna</span>
          </router-link>
          <router-link to="/admin/settings" class="p-4 bg-white hover:bg-stone-50 border border-stone-200 rounded-2xl shadow-sm text-center transition-all">
            <span class="text-xl block mb-1">⚙️</span>
            <span class="text-xs font-bold text-stone-800">Pengaturan Web</span>
          </router-link>
        </div>

        <!-- TRANSAKSI TERBARU -->
        <div class="bg-white rounded-3xl border border-stone-200 shadow-sm p-6 sm:p-8">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-extrabold text-stone-900">Transaksi Terbaru Sistem</h3>
            <router-link to="/admin/transactions" class="text-xs text-rose-600 font-bold hover:underline">Lihat Semua →</router-link>
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
                <tr v-for="tx in stats.recentTransactions" :key="tx.id">
                  <td class="py-3 px-4 font-mono font-bold text-stone-800">{{ tx.order_id }}</td>
                  <td class="py-3 px-4">{{ tx.users?.name || '-' }} ({{ tx.users?.email || '-' }})</td>
                  <td class="py-3 px-4">{{ tx.plans?.name || '-' }}</td>
                  <td class="py-3 px-4 font-semibold text-stone-900">Rp {{ formatNumber(tx.amount) }}</td>
                  <td class="py-3 px-4">
                    <span v-if="tx.status === 'paid'" class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px]">PAID</span>
                    <span v-else class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 font-bold text-[10px]">{{ (tx.status || '').toUpperCase() }}</span>
                  </td>
                  <td class="py-3 px-4 text-stone-500">{{ formatDate(tx.created_at) }}</td>
                </tr>
                <tr v-if="!stats.recentTransactions || stats.recentTransactions.length === 0">
                  <td colspan="6" class="py-4 text-center text-stone-400">Belum ada data transaksi.</td>
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
const stats = ref({
  totalRevenue: 0,
  totalTransactions: 0,
  paidTransactions: 0,
  totalInvitations: 0,
  totalUsers: 0,
  activeTemplates: 0,
  recentTransactions: []
});

const formatNumber = (num) => {
  return Number(num || 0).toLocaleString('id-ID');
};

const formatDate = (dateStr) => {
  if (!dateStr) return '-';
  const d = new Date(dateStr);
  return d.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
};

const fetchDashboard = async () => {
  try {
    loading.value = true;
    const res = await api.get('/admin/dashboard');
    stats.value = res.data;
  } catch (err) {
    console.error('Failed to load admin dashboard:', err);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchDashboard();
});
</script>
