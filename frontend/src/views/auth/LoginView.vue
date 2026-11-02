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

        <div class="relative my-3">
          <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-stone-200"></div></div>
          <div class="relative flex justify-center text-xs"><span class="px-2 bg-white text-stone-400">atau</span></div>
        </div>

        <button
          type="button"
          @click="handleGoogleLogin"
          :disabled="loading"
          class="w-full py-2.5 px-4 text-xs font-semibold text-stone-700 bg-white border border-stone-300 hover:bg-stone-50 rounded-xl shadow-sm transition-all flex items-center justify-center gap-2"
        >
          <svg class="w-4 h-4" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/></svg>
          <span>Masuk dengan Google</span>
        </button>

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
