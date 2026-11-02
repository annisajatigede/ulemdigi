<template>
  <div class="font-body bg-[#f0f9ff] text-slate-700 antialiased selection:bg-sky-100 selection:text-sky-600 min-h-screen overflow-x-hidden relative">

    <!-- Audio Player Element -->
    <audio v-if="audioSrc" :src="audioSrc" ref="audioPlayer" loop preload="auto">
      <source :src="audioSrc" type="audio/mpeg">
    </audio>

    <!-- Floating Audio Toggle Button -->
    <button v-if="audioSrc && opened"
            @click="toggleMusic"
            class="fixed bottom-6 right-6 z-40 bg-white border-2 border-sky-300 text-sky-600 p-3.5 rounded-full shadow-xl hover:scale-110 transition-transform active:scale-95 focus:outline-none"
            :class="{ 'spin-slow': isPlaying }"
            title="Musik Latar">
      <svg v-if="isPlaying" class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
        <path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/>
      </svg>
      <svg v-else class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2" />
      </svg>
    </button>

    <!-- COVER / BUKA UNDANGAN -->
    <transition
      leave-active-class="transition ease-in-out duration-700 transform"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 -translate-y-full">
      <div v-if="!opened"
           class="fixed inset-0 z-50 flex items-center justify-center bg-gradient-to-b from-[#e0f2fe] via-[#bae6fd] to-[#e0f2fe] p-6 text-center select-none">
        <div class="max-w-md w-full bg-white/95 backdrop-blur p-8 sm:p-10 rounded-3xl shadow-2xl border-4 border-sky-200 relative overflow-hidden">
          <span class="inline-block bg-amber-100 text-amber-700 font-fredoka font-semibold text-xs px-3 py-1 rounded-full uppercase tracking-wider mb-3">
            🎈 Syukuran Khitanan 🎈
          </span>

          <h1 class="font-fredoka text-3xl sm:text-4xl font-bold text-sky-800 tracking-tight mb-2">
            {{ data.opening?.title || 'Walimatul Khitan' }}
          </h1>
          <p class="font-fredoka text-2xl text-amber-500 font-bold mb-4">
            {{ data.party1?.name || 'Rayyan' }}
          </p>

          <div class="w-16 h-1 bg-sky-300 rounded-full mx-auto my-3"></div>

          <p class="text-xs text-slate-500 mb-1">Kepada Yth. Bapak/Ibu/Saudara/i:</p>
          <div class="inline-block bg-sky-50 border-2 border-dashed border-sky-300 px-5 py-2.5 rounded-2xl my-2">
            <span class="text-base sm:text-lg font-bold text-sky-900 font-fredoka">{{ recipientName || 'Tamu Undangan' }}</span>
            <span v-if="isPersonal" class="block text-[11px] text-sky-600 font-medium">Tamu Terhormat</span>
          </div>

          <button @click="openInvitation"
                  class="mt-6 w-full inline-flex items-center justify-center gap-2 bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-600 hover:to-blue-700 text-white font-fredoka font-bold px-6 py-3.5 rounded-2xl shadow-lg shadow-sky-500/30 transition-all transform hover:-translate-y-0.5 text-base tracking-wide">
            <span>Buka Undangan Ceria</span>
            <svg class="w-5 h-5 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
            </svg>
          </button>
        </div>
      </div>
    </transition>

    <!-- MAIN CONTENT -->
    <main class="max-w-xl mx-auto bg-white min-h-screen shadow-2xl relative border-x border-sky-100">

      <!-- 1. HERO SECTION -->
      <header class="relative pt-16 pb-16 px-6 text-center bg-gradient-to-b from-[#e0f2fe]/60 via-white to-white">
        <div class="inline-flex items-center gap-1.5 px-3.5 py-1 bg-amber-400 text-amber-950 font-fredoka text-xs font-bold rounded-full mb-4 shadow-sm">
          <span>✨ Walimatul Khitan ✨</span>
        </div>
        <h1 class="font-fredoka text-4xl sm:text-5xl text-sky-600 font-bold mb-2">
          {{ data.party1?.name || 'Rayyan' }}
        </h1>
        <p class="font-fredoka text-slate-500 text-sm tracking-wider uppercase mb-6">
          {{ formattedEventDate }}
        </p>

        <!-- COUNTDOWN -->
        <div class="grid grid-cols-4 gap-2.5 max-w-xs mx-auto text-center mt-6">
          <div class="bg-sky-50 border-2 border-sky-200 rounded-2xl p-2.5">
            <span class="block text-xl sm:text-2xl font-fredoka font-bold text-sky-700">{{ countdown.days }}</span>
            <span class="text-[10px] text-slate-500 font-semibold uppercase">Hari</span>
          </div>
          <div class="bg-sky-50 border-2 border-sky-200 rounded-2xl p-2.5">
            <span class="block text-xl sm:text-2xl font-fredoka font-bold text-sky-700">{{ countdown.hours }}</span>
            <span class="text-[10px] text-slate-500 font-semibold uppercase">Jam</span>
          </div>
          <div class="bg-sky-50 border-2 border-sky-200 rounded-2xl p-2.5">
            <span class="block text-xl sm:text-2xl font-fredoka font-bold text-sky-700">{{ countdown.minutes }}</span>
            <span class="text-[10px] text-slate-500 font-semibold uppercase">Menit</span>
          </div>
          <div class="bg-sky-50 border-2 border-sky-200 rounded-2xl p-2.5">
            <span class="block text-xl sm:text-2xl font-fredoka font-bold text-sky-700">{{ countdown.seconds }}</span>
            <span class="text-[10px] text-slate-500 font-semibold uppercase">Detik</span>
          </div>
        </div>
      </header>

      <!-- 2. DOA KHITAN SECTION -->
      <section class="py-10 px-6 sm:px-10 text-center border-t border-sky-50">
        <div class="bg-sky-50/70 border-2 border-dashed border-sky-200 p-6 rounded-3xl max-w-md mx-auto">
          <p class="font-fredoka text-xs font-semibold text-sky-700 uppercase tracking-wider mb-2">Doa Syukuran Khitan</p>
          <p class="italic text-slate-700 text-xs sm:text-sm leading-relaxed">
            "{{ data.opening?.quote || 'Semoga ananda menjadi anak yang sholeh, berbakti kepada kedua orang tua, agama, nusa, dan bangsa. Serta dikaruniai kesehatan dan kecerdasan.' }}"
          </p>
        </div>

        <div class="mt-8">
          <p class="text-xs text-sky-600 font-bold uppercase tracking-wider">{{ data.opening?.greeting || 'Assalamu’alaikum Warahmatullahi Wabarakatuh' }}</p>
          <p class="text-xs sm:text-sm text-slate-600 mt-2 leading-relaxed max-w-md mx-auto">
            Dengan memohon ridho dan rahmat Allah SWT, kami sekeluarga bermaksud menyelenggarakan syukuran khitanan putra kami tersayang:
          </p>
        </div>
      </section>

      <!-- 3. PROFILE ANAK YANG DIKHITAN & ORANG TUA -->
      <section class="py-12 px-6 sm:px-10 bg-sky-50/40">
        <div class="text-center">
          <div class="w-36 h-36 mx-auto rounded-3xl p-1.5 border-4 border-sky-300 shadow-lg overflow-hidden bg-white mb-4 transform -rotate-1">
            <img v-if="data.party1?.photo_path" :src="data.party1.photo_path" :alt="data.party1.name" class="w-full h-full object-cover rounded-2xl">
            <div v-else class="w-full h-full rounded-2xl bg-sky-100 flex items-center justify-center text-sky-500 font-fredoka text-5xl">
              👦
            </div>
          </div>

          <h3 class="font-fredoka text-2xl sm:text-3xl font-bold text-sky-900">{{ data.party1?.full_name || 'Muhammad Rayyan Al-Ghifari' }}</h3>
          <p class="text-xs font-bold text-amber-600 uppercase tracking-wider mt-1">{{ data.party1?.role_label || 'Anak yang Dikhitan' }}</p>
          <p class="text-xs sm:text-sm text-slate-600 mt-2 font-medium">
            {{ data.party1?.child_order || 'Putra tercinta dari' }}
            <span class="font-bold text-slate-800">{{ data.party1?.parents || 'Bpk. Ahmad & Ibu Fatimah' }}</span>
          </p>
        </div>
      </section>

      <!-- 4. RANGKAIAN ACARA -->
      <section class="py-14 px-6 sm:px-10">
        <div class="text-center mb-8">
          <span class="text-xs font-bold text-sky-600 uppercase tracking-widest">Informasi Acara</span>
          <h2 class="font-fredoka text-3xl font-bold text-slate-800 mt-1">Waktu & Tempat Syukuran</h2>
          <div class="w-12 h-1 bg-sky-400 rounded-full mx-auto mt-2"></div>
        </div>

        <div class="space-y-6">
          <div v-for="event in events" :key="event.id"
               class="bg-gradient-to-br from-white to-sky-50/60 border-2 border-sky-100 rounded-3xl p-6 sm:p-8 shadow-sm text-center relative overflow-hidden">
            <div class="inline-block bg-sky-100 text-sky-800 font-fredoka font-semibold text-xs px-3 py-1 rounded-full mb-2">
              {{ event.title }}
            </div>
            
            <div class="my-4 py-3 border-y-2 border-dashed border-sky-100">
              <p class="font-fredoka font-bold text-slate-800 text-base sm:text-lg">{{ formatEventDate(event.start_at) }}</p>
              <p class="text-xs text-sky-600 font-bold mt-1">
                Pukul {{ formatTime(event.start_at) }} <span v-if="event.end_at">- {{ formatTime(event.end_at) }}</span> {{ event.timezone }}
              </p>
            </div>

            <p class="font-bold text-slate-800 text-sm">{{ event.venue_name }}</p>
            <p v-if="event.address" class="text-xs text-slate-500 mt-1 leading-relaxed">{{ event.address }}</p>

            <a v-if="event.maps_url" :href="event.maps_url" target="_blank"
               class="inline-flex items-center gap-1.5 mt-5 bg-sky-500 hover:bg-sky-600 text-white text-xs font-fredoka font-bold px-4 py-2.5 rounded-xl shadow-md transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              <span>Buka Petunjuk Peta Google Maps</span>
            </a>
          </div>

          <p v-if="events.length === 0" class="text-center text-xs text-slate-400">Jadwal acara belum diatur.</p>
        </div>
      </section>

      <!-- 5. GALERI FOTO KECERIAAN -->
      <section v-if="data.gallery && data.gallery.length > 0" class="py-12 px-6 sm:px-10 bg-sky-50/20">
        <div class="text-center mb-8">
          <span class="text-xs font-bold text-sky-600 uppercase tracking-widest">Momen Ceria</span>
          <h2 class="font-fredoka text-3xl font-bold text-slate-800 mt-1">Galeri Foto</h2>
          <div class="w-12 h-1 bg-sky-400 rounded-full mx-auto mt-2"></div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
          <div v-for="(photo, pIdx) in data.gallery" :key="pIdx"
               class="aspect-square rounded-2xl overflow-hidden shadow-sm border-2 border-sky-100 group">
            <img :src="photo.url || photo" alt="Galeri Khitan" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
          </div>
        </div>
      </section>

      <!-- 6. AMPLOP DIGITAL / TANDA KASIH -->
      <section v-if="data.gifts && data.gifts.length > 0" class="py-12 px-6 sm:px-10 bg-sky-50/50">
        <div class="text-center mb-8">
          <span class="text-xs font-bold text-sky-600 uppercase tracking-widest">Tanda Kasih</span>
          <h2 class="font-fredoka text-3xl font-bold text-slate-800 mt-1">Amplop Digital</h2>
          <div class="w-12 h-1 bg-sky-400 rounded-full mx-auto mt-2"></div>
          <p class="text-xs text-slate-500 mt-3 max-w-sm mx-auto leading-relaxed">
            Bagi kerabat yang ingin memberikan kado/tanda kasih secara cashless kepada ananda tercinta, dapat melalui rekening berikut:
          </p>
        </div>

        <div class="space-y-4 max-w-sm mx-auto">
          <div v-for="(gift, idx) in data.gifts" :key="idx"
               class="bg-white border-2 border-sky-200 rounded-2xl p-5 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between mb-2">
              <span class="font-fredoka font-bold text-sky-800 tracking-wider text-base">{{ gift.bank_name || 'BANK' }}</span>
              <span class="text-[10px] text-slate-400 uppercase font-semibold">Transfer / E-Wallet</span>
            </div>
            <div class="font-mono text-lg font-bold text-slate-800 tracking-wider my-1">
              {{ gift.account_number }}
            </div>
            <p class="text-xs text-slate-500">a.n. <span class="font-bold text-slate-700">{{ gift.account_holder }}</span></p>

            <button @click="copyGift(gift.account_number, idx)"
                    class="mt-4 w-full inline-flex items-center justify-center gap-1.5 bg-sky-100 hover:bg-sky-200 text-sky-800 text-xs font-fredoka font-bold py-2 rounded-xl transition-colors">
              <span v-if="copiedIdx !== idx">Salin Nomor Rekening</span>
              <span v-else class="text-emerald-600 font-bold">✓ Tersalin ke Clipboard</span>
            </button>
          </div>
        </div>
      </section>

      <!-- 7. RSVP & UCAPAN -->
      <section v-if="data.rsvp_enabled !== false || data.wishes_enabled !== false" class="py-14 px-6 sm:px-10" id="rsvp-section">
        <div class="text-center mb-8">
          <span class="text-xs font-bold text-sky-600 uppercase tracking-widest">Doa & Kehadiran</span>
          <h2 class="font-fredoka text-3xl font-bold text-slate-800 mt-1">Konfirmasi & Ucapan</h2>
          <div class="w-12 h-1 bg-sky-400 rounded-full mx-auto mt-2"></div>
        </div>

        <div v-if="successRsvpMessage" class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-medium text-center">
          <span>{{ successRsvpMessage }}</span>
        </div>

        <form @submit.prevent="submitRsvp" class="bg-white border-2 border-sky-100 rounded-3xl p-6 sm:p-8 shadow-md space-y-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap *</label>
            <input type="text" v-model="rsvpForm.name" required
                   class="w-full text-xs sm:text-sm px-4 py-2.5 rounded-xl border border-sky-200 focus:ring-2 focus:ring-sky-400 focus:outline-none"
                   placeholder="Tulis nama Anda">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">No. WhatsApp / HP</label>
            <input type="text" v-model="rsvpForm.phone"
                   class="w-full text-xs sm:text-sm px-4 py-2.5 rounded-xl border border-sky-200 focus:ring-2 focus:ring-sky-400 focus:outline-none"
                   placeholder="Contoh: 08123456789">
          </div>

          <div v-if="data.rsvp_enabled !== false">
            <label class="block text-xs font-bold text-slate-700 mb-1">Konfirmasi Kehadiran *</label>
            <select v-model="rsvpForm.rsvp_status" required
                    class="w-full text-xs sm:text-sm px-4 py-2.5 rounded-xl border border-sky-200 focus:ring-2 focus:ring-sky-400 focus:outline-none">
              <option value="attended">Insya Allah Hadir</option>
              <option value="maybe">Masih Ragu</option>
              <option value="not_attended">Berhalangan Hadir</option>
            </select>
          </div>

          <div v-if="data.rsvp_enabled !== false && rsvpForm.rsvp_status === 'attended'">
            <label class="block text-xs font-bold text-slate-700 mb-1">Jumlah Orang</label>
            <input type="number" min="1" max="10" v-model.number="rsvpForm.guest_count"
                   class="w-full text-xs sm:text-sm px-4 py-2.5 rounded-xl border border-sky-200 focus:ring-2 focus:ring-sky-400 focus:outline-none">
          </div>

          <div v-if="data.wishes_enabled !== false">
            <label class="block text-xs font-bold text-slate-700 mb-1">Doa & Harapan untuk Ananda</label>
            <textarea v-model="rsvpForm.message" rows="3"
                      class="w-full text-xs sm:text-sm px-4 py-2.5 rounded-xl border border-sky-200 focus:ring-2 focus:ring-sky-400 focus:outline-none"
                      placeholder="Tulis ucapan dan doa terbaik..."></textarea>
          </div>

          <button type="submit" :disabled="submittingRsvp"
                  class="w-full bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-600 hover:to-blue-700 text-white font-fredoka font-bold py-3.5 rounded-xl shadow-md transition-all text-sm disabled:opacity-50">
            <span v-if="!submittingRsvp">Kirim Doa & Konfirmasi</span>
            <span v-else>Mengirimkan...</span>
          </button>
        </form>

        <div class="mt-8 space-y-3">
          <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Doa dari Sahabat & Kerabat ({{ wishesList.length }})</h4>
          <div class="max-h-72 overflow-y-auto space-y-2.5 pr-1">
            <div v-for="w in wishesList" :key="w.id" class="bg-sky-50/60 border border-sky-100 p-3.5 rounded-2xl">
              <span class="font-bold text-sky-900 text-xs sm:text-sm block">{{ w.name }}</span>
              <p class="text-xs text-slate-600 mt-1 leading-relaxed">{{ w.message }}</p>
              <span class="text-[10px] text-slate-400 mt-1 block">{{ formatWishDate(w.created_at) }}</span>
            </div>
            <p v-if="wishesList.length === 0" class="text-xs text-slate-400">Belum ada doa masuk.</p>
          </div>
        </div>
      </section>

      <!-- 8. PENUTUP -->
      <section class="py-14 px-6 sm:px-10 text-center bg-gradient-to-b from-white to-[#e0f2fe]/50 border-t border-sky-100">
        <p class="text-xs sm:text-sm text-slate-600 max-w-md mx-auto leading-relaxed">
          {{ data.closing?.text || 'Merupakan suatu kehormatan dan kebahagiaan bagi kami sekeluarga apabila Bapak/Ibu/Saudara/i berkenan hadir untuk memberikan doa restu kepada ananda tercinta.' }}
        </p>
        <h3 class="font-fredoka text-2xl text-sky-700 mt-6">{{ data.closing?.signature || ('Keluarga Besar ' + (data.party1?.name || 'Kami')) }}</h3>

        <div class="mt-8 pt-6 border-t border-sky-100">
          <a :href="whatsappShareUrl" target="_blank"
             class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-4 py-2.5 rounded-xl shadow-md transition-colors">
            <span>Bagikan ke WhatsApp</span>
          </a>
        </div>

        <p class="text-[10px] text-slate-400 mt-8">Dibuat dengan segenap cinta di <router-link to="/" class="font-bold text-sky-600 hover:underline">Forkoway.com</router-link></p>
      </section>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import api from '../../api/client';
