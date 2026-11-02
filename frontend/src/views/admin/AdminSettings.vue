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
              Pengaturan Situs & Payment Gateway
            </h2>
          </div>
          <span class="px-3 py-1 bg-rose-100 text-rose-800 text-xs font-bold rounded-full">Admin Mode</span>
        </div>
      </div>
    </header>

    <div v-if="loading" class="py-20 text-center text-stone-400 text-sm">
      Memuat pengaturan situs...
    </div>

    <div v-else class="py-10">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <div v-if="statusMessage" class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold rounded-2xl flex items-center justify-between">
          <span>{{ statusMessage }}</span>
          <button @click="statusMessage = null" class="text-emerald-600 font-bold hover:text-emerald-800">×</button>
        </div>

        <form @submit.prevent="saveSettings" class="bg-white rounded-3xl border border-stone-200 shadow-sm p-6 sm:p-8 space-y-8">

          <!-- 1. Identitas Situs & SEO -->
          <div>
            <h3 class="text-base font-extrabold text-stone-900 mb-1">Identitas Situs & Informasi Meta</h3>
            <p class="text-xs text-stone-500 mb-4">Pengaturan nama platform, deskripsi untuk SEO Google, dan kontak.</p>

            <div class="space-y-4">
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-bold text-stone-700 mb-1">Nama Situs *</label>
                  <input type="text" v-model="settings.site_name" required
                         class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-stone-200 focus:ring-1 focus:ring-rose-500">
                </div>
                <div>
                  <label class="block text-xs font-bold text-stone-700 mb-1">Tagline Platform *</label>
                  <input type="text" v-model="settings.site_tagline" required
                         class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-stone-200 focus:ring-1 focus:ring-rose-500">
                </div>
              </div>

              <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">Deskripsi Meta (SEO & Social Share) *</label>
                <textarea v-model="settings.site_description" rows="3" required
                          class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-stone-200 focus:ring-1 focus:ring-rose-500"></textarea>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-bold text-stone-700 mb-1">No. WhatsApp Bantuan</label>
                  <input type="text" v-model="settings.contact_whatsapp"
                         class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-stone-200" placeholder="6281234567890">
                </div>
                <div>
                  <label class="block text-xs font-bold text-stone-700 mb-1">Email Dukungan</label>
                  <input type="email" v-model="settings.contact_email"
                         class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-stone-200" placeholder="support@kawinan.id">
                </div>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <div>
                  <label class="block text-xs font-bold text-stone-700 mb-1">Upload Logo Platform</label>
                  <input type="file" ref="logoFileInput" accept="image/*" class="w-full text-xs text-stone-500">
                </div>
                <div>
                  <label class="block text-xs font-bold text-stone-700 mb-1">Upload Favicon (.ico / .png)</label>
                  <input type="file" ref="faviconFileInput" accept="image/*" class="w-full text-xs text-stone-500">
                </div>
              </div>
            </div>
          </div>

          <!-- 2. Pengaturan Payment Gateway Midtrans -->
          <div class="pt-6 border-t border-stone-200">
            <h3 class="text-base font-extrabold text-stone-900 mb-1">Konfigurasi Midtrans Snap Sandbox / Production</h3>
            <p class="text-xs text-stone-500 mb-4">Kredensial API Midtrans untuk checkout Snap dan verifikasi webhook.</p>

            <div class="space-y-4">
              <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">Midtrans Server Key</label>
                <input type="text" v-model="settings.midtrans_server_key"
                       class="w-full text-xs font-mono px-3.5 py-2.5 rounded-xl border border-stone-200">
              </div>

              <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">Midtrans Client Key</label>
                <input type="text" v-model="settings.midtrans_client_key"
                       class="w-full text-xs font-mono px-3.5 py-2.5 rounded-xl border border-stone-200">
              </div>

              <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">Mode Lingkungan Midtrans</label>
                <select v-model="settings.midtrans_is_production" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-stone-200">
                  <option value="0">Sandbox (Pengujian)</option>
                  <option value="1">Production (Live Pembayaran Riil)</option>
                </select>
              </div>

              <div class="p-4 bg-stone-50 rounded-xl border border-stone-200 text-xs text-stone-600">
                <span class="font-bold text-stone-800">URL Webhook Notification Midtrans:</span>
                <code class="block font-mono bg-white p-2 rounded border border-stone-200 text-rose-600 mt-1 select-all">
                  {{ webhookUrl }}
                </code>
                <p class="text-[11px] text-stone-400 mt-1">Masukkan URL ini ke dashboard Midtrans Anda di menu Settings → Configuration → Payment Notification URL.</p>
              </div>
            </div>
          </div>

          <div class="pt-6 border-t border-stone-200 flex justify-end">
            <button type="submit" :disabled="saving"
                    class="bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs sm:text-sm px-8 py-3 rounded-2xl shadow-lg shadow-rose-600/25 transition-all disabled:opacity-50">
              <span v-if="saving">Menyimpan...</span>
              <span v-else>Simpan Pengaturan</span>
            </button>
          </div>
        </form>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import api from '../../api/client';

const loading = ref(true);
const saving = ref(false);
const statusMessage = ref(null);
const logoFileInput = ref(null);
const faviconFileInput = ref(null);

const settings = ref({
  site_name: 'Forkoway.com',
  site_tagline: 'Platform Undangan Digital Modern & Elegan',
  site_description: 'Buat undangan digital pernikahan dan khitanan kekinian.',
  contact_whatsapp: '6281234567890',
  contact_email: 'support@forkoway.com',
  midtrans_server_key: '',
  midtrans_client_key: '',
  midtrans_is_production: '0'
});

const webhookUrl = computed(() => `${window.location.origin}/api/payment/midtrans/webhook`);

const fetchSettings = async () => {
  try {
    loading.value = true;
    const res = await api.get('/admin/settings');
    if (res.data.settings) {
      settings.value = { ...settings.value, ...res.data.settings };
    }
  } catch (err) {
    console.error('Failed to load settings:', err);
  } finally {
    loading.value = false;
  }
};

const saveSettings = async () => {
  try {
    saving.value = true;
    const body = new FormData();
    Object.keys(settings.value).forEach(k => {
      body.append(k, settings.value[k] || '');
    });

    if (logoFileInput.value?.files?.[0]) {
      body.append('logo', logoFileInput.value.files[0]);
    }
    if (faviconFileInput.value?.files?.[0]) {
      body.append('favicon', faviconFileInput.value.files[0]);
    }

    const res = await api.post('/admin/settings', body, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });

    statusMessage.value = res.data.message || 'Pengaturan situs berhasil diperbarui!';
  } catch (err) {
    alert(err.response?.data?.message || 'Gagal menyimpan pengaturan.');
  } finally {
    saving.value = false;
  }
};

onMounted(() => {
  fetchSettings();
});
</script>
