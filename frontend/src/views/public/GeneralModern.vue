<template>
  <div class="font-montserrat bg-[#0f172a] text-slate-300 antialiased selection:bg-emerald-500/20 selection:text-emerald-400 min-h-screen overflow-x-hidden relative">

    <!-- Audio Player Element -->
    <audio v-if="audioSrc" ref="audioPlayer" loop preload="none">
      <source :src="audioSrc" type="audio/mpeg">
    </audio>

    <!-- Floating Audio Toggle Button -->
    <button v-if="audioSrc && opened"
            @click="toggleMusic"
            class="fixed bottom-6 right-6 z-40 bg-slate-900/90 border border-slate-700 text-emerald-400 p-3.5 rounded-full shadow-2xl hover:scale-110 transition-transform active:scale-95 focus:outline-none"
            :class="{ 'spin-slow': isPlaying }"
            title="Audio">
      <svg v-if="isPlaying" class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
        <path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/>
      </svg>
      <svg v-else class="w-6 h-6 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
           class="fixed inset-0 z-50 flex items-center justify-center bg-gradient-to-b from-[#090d16] via-[#0f172a] to-[#090d16] p-6 text-center select-none">
        <div class="max-w-md w-full bg-slate-900/90 backdrop-blur-md p-8 sm:p-10 rounded-3xl shadow-2xl border border-slate-800 relative overflow-hidden">
          <div class="w-12 h-12 rounded-full border border-emerald-500/40 flex items-center justify-center mx-auto mb-4 text-emerald-400">
            ✦
          </div>

          <p class="text-xs uppercase tracking-[0.3em] text-emerald-400 mb-2">OFFICIAL INVITATION</p>
          <h1 class="font-cinzel text-3xl sm:text-4xl font-bold text-white tracking-wide mb-3">
            {{ data.opening?.title || 'Special Celebration' }}
          </h1>
          <p class="text-xs text-slate-400 max-w-xs mx-auto mb-6">
            {{ data.party1?.full_name || 'Penyelenggara Acara' }}
          </p>

          <div class="w-16 h-px bg-slate-700 mx-auto my-4"></div>

          <p class="text-[11px] text-slate-400 uppercase tracking-widest mb-1">TERUNTUK:</p>
          <div class="inline-block bg-slate-800/80 border border-slate-700 px-5 py-2.5 rounded-xl my-2">
            <span class="text-base font-semibold text-emerald-300 font-cinzel">{{ recipientName || 'Tamu Undangan' }}</span>
            <span v-if="isPersonal" class="block text-[10px] text-emerald-500 uppercase tracking-widest mt-0.5">VIP Guest</span>
          </div>

          <button @click="openInvitation"
                  class="mt-6 w-full inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-500 text-white font-cinzel font-semibold px-6 py-3.5 rounded-xl shadow-lg shadow-emerald-900/40 transition-all text-xs tracking-[0.2em] uppercase">
            <span>Buka Undangan</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
            </svg>
          </button>
        </div>
      </div>
    </transition>

    <!-- MAIN INVITATION -->
    <main class="max-w-xl mx-auto bg-slate-900 min-h-screen shadow-2xl relative border-x border-slate-800">

      <!-- 1. HERO -->
      <header class="relative pt-16 pb-16 px-6 text-center border-b border-slate-800 bg-gradient-to-b from-slate-950 to-slate-900">
        <span class="text-xs font-semibold tracking-[0.3em] text-emerald-400 uppercase mb-3 inline-block">ACARA RESMI</span>
        <h1 class="font-cinzel text-3xl sm:text-4xl text-white font-bold tracking-wide mb-3">
          {{ data.opening?.title || 'Special Celebration' }}
        </h1>
        <p class="text-xs text-slate-400 tracking-widest uppercase mb-8">
          {{ formattedEventDate }}
        </p>

        <!-- COUNTDOWN -->
        <div class="grid grid-cols-4 gap-2.5 max-w-xs mx-auto text-center">
          <div class="bg-slate-800/80 border border-slate-700/80 rounded-xl p-2.5">
            <span class="block text-xl font-bold font-cinzel text-emerald-400">{{ countdown.days }}</span>
            <span class="text-[9px] text-slate-400 uppercase tracking-widest">Hari</span>
          </div>
          <div class="bg-slate-800/80 border border-slate-700/80 rounded-xl p-2.5">
            <span class="block text-xl font-bold font-cinzel text-emerald-400">{{ countdown.hours }}</span>
            <span class="text-[9px] text-slate-400 uppercase tracking-widest">Jam</span>
          </div>
          <div class="bg-slate-800/80 border border-slate-700/80 rounded-xl p-2.5">
            <span class="block text-xl font-bold font-cinzel text-emerald-400">{{ countdown.minutes }}</span>
            <span class="text-[9px] text-slate-400 uppercase tracking-widest">Menit</span>
          </div>
          <div class="bg-slate-800/80 border border-slate-700/80 rounded-xl p-2.5">
            <span class="block text-xl font-bold font-cinzel text-emerald-400">{{ countdown.seconds }}</span>
            <span class="text-[9px] text-slate-400 uppercase tracking-widest">Detik</span>
          </div>
        </div>
      </header>

      <!-- 2. GREETINGS & QUOTE -->
      <section class="py-12 px-6 sm:px-10 text-center border-b border-slate-800">
        <p class="italic text-slate-300 text-xs sm:text-sm max-w-md mx-auto leading-relaxed">
          "{{ data.opening?.quote || 'Kebahagiaan terasa sempurna ketika dapat berbagi momen istimewa bersama orang-orang tercinta.' }}"
        </p>
        <div class="mt-6 text-xs text-slate-400 max-w-md mx-auto leading-relaxed">
          {{ data.opening?.greeting || 'Salam Sejahtera / Assalamu’alaikum Warahmatullahi Wabarakatuh' }}
        </div>
      </section>

      <!-- 3. RANGKAIAN ACARA -->
      <section class="py-14 px-6 sm:px-10 border-b border-slate-800">
        <div class="text-center mb-8">
          <span class="text-[10px] font-semibold text-emerald-400 uppercase tracking-[0.25em]">SCHEDULE</span>
          <h2 class="font-cinzel text-2xl font-bold text-white mt-1">Waktu & Tempat</h2>
          <div class="w-12 h-0.5 bg-emerald-500/50 mx-auto mt-2"></div>
        </div>

        <div class="space-y-6">
          <div v-for="event in events" :key="event.id"
               class="bg-slate-800/40 border border-slate-700/70 rounded-2xl p-6 text-center">
            <h3 class="font-cinzel text-lg font-bold text-emerald-300 mb-2">{{ event.title }}</h3>
            
            <div class="my-4 py-3 border-y border-slate-700/60">
              <p class="font-semibold text-white text-sm">{{ formatEventDate(event.start_at) }}</p>
              <p class="text-xs text-slate-400 mt-1">
                {{ formatTime(event.start_at) }} <span v-if="event.end_at">- {{ formatTime(event.end_at) }}</span> {{ event.timezone }}
              </p>
            </div>

            <p class="font-medium text-slate-200 text-xs sm:text-sm">{{ event.venue_name }}</p>
            <p v-if="event.address" class="text-xs text-slate-400 mt-1">{{ event.address }}</p>

            <a v-if="event.maps_url" :href="event.maps_url" target="_blank"
               class="inline-flex items-center gap-1.5 mt-4 bg-slate-700/60 hover:bg-slate-700 text-emerald-300 text-xs px-4 py-2 rounded-xl transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
              </svg>
              <span>Petunjuk Lokasi Google Maps</span>
            </a>
          </div>

          <p v-if="events.length === 0" class="text-center text-xs text-slate-500">Jadwal belum ditentukan.</p>
        </div>
      </section>

      <!-- 4. GALERI FOTO -->
      <section v-if="data.gallery && data.gallery.length > 0" class="py-12 px-6 sm:px-10 border-b border-slate-800">
        <div class="text-center mb-8">
          <span class="text-[10px] font-semibold text-emerald-400 uppercase tracking-[0.25em]">GALLERY</span>
          <h2 class="font-cinzel text-2xl font-bold text-white mt-1">Galeri Foto</h2>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
          <div v-for="(photo, pIdx) in data.gallery" :key="pIdx"
               class="aspect-square rounded-xl overflow-hidden border border-slate-700 group">
            <img :src="photo.url || photo" alt="Foto" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
          </div>
        </div>
      </section>

      <!-- 5. AMPLOP DIGITAL / TANDA TERIMA KASIH -->
      <section v-if="data.gifts && data.gifts.length > 0" class="py-12 px-6 sm:px-10 border-b border-slate-800">
        <div class="text-center mb-8">
          <span class="text-[10px] font-semibold text-emerald-400 uppercase tracking-[0.25em]">GIFTS</span>
          <h2 class="font-cinzel text-2xl font-bold text-white mt-1">Amplop Digital</h2>
        </div>

        <div class="space-y-4 max-w-sm mx-auto">
          <div v-for="(gift, idx) in data.gifts" :key="idx"
               class="bg-slate-800/60 border border-slate-700 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-2">
              <span class="font-bold text-white text-sm">{{ gift.bank_name || 'BANK' }}</span>
              <span class="text-[10px] text-slate-400 uppercase tracking-widest">Digital Transfer</span>
            </div>
            <div class="font-mono text-lg font-bold text-emerald-400 tracking-wider my-1">
              {{ gift.account_number }}
            </div>
            <p class="text-xs text-slate-400">a.n. <span class="text-slate-200">{{ gift.account_holder }}</span></p>

            <button @click="copyGift(gift.account_number, idx)"
                    class="mt-4 w-full bg-slate-700/50 hover:bg-slate-700 text-xs font-semibold py-2 rounded-xl border border-slate-600 transition-colors">
              <span v-if="copiedIdx !== idx">Salin Nomor</span>
              <span v-else class="text-emerald-400 font-bold">✓ Tersalin</span>
            </button>
          </div>
        </div>
      </section>

      <!-- 6. RSVP & UCAPAN -->
      <section v-if="data.rsvp_enabled !== false || data.wishes_enabled !== false" class="py-14 px-6 sm:px-10 border-b border-slate-800" id="rsvp-section">
        <div class="text-center mb-8">
          <span class="text-[10px] font-semibold text-emerald-400 uppercase tracking-[0.25em]">RSVP</span>
          <h2 class="font-cinzel text-2xl font-bold text-white mt-1">Konfirmasi & Ucapan</h2>
        </div>

        <div v-if="successRsvpMessage" class="mb-6 p-4 rounded-xl bg-emerald-950/60 border border-emerald-700 text-emerald-300 text-xs text-center">
          <span>{{ successRsvpMessage }}</span>
        </div>

        <form @submit.prevent="submitRsvp" class="bg-slate-800/50 border border-slate-700 rounded-2xl p-6 sm:p-8 space-y-4">
          <div>
            <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Lengkap *</label>
            <input type="text" v-model="rsvpForm.name" required
                   class="w-full text-xs px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white focus:ring-1 focus:ring-emerald-500 focus:outline-none"
                   placeholder="Nama Anda">
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-300 mb-1">No. WhatsApp / HP</label>
            <input type="text" v-model="rsvpForm.phone"
                   class="w-full text-xs px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white focus:ring-1 focus:ring-emerald-500 focus:outline-none"
                   placeholder="0812...">
          </div>

          <div v-if="data.rsvp_enabled !== false">
            <label class="block text-xs font-semibold text-slate-300 mb-1">Konfirmasi Kehadiran *</label>
            <select v-model="rsvpForm.rsvp_status" required
                    class="w-full text-xs px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white focus:ring-1 focus:ring-emerald-500 focus:outline-none">
              <option value="attended">Hadir</option>
              <option value="maybe">Masih Ragu</option>
              <option value="not_attended">Tidak Hadir</option>
            </select>
          </div>

          <div v-if="data.rsvp_enabled !== false && rsvpForm.rsvp_status === 'attended'">
            <label class="block text-xs font-semibold text-slate-300 mb-1">Jumlah Orang</label>
            <input type="number" min="1" max="10" v-model.number="rsvpForm.guest_count"
                   class="w-full text-xs px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white focus:ring-1 focus:ring-emerald-500 focus:outline-none">
          </div>

          <div v-if="data.wishes_enabled !== false">
            <label class="block text-xs font-semibold text-slate-300 mb-1">Ucapan & Pesan</label>
            <textarea v-model="rsvpForm.message" rows="3"
                      class="w-full text-xs px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white focus:ring-1 focus:ring-emerald-500 focus:outline-none"
                      placeholder="Tulis pesan Anda..."></textarea>
          </div>

          <button type="submit" :disabled="submittingRsvp"
                  class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-cinzel font-semibold py-3 rounded-xl transition-all text-xs tracking-wider uppercase disabled:opacity-50">
            <span v-if="!submittingRsvp">Kirim RSVP</span>
            <span v-else>Mengirimkan...</span>
          </button>
        </form>

        <div class="mt-8 space-y-3">
          <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-widest mb-2">Ucapan ({{ wishesList.length }})</h4>
          <div class="max-h-72 overflow-y-auto space-y-2.5 pr-1">
            <div v-for="w in wishesList" :key="w.id" class="bg-slate-800/40 border border-slate-700/60 p-3.5 rounded-xl">
              <span class="font-bold text-emerald-400 text-xs block">{{ w.name }}</span>
              <p class="text-xs text-slate-300 mt-1 leading-relaxed">{{ w.message }}</p>
              <span class="text-[10px] text-slate-500 mt-1 block">{{ formatWishDate(w.created_at) }}</span>
            </div>
          </div>
        </div>
      </section>

      <!-- 7. PENUTUP -->
      <section class="py-14 px-6 sm:px-10 text-center bg-slate-950">
        <p class="text-xs text-slate-400 max-w-md mx-auto leading-relaxed">
          {{ data.closing?.text || 'Merupakan suatu kehormatan dan kebanggaan bagi kami atas kehadiran Bapak/Ibu/Saudara/i sekalian.' }}
        </p>
        <h3 class="font-cinzel text-xl text-emerald-400 mt-6 tracking-wide">{{ data.closing?.signature || (data.party1?.name || 'Penyelenggara') }}</h3>

        <div class="mt-8 pt-6 border-t border-slate-800">
          <a :href="whatsappShareUrl" target="_blank"
             class="inline-flex items-center gap-2 bg-emerald-700 hover:bg-emerald-600 text-white text-xs px-4 py-2.5 rounded-xl transition-colors">
            <span>Bagikan via WhatsApp</span>
          </a>
        </div>

        <p class="text-[10px] text-slate-600 mt-8">Powered by <router-link to="/" class="text-emerald-500 hover:underline">Forkoway.com</router-link></p>
      </section>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import api from '../../api/client';

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
  return 'Segera';
});

const whatsappShareUrl = computed(() => {
  const title = `Undangan Acara: ${props.data?.opening?.title || ''} - ${window.location.href}`;
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
    successRsvpMessage.value = res.data.message || 'Konfirmasi kehadiran berhasil dikirim!';
    if (rsvpForm.value.message) {
      wishesList.value.unshift({
        id: Date.now(),
        name: rsvpForm.value.name,
        message: rsvpForm.value.message,
        created_at: new Date().toISOString()
      });
    }
  } catch (err) {
    alert(err.response?.data?.message || 'Gagal mengirimkan konfirmasi.');
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
.font-cinzel { font-family: 'Cinzel', serif; }
.font-montserrat { font-family: 'Montserrat', sans-serif; }
.spin-slow { animation: spin 8s linear infinite; }
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>
