<template>
  <div class="py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <div v-if="loading" class="text-center py-12 text-stone-500 text-sm">
        Memuat opsi checkout...
      </div>

      <form v-else @submit.prevent="handleSubmit" class="bg-white rounded-3xl shadow-sm border border-stone-200 overflow-hidden">
        <div class="p-6 sm:p-8 border-b border-stone-100">
          <span class="text-xs font-bold text-rose-600 uppercase tracking-wider">Langkah 1 dari 2</span>
          <h3 class="text-2xl font-extrabold text-stone-900 mt-1">Review Pilihan Undangan & Paket</h3>
          <div v-if="existingInvitation" class="mt-2 p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800">
            <strong>Perpanjangan / Upgrade:</strong> Anda sedang memperpanjang paket untuk undangan <code>{{ existingInvitation.slug }}</code>.
          </div>
        </div>

        <div class="p-6 sm:p-8 space-y-8">
          <!-- 1. Pilihan Template -->
          <div>
            <label class="block text-sm font-bold text-stone-900 mb-3">1. Pilih Desain Template</label>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <label
                v-for="tmpl in allTemplates"
                :key="tmpl.id"
                class="cursor-pointer"
              >
                <input
                  type="radio"
                  name="template_id"
                  :value="tmpl.id"
                  v-model="selectedTemplateId"
                  class="peer sr-only"
                />
                <div
                  class="p-4 rounded-2xl border-2 transition-all hover:border-stone-300"
                  :class="selectedTemplateId === tmpl.id ? 'border-rose-600 bg-rose-50/30' : 'border-stone-200'"
                >
                  <div class="aspect-video bg-stone-100 rounded-xl overflow-hidden mb-3">
                    <img :src="tmpl.thumbnail_path || '/templates/thumbnails/wedding_floral.svg'" :alt="tmpl.name" class="w-full h-full object-cover" />
                  </div>
                  <div class="font-bold text-stone-900 text-sm">{{ tmpl.name }}</div>
                  <span class="text-[11px] text-stone-500 capitalize">{{ tmpl.category }} • {{ tmpl.style }}</span>
                </div>
              </label>
            </div>
          </div>

          <!-- 2. Pilihan Paket -->
          <div>
            <label class="block text-sm font-bold text-stone-900 mb-3">2. Pilih Paket Layanan</label>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <label
                v-for="p in allPlans"
                :key="p.id"
                class="cursor-pointer"
              >
                <input
                  type="radio"
                  name="plan_id"
                  :value="p.id"
                  v-model="selectedPlanId"
                  class="peer sr-only"
                />
                <div
                  class="p-5 rounded-2xl border-2 transition-all hover:border-stone-300 h-full flex flex-col justify-between"
                  :class="selectedPlanId === p.id ? 'border-rose-600 bg-rose-50/30' : 'border-stone-200'"
                >
                  <div>
                    <div class="font-extrabold text-stone-900 text-base">{{ p.name }}</div>
                    <div class="text-2xl font-extrabold text-rose-600 my-2">Rp {{ Number(p.price).toLocaleString('id-ID') }}</div>
                    <span class="inline-block px-2.5 py-0.5 rounded-full bg-stone-100 text-stone-600 text-xs font-semibold mb-3">Aktif {{ p.duration_days }} hari</span>
                    <ul class="text-xs text-stone-600 space-y-1.5">
                      <li>✓ {{ p.max_events }} Rangkaian Acara</li>
                      <li>✓ Maks. {{ p.max_photos }} Foto Galeri</li>
                      <li>✓ RSVP & Amplop Digital</li>
                    </ul>
                  </div>
                </div>
              </label>
            </div>
          </div>

          <!-- Ringkasan Biaya -->
          <div class="bg-stone-50 rounded-2xl p-6 border border-stone-200">
            <h4 class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-4">Rincian Pembayaran</h4>
            <div class="flex items-center justify-between text-sm py-1">
              <span class="text-stone-600">Model Pembayaran</span>
              <span class="font-semibold text-stone-900">Sekali Bayar (Tanpa Langganan)</span>
            </div>
            <div class="flex items-center justify-between text-sm py-1">
              <span class="text-stone-600">Metode Pembayaran</span>
              <span class="font-semibold text-stone-900">Midtrans Snap (QRIS, VA Bank, E-Wallet)</span>
            </div>
            <div class="border-t border-stone-200 my-3"></div>
            <div class="flex items-center justify-between">
              <span class="font-bold text-stone-900">Total Pembayaran</span>
              <span class="font-extrabold text-2xl text-rose-600">
                {{ selectedPlanPrice === 0 ? 'GRATIS (Promo)' : `Rp ${selectedPlanPrice.toLocaleString('id-ID')}` }}
              </span>
            </div>
          </div>
        </div>

        <div class="p-6 sm:p-8 bg-stone-50 border-t border-stone-100 flex items-center justify-between">
          <router-link to="/" class="text-xs font-bold text-stone-500 hover:text-stone-700">← Batal</router-link>
          <button
            type="submit"
            :disabled="submitting"
            class="bg-rose-600 hover:bg-rose-700 text-white font-bold text-sm px-8 py-3.5 rounded-2xl shadow-lg shadow-rose-600/30 transition-all transform hover:-translate-y-0.5"
            :class="selectedPlanPrice === 0 ? 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-600/30' : ''"
          >
            <span v-if="submitting">Memproses Undangan...</span>
            <span v-else>{{ selectedPlanPrice === 0 ? 'Mulai Buat Undangan (Gratis Promo) →' : 'Lanjut ke Pembayaran Snap →' }}</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '../../api/client';
