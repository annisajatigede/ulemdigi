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
      <h2 class="text-xl font-extrabold text-stone-900 mb-6 text-center">Masuk ke Akun Anda</h2>

      <div v-if="errorMessage" class="mb-4 p-3 bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded-xl font-semibold">
        {{ errorMessage }}
      </div>

      <form @submit.prevent="handleLogin" class="space-y-4">
        <!-- Email Address -->
        <div>
          <label class="block font-medium text-xs text-stone-700 mb-1" for="email">Alamat Email</label>
          <input
            id="email"
            v-model="email"
            type="email"
            required
            autofocus
            autocomplete="username"
            placeholder="nama@email.com"
            class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-stone-300 focus:border-rose-500 focus:ring-1 focus:ring-rose-500 shadow-sm"
          />
        </div>

        <!-- Password -->
        <div>
          <label class="block font-medium text-xs text-stone-700 mb-1" for="password">Kata Sandi</label>
          <input
            id="password"
            v-model="password"
            type="password"
            required
            autocomplete="current-password"
            placeholder="••••••••"
            class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-stone-300 focus:border-rose-500 focus:ring-1 focus:ring-rose-500 shadow-sm"
          />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between text-xs">
          <label for="remember_me" class="inline-flex items-center cursor-pointer">
            <input
              id="remember_me"
              type="checkbox"
              class="rounded border-gray-300 text-rose-600 shadow-sm focus:ring-rose-500"
            />
            <span class="ms-2 text-stone-600">Ingat Saya</span>
          </label>

          <router-link to="/forgot-password" class="text-rose-600 hover:text-rose-700 font-semibold">
            Lupa kata sandi?
          </router-link>
        </div>

        <div class="pt-2">
          <button
            type="submit"
            :disabled="loading"
            class="w-full py-2.5 px-4 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-md transition-all flex items-center justify-center gap-2"
          >
            <span v-if="loading">Memproses...</span>
            <span v-else>Masuk Sekarang →</span>
          </button>
        </div>

        <div class="text-center text-xs text-stone-500 pt-4 border-t border-stone-100">
          Belum punya akun?
          <router-link to="/register" class="text-rose-600 font-bold hover:underline">
            Daftar Gratis
          </router-link>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '../../stores/auth';

const authStore = useAuthStore();
const router = useRouter();
const route = useRoute();

const email = ref('');
const password = ref('');
const errorMessage = ref('');
const loading = ref(false);

async function handleLogin() {
  errorMessage.value = '';
  loading.value = true;
  try {
    const res = await authStore.login(email.value, password.value);
    const redirect = route.query.redirect;
    if (redirect) {
      router.push(redirect);
    } else if (res.user.role === 'admin') {
      router.push('/admin');
    } else {
      router.push('/dashboard');
    }
  } catch (err) {
    if (err.response?.data?.errors) {
      const errs = err.response.data.errors;
      errorMessage.value = Object.values(errs).flat().join(', ');
    } else {
      errorMessage.value = err.response?.data?.message || 'Gagal login. Periksa email dan password.';
    }
  } finally {
    loading.value = false;
  }
}
</script>
