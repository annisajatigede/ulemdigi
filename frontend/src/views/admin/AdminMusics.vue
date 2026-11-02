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
              Pustaka Musik Latar Undangan
            </h2>
          </div>
          <span class="px-3 py-1 bg-rose-100 text-rose-800 text-xs font-bold rounded-full">Admin Mode</span>
        </div>
      </div>
    </header>

    <div v-if="loading" class="py-20 text-center text-stone-400 text-sm">
      Memuat pustaka musik...
    </div>

    <div v-else class="py-10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <div v-if="statusMessage" class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold rounded-2xl flex items-center justify-between">
          <span>{{ statusMessage }}</span>
          <button @click="statusMessage = null" class="text-emerald-600 font-bold hover:text-emerald-800">×</button>
        </div>

        <!-- STATS CARDS -->
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
          <div class="bg-white rounded-2xl border border-stone-200 p-4 shadow-sm">
            <div class="text-[11px] font-semibold text-stone-500 uppercase tracking-wider">Total Musik</div>
            <div class="text-2xl font-black text-stone-900 mt-1">{{ musics.length }}</div>
          </div>
          <div class="bg-white rounded-2xl border border-stone-200 p-4 shadow-sm">
            <div class="text-[11px] font-semibold text-stone-500 uppercase tracking-wider">Musik Aktif</div>
            <div class="text-2xl font-black text-emerald-600 mt-1">{{ activeCount }}</div>
          </div>
          <div class="bg-white rounded-2xl border border-stone-200 p-4 shadow-sm">
            <div class="text-[11px] font-semibold text-stone-500 uppercase tracking-wider">Pernikahan</div>
            <div class="text-2xl font-black text-rose-600 mt-1">{{ weddingCount }}</div>
          </div>
          <div class="bg-white rounded-2xl border border-stone-200 p-4 shadow-sm">
            <div class="text-[11px] font-semibold text-stone-500 uppercase tracking-wider">Khitanan</div>
            <div class="text-2xl font-black text-amber-600 mt-1">{{ khitanCount }}</div>
          </div>
          <div class="bg-white rounded-2xl border border-stone-200 p-4 shadow-sm col-span-2 sm:col-span-1">
            <div class="text-[11px] font-semibold text-stone-500 uppercase tracking-wider">Acara Umum</div>
            <div class="text-2xl font-black text-indigo-600 mt-1">{{ generalCount }}</div>
          </div>
        </div>

        <!-- MAIN CARD -->
        <div class="bg-white rounded-3xl border border-stone-200 shadow-sm p-6 sm:p-8">
          <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
            <div>
              <h3 class="text-base font-extrabold text-stone-900 flex items-center gap-2">
                <span>🎵</span>
                <span>Koleksi Musik Latar Rekomendasi</span>
              </h3>
              <p class="text-xs text-stone-500">Disediakan oleh Admin untuk langsung dipilih pengguna saat mendesain undangan.</p>
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto">
              <!-- Filter Kategori -->
              <div class="flex items-center bg-stone-100 p-1 rounded-xl text-xs">
                <button type="button" @click="categoryFilter = ''"
                        class="px-3 py-1.5 rounded-lg font-semibold transition-colors"
                        :class="!categoryFilter ? 'bg-white shadow text-stone-900' : 'text-stone-600 hover:text-stone-900'">
                  Semua
                </button>
                <button type="button" @click="categoryFilter = 'wedding'"
                        class="px-3 py-1.5 rounded-lg font-semibold transition-colors"
                        :class="categoryFilter === 'wedding' ? 'bg-white shadow text-stone-900' : 'text-stone-600 hover:text-stone-900'">
                  Wedding
                </button>
                <button type="button" @click="categoryFilter = 'khitan'"
                        class="px-3 py-1.5 rounded-lg font-semibold transition-colors"
                        :class="categoryFilter === 'khitan' ? 'bg-white shadow text-stone-900' : 'text-stone-600 hover:text-stone-900'">
                  Khitan
                </button>
                <button type="button" @click="categoryFilter = 'general'"
                        class="px-3 py-1.5 rounded-lg font-semibold transition-colors"
                        :class="categoryFilter === 'general' ? 'bg-white shadow text-stone-900' : 'text-stone-600 hover:text-stone-900'">
                  Umum
                </button>
              </div>

              <button type="button" @click="showAddModal = true"
                      class="bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-md transition-colors shrink-0">
                + Tambah Musik
              </button>
            </div>
          </div>

          <!-- MODAL TAMBAH MUSIK -->
          <div v-if="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-stone-900/50 p-4">
            <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl">
              <h4 class="text-base font-bold text-stone-900 mb-2">Tambah Musik Latar Baru</h4>
              <p class="text-xs text-stone-500 mb-4">Tambahkan lagu favorit untuk melengkapi pustaka undangan digital.</p>
              
              <form @submit.prevent="createMusic" class="space-y-4">
                <div>
                  <label class="block text-xs font-bold text-stone-700 mb-1">Judul Lagu / Musik *</label>
                  <input type="text" v-model="newMusic.title" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-stone-200" placeholder="Contoh: Beautiful in White">
                </div>
                <div>
                  <label class="block text-xs font-bold text-stone-700 mb-1">Artis / Penyanyi / Musisi</label>
                  <input type="text" v-model="newMusic.artist" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-stone-200" placeholder="Contoh: Shane Filan">
                </div>
                <div class="grid grid-cols-2 gap-3">
                  <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">Kategori Acara *</label>
                    <select v-model="newMusic.category" required class="w-full text-xs px-3 py-2.5 rounded-xl border border-stone-200">
                      <option value="wedding">Pernikahan (Wedding)</option>
                      <option value="khitan">Khitanan (Khitan)</option>
                      <option value="general">Acara Umum (General)</option>
                    </select>
                  </div>
                  <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">Urutan (Sort Order)</label>
                    <input type="number" v-model.number="newMusic.sort_order" min="0" class="w-full text-xs px-3 py-2.5 rounded-xl border border-stone-200">
                  </div>
                </div>

                <!-- Metode Input Audio: URL atau File -->
                <div class="pt-2 border-t border-stone-100">
                  <label class="block text-xs font-bold text-stone-800 mb-2">Metode Masukan Audio *</label>
                  <div class="grid grid-cols-2 gap-2 mb-3">
                    <button type="button" @click="audioSourceType = 'url'"
                            class="py-2 text-xs font-bold rounded-xl border transition-all text-center"
                            :class="audioSourceType === 'url' ? 'bg-rose-50 border-rose-500 text-rose-700 shadow-sm' : 'border-stone-200 text-stone-600 hover:bg-stone-50'">
                      🔗 Tautan URL MP3
                    </button>
                    <button type="button" @click="audioSourceType = 'file'"
                            class="py-2 text-xs font-bold rounded-xl border transition-all text-center"
                            :class="audioSourceType === 'file' ? 'bg-rose-50 border-rose-500 text-rose-700 shadow-sm' : 'border-stone-200 text-stone-600 hover:bg-stone-50'">
                      📁 Unggah Berkas MP3
                    </button>
                  </div>

                  <!-- 1. URL MODE -->
                  <div v-if="audioSourceType === 'url'" class="space-y-2">
                    <input type="url" v-model="newMusic.url"
                           class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-stone-200 font-mono"
                           placeholder="https://example.com/audio/lagu.mp3">
                    <p class="text-[11px] text-stone-400">Masukkan tautan langsung file MP3 dari hosting, Google Drive, Dropbox, atau CDN.</p>
                  </div>

                  <!-- 2. FILE UPLOAD MODE -->
                  <div v-else class="space-y-2">
                    <input type="file" ref="musicFileInput" accept="audio/*" class="w-full text-xs text-stone-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-stone-900 file:text-white hover:file:bg-stone-800 cursor-pointer">
                    <p class="text-[11px] text-stone-400">Format: MP3, WAV, OGG. Maksimal 15MB.</p>
                  </div>
                </div>

                <div class="flex justify-end gap-2 pt-4 border-t border-stone-100">
                  <button type="button" @click="showAddModal = false" class="px-4 py-2 text-xs font-bold text-stone-500 hover:text-stone-700">Batal</button>
                  <button type="submit" :disabled="submitting" class="bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold px-6 py-2.5 rounded-xl shadow-md disabled:opacity-50 transition-all">
                    <span v-if="submitting">Memproses...</span>
                    <span v-else>Simpan Musik</span>
                  </button>
                </div>
              </form>
            </div>
          </div>

          <!-- TABLE LISTING -->
          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="border-b border-stone-200 text-[11px] font-bold text-stone-400 uppercase tracking-wider">
                  <th class="py-3 px-4">Urutan</th>
                  <th class="py-3 px-4">Judul Lagu / Musik</th>
                  <th class="py-3 px-4">Kategori</th>
                  <th class="py-3 px-4">Putar Preview</th>
                  <th class="py-3 px-4">Status</th>
                  <th class="py-3 px-4 text-right">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-stone-100 text-xs">
                <tr v-for="m in filteredMusics" :key="m.id" class="hover:bg-stone-50/80 transition-colors">
                  <td class="py-4 px-4 font-mono text-stone-400 font-semibold">
                    #{{ m.sort_order }}
                  </td>
                  <td class="py-4 px-4">
                    <div class="font-bold text-stone-900">{{ m.title }}</div>
                    <div class="text-[11px] text-stone-500 font-medium">{{ m.artist || 'Tanpa Nama Artis' }}</div>
                    <div class="text-[10px] text-stone-400 font-mono mt-0.5 truncate max-w-xs">{{ m.file_path }}</div>
                  </td>
                  <td class="py-4 px-4">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider"
                          :class="m.category === 'wedding' ? 'bg-rose-50 text-rose-700 border border-rose-200' :
                                 (m.category === 'khitan' ? 'bg-amber-50 text-amber-700 border border-amber-200' :
                                 (m.category === 'general' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-stone-100 text-stone-700'))">
                      {{ m.category }}
                    </span>
                  </td>
                  <td class="py-4 px-4">
                    <audio controls preload="none" class="h-9 w-48 sm:w-64 rounded-lg">
                      <source :src="m.file_path" type="audio/mpeg">
                    </audio>
                  </td>
                  <td class="py-4 px-4">
                    <button type="button" @click="toggleActive(m.id)"
                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold transition-colors"
                            :class="m.is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100' : 'bg-stone-100 text-stone-500 hover:bg-stone-200'"
                            title="Klik untuk ubah status">
                      <span class="w-1.5 h-1.5 rounded-full" :class="m.is_active ? 'bg-emerald-500' : 'bg-stone-400'"></span>
                      {{ m.is_active ? 'Aktif' : 'Nonaktif' }}
                    </button>
                  </td>
                  <td class="py-4 px-4 text-right">
                    <button type="button" @click="deleteMusic(m.id)" class="text-rose-600 hover:text-rose-800 font-bold text-xs p-1">
                      Hapus
                    </button>
                  </td>
                </tr>
                <tr v-if="filteredMusics.length === 0">
                  <td colspan="6" class="py-8 text-center text-stone-400 text-xs">
                    Belum ada musik dalam kategori ini.
                  </td>
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
import { ref, computed, onMounted } from 'vue';
import api from '../../api/client';
import { alertSuccess, alertError, alertConfirm, notifySuccess } from '../../utils/alert';

const loading = ref(true);
const submitting = ref(false);
const statusMessage = ref(null);
const musics = ref([]);
const categoryFilter = ref('');

const showAddModal = ref(false);
const audioSourceType = ref('url');
const musicFileInput = ref(null);
const newMusic = ref({ title: '', artist: '', category: 'wedding', sort_order: 0, url: '' });

const activeCount = computed(() => musics.value.filter(m => m.is_active).length);
const weddingCount = computed(() => musics.value.filter(m => m.category === 'wedding').length);
const khitanCount = computed(() => musics.value.filter(m => m.category === 'khitan').length);
const generalCount = computed(() => musics.value.filter(m => m.category === 'general').length);

const filteredMusics = computed(() => {
  if (!categoryFilter.value) return musics.value;
  return musics.value.filter(m => m.category === categoryFilter.value);
});

const fetchMusics = async () => {
  try {
    loading.value = true;
    const res = await api.get('/admin/musics');
    musics.value = res.data.musics || [];
  } catch (err) {
    console.error('Failed to load musics:', err);
  } finally {
    loading.value = false;
  }
};

const createMusic = async () => {
  try {
    submitting.value = true;
    const body = new FormData();
    body.append('title', newMusic.value.title);
    body.append('artist', newMusic.value.artist);
    body.append('category', newMusic.value.category);
    body.append('sort_order', newMusic.value.sort_order);

    if (audioSourceType.value === 'url') {
      if (!newMusic.value.url) {
        await alertError('Wajib Diisi', 'Silakan masukkan tautan / URL file MP3.');
        submitting.value = false;
        return;
      }
      body.append('url', newMusic.value.url);
    } else {
      if (!musicFileInput.value?.files?.[0]) {
        await alertError('Wajib Diisi', 'Silakan pilih berkas audio MP3 untuk diunggah.');
        submitting.value = false;
        return;
      }
      body.append('music_file', musicFileInput.value.files[0]);
    }

    await api.post('/admin/musics', body, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });

    showAddModal.value = false;
    newMusic.value = { title: '', artist: '', category: 'wedding', sort_order: 0, url: '' };
    if (musicFileInput.value) musicFileInput.value.value = '';
    await alertSuccess('Berhasil!', 'Musik baru berhasil ditambahkan ke pustaka.');
    await fetchMusics();
  } catch (err) {
    await alertError('Gagal Menambahkan Musik', err.response?.data?.message || err.message || 'Gagal menambahkan musik');
  } finally {
    submitting.value = false;
  }
};

const toggleActive = async (id) => {
  try {
    await api.post(`/admin/musics/${id}/toggle`);
    notifySuccess('Status musik diperbarui');
    await fetchMusics();
  } catch (err) {
    await alertError('Gagal!', 'Gagal mengubah status musik');
  }
};

const deleteMusic = async (id) => {
  const confirmed = await alertConfirm('Hapus Musik?', 'Hapus musik ini dari pustaka?');
  if (!confirmed) return;
  try {
    await api.delete(`/admin/musics/${id}`);
    await alertSuccess('Terhapus!', 'Musik berhasil dihapus dari pustaka.');
    await fetchMusics();
  } catch (err) {
    await alertError('Gagal Menghapus', err.response?.data?.message || 'Gagal menghapus musik');
  }
};

onMounted(() => {
  fetchMusics();
});
</script>
