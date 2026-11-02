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
      <h2 class="text-xl font-extrabold text-stone-900 mb-2 text-center">Lupa Kata Sandi</h2>
      <p class="text-xs text-stone-500 mb-6 text-center leading-relaxed">
        Masukkan alamat email Anda yang terdaftar dan kami akan mengirimkan tautan untuk mengatur ulang kata sandi Anda.
      </p>

      <div v-if="successMessage" class="mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs rounded-xl font-semibold">
        {{ successMessage }}
      </div>

      <form @submit.prevent="handleSubmit" class="space-y-4">
        <div>
          <label class="block font-medium text-xs text-stone-700 mb-1" for="email">Alamat Email</label>
          <input
            id="email"
            v-model="email"
            type="email"
            required
            autofocus
            placeholder="nama@email.com"
            class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-stone-300 focus:border-rose-500 focus:ring-1 focus:ring-rose-500 shadow-sm"
          />
        </div>

        <div class="pt-2">
          <button
            type="submit"
            class="w-full py-2.5 px-4 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-md transition-all"
          >
            Kirim Tautan Reset Password
          </button>
        </div>

        <div class="text-center text-xs text-stone-500 pt-4 border-t border-stone-100">
          Ingat kata sandi Anda?
          <router-link to="/login" class="text-rose-600 font-bold hover:underline">
            Kembali ke Halaman Masuk
          </router-link>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const email = ref('');
const successMessage = ref('');

function handleSubmit() {
  successMessage.value = 'Jika email terdaftar, tautan reset password telah dikirim ke kotak masuk Anda.';
}
</script>
