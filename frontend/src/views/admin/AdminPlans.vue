<template>
  <div class="min-h-screen bg-stone-50 pb-20">
    <!-- Header -->
    <header class="bg-white border-b border-stone-200 sticky top-16 z-30 shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex items-center justify-between">
          <div>
            <div class="flex items-center gap-2">
              <router-link to="/admin" class="text-xs text-stone-500 hover:text-stone-800 font-semibold">← Admin Dashboard</router-link>
            </div>
            <h2 class="font-extrabold text-2xl text-stone-900 leading-tight mt-1">
              Manajemen Paket Layanan & Harga
            </h2>
          </div>
          <span class="px-3 py-1 bg-rose-100 text-rose-800 text-xs font-bold rounded-full">Admin Mode</span>
        </div>
      </div>
    </header>

    <div v-if="loading" class="py-20 text-center text-stone-400 text-sm">
      Memuat daftar paket...
    </div>

    <div v-else class="py-10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <div v-if="statusMessage" class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold rounded-2xl flex items-center justify-between">
          <span>{{ statusMessage }}</span>
          <button @click="statusMessage = null" class="text-emerald-600 font-bold hover:text-emerald-800">×</button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <div v-for="plan in plans" :key="plan.id"
               class="bg-white rounded-3xl border border-stone-200 shadow-sm p-6 sm:p-8 flex flex-col justify-between">
            <div>
              <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-mono font-bold text-stone-400 uppercase tracking-wider">{{ plan.code }}</span>
                <span class="text-xs font-semibold text-stone-500">Terjual: <strong>{{ plan.transactions_count }}x</strong></span>
              </div>

              <form @submit.prevent="updatePlan(plan)" class="space-y-4">
                <div>
                  <label class="block text-xs font-bold text-stone-700 mb-1">Nama Paket *</label>
                  <input type="text" v-model="plan.name" required class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200 focus:ring-1 focus:ring-rose-500">
                </div>

                <div>
                  <label class="block text-xs font-bold text-stone-700 mb-1">Harga (Rupiah) *</label>
                  <input type="number" v-model.number="plan.price" required class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200 focus:ring-1 focus:ring-rose-500">
                </div>

                <div class="grid grid-cols-3 gap-2">
                  <div>
                    <label class="block text-[11px] font-bold text-stone-700 mb-1">Durasi (Hari)</label>
                    <input type="number" v-model.number="plan.duration_days" required class="w-full text-xs px-2 py-1.5 rounded-lg border border-stone-200">
                  </div>
                  <div>
                    <label class="block text-[11px] font-bold text-stone-700 mb-1">Maks Foto</label>
                    <input type="number" v-model.number="plan.max_photos" required class="w-full text-xs px-2 py-1.5 rounded-lg border border-stone-200">
                  </div>
                  <div>
                    <label class="block text-[11px] font-bold text-stone-700 mb-1">Maks Acara</label>
                    <input type="number" v-model.number="plan.max_events" required class="w-full text-xs px-2 py-1.5 rounded-lg border border-stone-200">
                  </div>
                </div>

                <div>
                  <label class="block text-xs font-bold text-stone-700 mb-1">Daftar Fitur (Satu per baris)</label>
                  <textarea v-model="plan.features_text" rows="6" required class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200 font-sans"></textarea>
                </div>

                <button type="submit"
                        class="w-full bg-stone-900 hover:bg-stone-800 text-white font-bold text-xs py-2.5 rounded-xl shadow-md transition-colors">
                  Simpan Paket {{ plan.name }}
                </button>
              </form>
            </div>
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
const statusMessage = ref(null);
const plans = ref([]);

const fetchPlans = async () => {
  try {
    loading.value = true;
    const res = await api.get('/admin/plans');
    plans.value = (res.data.plans || []).map(p => ({
      ...p,
      features_text: Array.isArray(p.features) ? p.features.join('\n') : ''
    }));
  } catch (err) {
    console.error('Failed to load plans:', err);
  } finally {
    loading.value = false;
  }
};

const updatePlan = async (plan) => {
  try {
    const res = await api.put(`/admin/plans/${plan.id}`, {
      name: plan.name,
      price: plan.price,
      duration_days: plan.duration_days,
      max_photos: plan.max_photos,
      max_events: plan.max_events,
      features_text: plan.features_text
    });
    statusMessage.value = res.data.message || 'Paket berhasil diperbarui!';
  } catch (err) {
    alert(err.response?.data?.message || 'Gagal memperbarui paket');
  }
};

onMounted(() => {
  fetchPlans();
});
</script>
