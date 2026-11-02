<template>
  <div class="bg-stone-50 font-sans text-stone-800 antialiased selection:bg-rose-500 selection:text-white">

    <!-- HERO SECTION -->
    <header class="relative pt-16 pb-24 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto text-center overflow-hidden">
      <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-rose-100 text-rose-800 text-xs font-semibold mb-6">
        <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
        <span>Platform Undangan Digital Berbayar #1 di Indonesia</span>
      </div>

      <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-stone-900 tracking-tight max-w-4xl mx-auto leading-[1.15]">
        Bagikan Momen Bahagiamu dengan Undangan Online <span class="bg-gradient-to-r from-rose-600 via-amber-600 to-rose-600 bg-clip-text text-transparent">Elegan & Modern</span>
      </h1>

      <p class="mt-6 text-base sm:text-lg text-stone-600 max-w-2xl mx-auto leading-relaxed">
        Cocok untuk Pernikahan, Khitanan, dan Acara Spesial. Sekali bayar, aktif instan dengan Midtrans Snap, fitur RSVP real-time, amplop digital, dan personalisasi nama tamu.
      </p>

      <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
        <a href="#template" class="bg-rose-600 hover:bg-rose-700 text-white font-bold text-sm px-6 py-3.5 rounded-2xl shadow-xl shadow-rose-600/30 transition-all transform hover:-translate-y-0.5">
          Pilih Desain Undangan
        </a>
        <a href="#harga" class="bg-white hover:bg-stone-100 text-stone-800 border border-stone-200 font-bold text-sm px-6 py-3.5 rounded-2xl shadow-sm transition-all">
          Lihat Paket Harga
        </a>
      </div>

      <div class="mt-12 flex flex-wrap items-center justify-center gap-6 text-xs font-medium text-stone-500">
        <div class="flex items-center gap-1.5">
          <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
          <span>Aktif Otomatis Tanpa Konfirmasi</span>
        </div>
        <div class="flex items-center gap-1.5">
          <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
          <span>Responsif & Ringan di Mobile</span>
        </div>
        <div class="flex items-center gap-1.5">
          <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
          <span>Midtrans Snap Multi-Payment</span>
        </div>
      </div>
    </header>

    <!-- SECTION KATEGORI & FILTER TEMPLATE -->
    <section id="kategori" class="py-16 bg-white border-y border-stone-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-10">
          <span class="text-xs font-bold uppercase tracking-wider text-rose-600">Pilih Kategori Acara</span>
          <h2 class="text-3xl font-extrabold text-stone-900 mt-1">Desain Sesuai Kebutuhan Momen Anda</h2>
        </div>

        <!-- Category Pills Filter -->
        <div class="flex flex-wrap items-center justify-center gap-2 mb-12">
          <button
            @click="filterCategory('all')"
            class="px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-bold transition-all"
            :class="selectedCategory === 'all' ? 'bg-stone-900 text-white shadow-md' : 'bg-stone-100 hover:bg-stone-200 text-stone-700'"
          >
            Semua Kategori ({{ allTemplatesCount }})
          </button>
          <button
            @click="filterCategory('wedding')"
            class="px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-bold transition-all"
            :class="selectedCategory === 'wedding' ? 'bg-rose-600 text-white shadow-md' : 'bg-stone-100 hover:bg-stone-200 text-stone-700'"
          >
            💍 Pernikahan
          </button>
          <button
            @click="filterCategory('khitan')"
            class="px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-bold transition-all"
            :class="selectedCategory === 'khitan' ? 'bg-sky-600 text-white shadow-md' : 'bg-stone-100 hover:bg-stone-200 text-stone-700'"
          >
            🎈 Khitanan Anak
          </button>
          <button
            @click="filterCategory('general')"
            class="px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-bold transition-all"
            :class="selectedCategory === 'general' ? 'bg-emerald-600 text-white shadow-md' : 'bg-stone-100 hover:bg-stone-200 text-stone-700'"
          >
            🎉 Acara Umum / Syukuran
          </button>
        </div>

        <!-- TEMPLATE CARDS GRID -->
        <div id="template" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div
            v-for="tmpl in filteredTemplates"
            :key="tmpl.id"
            class="bg-white rounded-3xl border border-stone-200 overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col group"
          >
            <!-- Thumbnail Preview -->
            <div class="relative aspect-[16/10] bg-stone-100 overflow-hidden">
              <img
                :src="tmpl.thumbnail_path || '/templates/thumbnails/wedding_floral.svg'"
                :alt="tmpl.name"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
              />

              <!-- Category Badge -->
              <div class="absolute top-4 left-4">
                <span v-if="tmpl.category === 'wedding'" class="bg-rose-600 text-white text-[11px] font-bold px-3 py-1 rounded-full shadow">Pernikahan</span>
                <span v-else-if="tmpl.category === 'khitan'" class="bg-sky-600 text-white text-[11px] font-bold px-3 py-1 rounded-full shadow">Khitanan</span>
                <span v-else class="bg-emerald-600 text-white text-[11px] font-bold px-3 py-1 rounded-full shadow">Umum</span>
              </div>
            </div>

            <!-- Card Info -->
            <div class="p-6 flex-1 flex flex-col justify-between">
              <div>
                <h3 class="text-xl font-bold text-stone-900">{{ tmpl.name }}</h3>
                <p class="text-xs text-stone-500 mt-1 capitalize">Gaya: {{ tmpl.style }} • Mobile-First Responsive</p>
              </div>

              <div class="mt-6 pt-4 border-t border-stone-100 flex items-center gap-3">
                <router-link
                  :to="`/checkout/select?template_id=${tmpl.id}`"
                  class="flex-1 text-center bg-rose-600 hover:bg-rose-700 text-white text-xs sm:text-sm font-bold py-2.5 rounded-xl shadow-md shadow-rose-600/20 transition-all"
                >
                  Gunakan Desain Ini
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- SECTION PAKET HARGA -->
    <section id="harga" class="py-20 bg-stone-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16">
          <span class="text-xs font-bold uppercase tracking-wider text-rose-600">Pilihan Paket Sekali Bayar</span>
          <h2 class="text-3xl sm:text-4xl font-extrabold text-stone-900 mt-1">Transparan, Tanpa Biaya Tersembunyi</h2>
          <p class="text-xs sm:text-sm text-stone-500 mt-2">Pilih paket terbaik yang sesuai dengan kebutuhan acara istimewa Anda.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-stretch">
          <div
            v-for="plan in plans"
            :key="plan.id"
            class="bg-white rounded-3xl p-8 border-2 flex flex-col justify-between"
            :class="plan.code === 'standard' ? 'border-rose-500 shadow-2xl relative lg:scale-105 z-10' : 'border-stone-200 shadow-sm'"
          >
            <div
              v-if="plan.code === 'standard'"
              class="absolute -top-4 left-1/2 transform -translate-x-1/2 bg-gradient-to-r from-rose-500 to-amber-500 text-white text-[11px] font-extrabold uppercase px-4 py-1 rounded-full shadow-md"
            >
              Paling Populer ★
            </div>

            <div>
              <h3 class="text-xl font-extrabold text-stone-900">{{ plan.name }}</h3>
              <p class="text-xs text-stone-500 mt-1">Masa aktif {{ plan.duration_days }} hari penuh</p>

              <div class="mt-6 mb-8">
                <span class="text-4xl font-extrabold text-stone-900">Rp {{ formatPrice(plan.price) }}</span>
                <span class="text-xs text-stone-500 font-medium">/ acara</span>
              </div>

              <ul class="space-y-3.5 text-xs sm:text-sm text-stone-600">
                <li
                  v-for="(feat, idx) in plan.features || []"
                  :key="idx"
                  class="flex items-start gap-2.5"
                >
                  <svg class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                  <span>{{ feat }}</span>
                </li>
              </ul>
            </div>

            <div class="mt-8 pt-6 border-t border-stone-100">
              <router-link
                :to="`/checkout/select?plan_code=${plan.code}`"
                class="block w-full py-3.5 text-center font-bold text-sm rounded-2xl transition-all"
                :class="plan.code === 'standard' ? 'bg-rose-600 hover:bg-rose-700 text-white shadow-lg shadow-rose-600/30' : 'bg-stone-900 hover:bg-stone-800 text-white'"
              >
                Pilih {{ plan.name }}
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- SECTION FITUR UTAMA -->
    <section id="fitur" class="py-20 bg-white border-t border-stone-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16">
          <span class="text-xs font-bold uppercase tracking-wider text-rose-600">Fitur Lengkap</span>
          <h2 class="text-3xl font-extrabold text-stone-900 mt-1">Semua yang Anda Butuhkan dalam Satu Link</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div class="p-6 rounded-3xl bg-stone-50 border border-stone-200">
            <div class="text-3xl mb-4">🎵</div>
            <h4 class="font-bold text-base text-stone-900">Musik Latar & Pustaka Pilihan</h4>
            <p class="text-xs text-stone-500 mt-2 leading-relaxed">
              Pilih dari beragam musik latar yang disiapkan admin atau unggah file MP3 kenangan favorit Anda sendiri.
            </p>
          </div>

          <div class="p-6 rounded-3xl bg-stone-50 border border-stone-200">
            <div class="text-3xl mb-4">💌</div>
            <h4 class="font-bold text-base text-stone-900">RSVP & Ucapan Real-time</h4>
            <p class="text-xs text-stone-500 mt-2 leading-relaxed">
              Tamu dapat langsung mengonfirmasi kehadiran dan mengirimkan doa restu yang langsung muncul di feed ucapan.
            </p>
          </div>

          <div class="p-6 rounded-3xl bg-stone-50 border border-stone-200">
            <div class="text-3xl mb-4">💳</div>
            <h4 class="font-bold text-base text-stone-900">Amplop Digital & Hadiah</h4>
            <p class="text-xs text-stone-500 mt-2 leading-relaxed">
              Dukungan nomor rekening bank dan e-wallet dengan tombol salin otomatis untuk mempermudah transfer amplop kado.
            </p>
          </div>

          <div class="p-6 rounded-3xl bg-stone-50 border border-stone-200">
            <div class="text-3xl mb-4">📍</div>
            <h4 class="font-bold text-base text-stone-900">Navigasi Peta Google Maps</h4>
            <p class="text-xs text-stone-500 mt-2 leading-relaxed">
              Tamu undangan cukup klik satu tombol untuk membuka rute Google Maps langsung menuju lokasi acara.
            </p>
          </div>

          <div class="p-6 rounded-3xl bg-stone-50 border border-stone-200">
            <div class="text-3xl mb-4">👥</div>
            <h4 class="font-bold text-base text-stone-900">Tautan Tamu & Personalisasi</h4>
            <p class="text-xs text-stone-500 mt-2 leading-relaxed">
              Sematkan nama tamu spesial di cover undangan dan bagikan langsung lewat tombol kirim WhatsApp otomatis.
            </p>
          </div>

          <div class="p-6 rounded-3xl bg-stone-50 border border-stone-200">
            <div class="text-3xl mb-4">⚡</div>
            <h4 class="font-bold text-base text-stone-900">Aktivasi Instan Otomatis</h4>
            <p class="text-xs text-stone-500 mt-2 leading-relaxed">
              Pembayaran lunas via Midtrans Snap (QRIS, VA, E-Wallet) langsung mengaktifkan undangan tanpa menunggu admin.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-stone-900 text-stone-400 py-12 border-t border-stone-800 text-xs">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-6">
        <div>
          <span class="font-extrabold text-base text-white tracking-tight">{{ settings.site_name || 'Forkoway.com' }}</span>
          <p class="mt-1 text-stone-400">{{ settings.site_tagline || 'Platform Undangan Digital Berbayar Modern & Elegan' }}</p>
        </div>

        <div class="flex items-center gap-6">
          <a :href="`https://wa.me/${settings.contact_whatsapp}`" target="_blank" class="hover:text-white transition-colors">Bantuan WhatsApp</a>
          <router-link to="/login" class="hover:text-white transition-colors">Masuk Akun</router-link>
          <router-link to="/register" class="hover:text-white transition-colors">Daftar Sekarang</router-link>
        </div>
      </div>
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 pt-8 border-t border-stone-800 text-center text-stone-500">
        &copy; 2026 {{ settings.site_name || 'Forkoway.com' }}. All rights reserved.
      </div>
    </footer>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { getAllTemplates, getAllPlans, getSiteSettings } from '../firebase/services/adminService';

