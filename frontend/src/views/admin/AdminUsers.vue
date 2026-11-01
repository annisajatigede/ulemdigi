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
              Manajemen Pengguna (Users)
            </h2>
          </div>
          <span class="px-3 py-1 bg-rose-100 text-rose-800 text-xs font-bold rounded-full">Admin Mode</span>
        </div>
      </div>
    </header>

    <div v-if="loading" class="py-20 text-center text-stone-400 text-sm">
      Memuat data pengguna...
    </div>

    <div v-else class="py-10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <div v-if="statusMessage" class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold rounded-2xl flex items-center justify-between">
          <span>{{ statusMessage }}</span>
          <button @click="statusMessage = null" class="text-emerald-600 font-bold hover:text-emerald-800">×</button>
        </div>

        <div v-if="errorMessage" class="p-4 bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm font-semibold rounded-2xl flex items-center justify-between">
          <span>{{ errorMessage }}</span>
          <button @click="errorMessage = null" class="text-rose-600 font-bold hover:text-rose-800">×</button>
        </div>

        <div class="bg-white rounded-3xl border border-stone-200 shadow-sm p-6 sm:p-8">
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div class="flex flex-wrap gap-2">
              <input type="text" v-model="searchQuery" @keyup.enter="applyFilter" placeholder="Cari nama atau email..."
                     class="text-xs px-3.5 py-2 rounded-xl border border-stone-200 w-64 focus:ring-1 focus:ring-rose-500">
              <select v-model="roleFilter" @change="applyFilter" class="text-xs px-3 py-2 rounded-xl border border-stone-200 focus:ring-1 focus:ring-rose-500">
                <option value="">Semua Role</option>
                <option value="user">User</option>
                <option value="admin">Admin</option>
              </select>
              <button type="button" @click="applyFilter" class="bg-stone-900 text-white text-xs font-bold px-4 py-2 rounded-xl">Filter</button>
              <button v-if="searchQuery || roleFilter" type="button" @click="resetFilter" class="text-xs text-rose-600 hover:underline self-center ml-2">Reset</button>
            </div>

            <button type="button" @click="showAddModal = true"
                    class="bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-md self-start">
              + Tambah User
            </button>
          </div>

          <!-- MODAL TAMBAH USER -->
          <div v-if="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-stone-900/50 p-4">
            <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl">
              <h4 class="text-base font-bold text-stone-900 mb-4">Tambah Pengguna Baru</h4>
              <form @submit.prevent="createUser" class="space-y-4">
                <div>
                  <label class="block text-xs font-bold text-stone-700 mb-1">Nama Lengkap *</label>
                  <input type="text" v-model="newUser.name" required class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200 focus:ring-1 focus:ring-rose-500">
                </div>
                <div>
                  <label class="block text-xs font-bold text-stone-700 mb-1">Email *</label>
                  <input type="email" v-model="newUser.email" required class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200 focus:ring-1 focus:ring-rose-500">
                </div>
                <div>
                  <label class="block text-xs font-bold text-stone-700 mb-1">Password *</label>
                  <input type="password" v-model="newUser.password" required minlength="8" class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200 focus:ring-1 focus:ring-rose-500">
                </div>
                <div>
                  <label class="block text-xs font-bold text-stone-700 mb-1">Role *</label>
                  <select v-model="newUser.role" class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200 focus:ring-1 focus:ring-rose-500">
                    <option value="user">User Biasa</option>
                    <option value="admin">Administrator</option>
                  </select>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                  <button type="button" @click="showAddModal = false" class="px-4 py-2 text-xs font-bold text-stone-500">Batal</button>
                  <button type="submit" :disabled="submitting" class="bg-rose-600 text-white text-xs font-bold px-5 py-2.5 rounded-xl disabled:opacity-50">
                    <span v-if="submitting">Menyimpan...</span>
                    <span v-else>Simpan</span>
                  </button>
                </div>
              </form>
            </div>
          </div>

          <!-- TABEL USERS -->
          <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
              <thead class="bg-stone-50 text-stone-500 uppercase tracking-wider border-b border-stone-200">
                <tr>
                  <th class="py-3 px-4">Nama</th>
                  <th class="py-3 px-4">Email</th>
                  <th class="py-3 px-4">Role</th>
                  <th class="py-3 px-4">Undangan</th>
                  <th class="py-3 px-4">Terdaftar</th>
                  <th class="py-3 px-4 text-right">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-stone-100">
                <tr v-for="u in users" :key="u.id">
                  <td class="py-3 px-4 font-bold text-stone-900">{{ u.name }}</td>
                  <td class="py-3 px-4 text-stone-600">{{ u.email }}</td>
                  <td class="py-3 px-4">
                    <span class="px-2 py-0.5 rounded-full font-bold text-[10px]"
                          :class="u.role === 'admin' ? 'bg-rose-100 text-rose-800' : 'bg-stone-100 text-stone-700'">
                      {{ (u.role || 'user').toUpperCase() }}
                    </span>
                  </td>
                  <td class="py-3 px-4 font-semibold text-stone-700">{{ u._count?.invitations || 0 }} Undangan</td>
                  <td class="py-3 px-4 text-stone-500">{{ formatDate(u.created_at) }}</td>
                  <td class="py-3 px-4 text-right">
                    <button v-if="authStore.user?.id !== u.id"
                            @click="deleteUser(u.id)"
                            class="text-rose-600 hover:text-rose-800 font-bold">
                      Hapus
                    </button>
                    <span v-else class="text-stone-400 italic">Akun Anda</span>
                  </td>
                </tr>
                <tr v-if="users.length === 0">
                  <td colspan="6" class="py-4 text-center text-stone-400">Tidak ada data pengguna.</td>
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
import { useAuthStore } from '../../stores/auth';

const authStore = useAuthStore();

const loading = ref(true);
const submitting = ref(false);
const statusMessage = ref(null);
const errorMessage = ref(null);
const users = ref([]);

const searchQuery = ref('');
const roleFilter = ref('');

const showAddModal = ref(false);
const newUser = ref({ name: '', email: '', password: '', role: 'user' });

const formatDate = (dateStr) => {
  if (!dateStr) return '-';
  const d = new Date(dateStr);
  return d.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' });
};

const fetchUsers = async () => {
  try {
    loading.value = true;
    const res = await api.get('/admin/users', {
      params: {
        q: searchQuery.value || undefined,
        role: roleFilter.value || undefined
      }
    });
    users.value = res.data.users || [];
  } catch (err) {
    errorMessage.value = 'Gagal memuat data pengguna.';
  } finally {
    loading.value = false;
  }
};

const applyFilter = () => fetchUsers();
const resetFilter = () => {
  searchQuery.value = '';
  roleFilter.value = '';
  fetchUsers();
};

const createUser = async () => {
  try {
    submitting.value = true;
    await api.post('/admin/users', newUser.value);
    showAddModal.value = false;
    newUser.value = { name: '', email: '', password: '', role: 'user' };
    statusMessage.value = 'Pengguna berhasil ditambahkan.';
    await fetchUsers();
  } catch (err) {
    alert(err.response?.data?.message || 'Gagal menambahkan pengguna');
  } finally {
    submitting.value = false;
  }
};

const deleteUser = async (id) => {
  if (!confirm('Hapus pengguna ini? Semua data undangannya akan terhapus.')) return;
  try {
    await api.delete(`/admin/users/${id}`);
    statusMessage.value = 'Pengguna berhasil dihapus.';
    await fetchUsers();
  } catch (err) {
    alert(err.response?.data?.message || 'Gagal menghapus pengguna');
  }
};

onMounted(() => {
  fetchUsers();
});
</script>
