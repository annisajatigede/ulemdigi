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

        <form @submit.prevent="saveSettings" class="bg-white rounded-3xl border border-stone-200 shadow-sm p-6 sm:p-8 space-y-8">

          <!-- 1. Identitas Situs & SEO -->
          <div>
            <h3 class="text-base font-extrabold text-stone-900 mb-1">Identitas Situs & Informasi Meta</h3>
            <p class="text-xs text-stone-500 mb-4">Pengaturan nama platform, logo, favicon, deskripsi SEO, dan kontak.</p>

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
                         class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-stone-200" placeholder="support@forkoway.com">
                </div>
              </div>

              <!-- Upload Logo & Favicon dengan Live Preview -->
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4 border-t border-stone-100">
                
                <!-- Logo Upload & Preview -->
                <div class="space-y-2">
                  <label class="block text-xs font-bold text-stone-800">
                    Logo Platform
                    <span class="text-[11px] font-normal text-stone-500 block">Menggantikan teks "Forkoway.com" di Navbar & Footer jika diisi.</span>
                  </label>

                  <div class="p-4 bg-stone-50 border border-stone-200 rounded-2xl flex flex-col items-center justify-center min-h-[110px] gap-2">
                    <img v-if="logoPreview || settings.logo_url" 
                         :src="logoPreview || settings.logo_url" 
                         alt="Preview Logo" 
                         class="max-h-12 max-w-[200px] object-contain" />
                    <span v-else class="text-xs text-stone-400 italic">Belum ada logo (menggunakan teks Forkoway.com)</span>

                    <div v-if="logoPreview || settings.logo_url" class="flex gap-2">
                      <button type="button" @click="removeLogo" class="text-[11px] font-bold text-rose-600 hover:text-rose-800 underline">
                        Hapus Logo (Gunakan Teks)
                      </button>
                    </div>
                  </div>

                  <input type="file" ref="logoFileInput" accept="image/*" @change="onLogoChange" class="w-full text-xs text-stone-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-stone-900 file:text-white hover:file:bg-stone-800 cursor-pointer">
                </div>

                <!-- Favicon Upload & Preview -->
                <div class="space-y-2">
                  <label class="block text-xs font-bold text-stone-800">
                    Icon Favicon (.png / .ico)
                    <span class="text-[11px] font-normal text-stone-500 block">Icon tab browser aplikasi Forkoway.com.</span>
                  </label>

                  <div class="p-4 bg-stone-50 border border-stone-200 rounded-2xl flex flex-col items-center justify-center min-h-[110px] gap-2">
                    <div class="flex items-center gap-2">
                      <img v-if="faviconPreview || settings.favicon_url" 
                           :src="faviconPreview || settings.favicon_url" 
                           alt="Preview Favicon" 
                           class="w-8 h-8 rounded object-contain shadow-sm border border-stone-200 bg-white p-0.5" />
                      <span v-else class="text-xs text-stone-400 italic">Favicon default bawaan</span>
                    </div>

                    <div v-if="faviconPreview || settings.favicon_url" class="flex gap-2">
                      <button type="button" @click="removeFavicon" class="text-[11px] font-bold text-rose-600 hover:text-rose-800 underline">
                        Hapus Favicon (Default)
                      </button>
                    </div>
                  </div>

                  <input type="file" ref="faviconFileInput" accept="image/*" @change="onFaviconChange" class="w-full text-xs text-stone-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-stone-900 file:text-white hover:file:bg-stone-800 cursor-pointer">
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
import { alertSuccess, alertError } from '../../utils/alert';
import { processImageToBase64 } from '../../utils/image';

const loading = ref(true);
const saving = ref(false);
const logoFileInput = ref(null);
const faviconFileInput = ref(null);

const logoPreview = ref(null);
const faviconPreview = ref(null);

const settings = ref({
  site_name: 'Forkoway.com',
  site_tagline: 'Platform Undangan Digital Modern & Elegan',
  site_description: 'Buat undangan digital pernikahan dan khitanan kekinian.',
  contact_whatsapp: '6281234567890',
  contact_email: 'support@forkoway.com',
  logo_url: '',
  favicon_url: '',
  midtrans_server_key: '',
  midtrans_client_key: '',
  midtrans_is_production: '0'
});

const webhookUrl = computed(() => `${window.location.origin}/api/payment/midtrans/webhook`);

const onLogoChange = async (e) => {
  const file = e.target.files?.[0];
  if (file) {
    try {
      logoPreview.value = await processImageToBase64(file, 400, 120);
    } catch (err) {
      console.warn('Failed to preview logo:', err);
    }
  }
};

const onFaviconChange = async (e) => {
  const file = e.target.files?.[0];
  if (file) {
    try {
      faviconPreview.value = await processImageToBase64(file, 64, 64);
    } catch (err) {
      console.warn('Failed to preview favicon:', err);
    }
  }
};

const removeLogo = () => {
  logoPreview.value = '';
  settings.value.logo_url = '';
  if (logoFileInput.value) logoFileInput.value.value = '';
};

const removeFavicon = () => {
  faviconPreview.value = '';
  settings.value.favicon_url = '';
  if (faviconFileInput.value) faviconFileInput.value.value = '';
};

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

    // Process logo if new file chosen
    let finalLogoUrl = settings.value.logo_url || '';
    if (logoPreview.value !== null) {
      finalLogoUrl = logoPreview.value;
    }

    // Process favicon if new file chosen
    let finalFaviconUrl = settings.value.favicon_url || '';
    if (faviconPreview.value !== null) {
      finalFaviconUrl = faviconPreview.value;
    }

    const payload = {
      ...settings.value,
      logo_url: finalLogoUrl,
      favicon_url: finalFaviconUrl
    };

    const res = await api.post('/admin/settings', payload);

    settings.value.logo_url = finalLogoUrl;
    settings.value.favicon_url = finalFaviconUrl;
    logoPreview.value = null;
    faviconPreview.value = null;

    // Dynamically update favicon in document head
    if (finalFaviconUrl) {
      let link = document.querySelector("link[rel~='icon']");
      if (!link) {
        link = document.createElement('link');
        link.rel = 'icon';
        document.head.appendChild(link);
      }
      link.href = finalFaviconUrl;
    }

    // Dispatch global event for instant Navbar/Footer reactive update
    window.dispatchEvent(new CustomEvent('settings-updated', { detail: payload }));

    await alertSuccess('Pengaturan Disimpan!', res.data?.message || 'Logo, Favicon, dan informasi platform berhasil diperbarui.');
  } catch (err) {
    await alertError('Gagal Menyimpan', err.response?.data?.message || err.message || 'Terjadi kesalahan saat menyimpan pengaturan.');
  } finally {
    saving.value = false;
  }
};

onMounted(() => {
  fetchSettings();
});
</script>
