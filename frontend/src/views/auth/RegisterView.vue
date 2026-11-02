<template>
  <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
    <div class="mb-4">
      <router-link to="/" class="flex items-center gap-2">
        <span class="text-3xl">💍</span>
        <span class="font-black text-2xl tracking-tight text-stone-900">
          Forkoway<span class="text-rose-600">.com</span>
        </span>
      </router-link>
    </div>

    <div class="w-full sm:max-w-md mt-2 px-6 py-8 bg-white shadow-md overflow-hidden sm:rounded-2xl border border-stone-200">
      <h2 class="text-xl font-extrabold text-stone-900 mb-6 text-center">Buat Akun Baru</h2>

      <div v-if="errorMessage" class="mb-4 p-3 bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded-xl font-semibold">
        {{ errorMessage }}
      </div>

      <form @submit.prevent="handleRegister" class="space-y-4">
        <!-- Name -->
        <div>
          <label class="block font-medium text-xs text-stone-700 mb-1" for="name">Nama Lengkap</label>
          <input
            id="name"
            v-model="name"
            type="text"
            required
            autofocus
            placeholder="Contoh: Budi Santoso"
            class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-stone-300 focus:border-rose-500 focus:ring-1 focus:ring-rose-500 shadow-sm"
          />
        </div>

        <!-- Email Address -->
        <div>
          <label class="block font-medium text-xs text-stone-700 mb-1" for="email">Alamat Email</label>
          <input
            id="email"
            v-model="email"
            type="email"
            required
            placeholder="nama@email.com"
            class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-stone-300 focus:border-rose-500 focus:ring-1 focus:ring-rose-500 shadow-sm"
          />
        </div>

        <!-- Password -->
        <div>
          <label class="block font-medium text-xs text-stone-700 mb-1" for="password">Kata Sandi (Min. 8 Karakter)</label>
          <input
            id="password"
            v-model="password"
            type="password"
            required
            placeholder="••••••••"
            class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-stone-300 focus:border-rose-500 focus:ring-1 focus:ring-rose-500 shadow-sm"
          />
        </div>

        <!-- Confirm Password -->
        <div>
          <label class="block font-medium text-xs text-stone-700 mb-1" for="password_confirmation">Konfirmasi Kata Sandi</label>
          <input
            id="password_confirmation"
            v-model="password_confirmation"
            type="password"
            required
            placeholder="••••••••"
            class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-stone-300 focus:border-rose-500 focus:ring-1 focus:ring-rose-500 shadow-sm"
          />
        </div>

        <div class="pt-2">
          <button
            type="submit"
            :disabled="loading"
            class="w-full py-2.5 px-4 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-md transition-all flex items-center justify-center gap-2"
          >
            <span v-if="loading">Membuat Akun...</span>
            <span v-else>Daftar Sekarang →</span>
          </button>
        </div>

        <div class="text-center text-xs text-stone-500 pt-4 border-t border-stone-100">
          Sudah memiliki akun?
          <router-link to="/login" class="text-rose-600 font-bold hover:underline">
            Masuk di Sini
          </router-link>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';

const authStore = useAuthStore();
const router = useRouter();

const name = ref('');
const email = ref('');
const password = ref('');
const password_confirmation = ref('');
const errorMessage = ref('');
const loading = ref(false);

async function handleRegister() {
  errorMessage.value = '';
  loading.value = true;
  try {
    await authStore.register({
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: password_confirmation.value,
    });
    router.push('/dashboard');
  } catch (err) {
    if (err.response?.data?.errors) {
      const errs = err.response.data.errors;
      errorMessage.value = Object.values(errs).flat().join(', ');
    } else {
      errorMessage.value = err.response?.data?.message || 'Gagal mendaftar.';
    }
  } finally {
    loading.value = false;
  }
}
</script>
