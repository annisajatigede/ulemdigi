<template>
  <nav class="bg-white border-b border-gray-100 sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <div class="flex items-center">
          <!-- Logo -->
          <div class="shrink-0 flex items-center">
            <router-link to="/" class="flex items-center gap-2 group">
              <img
                v-if="siteSettings.logo_url"
                :src="siteSettings.logo_url"
                :alt="siteSettings.site_name || 'Forkoway.com'"
                class="h-9 w-auto max-w-[190px] object-contain transition-transform group-hover:scale-105"
              />
              <template v-else>
                <span class="text-2xl">💍</span>
                <span class="font-black text-xl tracking-tight text-stone-900 group-hover:text-rose-600 transition-colors">
                  Forkoway<span class="text-rose-600">.com</span>
                </span>
              </template>
            </router-link>
          </div>

          <!-- Navigation Links -->
          <div class="hidden space-x-6 sm:-my-px sm:ms-10 sm:flex items-center text-sm font-semibold">
            <router-link
              v-if="authStore.isAuthenticated"
              to="/dashboard"
              class="transition-colors pb-1 border-b-2"
              :class="$route.name === 'dashboard' ? 'border-rose-600 text-stone-900 font-bold' : 'border-transparent text-stone-500 hover:text-stone-800'"
            >
              Dashboard
            </router-link>

            <router-link
              to="/"
              class="text-xs font-semibold text-stone-500 hover:text-stone-800"
            >
              Lihat Web ↗
            </router-link>

            <template v-if="authStore.isAdmin">
              <span class="text-stone-300">|</span>
              <router-link
                to="/admin"
                class="transition-colors pb-1 border-b-2"
                :class="$route.name === 'admin.dashboard' ? 'border-rose-600 text-stone-900 font-bold' : 'border-transparent text-stone-500 hover:text-stone-800'"
              >
                Admin
              </router-link>
              <router-link
                to="/admin/templates"
                class="transition-colors pb-1 border-b-2"
                :class="$route.name === 'admin.templates' ? 'border-rose-600 text-stone-900 font-bold' : 'border-transparent text-stone-500 hover:text-stone-800'"
              >
                Templates
              </router-link>
              <router-link
                to="/admin/musics"
                class="transition-colors pb-1 border-b-2"
                :class="$route.name === 'admin.musics' ? 'border-rose-600 text-stone-900 font-bold' : 'border-transparent text-stone-500 hover:text-stone-800'"
              >
                Musik
              </router-link>
              <router-link
                to="/admin/plans"
                class="transition-colors pb-1 border-b-2"
                :class="$route.name === 'admin.plans' ? 'border-rose-600 text-stone-900 font-bold' : 'border-transparent text-stone-500 hover:text-stone-800'"
              >
                Paket
              </router-link>
              <router-link
                to="/admin/transactions"
                class="transition-colors pb-1 border-b-2"
                :class="$route.name === 'admin.transactions' ? 'border-rose-600 text-stone-900 font-bold' : 'border-transparent text-stone-500 hover:text-stone-800'"
              >
                Transaksi
              </router-link>
              <router-link
                to="/admin/users"
                class="transition-colors pb-1 border-b-2"
                :class="$route.name === 'admin.users' ? 'border-rose-600 text-stone-900 font-bold' : 'border-transparent text-stone-500 hover:text-stone-800'"
              >
                Users
              </router-link>
              <router-link
                to="/admin/settings"
                class="transition-colors pb-1 border-b-2"
                :class="$route.name === 'admin.settings' ? 'border-rose-600 text-stone-900 font-bold' : 'border-transparent text-stone-500 hover:text-stone-800'"
              >
                Pengaturan
              </router-link>
            </template>
          </div>
        </div>

        <!-- Right Side: User Dropdown or Login Buttons -->
        <div class="hidden sm:flex sm:items-center sm:ms-6">
          <template v-if="authStore.isAuthenticated">
            <div class="relative" @click="dropdownOpen = !dropdownOpen">
              <button
                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150"
              >
                <div>{{ authStore.user?.name }}</div>
                <div class="ms-1">
                  <svg class="fill-current h-4 w-4" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                  </svg>
                </div>
              </button>

              <div
                v-if="dropdownOpen"
                @click.outside="dropdownOpen = false"
                class="absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-xl border border-stone-100 py-1 z-50"
              >
                <div class="px-4 py-2 border-b border-stone-100">
                  <p class="text-xs text-stone-400">Masuk sebagai:</p>
                  <p class="text-xs font-bold text-stone-800 truncate">{{ authStore.user?.email }}</p>
                </div>
                <router-link
                  to="/dashboard"
                  class="block px-4 py-2 text-xs text-stone-700 hover:bg-stone-50 font-medium"
                >
                  Dashboard Saya
                </router-link>
                <button
                  @click="handleLogout"
                  class="w-full text-left block px-4 py-2 text-xs text-rose-600 hover:bg-rose-50 font-bold"
                >
                  Keluar (Log Out)
                </button>
              </div>
            </div>
          </template>

          <template v-else>
            <div class="flex items-center gap-3">
              <router-link
                to="/login"
                class="text-xs font-bold text-stone-600 hover:text-stone-900 px-3 py-2 rounded-xl hover:bg-stone-100 transition-colors"
              >
                Masuk
              </router-link>
              <router-link
                to="/register"
                class="text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 px-4 py-2 rounded-xl shadow-md transition-colors"
              >
                Daftar Gratis
              </router-link>
            </div>
          </template>
        </div>

        <!-- Hamburger for Mobile -->
        <div class="-me-2 flex items-center sm:hidden">
          <button
            @click="mobileOpen = !mobileOpen"
            class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none"
          >
            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
              <path
                v-if="!mobileOpen"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16"
              />
              <path
                v-else
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M6 18L18 6M6 6l12 12"
              />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile menu -->
    <div v-if="mobileOpen" class="sm:hidden border-t border-stone-100 bg-white px-4 pt-2 pb-4 space-y-2 text-sm font-semibold">
      <router-link to="/" class="block py-2 text-stone-700" @click="mobileOpen = false">Lihat Web Utama</router-link>
      <template v-if="authStore.isAuthenticated">
        <router-link to="/dashboard" class="block py-2 text-stone-700" @click="mobileOpen = false">Dashboard Saya</router-link>
        <template v-if="authStore.isAdmin">
          <div class="pt-2 border-t border-stone-100 text-xs font-bold text-stone-400 uppercase">Panel Admin</div>
          <router-link to="/admin" class="block py-1.5 text-stone-700" @click="mobileOpen = false">Admin Dashboard</router-link>
          <router-link to="/admin/templates" class="block py-1.5 text-stone-700" @click="mobileOpen = false">Kelola Templates</router-link>
          <router-link to="/admin/musics" class="block py-1.5 text-stone-700" @click="mobileOpen = false">Pustaka Musik</router-link>
          <router-link to="/admin/plans" class="block py-1.5 text-stone-700" @click="mobileOpen = false">Paket & Harga</router-link>
          <router-link to="/admin/transactions" class="block py-1.5 text-stone-700" @click="mobileOpen = false">Data Transaksi</router-link>
          <router-link to="/admin/users" class="block py-1.5 text-stone-700" @click="mobileOpen = false">Pengguna</router-link>
          <router-link to="/admin/settings" class="block py-1.5 text-stone-700" @click="mobileOpen = false">Pengaturan Web</router-link>
        </template>
        <div class="pt-2 border-t border-stone-100">
          <button @click="handleLogout" class="w-full text-left py-2 text-rose-600 font-bold">
            Keluar (Log Out)
          </button>
        </div>
      </template>
      <template v-else>
        <div class="pt-2 border-t border-stone-100 flex flex-col gap-2">
          <router-link to="/login" class="text-center py-2 border border-stone-200 rounded-xl" @click="mobileOpen = false">Masuk</router-link>
          <router-link to="/register" class="text-center py-2 bg-rose-600 text-white rounded-xl font-bold" @click="mobileOpen = false">Daftar</router-link>
        </div>
      </template>
    </div>
  </nav>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import { getSiteSettings } from '../firebase/services/adminService';

const authStore = useAuthStore();
const router = useRouter();

const dropdownOpen = ref(false);
const mobileOpen = ref(false);
const siteSettings = ref({});

async function handleLogout() {
  dropdownOpen.value = false;
  mobileOpen.value = false;
  await authStore.logout();
  router.push('/login');
}

const applyFavicon = (url) => {
  if (!url) return;
  let link = document.querySelector("link[rel~='icon']");
  if (!link) {
    link = document.createElement('link');
    link.rel = 'icon';
    document.head.appendChild(link);
  }
  link.href = url;
};

onMounted(async () => {
  try {
    const s = await getSiteSettings();
    if (s) {
      siteSettings.value = s;
      if (s.favicon_url) applyFavicon(s.favicon_url);
    }
  } catch (e) {
    console.warn('Navbar load settings error:', e);
  }

  window.addEventListener('settings-updated', (e) => {
    if (e.detail) {
      siteSettings.value = { ...siteSettings.value, ...e.detail };
      if (e.detail.favicon_url) applyFavicon(e.detail.favicon_url);
    }
  });
});
</script>