const defaultTemplates = [
  {
    id: '1',
    name: 'Floral Romance',
    category: 'wedding',
    style: 'romantic',
    blade_view: 'templates.wedding_floral',
    thumbnail_path: '/templates/thumbnails/wedding_floral.svg'
  },
  {
    id: '2',
    name: 'Khitanan Ceria',
    category: 'khitan',
    style: 'playful',
    blade_view: 'templates.khitan_ceria',
    thumbnail_path: '/templates/thumbnails/khitan_ceria.svg'
  },
  {
    id: '3',
    name: 'Modern Minimalist',
    category: 'general',
    style: 'modern',
    blade_view: 'templates.general_modern',
    thumbnail_path: '/templates/thumbnails/general_modern.svg'
  },
  {
    id: '4',
    name: 'Islami Minimalis',
    category: 'wedding',
    style: 'islami',
    blade_view: 'templates.wedding_islami',
    thumbnail_path: '/templates/thumbnails/wedding_islami.svg'
  },
  {
    id: '5',
    name: 'Rustic Autumn Floral',
    category: 'wedding',
    style: 'rustic',
    blade_view: 'templates.wedding_autumn',
    thumbnail_path: '/templates/thumbnails/wedding_autumn.png'
  }
];

const defaultPlans = [
  {
    id: '1',
    name: 'Silver / Hemat',
    slug: 'silver',
    price: 49000,
    active_days: 30,
    max_photos: 5,
    features: ['1 Tema Pilihan', 'Masa Aktif 30 Hari', 'Galeri hingga 5 Foto', 'Amplop Digital & RSVP', 'Google Maps Lokasi']
  },
  {
    id: '2',
    name: 'Gold / Populer',
    slug: 'gold',
    price: 89000,
    active_days: 90,
    max_photos: 15,
    features: ['Bebas Ganti Tema Kapan Saja', 'Masa Aktif 90 Hari', 'Galeri hingga 15 Foto', 'Kustom Musik Latar & Preset', 'Cerita Cinta & Hitung Mundur', 'Buku Tamu & RSVP Interaktif']
  },
  {
    id: '3',
    name: 'Platinum / Mewah',
    slug: 'platinum',
    price: 149000,
    active_days: 365,
    max_photos: 30,
    features: ['Semua Fitur Gold', 'Masa Aktif 365 Hari (1 Tahun)', 'Galeri hingga 30 Foto', 'Bebas Custom Domain Khusus', 'Prioritas Dukungan WhatsApp 24/7']
  }
];

