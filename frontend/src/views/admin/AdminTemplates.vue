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
              Manajemen Template Undangan
            </h2>
          </div>
          <span class="px-3 py-1 bg-rose-100 text-rose-800 text-xs font-bold rounded-full">Admin Mode</span>
        </div>
      </div>
    </header>

    <div v-if="loading" class="py-20 text-center text-stone-400 text-sm">
      Memuat template undangan...
    </div>

    <div v-else class="py-10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <div v-if="statusMessage" class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold rounded-2xl flex items-center justify-between">
          <span>{{ statusMessage }}</span>
          <button @click="statusMessage = null" class="text-emerald-600 font-bold hover:text-emerald-800">×</button>
        </div>

        <div class="bg-white rounded-3xl border border-stone-200 shadow-sm p-6 sm:p-8">
          <div class="flex items-center justify-between mb-6">
            <div>
              <h3 class="text-base font-extrabold text-stone-900">Koleksi Desain Template Undangan</h3>
              <p class="text-xs text-stone-500">Kelola template desain dan gambar thumbnail.</p>
            </div>

            <button type="button" @click="showAddModal = true"
                    class="bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-md">
              + Tambah Template Baru
            </button>
          </div>

          <!-- MODAL TAMBAH TEMPLATE -->
          <div v-if="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-stone-900/50 p-4">
            <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl">
              <h4 class="text-base font-bold text-stone-900 mb-4">Unggah Template Desain Baru</h4>
              <form @submit.prevent="createTemplate" class="space-y-4">
                <div>
                  <label class="block text-xs font-bold text-stone-700 mb-1">Nama Template *</label>
                  <input type="text" v-model="newTemplate.name" required class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200 focus:ring-1 focus:ring-rose-500" placeholder="Contoh: Royal Gold Wedding">
                </div>

                <div class="grid grid-cols-2 gap-3">
                  <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">Kategori *</label>
                    <select v-model="newTemplate.category" required class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200 focus:ring-1 focus:ring-rose-500">
                      <option value="wedding">Pernikahan (Wedding)</option>
                      <option value="khitan">Khitanan (Khitan)</option>
                      <option value="general">Acara Umum (General)</option>
                    </select>
                  </div>
                  <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">Gaya / Style *</label>
                    <input type="text" v-model="newTemplate.style" required class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200 focus:ring-1 focus:ring-rose-500" placeholder="classic, floral, modern, etc.">
                  </div>
                </div>

                <div>
                  <label class="block text-xs font-bold text-stone-700 mb-1">Upload Gambar Thumbnail</label>
                  <input type="file" ref="thumbnailFileInput" accept="image/*" class="w-full text-xs text-stone-500">
                  <p class="text-[11px] text-stone-400 mt-1">Format: JPG, PNG, WEBP, SVG. Maks 2MB.</p>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                  <button type="button" @click="showAddModal = false" class="px-4 py-2 text-xs font-bold text-stone-500">Batal</button>
                  <button type="submit" :disabled="submitting" class="bg-rose-600 text-white text-xs font-bold px-5 py-2.5 rounded-xl shadow-md disabled:opacity-50">
                    <span v-if="submitting">Menyimpan...</span>
                    <span v-else>Simpan & Terbitkan</span>
                  </button>
                </div>
              </form>
            </div>
          </div>

          <!-- DAFTAR TEMPLATE CARDS -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div v-for="t in templates" :key="t.id"
                 class="bg-stone-50 rounded-2xl border border-stone-200 overflow-hidden flex flex-col justify-between">
              <div>
                <div class="aspect-video bg-stone-200 relative overflow-hidden">
                  <img :src="t.thumbnail_path || '/templates/wedding_floral.svg'" class="w-full h-full object-cover">
                  <span class="absolute top-3 left-3 px-2 py-0.5 rounded-full text-[10px] font-bold text-white"
                        :class="t.category === 'wedding' ? 'bg-rose-600' : (t.category === 'khitan' ? 'bg-sky-600' : 'bg-emerald-600')">
                    {{ (t.category || '').toUpperCase() }}
                  </span>
                </div>
                <div class="p-4">
                  <h4 class="font-bold text-stone-900 text-base">{{ t.name }}</h4>
                  <p class="text-xs text-stone-500 font-mono mt-1">View: {{ t.blade_view }}</p>
                  <div class="mt-3 flex items-center justify-between text-xs text-stone-600">
                    <span>Digunakan: <strong>{{ t._count?.invitations || 0 }}x</strong></span>
                    <span class="font-bold" :class="t.is_active ? 'text-emerald-600' : 'text-stone-400'">
                      {{ t.is_active ? '● Aktif' : 'Non-aktif' }}
                    </span>
                  </div>
                </div>
              </div>

              <div class="p-4 pt-0 border-t border-stone-200/60 mt-3 flex justify-between items-center">
                <button type="button" @click="deleteTemplate(t.id)"
                        class="text-rose-600 hover:text-rose-800 text-xs font-bold"
                        :disabled="t._count?.invitations > 0"
                        :title="t._count?.invitations > 0 ? 'Sedang digunakan' : ''">
                  Hapus
                </button>
              </div>
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
import { alertSuccess, alertError, alertConfirm } from '../../utils/alert';

const loading = ref(true);
const submitting = ref(false);
const statusMessage = ref(null);
const templates = ref([]);

const showAddModal = ref(false);
const thumbnailFileInput = ref(null);
const newTemplate = ref({ name: '', category: 'wedding', style: 'classic' });

const fetchTemplates = async () => {
  try {
    loading.value = true;
    const res = await api.get('/admin/templates');
    templates.value = res.data.templates || [];
  } catch (err) {
    console.error('Failed to load templates:', err);
  } finally {
    loading.value = false;
  }
};

const createTemplate = async () => {
  try {
    submitting.value = true;
    const body = new FormData();
    body.append('name', newTemplate.value.name);
    body.append('category', newTemplate.value.category);
    body.append('style', newTemplate.value.style);

    if (thumbnailFileInput.value?.files?.[0]) {
      body.append('thumbnail', thumbnailFileInput.value.files[0]);
    }

    await api.post('/admin/templates', body, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });

    showAddModal.value = false;
    newTemplate.value = { name: '', category: 'wedding', style: 'classic' };
    await alertSuccess('Berhasil!', 'Template baru berhasil ditambahkan.');
    await fetchTemplates();
  } catch (err) {
    await alertError('Gagal Menambahkan', err.response?.data?.message || 'Gagal menambahkan template');
  } finally {
    submitting.value = false;
  }
};

const deleteTemplate = async (id) => {
  const confirmed = await alertConfirm('Hapus Template?', 'Apakah Anda yakin ingin menghapus template ini?');
  if (!confirmed) return;
  try {
    await api.delete(`/admin/templates/${id}`);
    await alertSuccess('Terhapus!', 'Template berhasil dihapus.');
    await fetchTemplates();
  } catch (err) {
    await alertError('Gagal Menghapus', err.response?.data?.message || 'Gagal menghapus template');
  }
};

onMounted(() => {
  fetchTemplates();
});
</script>