import { alertError } from '../../utils/alert';

const props = defineProps({
  invitation: Object,
  data: Object,
  events: Array,
  wishes: Array,
  recipientName: String,
  isPersonal: Boolean,
  guest: Object,
  previewMode: Boolean
});

const opened = ref(!!props.previewMode);
const isPlaying = ref(false);
const audioPlayer = ref(null);
const copiedIdx = ref(null);
const successRsvpMessage = ref(null);
const submittingRsvp = ref(false);
const wishesList = ref([...(props.wishes || [])]);

const rsvpForm = ref({
  name: props.guest?.name || (props.recipientName !== 'Tamu Undangan' ? props.recipientName : ''),
  phone: props.guest?.phone || '',
  rsvp_status: 'attended',
  guest_count: 1,
  message: '',
  personal_token: props.guest?.personal_token || ''
});

const audioSrc = computed(() => props.data?.music_url || props.data?.music_path || '');
const countdown = ref({ days: 0, hours: 0, minutes: 0, seconds: 0 });
let timerInterval = null;

const targetDate = computed(() => {
  if (props.events && props.events[0]?.start_at) return new Date(props.events[0].start_at);
  const d = new Date();
  d.setDate(d.getDate() + 14);
  return d;
});

const formattedEventDate = computed(() => {
  if (props.events && props.events[0]?.start_at) {
    return new Date(props.events[0].start_at).toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
  }
  return 'Segera Hadir';
});