const templates = ref(defaultTemplates);
const plans = ref(defaultPlans);
const settings = ref({
  site_name: 'Forkoway.com',
  site_tagline: 'Platform Undangan Digital Berbayar Modern & Elegan',
  site_description: 'Solusi undangan online terbaik untuk pernikahan, khitanan, dan acara spesial.',
  contact_whatsapp: '6281322231433'
});
const selectedCategory = ref('all');

const allTemplatesCount = computed(() => templates.value.length);

const filteredTemplates = computed(() => {
  if (selectedCategory.value === 'all') {
    return templates.value;
  }
  return templates.value.filter((t) => t.category === selectedCategory.value);
});

function filterCategory(cat) {
  selectedCategory.value = cat;
}

function formatPrice(val) {
  if (!val) return '0';
  return Number(val).toLocaleString('id-ID');
}

onMounted(async () => {
  try {
    const [remoteTemplates, remotePlans, remoteSettings] = await Promise.allSettled([
      getAllTemplates(),
      getAllPlans(),
      getSiteSettings()
    ]);

    if (remoteTemplates.status === 'fulfilled' && remoteTemplates.value?.length > 0) {
      templates.value = remoteTemplates.value;
    }
    if (remotePlans.status === 'fulfilled' && remotePlans.value?.length > 0) {
      plans.value = remotePlans.value;
    }
    if (remoteSettings.status === 'fulfilled' && remoteSettings.value) {
      settings.value = { ...settings.value, ...remoteSettings.value };
    }
  } catch (err) {
    console.warn('Using fallback home data:', err);
  }
});
</script>