import { useAuthStore } from '../../stores/auth';
import { alertError } from '../../utils/alert';

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();

const allTemplates = ref([]);
const allPlans = ref([]);
const existingInvitation = ref(null);
const selectedTemplateId = ref(null);
const selectedPlanId = ref(null);
const loading = ref(true);
const submitting = ref(false);

const selectedPlanPrice = computed(() => {
  const p = allPlans.value.find((plan) => plan.id === selectedPlanId.value);
  return p ? Number(p.price) : 0;
});

async function loadOptions() {
  loading.value = true;
  try {
    const res = await api.get('/checkout/select', {
      params: {
        template_id: route.query.template_id,
        plan_code: route.query.plan_code,
        invitation_id: route.query.invitation_id,
      },
    });

    allTemplates.value = res.data.allTemplates || [];
    allPlans.value = res.data.allPlans || [];
    existingInvitation.value = res.data.existingInvitation;

    selectedTemplateId.value = res.data.template?.id || allTemplates.value[0]?.id;
    selectedPlanId.value = res.data.plan?.id || allPlans.value[0]?.id;
  } catch (err) {
    console.error('Failed to load checkout options:', err);
  } finally {
    loading.value = false;
  }
}

async function handleSubmit() {
  if (!authStore.isAuthenticated) {
    router.push({
      path: '/login',
      query: { redirect: route.fullPath },
    });
    return;
  }

  submitting.value = true;
  try {
    const payload = {
      template_id: selectedTemplateId.value,
      plan_id: selectedPlanId.value,
      invitation_id: existingInvitation.value ? existingInvitation.value.id : null,
    };

    const res = await api.post('/checkout/pay', payload);
    if (res.data.is_free || selectedPlanPrice.value === 0) {
      const invId = res.data.invitation_id || res.data.transaction?.invitation_id;
      router.push(`/editor/${invId}`);
    } else {
      const transId = res.data.transaction?.id || res.data.transaction_id;
      router.push(`/checkout/pay/${transId}`);
    }
  } catch (err) {
    await alertError('Gagal Memulai Checkout', err.response?.data?.message || err.message || 'Gagal memulai checkout.');
  } finally {
    submitting.value = false;
  }
}

onMounted(() => {
  loadOptions();
});
</script>
