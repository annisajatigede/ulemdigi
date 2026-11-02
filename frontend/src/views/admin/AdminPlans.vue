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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <div v-for="plan in plans" :key="plan.id"
               class="bg-white rounded-3xl border border-stone-200 shadow-sm p-6 sm:p-8 flex flex-col justify-between">
            <div>
              <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-mono font-bold text-stone-400 uppercase tracking-wider">{{ plan.slug || plan.code || ('Paket ' + plan.id) }}</span>
                <span v-if="Number(plan.price) === 0" class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-extrabold">
                  GRATIS / PROMO
                </span>
                <span v-else class="text-xs font-semibold text-stone-500">Terjual: <strong>{{ plan.transactions_count || 0 }}x</strong></span>
              </div>

              <form @submit.prevent="updatePlan(plan)" class="space-y-4">
                <div>
                  <label class="block text-xs font-bold text-stone-700 mb-1">Nama Paket *</label>
                  <input type="text" v-model="plan.name" required class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200 focus:ring-1 focus:ring-rose-500">
                </div>

                <div>
                  <div class="flex items-center justify-between mb-1">
                    <label class="block text-xs font-bold text-stone-700">Harga (Rupiah) *</label>
                    <span class="text-[11px] text-stone-400">Isi 0 untuk paket promo/gratis</span>
                  </div>
                  <input type="number" v-model.number="plan.price" required min="0" class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200 focus:ring-1 focus:ring-rose-500">
                </div>

                <div class="grid grid-cols-3 gap-2">
                  <div>
                    <label class="block text-[11px] font-bold text-stone-700 mb-1">Durasi (Hari)</label>
                    <input type="number" v-model.number="plan.duration_days" @input="onDurationChange(plan)" required min="1" class="w-full text-xs px-2 py-1.5 rounded-lg border border-stone-200">
                  </div>
                  <div>
                    <label class="block text-[11px] font-bold text-stone-700 mb-1">Maks Foto</label>
                    <input type="number" v-model.number="plan.max_photos" required min="1" class="w-full text-xs px-2 py-1.5 rounded-lg border border-stone-200">
                  </div>
                  <div>
                    <label class="block text-[11px] font-bold text-stone-700 mb-1">Maks Acara</label>
                    <input type="number" v-model.number="plan.max_events" required min="1" class="w-full text-xs px-2 py-1.5 rounded-lg border border-stone-200">
                  </div>
                </div>

                <div>
                  <div class="flex items-center justify-between mb-1">
                    <label class="block text-xs font-bold text-stone-700">Daftar Fitur (Satu per baris)</label>
                    <button type="button" @click="syncDurationInFeatures(plan)" class="text-[10px] text-rose-600 hover:text-rose-800 font-semibold underline">
                      Sinkron Durasi Hari
                    </button>
                  </div>
                  <textarea v-model="plan.features_text" rows="6" required class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200 font-sans"></textarea>
                </div>

                <button type="submit" :disabled="plan._saving"
                        class="w-full bg-stone-900 hover:bg-stone-800 text-white font-bold text-xs py-2.5 rounded-xl shadow-md transition-colors disabled:opacity-50">
                  <span v-if="plan._saving">Menyimpan...</span>
                  <span v-else>Simpan Paket {{ plan.name }}</span>
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
import { alertSuccess, alertError } from '../../utils/alert';

const loading = ref(true);
const plans = ref([]);

const onDurationChange = (plan) => {
  // Check if features_text has a line like "Masa Aktif X Hari"
  if (typeof plan.features_text === 'string' && plan.duration_days) {
    const lines = plan.features_text.split('\n');
    let updated = false;
    const newLines = lines.map(line => {
      if (/^Masa\s+Aktif\s+\d+\s+Hari/i.test(line.trim())) {
        updated = true;
        return `Masa Aktif ${plan.duration_days} Hari`;
      }
      return line;
    });
    if (updated) {
      plan.features_text = newLines.join('\n');
    }
  }
};

const syncDurationInFeatures = (plan) => {
  onDurationChange(plan);
};

const fetchPlans = async () => {
  try {
    loading.value = true;
    const res = await api.get('/admin/plans');
    plans.value = (res.data.plans || []).map(p => ({
      ...p,
      duration_days: p.duration_days || p.active_days || 30,
      active_days: p.active_days || p.duration_days || 30,
      features_text: Array.isArray(p.features) ? p.features.join('\n') : (p.features_text || ''),
      _saving: false
    }));
  } catch (err) {
    console.error('Failed to load plans:', err);
  } finally {
    loading.value = false;
  }
};

const updatePlan = async (plan) => {
  try {
    plan._saving = true;

    // Parse features from lines
    let featuresArray = (plan.features_text || '')
      .split('\n')
      .map(s => s.trim())
      .filter(s => s.length > 0);

    const payload = {
      name: plan.name,
      price: Number(plan.price || 0),
      duration_days: Number(plan.duration_days || plan.active_days || 30),
      active_days: Number(plan.duration_days || plan.active_days || 30),
      max_photos: Number(plan.max_photos || 5),
      max_events: Number(plan.max_events || 1),
      features: featuresArray,
      features_text: plan.features_text
    };

    const res = await api.put(`/admin/plans/${plan.id}`, payload);
    
    // Update local plan object directly
    Object.assign(plan, payload);

    await alertSuccess(
      'Paket Berhasil Diperbarui!',
      `Paket "${plan.name}" berhasil disimpan dengan masa aktif ${payload.duration_days} hari dan ${featuresArray.length} fitur.`
    );

    await fetchPlans();
  } catch (err) {
    await alertError('Gagal Menyimpan Paket', err.response?.data?.message || err.message || 'Terjadi kesalahan');
  } finally {
    plan._saving = false;
  }
};

onMounted(() => {
  fetchPlans();
});
</script>
