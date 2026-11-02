<template>
  <div class="font-body bg-[#fff9fa] text-stone-700 antialiased selection:bg-rose-100 selection:text-rose-600 min-h-screen overflow-x-hidden relative">

    <!-- Audio Player Element -->
    <audio v-if="audioSrc" :src="audioSrc" ref="audioPlayer" loop preload="auto">
      <source :src="audioSrc" type="audio/mpeg">
    </audio>

    <!-- Floating Audio Toggle Button -->
    <button v-if="audioSrc && opened"
            @click="toggleMusic"
            class="fixed bottom-6 right-6 z-40 bg-white/90 backdrop-blur border border-rose-200 text-rose-600 p-3.5 rounded-full shadow-xl hover:scale-110 transition-transform active:scale-95 focus:outline-none"
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
           class="fixed inset-0 z-50 flex items-center justify-center bg-gradient-to-b from-[#fff1f2] via-[#ffe4e6] to-[#fff1f2] p-6 text-center select-none">
        <div class="max-w-md w-full bg-white/80 backdrop-blur-md p-8 sm:p-10 rounded-3xl shadow-2xl border border-rose-100/80 relative overflow-hidden">
          <!-- Floral Decorative corners -->
          <div class="absolute -top-10 -left-10 w-28 h-28 bg-rose-200/40 rounded-full blur-xl"></div>
          <div class="absolute -bottom-10 -right-10 w-28 h-28 bg-rose-200/40 rounded-full blur-xl"></div>

          <p class="text-xs font-semibold tracking-widest text-rose-500 uppercase mb-3">{{ data.opening?.title || 'The Wedding of' }}</p>
          <h1 class="font-serif-title text-4xl sm:text-5xl font-bold text-stone-800 tracking-tight mb-2">
            {{ data.party1?.name || 'Alex' }} <span class="font-cursive text-rose-500 font-normal text-3xl">&</span> {{ data.party2?.name || 'Olivia' }}
          </h1>
          <div class="w-16 h-0.5 bg-rose-300 mx-auto my-4"></div>

          <p class="text-xs text-stone-500 mb-1">Kepada Yth. Bapak/Ibu/Saudara/i:</p>
          <div class="inline-block bg-rose-50 border border-rose-200/80 px-4 py-2 rounded-xl my-2">
            <span class="text-base sm:text-lg font-semibold text-rose-800">{{ recipientName || 'Tamu Undangan' }}</span>
            <span v-if="isPersonal" class="block text-[11px] text-rose-600 font-medium">Link Tamu Spesial</span>
          </div>
          <p class="text-xs text-stone-500 mt-2 mb-6 italic">*Mohon maaf bila ada kesalahan penulisan nama/gelar</p>

          <button @click="openInvitation"
                  class="w-full inline-flex items-center justify-center gap-2 bg-gradient-to-r from-rose-500 to-rose-600 hover:from-rose-600 hover:to-rose-700 text-white font-medium px-6 py-3.5 rounded-2xl shadow-lg shadow-rose-500/25 transition-all transform hover:-translate-y-0.5 active:translate-y-0 text-sm tracking-wide">
            <svg class="w-5 h-5 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76" />
            </svg>
            <span>Buka Undangan</span>
          </button>
        </div>
      </div>
    </transition>

    <!-- MAIN INVITATION CONTENT -->
    <main class="max-w-xl mx-auto bg-white min-h-screen shadow-2xl relative border-x border-rose-100">

      <!-- 1. HERO SECTION -->
      <header class="relative pt-16 pb-20 px-6 text-center bg-gradient-to-b from-[#fff1f2]/80 via-white to-white overflow-hidden">
        <div class="inline-block px-3 py-1 bg-rose-100/70 text-rose-700 text-xs font-semibold rounded-full tracking-wider uppercase mb-4">
          Walimatul 'Urs
        </div>
        <h1 class="font-cursive text-5xl sm:text-6xl text-rose-600 mb-2">
          {{ data.party1?.name || 'Alex' }} & {{ data.party2?.name || 'Olivia' }}
        </h1>
        <p class="font-serif-title text-stone-600 text-sm tracking-widest uppercase mb-6">
          {{ formattedEventDate }}
        </p>

        <!-- COUNTDOWN TIMER -->
        <div class="grid grid-cols-4 gap-2.5 max-w-xs mx-auto text-center mt-6">
          <div class="bg-rose-50/80 border border-rose-100 rounded-2xl p-2.5">
            <span class="block text-xl sm:text-2xl font-bold text-rose-700">{{ countdown.days }}</span>
            <span class="text-[10px] text-stone-500 uppercase tracking-wider">Hari</span>
          </div>
          <div class="bg-rose-50/80 border border-rose-100 rounded-2xl p-2.5">
            <span class="block text-xl sm:text-2xl font-bold text-rose-700">{{ countdown.hours }}</span>
            <span class="text-[10px] text-stone-500 uppercase tracking-wider">Jam</span>
          </div>
          <div class="bg-rose-50/80 border border-rose-100 rounded-2xl p-2.5">
            <span class="block text-xl sm:text-2xl font-bold text-rose-700">{{ countdown.minutes }}</span>
            <span class="text-[10px] text-stone-500 uppercase tracking-wider">Menit</span>
          </div>
          <div class="bg-rose-50/80 border border-rose-100 rounded-2xl p-2.5">
            <span class="block text-xl sm:text-2xl font-bold text-rose-700">{{ countdown.seconds }}</span>
            <span class="text-[10px] text-stone-500 uppercase tracking-wider">Detik</span>
          </div>
        </div>
      </header>

      <!-- 2. OPENING & AYAT SUCI -->
      <section class="py-12 px-6 sm:px-10 text-center border-t border-rose-50">
        <p class="font-serif-title italic text-stone-600 text-xs sm:text-sm max-w-md mx-auto leading-relaxed">
          "{{ data.opening?.quote || 'Dan di antara tanda-tanda kebesaran-Nya ialah Dia menciptakan pasangan-pasangan untukmu dari jenismu sendiri...' }}"
        </p>
        <p v-if="data.opening?.quote_source" class="text-xs font-semibold text-rose-600 mt-3 tracking-wider">{{ data.opening.quote_source }}</p>

        <div class="mt-8">
          <p class="text-xs text-rose-500 font-semibold tracking-wide uppercase">{{ data.opening?.greeting || 'Assalamu’alaikum Warahmatullahi Wabarakatuh' }}</p>
          <p class="text-xs sm:text-sm text-stone-600 mt-2 leading-relaxed max-w-md mx-auto">
            Dengan memohon rahmat dan ridho Allah SWT, kami bermaksud menyelenggarakan syukuran pernikahan putra-putri kami:
          </p>
        </div>
      </section>

      <!-- 3. PROFILE MEMPELAI -->
      <section class="py-10 px-6 sm:px-10 bg-rose-50/30">
        <!-- Mempelai Pria -->
        <div class="text-center mb-10">
          <div class="w-32 h-32 mx-auto rounded-full p-1 border-2 border-dashed border-rose-300 shadow-md overflow-hidden bg-white mb-4">
            <img v-if="data.party1?.photo_path" :src="data.party1.photo_path" :alt="data.party1.name" class="w-full h-full object-cover rounded-full">
            <div v-else class="w-full h-full rounded-full bg-rose-100 flex items-center justify-center text-rose-400 font-serif-title text-4xl">
              {{ (data.party1?.name || 'P').charAt(0) }}
            </div>
          </div>
          <h3 class="font-serif-title text-2xl font-bold text-stone-800">{{ data.party1?.full_name || data.party1?.name || 'Muhammad Fulan, S.T' }}</h3>
          <p class="text-xs text-rose-600 font-medium mt-1">{{ data.party1?.role_label || 'Mempelai Pria' }}</p>
          <p class="text-xs text-stone-500 mt-1">{{ data.party1?.child_order || 'Putra pertama dari' }} {{ data.party1?.parents || 'Bpk. Robert & Ibu Sarah' }}</p>
        </div>

        <div class="text-center my-4 font-cursive text-4xl text-rose-400">&</div>

        <!-- Mempelai Wanita -->
        <div class="text-center">
          <div class="w-32 h-32 mx-auto rounded-full p-1 border-2 border-dashed border-rose-300 shadow-md overflow-hidden bg-white mb-4">
            <img v-if="data.party2?.photo_path" :src="data.party2.photo_path" :alt="data.party2.name" class="w-full h-full object-cover rounded-full">
            <div v-else class="w-full h-full rounded-full bg-rose-100 flex items-center justify-center text-rose-400 font-serif-title text-4xl">
              {{ (data.party2?.name || 'W').charAt(0) }}
            </div>
          </div>
          <h3 class="font-serif-title text-2xl font-bold text-stone-800">{{ data.party2?.full_name || data.party2?.name || 'Siti Fulanah, S.Ked' }}</h3>
          <p class="text-xs text-rose-600 font-medium mt-1">{{ data.party2?.role_label || 'Mempelai Wanita' }}</p>
          <p class="text-xs text-stone-500 mt-1">{{ data.party2?.child_order || 'Putri kedua dari' }} {{ data.party2?.parents || 'Bpk. David & Ibu Emily' }}</p>
        </div>
      </section>

      <!-- 4. RANGKAIAN ACARA -->
      <section class="py-14 px-6 sm:px-10">
        <div class="text-center mb-8">
          <span class="text-xs font-semibold text-rose-500 uppercase tracking-widest">Informasi Acara</span>
          <h2 class="font-serif-title text-3xl font-bold text-stone-800 mt-1">Waktu & Tempat</h2>
          <div class="w-12 h-0.5 bg-rose-300 mx-auto mt-3"></div>
        </div>

        <div class="space-y-6">
          <div v-for="event in events" :key="event.id"
               class="bg-gradient-to-br from-white to-rose-50/50 border border-rose-100 rounded-3xl p-6 sm:p-8 shadow-sm text-center relative overflow-hidden">
            <h3 class="font-serif-title text-xl font-bold text-rose-900 mb-2">{{ event.title }}</h3>
            
            <div class="my-4 py-3 border-y border-rose-100">
              <p class="font-semibold text-stone-800 text-sm sm:text-base">{{ formatEventDate(event.start_at) }}</p>
              <p class="text-xs text-rose-600 font-medium mt-1">
                Pukul {{ formatTime(event.start_at) }} <span v-if="event.end_at">- {{ formatTime(event.end_at) }}</span> {{ event.timezone }}
              </p>
            </div>

            <p class="font-semibold text-stone-800 text-sm">{{ event.venue_name }}</p>
            <p v-if="event.address" class="text-xs text-stone-500 mt-1 leading-relaxed">{{ event.address }}</p>

            <a v-if="event.maps_url" :href="event.maps_url" target="_blank"
               class="inline-flex items-center gap-1.5 mt-5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-semibold px-4 py-2.5 rounded-xl transition-colors">
              <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              <span>Petunjuk Lokasi (Google Maps)</span>
            </a>
          </div>

          <p v-if="events.length === 0" class="text-center text-xs text-stone-400">Jadwal acara belum diatur.</p>
        </div>
      </section>

      <!-- 5. KISAH CINTA / STORY -->
      <section v-if="data.story && data.story.length > 0" class="py-12 px-6 sm:px-10 bg-rose-50/20">
        <div class="text-center mb-8">
          <span class="text-xs font-semibold text-rose-500 uppercase tracking-widest">Our Journey</span>
          <h2 class="font-serif-title text-3xl font-bold text-stone-800 mt-1">Kisah Cinta Kami</h2>
          <div class="w-12 h-0.5 bg-rose-300 mx-auto mt-3"></div>
        </div>

        <div class="relative border-l-2 border-rose-200 ml-4 sm:ml-8 space-y-6">
          <div v-for="(story, sIdx) in data.story" :key="sIdx" class="relative pl-6">
            <div class="absolute -left-2.5 top-1.5 w-4 h-4 rounded-full bg-rose-500 border-4 border-white shadow"></div>
            <span class="text-xs font-bold text-rose-600 bg-rose-100/80 px-2.5 py-0.5 rounded-full">{{ story.year }}</span>
            <h4 class="font-serif-title text-base font-bold text-stone-800 mt-1.5">{{ story.title }}</h4>
            <p class="text-xs text-stone-600 mt-1 leading-relaxed">{{ story.description }}</p>
          </div>
        </div>
      </section>

      <!-- 6. GALERI FOTO -->
      <section v-if="data.gallery && data.gallery.length > 0" class="py-12 px-6 sm:px-10">
        <div class="text-center mb-8">
          <span class="text-xs font-semibold text-rose-500 uppercase tracking-widest">Momen Indah</span>
          <h2 class="font-serif-title text-3xl font-bold text-stone-800 mt-1">Galeri Foto</h2>
          <div class="w-12 h-0.5 bg-rose-300 mx-auto mt-3"></div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
          <div v-for="(photo, pIdx) in data.gallery" :key="pIdx"
               class="aspect-square rounded-2xl overflow-hidden shadow-sm border border-rose-100 group">
            <img :src="photo.url || photo" alt="Galeri" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
          </div>
        </div>
      </section>

      <!-- 7. AMPLOP DIGITAL / HADIAH -->
      <section v-if="data.gifts && data.gifts.length > 0" class="py-12 px-6 sm:px-10 bg-rose-50/40">
        <div class="text-center mb-8">
          <span class="text-xs font-semibold text-rose-500 uppercase tracking-widest">Tanda Kasih</span>
          <h2 class="font-serif-title text-3xl font-bold text-stone-800 mt-1">Amplop Digital</h2>
          <div class="w-12 h-0.5 bg-rose-300 mx-auto mt-3"></div>
          <p class="text-xs text-stone-500 mt-3 max-w-sm mx-auto leading-relaxed">
            Doa restu Anda merupakan karunia terindah bagi kami. Namun jika ingin memberikan tanda kasih secara cashless, silakan melalui rekening di bawah ini:
          </p>
        </div>

        <div class="space-y-4 max-w-sm mx-auto">
          <div v-for="(gift, idx) in data.gifts" :key="idx"
               class="bg-white border border-rose-200 rounded-2xl p-5 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between mb-2">
              <span class="font-bold text-rose-800 tracking-wider text-sm">{{ gift.bank_name || 'BANK' }}</span>
              <span class="text-[10px] text-stone-400 uppercase tracking-wider">Transfer Bank</span>
            </div>
            <div class="font-mono text-lg font-bold text-stone-800 tracking-wider my-1">
              {{ gift.account_number }}
            </div>
            <p class="text-xs text-stone-500">a.n. <span class="font-medium text-stone-700">{{ gift.account_holder }}</span></p>

            <button @click="copyGift(gift.account_number, idx)"
                    class="mt-4 w-full inline-flex items-center justify-center gap-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-semibold py-2 rounded-xl border border-rose-200 transition-colors">
              <span v-if="copiedIdx !== idx">Salin Nomor Rekening</span>
              <span v-else class="text-emerald-600 font-bold">✓ Tersalin ke Clipboard</span>
            </button>
          </div>
        </div>
      </section>

      <!-- 8. FORM RSVP & BUKU TAMU -->
      <section v-if="data.rsvp_enabled !== false || data.wishes_enabled !== false" class="py-14 px-6 sm:px-10" id="rsvp-section">
        <div class="text-center mb-8">
          <span class="text-xs font-semibold text-rose-500 uppercase tracking-widest">Konfirmasi & Doa</span>
          <h2 class="font-serif-title text-3xl font-bold text-stone-800 mt-1">RSVP & Ucapan</h2>
          <div class="w-12 h-0.5 bg-rose-300 mx-auto mt-3"></div>
        </div>

        <!-- Toast Alert -->
        <div v-if="successRsvpMessage" class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-medium text-center">
          <span>{{ successRsvpMessage }}</span>
        </div>

        <form @submit.prevent="submitRsvp" class="bg-white border border-rose-100 rounded-3xl p-6 sm:p-8 shadow-md space-y-4">
          <div>
            <label class="block text-xs font-semibold text-stone-700 mb-1">Nama Lengkap *</label>
            <input type="text" v-model="rsvpForm.name" required
                   class="w-full text-xs sm:text-sm px-4 py-2.5 rounded-xl border border-rose-200 focus:ring-2 focus:ring-rose-400 focus:border-rose-400 focus:outline-none"
                   placeholder="Tulis nama Anda">
          </div>

          <div>
            <label class="block text-xs font-semibold text-stone-700 mb-1">No. WhatsApp / HP</label>
            <input type="text" v-model="rsvpForm.phone"
                   class="w-full text-xs sm:text-sm px-4 py-2.5 rounded-xl border border-rose-200 focus:ring-2 focus:ring-rose-400 focus:border-rose-400 focus:outline-none"
                   placeholder="Contoh: 08123456789">
          </div>

          <div v-if="data.rsvp_enabled !== false">
            <label class="block text-xs font-semibold text-stone-700 mb-1">Konfirmasi Kehadiran *</label>
            <select v-model="rsvpForm.rsvp_status" required
                    class="w-full text-xs sm:text-sm px-4 py-2.5 rounded-xl border border-rose-200 focus:ring-2 focus:ring-rose-400 focus:border-rose-400 focus:outline-none">
              <option value="attended">Ya, Saya akan Hadir</option>
              <option value="maybe">Masih Ragu / Belum Pasti</option>
              <option value="not_attended">Maaf, Tidak Dapat Hadir</option>
            </select>
          </div>

          <div v-if="data.rsvp_enabled !== false && rsvpForm.rsvp_status === 'attended'">
            <label class="block text-xs font-semibold text-stone-700 mb-1">Jumlah Orang yang Hadir</label>
            <input type="number" min="1" max="10" v-model.number="rsvpForm.guest_count"
                   class="w-full text-xs sm:text-sm px-4 py-2.5 rounded-xl border border-rose-200 focus:ring-2 focus:ring-rose-400 focus:border-rose-400 focus:outline-none">
          </div>

          <div v-if="data.wishes_enabled !== false">
            <label class="block text-xs font-semibold text-stone-700 mb-1">Doa Restu & Ucapan</label>
            <textarea v-model="rsvpForm.message" rows="3"
                      class="w-full text-xs sm:text-sm px-4 py-2.5 rounded-xl border border-rose-200 focus:ring-2 focus:ring-rose-400 focus:border-rose-400 focus:outline-none"
                      placeholder="Tulis ucapan dan doa terbaik untuk kami..."></textarea>
          </div>

          <button type="submit" :disabled="submittingRsvp"
                  class="w-full bg-gradient-to-r from-rose-500 to-rose-600 hover:from-rose-600 hover:to-rose-700 text-white font-semibold py-3 rounded-xl shadow-md transition-all text-xs sm:text-sm disabled:opacity-50">
            <span v-if="!submittingRsvp">Kirim Konfirmasi & Ucapan</span>
            <span v-else>Mengirimkan...</span>
          </button>
        </form>

        <!-- FEED UCAPAN TERBARU -->
        <div class="mt-8 space-y-3">
          <h4 class="text-xs font-bold text-stone-700 uppercase tracking-wider mb-2">Ucapan & Doa Teman/Kerabat ({{ wishesList.length }})</h4>
          <div class="max-h-72 overflow-y-auto space-y-2.5 pr-1">
            <div v-for="w in wishesList" :key="w.id" class="bg-rose-50/50 border border-rose-100 p-3.5 rounded-2xl">
              <span class="font-bold text-rose-900 text-xs sm:text-sm block">{{ w.name }}</span>
              <p class="text-xs text-stone-600 mt-1 leading-relaxed">{{ w.message }}</p>
              <span class="text-[10px] text-stone-400 mt-1 block">{{ formatWishDate(w.created_at) }}</span>
            </div>
            <p v-if="wishesList.length === 0" class="text-xs text-stone-400">Belum ada ucapan masuk.</p>
          </div>
        </div>
      </section>

      <!-- 9. CLOSING SECTION -->
      <section class="py-14 px-6 sm:px-10 text-center bg-gradient-to-b from-white to-[#fff1f2]/60 border-t border-rose-100">
        <p class="text-xs sm:text-sm text-stone-600 max-w-md mx-auto leading-relaxed">
          {{ data.closing?.text || 'Merupakan suatu kehormatan dan kebahagiaan bagi kami apabila Bapak/Ibu/Saudara/i berkenan hadir untuk memberikan doa restu.' }}
        </p>
        <h3 class="font-cursive text-4xl text-rose-600 mt-6">{{ data.closing?.signature || (data.party1?.name + ' & ' + data.party2?.name) }}</h3>

        <!-- Tombol Bagikan WhatsApp -->
        <div class="mt-8 pt-6 border-t border-rose-100">
          <a :href="whatsappShareUrl"
             target="_blank"
             class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-4 py-2.5 rounded-xl shadow-md transition-colors">
            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
              <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
            </svg>
            <span>Bagikan Undangan via WhatsApp</span>
          </a>
        </div>

        <p class="text-[10px] text-stone-400 mt-8">Dibuat dengan segenap cinta di <router-link to="/" class="font-semibold text-rose-500 hover:underline">Forkoway.com</router-link></p>
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

