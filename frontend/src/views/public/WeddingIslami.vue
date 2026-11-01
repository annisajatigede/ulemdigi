<template>
  <div class="font-body bg-[#fbfbfa] text-stone-700 antialiased selection:bg-emerald-100 selection:text-emerald-800 min-h-screen overflow-x-hidden relative">

    <!-- Audio Player Element -->
    <audio v-if="audioSrc" ref="audioPlayer" loop preload="none">
      <source :src="audioSrc" type="audio/mpeg">
    </audio>

    <!-- Floating Audio Toggle Button -->
    <button v-if="audioSrc && opened"
            @click="toggleMusic"
            class="fixed bottom-6 right-6 z-40 bg-white/95 backdrop-blur border border-emerald-300 text-emerald-800 p-3.5 rounded-full shadow-2xl hover:scale-110 transition-transform active:scale-95 focus:outline-none"
            :class="{ 'spin-slow': isPlaying }"
            title="Musik Latar">
      <svg v-if="isPlaying" class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
        <path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/>
      </svg>
      <svg v-else class="w-6 h-6 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2" />
      </svg>
    </button>

    <!-- COVER / OPENING MODAL -->
    <transition
      leave-active-class="transition ease-in-out duration-700 transform"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 -translate-y-full">
      <div v-if="!opened"
           class="fixed inset-0 z-50 flex items-center justify-center bg-gradient-to-b from-[#f0fdf4] via-[#ecfdf5] to-[#f0fdf4] p-6 text-center select-none">
        <div class="max-w-md w-full bg-white/95 backdrop-blur-md p-8 sm:p-10 rounded-3xl shadow-2xl border border-emerald-200/80 relative overflow-hidden">
          <div class="w-16 h-16 mx-auto mb-3 text-emerald-700/80 flex items-center justify-center">
            <svg viewBox="0 0 100 100" class="w-12 h-12 fill-current">
              <path d="M50 0 C65 25 80 40 100 50 C80 60 65 75 50 100 C35 75 20 60 0 50 C20 40 35 25 50 0 Z" opacity="0.8"/>
            </svg>
          </div>

          <p class="font-arabic text-2xl text-emerald-800 mb-1">بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</p>
          <p class="text-xs font-semibold tracking-widest text-emerald-700 uppercase mb-2">Walimatul 'Urs</p>

          <h1 class="font-serif-title text-3xl sm:text-4xl font-bold text-stone-900 mb-2">
            {{ data.party1?.name || 'Fulanah' }} & {{ data.party2?.name || 'Fulan' }}
          </h1>
          
          <div class="w-16 h-0.5 bg-emerald-400 mx-auto my-3"></div>

          <p class="text-xs text-stone-500 mb-1">Kepada Yth. Bapak/Ibu/Saudara/i:</p>
          <div class="inline-block bg-emerald-50 border border-emerald-200 px-5 py-2.5 rounded-2xl my-2">
            <span class="text-base sm:text-lg font-bold text-emerald-900 font-serif-title">{{ recipientName || 'Tamu Undangan' }}</span>
            <span v-if="isPersonal" class="block text-[11px] text-emerald-700 font-medium">Tamu Kehormatan</span>
          </div>

          <button @click="openInvitation"
                  class="mt-6 w-full inline-flex items-center justify-center gap-2 bg-gradient-to-r from-emerald-700 to-emerald-800 hover:from-emerald-800 hover:to-emerald-900 text-white font-semibold px-6 py-3.5 rounded-2xl shadow-xl shadow-emerald-800/25 transition-all transform hover:-translate-y-0.5 text-xs sm:text-sm tracking-wide">
            <span>Buka Undangan</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
            </svg>
          </button>
        </div>
      </div>
    </transition>

    <!-- MAIN CONTENT -->
    <main class="max-w-xl mx-auto bg-white min-h-screen shadow-2xl relative border-x border-stone-200">

      <!-- 1. HERO SECTION -->
      <header class="relative pt-16 pb-16 px-6 text-center bg-gradient-to-b from-[#f0fdf4]/70 via-white to-white">
        <p class="font-arabic text-3xl text-emerald-800 mb-3 leading-relaxed">بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</p>
        <span class="inline-block px-3 py-1 bg-emerald-100 text-emerald-800 text-[11px] font-bold rounded-full uppercase tracking-wider mb-3">
          Undangan Pernikahan
        </span>

        <h1 class="font-serif-title text-4xl sm:text-5xl text-stone-900 font-bold mb-2">
          {{ data.party1?.name || 'Fulanah' }} <span class="text-emerald-700 font-normal">&</span> {{ data.party2?.name || 'Fulan' }}
        </h1>
        <p class="text-xs text-stone-500 font-medium tracking-widest uppercase mb-6">
          {{ formattedEventDate }}
        </p>

        <!-- COUNTDOWN -->
        <div class="grid grid-cols-4 gap-2.5 max-w-xs mx-auto text-center mt-6">
          <div class="bg-emerald-50/70 border border-emerald-200/80 rounded-2xl p-2.5">
            <span class="block text-xl font-bold font-serif-title text-emerald-800">{{ countdown.days }}</span>
            <span class="text-[9px] text-stone-500 uppercase tracking-widest font-semibold">Hari</span>
          </div>
          <div class="bg-emerald-50/70 border border-emerald-200/80 rounded-2xl p-2.5">
            <span class="block text-xl font-bold font-serif-title text-emerald-800">{{ countdown.hours }}</span>
            <span class="text-[9px] text-stone-500 uppercase tracking-widest font-semibold">Jam</span>
          </div>
          <div class="bg-emerald-50/70 border border-emerald-200/80 rounded-2xl p-2.5">
            <span class="block text-xl font-bold font-serif-title text-emerald-800">{{ countdown.minutes }}</span>
            <span class="text-[9px] text-stone-500 uppercase tracking-widest font-semibold">Menit</span>
          </div>
          <div class="bg-emerald-50/70 border border-emerald-200/80 rounded-2xl p-2.5">
            <span class="block text-xl font-bold font-serif-title text-emerald-800">{{ countdown.seconds }}</span>
            <span class="text-[9px] text-stone-500 uppercase tracking-widest font-semibold">Detik</span>
          </div>
        </div>
      </header>

      <!-- 2. AYAT SUCI & GREETING -->
      <section class="py-12 px-6 sm:px-10 text-center border-t border-emerald-50">
        <p class="font-serif-title italic text-stone-600 text-xs sm:text-sm max-w-md mx-auto leading-relaxed">
          "{{ data.opening?.quote || 'Dan di antara tanda-tanda kebesaran-Nya ialah Dia menciptakan pasangan-pasangan untukmu dari jenismu sendiri, agar kamu cenderung dan merasa tenteram kepadanya, dan Dia menjadikan di antaramu rasa kasih dan sayang.' }}"
        </p>
        <p v-if="data.opening?.quote_source" class="text-xs font-bold text-emerald-700 mt-3 tracking-wider">{{ data.opening.quote_source }}</p>

        <div class="mt-8">
          <p class="text-xs text-emerald-700 font-bold tracking-wide uppercase">{{ data.opening?.greeting || 'Assalamu’alaikum Warahmatullahi Wabarakatuh' }}</p>
          <p class="text-xs sm:text-sm text-stone-600 mt-2 leading-relaxed max-w-md mx-auto">
            Dengan memohon rahmat, taufik, dan ridho Allah Subhanahu Wa Ta’ala, kami mengundang Bapak/Ibu/Saudara/i untuk menghadiri syukuran pernikahan kami:
          </p>
        </div>
      </section>

      <!-- 3. PROFILE MEMPELAI -->
      <section class="py-12 px-6 sm:px-10 bg-emerald-50/30">
        <!-- Mempelai Wanita -->
        <div class="text-center mb-10">
          <div class="w-32 h-32 mx-auto rounded-full p-1 border-2 border-dashed border-emerald-300 shadow-md overflow-hidden bg-white mb-4">
            <img v-if="data.party1?.photo_path" :src="data.party1.photo_path" :alt="data.party1.name" class="w-full h-full object-cover rounded-full">
            <div v-else class="w-full h-full rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-serif-title text-4xl">
              {{ (data.party1?.name || 'W').charAt(0) }}
            </div>
          </div>
          <h3 class="font-serif-title text-2xl font-bold text-stone-900">{{ data.party1?.full_name || 'Fulanah, S.Pd' }}</h3>
          <p class="text-xs text-emerald-700 font-semibold mt-1">{{ data.party1?.role_label || 'Mempelai Wanita' }}</p>
          <p class="text-xs text-stone-500 mt-1">{{ data.party1?.child_order || 'Putri pertama dari' }} {{ data.party1?.parents || 'Bpk. Abdullah & Ibu Aminah' }}</p>
        </div>

        <div class="text-center my-4 font-serif-title text-3xl text-emerald-600">&</div>

        <!-- Mempelai Pria -->
        <div class="text-center">
          <div class="w-32 h-32 mx-auto rounded-full p-1 border-2 border-dashed border-emerald-300 shadow-md overflow-hidden bg-white mb-4">
            <img v-if="data.party2?.photo_path" :src="data.party2.photo_path" :alt="data.party2.name" class="w-full h-full object-cover rounded-full">
            <div v-else class="w-full h-full rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-serif-title text-4xl">
              {{ (data.party2?.name || 'P').charAt(0) }}
            </div>
          </div>
          <h3 class="font-serif-title text-2xl font-bold text-stone-900">{{ data.party2?.full_name || 'Fulan, Lc' }}</h3>
          <p class="text-xs text-emerald-700 font-semibold mt-1">{{ data.party2?.role_label || 'Mempelai Pria' }}</p>
          <p class="text-xs text-stone-500 mt-1">{{ data.party2?.child_order || 'Putra kedua dari' }} {{ data.party2?.parents || 'Bpk. Ibrahim & Ibu Khadijah' }}</p>
        </div>
      </section>

      <!-- 4. RANGKAIAN ACARA -->
      <section class="py-14 px-6 sm:px-10">
        <div class="text-center mb-8">
          <span class="text-xs font-semibold text-emerald-700 uppercase tracking-widest">Informasi Acara</span>
          <h2 class="font-serif-title text-3xl font-bold text-stone-900 mt-1">Waktu & Tempat</h2>
          <div class="w-12 h-0.5 bg-emerald-400 mx-auto mt-3"></div>
        </div>

        <div class="space-y-6">
          <div v-for="event in events" :key="event.id"
               class="bg-gradient-to-br from-white to-emerald-50/40 border border-emerald-200/80 rounded-3xl p-6 sm:p-8 shadow-sm text-center relative overflow-hidden">
            <h3 class="font-serif-title text-xl font-bold text-emerald-900 mb-2">{{ event.title }}</h3>
            
            <div class="my-4 py-3 border-y border-emerald-100">
              <p class="font-semibold text-stone-800 text-sm sm:text-base">{{ formatEventDate(event.start_at) }}</p>
              <p class="text-xs text-emerald-700 font-semibold mt-1">
                Pukul {{ formatTime(event.start_at) }} <span v-if="event.end_at">- {{ formatTime(event.end_at) }}</span> {{ event.timezone }}
              </p>
            </div>

            <p class="font-semibold text-stone-800 text-sm">{{ event.venue_name }}</p>
            <p v-if="event.address" class="text-xs text-stone-500 mt-1 leading-relaxed">{{ event.address }}</p>

            <a v-if="event.maps_url" :href="event.maps_url" target="_blank"
               class="inline-flex items-center gap-1.5 mt-5 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-300 text-xs font-semibold px-4 py-2.5 rounded-xl transition-colors">
              <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              <span>Petunjuk Lokasi (Google Maps)</span>
            </a>
          </div>

          <p v-if="events.length === 0" class="text-center text-xs text-stone-400">Jadwal acara belum diatur.</p>
        </div>
      </section>

      <!-- 5. KISAH KAMI / TIMELINE -->
      <section v-if="data.story && data.story.length > 0" class="py-12 px-6 sm:px-10 bg-emerald-50/20">
        <div class="text-center mb-8">
          <span class="text-xs font-semibold text-emerald-700 uppercase tracking-widest">Kisah Kami</span>
          <h2 class="font-serif-title text-3xl font-bold text-stone-900 mt-1">Perjalanan Menuju Halal</h2>
          <div class="w-12 h-0.5 bg-emerald-400 mx-auto mt-3"></div>
        </div>

        <div class="relative border-l-2 border-emerald-200 ml-4 sm:ml-8 space-y-6">
          <div v-for="(story, sIdx) in data.story" :key="sIdx" class="relative pl-6">
            <div class="absolute -left-2.5 top-1.5 w-4 h-4 rounded-full bg-emerald-600 border-4 border-white shadow"></div>
            <span class="text-xs font-bold text-emerald-800 bg-emerald-100/90 px-2.5 py-0.5 rounded-full">{{ story.year }}</span>
            <h4 class="font-serif-title text-base font-bold text-stone-900 mt-1.5">{{ story.title }}</h4>
            <p class="text-xs text-stone-600 mt-1 leading-relaxed">{{ story.description }}</p>
          </div>
        </div>
      </section>

      <!-- 6. GALERI FOTO -->
      <section v-if="data.gallery && data.gallery.length > 0" class="py-12 px-6 sm:px-10">
        <div class="text-center mb-8">
          <span class="text-xs font-semibold text-emerald-700 uppercase tracking-widest">Momen Berharga</span>
          <h2 class="font-serif-title text-3xl font-bold text-stone-900 mt-1">Galeri Foto</h2>
          <div class="w-12 h-0.5 bg-emerald-400 mx-auto mt-3"></div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
          <div v-for="(photo, pIdx) in data.gallery" :key="pIdx"
               class="aspect-square rounded-2xl overflow-hidden shadow-sm border border-emerald-100 group">
            <img :src="photo.url || photo" alt="Galeri" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
          </div>
        </div>
      </section>

      <!-- 7. AMPLOP DIGITAL -->
      <section v-if="data.gifts && data.gifts.length > 0" class="py-12 px-6 sm:px-10 bg-emerald-50/40">
        <div class="text-center mb-8">
          <span class="text-xs font-semibold text-emerald-700 uppercase tracking-widest">Tanda Kasih</span>
          <h2 class="font-serif-title text-3xl font-bold text-stone-900 mt-1">Amplop Digital</h2>
          <div class="w-12 h-0.5 bg-emerald-400 mx-auto mt-3"></div>
          <p class="text-xs text-stone-500 mt-3 max-w-sm mx-auto leading-relaxed">
            Doa restu Anda merupakan karunia terindah bagi kami. Namun jika hendak memberikan tanda kasih secara cashless, silakan melalui rekening di bawah ini:
          </p>
        </div>

        <div class="space-y-4 max-w-sm mx-auto">
          <div v-for="(gift, idx) in data.gifts" :key="idx"
               class="bg-white border border-emerald-200 rounded-2xl p-5 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between mb-2">
              <span class="font-bold text-emerald-800 tracking-wider text-sm">{{ gift.bank_name || 'BANK' }}</span>
              <span class="text-[10px] text-stone-400 uppercase tracking-wider">Transfer Bank</span>
            </div>
            <div class="font-mono text-lg font-bold text-stone-800 tracking-wider my-1">
              {{ gift.account_number }}
            </div>
            <p class="text-xs text-stone-500">a.n. <span class="font-semibold text-stone-800">{{ gift.account_holder }}</span></p>

            <button @click="copyGift(gift.account_number, idx)"
                    class="mt-4 w-full inline-flex items-center justify-center gap-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-semibold py-2 rounded-xl border border-emerald-200 transition-colors">
              <span v-if="copiedIdx !== idx">Salin Nomor Rekening</span>
              <span v-else class="text-emerald-700 font-bold">✓ Tersalin ke Clipboard</span>
            </button>
          </div>
        </div>
      </section>

      <!-- 8. RSVP & DOA RESTU -->
      <section v-if="data.rsvp_enabled !== false || data.wishes_enabled !== false" class="py-14 px-6 sm:px-10" id="rsvp-section">
        <div class="text-center mb-8">
          <span class="text-xs font-semibold text-emerald-700 uppercase tracking-widest">Konfirmasi Kehadiran</span>
          <h2 class="font-serif-title text-3xl font-bold text-stone-900 mt-1">RSVP & Doa Restu</h2>
          <div class="w-12 h-0.5 bg-emerald-400 mx-auto mt-3"></div>
        </div>

        <div v-if="successRsvpMessage" class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-medium text-center">
          <span>{{ successRsvpMessage }}</span>
        </div>

        <form @submit.prevent="submitRsvp" class="bg-white border border-emerald-100 rounded-3xl p-6 sm:p-8 shadow-md space-y-4">
          <div>
            <label class="block text-xs font-semibold text-stone-700 mb-1">Nama Lengkap *</label>
            <input type="text" v-model="rsvpForm.name" required
                   class="w-full text-xs sm:text-sm px-4 py-2.5 rounded-xl border border-emerald-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                   placeholder="Tulis nama Anda">
          </div>

          <div>
            <label class="block text-xs font-semibold text-stone-700 mb-1">No. WhatsApp / HP</label>
            <input type="text" v-model="rsvpForm.phone"
                   class="w-full text-xs sm:text-sm px-4 py-2.5 rounded-xl border border-emerald-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                   placeholder="Contoh: 08123456789">
          </div>

          <div v-if="data.rsvp_enabled !== false">
            <label class="block text-xs font-semibold text-stone-700 mb-1">Konfirmasi Kehadiran *</label>
            <select v-model="rsvpForm.rsvp_status" required
                    class="w-full text-xs sm:text-sm px-4 py-2.5 rounded-xl border border-emerald-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
              <option value="attended">Insya Allah Hadir</option>
              <option value="maybe">Masih Ragu / Belum Pasti</option>
              <option value="not_attended">Afwan, Berhalangan Hadir</option>
            </select>
          </div>

          <div v-if="data.rsvp_enabled !== false && rsvpForm.rsvp_status === 'attended'">
            <label class="block text-xs font-semibold text-stone-700 mb-1">Jumlah Orang yang Hadir</label>
            <input type="number" min="1" max="10" v-model.number="rsvpForm.guest_count"
                   class="w-full text-xs sm:text-sm px-4 py-2.5 rounded-xl border border-emerald-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
          </div>

          <div v-if="data.wishes_enabled !== false">
            <label class="block text-xs font-semibold text-stone-700 mb-1">Doa Restu untuk Mempelai</label>
            <textarea v-model="rsvpForm.message" rows="3"
                      class="w-full text-xs sm:text-sm px-4 py-2.5 rounded-xl border border-emerald-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                      placeholder="Barakallahu laka wa baraka 'alaika..."></textarea>
          </div>

          <button type="submit" :disabled="submittingRsvp"
                  class="w-full bg-gradient-to-r from-emerald-700 to-emerald-800 hover:from-emerald-800 hover:to-emerald-900 text-white font-semibold py-3.5 rounded-xl shadow-md transition-all text-xs sm:text-sm disabled:opacity-50">
            <span v-if="!submittingRsvp">Kirim Doa Restu & Konfirmasi</span>
            <span v-else>Mengirimkan...</span>
          </button>
        </form>

        <div class="mt-8 space-y-3">
          <h4 class="text-xs font-bold text-stone-700 uppercase tracking-wider mb-2">Doa Restu Teman/Kerabat ({{ wishesList.length }})</h4>
          <div class="max-h-72 overflow-y-auto space-y-2.5 pr-1">
            <div v-for="w in wishesList" :key="w.id" class="bg-emerald-50/50 border border-emerald-100 p-3.5 rounded-2xl">
              <span class="font-bold text-emerald-900 text-xs sm:text-sm block">{{ w.name }}</span>
              <p class="text-xs text-stone-600 mt-1 leading-relaxed">{{ w.message }}</p>
              <span class="text-[10px] text-stone-400 mt-1 block">{{ formatWishDate(w.created_at) }}</span>
            </div>
            <p v-if="wishesList.length === 0" class="text-xs text-stone-400">Belum ada ucapan doa masuk.</p>
          </div>
        </div>
      </section>

      <!-- 9. CLOSING SECTION -->
      <section class="py-14 px-6 sm:px-10 text-center bg-gradient-to-b from-white to-[#f0fdf4]/50 border-t border-emerald-100">
        <p class="text-xs sm:text-sm text-stone-600 max-w-md mx-auto leading-relaxed">
          {{ data.closing?.text || 'Merupakan suatu kehormatan dan kebahagiaan bagi kami apabila Bapak/Ibu/Saudara/i berkenan hadir untuk memberikan doa restu kepada kami berdua.' }}
        </p>
        <h3 class="font-serif-title text-2xl font-bold text-emerald-900 mt-6">{{ data.closing?.signature || (data.party1?.name + ' & ' + data.party2?.name) }}</h3>

        <div class="mt-8 pt-6 border-t border-emerald-100">
          <a :href="whatsappShareUrl" target="_blank"
             class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-4 py-2.5 rounded-xl shadow-md transition-colors">
            <span>Bagikan via WhatsApp</span>
          </a>
        </div>

        <p class="text-[10px] text-stone-400 mt-8">Dibuat dengan segenap cinta di <router-link to="/" class="font-semibold text-emerald-700 hover:underline">Kawinan.id</router-link></p>
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
  return 'Segera Hadir';
});

const whatsappShareUrl = computed(() => {
  const title = `Undangan Walimatul 'Urs: ${props.data?.party1?.name || ''} & ${props.data?.party2?.name || ''} - ${window.location.href}`;
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
.font-arabic { font-family: 'Amiri', serif; }
.font-serif-title { font-family: 'Playfair Display', serif; }
.font-body { font-family: 'Plus Jakarta Sans', sans-serif; }
.spin-slow { animation: spin 8s linear infinite; }
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>