const whatsappShareUrl = computed(() => {
  const title = `Halo! Berikut undangan Walimatul Khitan ${props.data?.party1?.name || ''}: ${window.location.href}`;
  return `https://api.whatsapp.com/send?text=${encodeURIComponent(title)}`;
});

const updateCountdown = () => {
  const now = new Date().getTime();
  const diff = targetDate.value.getTime() - now;
  if (diff <= 0) {
    countdown.value = { days: 0, hours: 0, minutes: 0, seconds: 0 };
    return;
  }
  countdown.value = {
    days: Math.floor(diff / (1000 * 60 * 60 * 24)),
    hours: Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)),
    minutes: Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60)),
    seconds: Math.floor((diff % (1000 * 60)) / 1000)
  };
};

const openInvitation = () => {
  opened.value = true;
  playMusic();
};

const playMusic = () => {
  if (audioPlayer.value) {
    audioPlayer.value.play().then(() => isPlaying.value = true).catch(() => isPlaying.value = false);
  }
};

const toggleMusic = () => {
  if (!audioPlayer.value) return;
  if (isPlaying.value) {
    audioPlayer.value.pause();
    isPlaying.value = false;
  } else {
    audioPlayer.value.play();
    isPlaying.value = true;
  }
};

const copyGift = (accNumber, idx) => {
  navigator.clipboard.writeText(accNumber);
  copiedIdx.value = idx;
  setTimeout(() => copiedIdx.value = null, 2500);
};