const audioSrc = computed(() => {
  return props.data?.music_url || props.data?.music_path || '';
});

const countdown = ref({ days: 0, hours: 0, minutes: 0, seconds: 0 });
let timerInterval = null;

const targetDate = computed(() => {
  if (props.events && props.events[0]?.start_at) {
    return new Date(props.events[0].start_at);
  }
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
  const title = `Halo! Berikut undangan pernikahan digital ${props.data?.party1?.name || ''} & ${props.data?.party2?.name || ''}: ${window.location.href}`;
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
    audioPlayer.value.play().then(() => {
      isPlaying.value = true;
    }).catch(() => {
      isPlaying.value = false;
    });
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
  setTimeout(() => {
    copiedIdx.value = null;
  }, 2500);
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
    successRsvpMessage.value = res.data.message || 'Konfirmasi kehadiran dan ucapan Anda berhasil terkirim!';
    if (rsvpForm.value.message) {
      wishesList.value.unshift({
        id: Date.now(),
        name: rsvpForm.value.name,
        message: rsvpForm.value.message,
        created_at: new Date().toISOString()
      });
    }
  } catch (err) {
    await alertError('Gagal Mengirim RSVP', err.response?.data?.message || 'Gagal mengirimkan konfirmasi.');
  } finally {
    submittingRsvp.value = false;
  }
};

onMounted(() => {
  updateCountdown();
  timerInterval = setInterval(updateCountdown, 1000);
  if (props.previewMode) {
    opened.value = true;
  }
});

onUnmounted(() => {
  if (timerInterval) clearInterval(timerInterval);
  if (audioPlayer.value) audioPlayer.value.pause();
});
</script>

<style scoped>
.font-serif-title { font-family: 'Playfair Display', serif; }
.font-cursive { font-family: 'Great Vibes', cursive; }
.font-body { font-family: 'Plus Jakarta Sans', sans-serif; }
.spin-slow { animation: spin 8s linear infinite; }
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>
