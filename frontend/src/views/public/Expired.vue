<template>
  <div class="bg-stone-50 min-h-screen flex items-center justify-center p-6 text-center antialiased">
    <div class="max-w-md w-full bg-white p-8 rounded-3xl shadow-xl border border-stone-200">
      <div class="w-16 h-16 bg-rose-100 text-rose-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
      </div>
      <h2 class="text-xl font-bold text-stone-800 mb-2">Masa Aktif Undangan Telah Berakhir</h2>
      <p class="text-xs text-stone-500 mb-6 leading-relaxed">
        Masa aktif publikasi undangan ini telah kedaluwarsa sesuai paket yang dipilih. Jika Anda pemilik undangan ini, Anda dapat memperpanjang masa aktif melalui dashboard akun Anda.
      </p>
      <div class="flex items-center justify-center gap-3">
        <router-link to="/dashboard" class="inline-block bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold px-5 py-2.5 rounded-xl shadow-md transition-colors">
          Buka Dashboard
        </router-link>
        <router-link to="/" class="inline-block bg-stone-100 hover:bg-stone-200 text-stone-700 text-xs font-semibold px-5 py-2.5 rounded-xl transition-colors">
          Ke Beranda
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  invitation: Object
});
</script>