const formatEventDate = (dateStr) => {
  if (!dateStr) return '';
  return new Date(dateStr).toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
};

const formatTime = (dateStr) => {
  if (!dateStr) return '';
  return new Date(dateStr).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', hour12: false });
};

const formatWishDate = (dateStr) => {
  if (!dateStr) return '';
  return new Date(dateStr).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
};

const submitRsvp = async () => {
  try {
    submittingRsvp.value = true;
    successRsvpMessage.value = null;
    const res = await api.post(`/invitations/public/${props.invitation.slug}/rsvp`, rsvpForm.value);
    successRsvpMessage.value = res.data.message || 'Konfirmasi kehadiran dan doa restu berhasil dikirim!';
    if (rsvpForm.value.message) {
      wishesList.value.unshift({
        id: Date.now(),
        name: rsvpForm.value.name,
        message: rsvpForm.value.message,
        created_at: new Date().toISOString()
      });
    }
  } catch (err) {
    await alertError('Gagal Mengirim Konfirmasi', err.response?.data?.message || 'Gagal mengirimkan konfirmasi.');
  } finally {
    submittingRsvp.value = false;
  }
};

onMounted(() => {
  updateCountdown();
  timerInterval = setInterval(updateCountdown, 1000);
  if (props.previewMode) opened.value = true;
});

onUnmounted(() => {
  if (timerInterval) clearInterval(timerInterval);
  if (audioPlayer.value) audioPlayer.value.pause();
});
</script>

<style scoped>
.font-fredoka { font-family: 'Fredoka', cursive, sans-serif; }
.font-body { font-family: 'Plus Jakarta Sans', sans-serif; }
.spin-slow { animation: spin 8s linear infinite; }
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>
