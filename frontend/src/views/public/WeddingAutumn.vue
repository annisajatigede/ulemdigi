<template>
  <div class="font-body bg-[#fbf9f5] text-[#4a3931] antialiased selection:bg-[#f6d6be] selection:text-[#a85a2a] min-h-screen overflow-x-hidden relative">

    <!-- Audio Player Element -->
    <audio v-if="audioSrc" ref="audioPlayer" loop preload="none">
      <source :src="audioSrc" type="audio/mpeg">
    </audio>

    <!-- Floating Audio Toggle Button -->
    <button v-if="audioSrc && opened"
            @click="toggleMusic"
            class="fixed bottom-6 right-6 z-40 bg-white/90 backdrop-blur border border-[#e5bfa7] text-[#b86835] p-3.5 rounded-full shadow-2xl hover:scale-110 transition-transform active:scale-95 focus:outline-none"
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

    <!-- 1. COVER / OPENING MODAL -->
    <transition
      leave-active-class="transition ease-in-out duration-700 transform"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 -translate-y-full">
      <div v-if="!opened"
           class="fixed inset-0 z-50 flex items-center justify-center bg-[#f7f2ea] p-4 sm:p-6 text-center select-none overflow-hidden">

        <!-- Marble Texture Overlay Background -->
        <div class="absolute inset-0 opacity-40 pointer-events-none marble-pattern"></div>

        <!-- Main Invitation Card -->
        <div class="max-w-md w-full bg-white/95 backdrop-blur-md p-8 sm:p-12 rounded-3xl shadow-2xl border border-[#ede2d6] relative overflow-hidden">

          <!-- TOP-RIGHT FLORAL CORNER BOUQUET (Marigold & Autumn Leaves) -->
          <div class="absolute -top-3 -right-3 w-44 sm:w-52 pointer-events-none z-10">
            <svg viewBox="0 0 240 240" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto drop-shadow-md">
              <g opacity="0.95">
                <!-- Russet Brown Leaves & Twigs -->
                <path d="M220 30 C190 70, 160 110, 190 160 C175 140, 170 115, 185 85 Z" fill="#6d4632" opacity="0.75"/>
                <path d="M230 60 C200 95, 175 140, 205 190 C190 170, 185 140, 200 110 Z" fill="#8d5f46" opacity="0.7"/>
                <path d="M160 40 C140 60, 120 75, 140 100 C135 85, 138 70, 150 55 Z" fill="#b08468" opacity="0.65"/>
                <path d="M185 20 C155 35, 130 50, 150 75 C145 60, 150 45, 170 30 Z" fill="#7d523c" opacity="0.6"/>
                <path d="M210 110 C195 130, 185 160, 200 185 C190 165, 192 145, 205 130 Z" fill="#6d4632" opacity="0.6"/>
                <circle cx="175" cy="115" r="3.5" fill="#543322"/>
                <circle cx="188" cy="138" r="3" fill="#543322"/>
                <circle cx="198" cy="165" r="2.5" fill="#543322"/>
                <circle cx="150" cy="85" r="3" fill="#8d5f46"/>

                <!-- Peach Daisy / Marigold 1 (Top) -->
                <g transform="translate(180, 50)">
                  <!-- Petals -->
                  <ellipse cx="0" cy="-28" rx="7.5" ry="20" fill="#fad2a4" transform="rotate(0)"/>
                  <ellipse cx="0" cy="-28" rx="7.5" ry="20" fill="#fad2a4" transform="rotate(30)"/>
                  <ellipse cx="0" cy="-28" rx="7.5" ry="20" fill="#fad2a4" transform="rotate(60)"/>
                  <ellipse cx="0" cy="-28" rx="7.5" ry="20" fill="#fad2a4" transform="rotate(90)"/>
                  <ellipse cx="0" cy="-28" rx="7.5" ry="20" fill="#fad2a4" transform="rotate(120)"/>
                  <ellipse cx="0" cy="-28" rx="7.5" ry="20" fill="#fad2a4" transform="rotate(150)"/>
                  <ellipse cx="0" cy="-28" rx="7.5" ry="20" fill="#fad2a4" transform="rotate(180)"/>
                  <ellipse cx="0" cy="-28" rx="7.5" ry="20" fill="#fad2a4" transform="rotate(210)"/>
                  <ellipse cx="0" cy="-28" rx="7.5" ry="20" fill="#fad2a4" transform="rotate(240)"/>
                  <ellipse cx="0" cy="-28" rx="7.5" ry="20" fill="#fad2a4" transform="rotate(270)"/>
                  <ellipse cx="0" cy="-28" rx="7.5" ry="20" fill="#fad2a4" transform="rotate(300)"/>
                  <ellipse cx="0" cy="-28" rx="7.5" ry="20" fill="#fad2a4" transform="rotate(330)"/>
                  <!-- Inner layered petals -->
                  <ellipse cx="0" cy="-18" rx="5.5" ry="14" fill="#f4be88" transform="rotate(15)"/>
                  <ellipse cx="0" cy="-18" rx="5.5" ry="14" fill="#f4be88" transform="rotate(45)"/>
                  <ellipse cx="0" cy="-18" rx="5.5" ry="14" fill="#f4be88" transform="rotate(75)"/>
                  <ellipse cx="0" cy="-18" rx="5.5" ry="14" fill="#f4be88" transform="rotate(105)"/>
                  <ellipse cx="0" cy="-18" rx="5.5" ry="14" fill="#f4be88" transform="rotate(135)"/>
                  <ellipse cx="0" cy="-18" rx="5.5" ry="14" fill="#f4be88" transform="rotate(165)"/>
                  <ellipse cx="0" cy="-18" rx="5.5" ry="14" fill="#f4be88" transform="rotate(195)"/>
                  <ellipse cx="0" cy="-18" rx="5.5" ry="14" fill="#f4be88" transform="rotate(225)"/>
                  <ellipse cx="0" cy="-18" rx="5.5" ry="14" fill="#f4be88" transform="rotate(255)"/>
                  <ellipse cx="0" cy="-18" rx="5.5" ry="14" fill="#f4be88" transform="rotate(285)"/>
                  <ellipse cx="0" cy="-18" rx="5.5" ry="14" fill="#f4be88" transform="rotate(315)"/>
                  <ellipse cx="0" cy="-18" rx="5.5" ry="14" fill="#f4be88" transform="rotate(345)"/>
                  <!-- Core center -->
                  <circle cx="0" cy="0" r="11" fill="#754329"/>
                  <circle cx="0" cy="0" r="9" fill="#915635"/>
                  <circle cx="0" cy="0" r="6" fill="#b0693f"/>
                </g>

                <!-- Deep Terracotta Blossom 2 (Right) -->
                <g transform="translate(205, 100)">
                  <ellipse cx="0" cy="-26" rx="7" ry="18" fill="#e07d46" transform="rotate(0)"/>
                  <ellipse cx="0" cy="-26" rx="7" ry="18" fill="#e07d46" transform="rotate(30)"/>
                  <ellipse cx="0" cy="-26" rx="7" ry="18" fill="#e07d46" transform="rotate(60)"/>
                  <ellipse cx="0" cy="-26" rx="7" ry="18" fill="#e07d46" transform="rotate(90)"/>
                  <ellipse cx="0" cy="-26" rx="7" ry="18" fill="#e07d46" transform="rotate(120)"/>
                  <ellipse cx="0" cy="-26" rx="7" ry="18" fill="#e07d46" transform="rotate(150)"/>
                  <ellipse cx="0" cy="-26" rx="7" ry="18" fill="#e07d46" transform="rotate(180)"/>
                  <ellipse cx="0" cy="-26" rx="7" ry="18" fill="#e07d46" transform="rotate(210)"/>
                  <ellipse cx="0" cy="-26" rx="7" ry="18" fill="#e07d46" transform="rotate(240)"/>
                  <ellipse cx="0" cy="-26" rx="7" ry="18" fill="#e07d46" transform="rotate(270)"/>
                  <ellipse cx="0" cy="-26" rx="7" ry="18" fill="#e07d46" transform="rotate(300)"/>
                  <ellipse cx="0" cy="-26" rx="7" ry="18" fill="#e07d46" transform="rotate(330)"/>
                  <circle cx="0" cy="0" r="10" fill="#5a311c"/>
                  <circle cx="0" cy="0" r="7" fill="#7c4427"/>
                </g>

                <!-- Small Soft Cream Daisy 3 -->
                <g transform="translate(150, 95) scale(0.65)">
                  <ellipse cx="0" cy="-24" rx="7" ry="16" fill="#fde5c3" transform="rotate(0)"/>
                  <ellipse cx="0" cy="-24" rx="7" ry="16" fill="#fde5c3" transform="rotate(40)"/>
                  <ellipse cx="0" cy="-24" rx="7" ry="16" fill="#fde5c3" transform="rotate(80)"/>
                  <ellipse cx="0" cy="-24" rx="7" ry="16" fill="#fde5c3" transform="rotate(120)"/>
                  <ellipse cx="0" cy="-24" rx="7" ry="16" fill="#fde5c3" transform="rotate(160)"/>
                  <ellipse cx="0" cy="-24" rx="7" ry="16" fill="#fde5c3" transform="rotate(200)"/>
                  <ellipse cx="0" cy="-24" rx="7" ry="16" fill="#fde5c3" transform="rotate(240)"/>
                  <ellipse cx="0" cy="-24" rx="7" ry="16" fill="#fde5c3" transform="rotate(280)"/>
                  <ellipse cx="0" cy="-24" rx="7" ry="16" fill="#fde5c3" transform="rotate(320)"/>
                  <circle cx="0" cy="0" r="9" fill="#804a29"/>
                </g>
              </g>
            </svg>
          </div>

          <!-- BOTTOM-LEFT FLORAL CORNER BOUQUET (Matching Cluster) -->
          <div class="absolute -bottom-3 -left-3 w-44 sm:w-52 pointer-events-none z-10 rotate-180">
            <svg viewBox="0 0 240 240" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto drop-shadow-md">
              <g opacity="0.95">
                <path d="M220 30 C190 70, 160 110, 190 160 C175 140, 170 115, 185 85 Z" fill="#6d4632" opacity="0.75"/>
                <path d="M230 60 C200 95, 175 140, 205 190 C190 170, 185 140, 200 110 Z" fill="#8d5f46" opacity="0.7"/>
                <path d="M160 40 C140 60, 120 75, 140 100 C135 85, 138 70, 150 55 Z" fill="#b08468" opacity="0.65"/>
                <path d="M185 20 C155 35, 130 50, 150 75 C145 60, 150 45, 170 30 Z" fill="#7d523c" opacity="0.6"/>
                <circle cx="175" cy="115" r="3.5" fill="#543322"/>
                <circle cx="188" cy="138" r="3" fill="#543322"/>

                <g transform="translate(180, 50)">
                  <ellipse cx="0" cy="-28" rx="7.5" ry="20" fill="#fad2a4" transform="rotate(0)"/>
                  <ellipse cx="0" cy="-28" rx="7.5" ry="20" fill="#fad2a4" transform="rotate(30)"/>
                  <ellipse cx="0" cy="-28" rx="7.5" ry="20" fill="#fad2a4" transform="rotate(60)"/>
                  <ellipse cx="0" cy="-28" rx="7.5" ry="20" fill="#fad2a4" transform="rotate(90)"/>
                  <ellipse cx="0" cy="-28" rx="7.5" ry="20" fill="#fad2a4" transform="rotate(120)"/>
                  <ellipse cx="0" cy="-28" rx="7.5" ry="20" fill="#fad2a4" transform="rotate(150)"/>
                  <ellipse cx="0" cy="-28" rx="7.5" ry="20" fill="#fad2a4" transform="rotate(180)"/>
                  <ellipse cx="0" cy="-28" rx="7.5" ry="20" fill="#fad2a4" transform="rotate(210)"/>
                  <ellipse cx="0" cy="-28" rx="7.5" ry="20" fill="#fad2a4" transform="rotate(240)"/>
                  <ellipse cx="0" cy="-28" rx="7.5" ry="20" fill="#fad2a4" transform="rotate(270)"/>
                  <ellipse cx="0" cy="-28" rx="7.5" ry="20" fill="#fad2a4" transform="rotate(300)"/>
                  <ellipse cx="0" cy="-28" rx="7.5" ry="20" fill="#fad2a4" transform="rotate(330)"/>
                  <circle cx="0" cy="0" r="11" fill="#754329"/>
                  <circle cx="0" cy="0" r="7" fill="#b0693f"/>
                </g>

                <g transform="translate(205, 100)">
                  <ellipse cx="0" cy="-26" rx="7" ry="18" fill="#e07d46" transform="rotate(0)"/>
                  <ellipse cx="0" cy="-26" rx="7" ry="18" fill="#e07d46" transform="rotate(45)"/>
                  <ellipse cx="0" cy="-26" rx="7" ry="18" fill="#e07d46" transform="rotate(90)"/>
                  <ellipse cx="0" cy="-26" rx="7" ry="18" fill="#e07d46" transform="rotate(135)"/>
                  <ellipse cx="0" cy="-26" rx="7" ry="18" fill="#e07d46" transform="rotate(180)"/>
                  <ellipse cx="0" cy="-26" rx="7" ry="18" fill="#e07d46" transform="rotate(225)"/>
                  <ellipse cx="0" cy="-26" rx="7" ry="18" fill="#e07d46" transform="rotate(270)"/>
                  <ellipse cx="0" cy="-26" rx="7" ry="18" fill="#e07d46" transform="rotate(315)"/>
                  <circle cx="0" cy="0" r="10" fill="#5a311c"/>
                </g>
              </g>
            </svg>
          </div>

          <!-- Card Content (Identical typography to reference image) -->
          <div class="relative z-20 py-4">
            <p class="text-xs sm:text-sm font-serif tracking-widest text-[#8d624b] uppercase mb-1">
              {{ data.opening?.greeting_short || 'Kami mengundang Anda di' }}
            </p>
            <h2 class="text-xs sm:text-sm font-serif tracking-[0.35em] text-[#b86835] font-semibold uppercase mb-6">
              {{ data.opening?.title_upper || 'P E R N I K A H A N' }}
            </h2>

            <!-- Dynamic Bride & Groom Calligraphy Names -->
            <div class="my-6">
              <h1 class="font-cursive text-4xl sm:text-5xl md:text-6xl text-[#9c5932] leading-tight">
                {{ data.party1?.name || 'Alex Chastian' }}
              </h1>
              <div class="font-serif italic text-2xl sm:text-3xl text-[#c27b4e] my-1 font-light">&</div>
              <h1 class="font-cursive text-4xl sm:text-5xl md:text-6xl text-[#9c5932] leading-tight">
                {{ data.party2?.name || 'Olivia Wilson' }}
              </h1>
            </div>

            <!-- Date Block with Divider Lines -->
            <div class="my-6 max-w-[260px] mx-auto text-[#7d5642]">
              <div class="flex items-center justify-center gap-3 text-sm tracking-widest font-serif font-semibold uppercase">
                <span>{{ eventMonthShort }}</span>
                <span class="text-[#c2845c]">|</span>
                <span class="text-lg font-bold text-[#9c5932]">{{ eventDay }}</span>
                <span class="text-[#c2845c]">|</span>
                <span>{{ eventYear }}</span>
              </div>
              <div class="w-full h-px bg-gradient-to-r from-transparent via-[#b37a54]/50 to-transparent my-2"></div>
              <div class="text-xs font-serif tracking-wide text-[#7d5642]">
                {{ eventDayName }}, Pukul {{ eventTime }}
              </div>
            </div>

            <!-- Location Venue -->
            <p class="text-[11px] sm:text-xs text-[#8c6b58] font-serif max-w-xs mx-auto leading-relaxed mb-6">
              {{ primaryEventVenue || '123 Anywhere St, Any City, ST 1234' }}
            </p>

            <!-- Recipient Badge -->
            <div class="bg-[#f9f4ec] border border-[#e8dacb] px-4 py-2.5 rounded-2xl my-4 inline-block max-w-xs">
              <p class="text-[10px] uppercase tracking-wider text-[#a47b63] font-medium">Kepada Yth. Tamu Undangan:</p>
              <p class="text-sm sm:text-base font-serif font-bold text-[#6d4632] mt-0.5">{{ recipientName || 'Tamu Undangan' }}</p>
              <span v-if="isPersonal" class="inline-block text-[10px] text-[#b86835] font-semibold bg-[#fae8dc] px-2 py-0.5 rounded-full mt-1">Tamu Spesial</span>
            </div>

            <!-- Open Invitation Button -->
            <div class="mt-4">
              <button @click="openInvitation"
                      class="inline-flex items-center justify-center gap-2.5 bg-gradient-to-r from-[#b86835] via-[#c97943] to-[#b86835] hover:from-[#a65b2a] hover:to-[#a65b2a] text-white font-serif font-medium px-8 py-3.5 rounded-full shadow-lg shadow-[#b86835]/30 transition-all transform hover:-translate-y-0.5 active:translate-y-0 text-xs sm:text-sm tracking-widest uppercase">
                <svg class="w-4 h-4 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76" />
                </svg>
                <span>Buka Undangan</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </transition>

    <!-- 2. MAIN INVITATION CONTENT -->
    <main class="max-w-xl mx-auto bg-white min-h-screen shadow-2xl relative border-x border-[#ede2d6] overflow-hidden">

      <!-- HERO SECTION -->
      <header class="relative pt-20 pb-24 px-6 text-center bg-[#fdfbf7] overflow-hidden border-b border-[#f0e6dc]">
        <div class="absolute inset-0 opacity-25 pointer-events-none marble-pattern"></div>

        <!-- Corner Ornaments on Hero -->
        <div class="absolute -top-6 -right-6 w-36 pointer-events-none opacity-80">
          <svg viewBox="0 0 240 240" fill="none" class="w-full h-auto">
            <ellipse cx="180" cy="50" rx="20" ry="8" fill="#fad2a4" transform="rotate(30 180 50)"/>
            <ellipse cx="180" cy="50" rx="20" ry="8" fill="#fad2a4" transform="rotate(90 180 50)"/>
            <ellipse cx="180" cy="50" rx="20" ry="8" fill="#fad2a4" transform="rotate(150 180 50)"/>
            <circle cx="180" cy="50" r="9" fill="#754329"/>
          </svg>
        </div>

        <div class="relative z-10">
          <span class="inline-block px-3.5 py-1 bg-[#faebd7] text-[#9c5932] text-[10px] font-serif font-semibold rounded-full tracking-[0.2em] uppercase mb-4 border border-[#e8d2be]">
            Walimatul 'Urs
          </span>

          <p class="text-xs font-serif tracking-widest text-[#8d624b] uppercase mb-2">
            The Wedding of
          </p>

          <h1 class="font-cursive text-5xl sm:text-6xl text-[#9c5932] my-2 leading-tight">
            {{ data.party1?.name || 'Alex' }} & {{ data.party2?.name || 'Olivia' }}
          </h1>

          <div class="w-16 h-0.5 bg-[#d49a78] mx-auto my-4"></div>

          <p class="text-xs sm:text-sm font-serif text-[#7d5642] tracking-wider">
            {{ formattedEventDate }}
          </p>
        </div>
      </header>

      <!-- OPENING QUOTE / AYAT -->
      <section v-if="data.opening?.quote" class="py-14 px-6 sm:px-10 text-center bg-white border-b border-[#f0e6dc] relative">
        <div class="max-w-md mx-auto relative p-6 sm:p-8 rounded-3xl bg-[#fdfbf7] border border-[#eee4da]">
          <span class="text-3xl font-serif text-[#c27b4e] block mb-2 leading-none">“</span>
          <p class="text-xs sm:text-sm font-serif italic text-[#634839] leading-relaxed">
            {{ data.opening.quote }}
          </p>
          <p v-if="data.opening.quote_source" class="text-[11px] font-serif font-bold text-[#a85a2a] mt-4 tracking-wider uppercase">
            — {{ data.opening.quote_source }}
          </p>
        </div>
      </section>

      <!-- COUPLE SECTION (MEMPELAI) -->
      <section class="py-16 px-6 sm:px-10 bg-[#fdfbf7] border-b border-[#f0e6dc]">
        <div class="text-center mb-12">
          <span class="text-[10px] font-serif font-semibold text-[#b86835] uppercase tracking-[0.25em]">Mempelai Bahagia</span>
          <h2 class="font-serif text-2xl sm:text-3xl font-bold text-[#5c3e2e] mt-1">Pengantin</h2>
        </div>

        <div class="space-y-12 max-w-md mx-auto">
          <!-- Mempelai Wanita / Party 1 -->
          <div class="text-center bg-white p-6 rounded-3xl shadow-sm border border-[#eee4da]">
            <div class="w-32 h-32 mx-auto rounded-full p-1.5 bg-gradient-to-tr from-[#b86835] via-[#fad2a4] to-[#b86835] shadow-md mb-4 overflow-hidden">
              <img v-if="data.party1?.photo_path" :src="data.party1.photo_path" :alt="data.party1.name" class="w-full h-full object-cover rounded-full">
              <div v-else class="w-full h-full bg-[#f7eedf] flex items-center justify-center text-4xl rounded-full">👰</div>
            </div>
            <h3 class="font-serif text-xl font-bold text-[#5c3e2e]">{{ data.party1?.full_name || data.party1?.name || 'Mempelai Wanita' }}</h3>
            <span class="inline-block text-[11px] font-serif text-[#b86835] font-semibold uppercase tracking-wider mt-0.5 mb-2">{{ data.party1?.role_label || 'Mempelai Wanita' }}</span>
            <p v-if="data.party1?.parents" class="text-xs font-serif text-[#7d5642] max-w-xs mx-auto leading-relaxed">
              {{ data.party1?.child_order ? data.party1.child_order + ' ' : '' }} {{ data.party1.parents }}
            </p>
            <div v-if="data.party1?.instagram" class="mt-3">
              <a :href="'https://instagram.com/' + data.party1.instagram.replace('@','')" target="_blank"
                 class="inline-flex items-center gap-1.5 text-xs font-serif text-[#9c5932] hover:text-[#5c3e2e] bg-[#fbf5ee] px-3 py-1 rounded-full border border-[#eedccb]">
                <span>📸</span>
                <span>{{ data.party1.instagram }}</span>
              </a>
            </div>
          </div>

          <!-- Divider & -->
          <div class="text-center font-cursive text-4xl text-[#c27b4e] -my-4">&</div>

          <!-- Mempelai Pria / Party 2 -->
          <div class="text-center bg-white p-6 rounded-3xl shadow-sm border border-[#eee4da]">
            <div class="w-32 h-32 mx-auto rounded-full p-1.5 bg-gradient-to-tr from-[#b86835] via-[#fad2a4] to-[#b86835] shadow-md mb-4 overflow-hidden">
              <img v-if="data.party2?.photo_path" :src="data.party2.photo_path" :alt="data.party2.name" class="w-full h-full object-cover rounded-full">
              <div v-else class="w-full h-full bg-[#f7eedf] flex items-center justify-center text-4xl rounded-full">🤵</div>
            </div>
            <h3 class="font-serif text-xl font-bold text-[#5c3e2e]">{{ data.party2?.full_name || data.party2?.name || 'Mempelai Pria' }}</h3>
            <span class="inline-block text-[11px] font-serif text-[#b86835] font-semibold uppercase tracking-wider mt-0.5 mb-2">{{ data.party2?.role_label || 'Mempelai Pria' }}</span>
            <p v-if="data.party2?.parents" class="text-xs font-serif text-[#7d5642] max-w-xs mx-auto leading-relaxed">
              {{ data.party2?.child_order ? data.party2.child_order + ' ' : '' }} {{ data.party2.parents }}
            </p>
            <div v-if="data.party2?.instagram" class="mt-3">
              <a :href="'https://instagram.com/' + data.party2.instagram.replace('@','')" target="_blank"
                 class="inline-flex items-center gap-1.5 text-xs font-serif text-[#9c5932] hover:text-[#5c3e2e] bg-[#fbf5ee] px-3 py-1 rounded-full border border-[#eedccb]">
                <span>📸</span>
                <span>{{ data.party2.instagram }}</span>
              </a>
            </div>
          </div>
        </div>
      </section>

      <!-- COUNTDOWN SECTION -->
      <section class="py-14 px-6 text-center bg-white border-b border-[#f0e6dc]">
        <span class="text-[10px] font-serif font-semibold text-[#b86835] uppercase tracking-[0.25em]">SAVE THE DATE</span>
        <h2 class="font-serif text-2xl font-bold text-[#5c3e2e] mt-1 mb-8">Menuju Hari Bahagia</h2>

        <div class="grid grid-cols-4 gap-2 sm:gap-4 max-w-sm mx-auto mb-8">
          <div class="bg-[#fcf7ee] border border-[#ebdccb] p-3 rounded-2xl shadow-sm">
            <span class="block text-2xl sm:text-3xl font-serif font-bold text-[#8d4f29]">{{ countdown.days }}</span>
            <span class="text-[10px] uppercase font-serif tracking-wider text-[#a47b63]">Hari</span>
          </div>
          <div class="bg-[#fcf7ee] border border-[#ebdccb] p-3 rounded-2xl shadow-sm">
            <span class="block text-2xl sm:text-3xl font-serif font-bold text-[#8d4f29]">{{ countdown.hours }}</span>
            <span class="text-[10px] uppercase font-serif tracking-wider text-[#a47b63]">Jam</span>
          </div>
          <div class="bg-[#fcf7ee] border border-[#ebdccb] p-3 rounded-2xl shadow-sm">
            <span class="block text-2xl sm:text-3xl font-serif font-bold text-[#8d4f29]">{{ countdown.minutes }}</span>
            <span class="text-[10px] uppercase font-serif tracking-wider text-[#a47b63]">Menit</span>
          </div>
          <div class="bg-[#fcf7ee] border border-[#ebdccb] p-3 rounded-2xl shadow-sm">
            <span class="block text-2xl sm:text-3xl font-serif font-bold text-[#8d4f29]">{{ countdown.seconds }}</span>
            <span class="text-[10px] uppercase font-serif tracking-wider text-[#a47b63]">Detik</span>
          </div>
        </div>

        <a :href="whatsappShareUrl" target="_blank"
           class="inline-flex items-center gap-2 text-xs font-serif text-[#8d4f29] hover:text-[#5c3e2e] bg-[#faefe4] px-4 py-2 rounded-full border border-[#e8d2be]">
          <span>💬</span>
          <span>Bagikan ke WhatsApp</span>
        </a>
      </section>

      <!-- EVENTS SCHEDULE SECTION -->
      <section v-if="events && events.length > 0" class="py-16 px-6 sm:px-10 bg-[#fdfbf7] border-b border-[#f0e6dc]">
        <div class="text-center mb-10">
          <span class="text-[10px] font-serif font-semibold text-[#b86835] uppercase tracking-[0.25em]">AGENDA ACARA</span>
          <h2 class="font-serif text-2xl sm:text-3xl font-bold text-[#5c3e2e] mt-1">Waktu & Tempat</h2>
        </div>

        <div class="space-y-6 max-w-md mx-auto">
          <div v-for="(ev, idx) in events" :key="idx"
               class="bg-white p-6 rounded-3xl shadow-sm border border-[#eee4da] text-center">
            <h3 class="font-serif text-lg font-bold text-[#5c3e2e] mb-2">{{ ev.title }}</h3>
            <div class="w-10 h-0.5 bg-[#d49a78] mx-auto mb-4"></div>

            <p class="text-xs sm:text-sm font-serif font-semibold text-[#8d4f29]">
              {{ formatEventDateTime(ev.start_at) }}
            </p>

            <div class="my-4 text-xs font-serif text-[#7d5642] leading-relaxed">
              <p class="font-bold text-[#5c3e2e]">{{ ev.venue_name }}</p>
              <p class="text-[11px] mt-1">{{ ev.address }}</p>
            </div>

            <div class="flex flex-wrap items-center justify-center gap-3 pt-2">
              <a v-if="ev.maps_url" :href="ev.maps_url" target="_blank"
                 class="inline-flex items-center gap-1.5 bg-[#b86835] text-white text-xs font-serif px-4 py-2 rounded-xl shadow-md hover:bg-[#a65b2a] transition-colors">
                <span>📍</span>
                <span>Petunjuk Lokasi (Maps)</span>
              </a>
              <button @click="downloadCalendar(ev)"
                      class="inline-flex items-center gap-1.5 bg-white border border-[#d6ba9f] text-[#8d4f29] text-xs font-serif px-4 py-2 rounded-xl hover:bg-[#fcf5eb] transition-colors">
                <span>📅</span>
                <span>Simpan Tanggal</span>
              </button>
            </div>
          </div>
        </div>
      </section>

      <!-- LOVE STORY SECTION -->
      <section v-if="data.story && data.story.length > 0" class="py-16 px-6 sm:px-10 bg-white border-b border-[#f0e6dc]">
        <div class="text-center mb-10">
          <span class="text-[10px] font-serif font-semibold text-[#b86835] uppercase tracking-[0.25em]">KISAH KAMI</span>
          <h2 class="font-serif text-2xl sm:text-3xl font-bold text-[#5c3e2e] mt-1">Cerita Cinta</h2>
        </div>

        <div class="space-y-6 max-w-md mx-auto relative border-l-2 border-[#e8d2be] ml-4 sm:ml-auto pl-6 sm:pl-8">
          <div v-for="(story, sIdx) in data.story" :key="sIdx" class="relative">
            <div class="absolute -left-[31px] sm:-left-[39px] top-1.5 w-4 h-4 rounded-full bg-[#b86835] border-4 border-white shadow"></div>
            <span class="text-[11px] font-serif font-bold text-[#a85a2a] bg-[#faebd7] px-2.5 py-0.5 rounded-full">{{ story.year || 'Momen Spesial' }}</span>
            <h4 class="font-serif font-bold text-sm text-[#5c3e2e] mt-2 mb-1">{{ story.title }}</h4>
            <p class="text-xs font-serif text-[#7d5642] leading-relaxed">{{ story.description }}</p>
          </div>
        </div>
      </section>

      <!-- GALLERY SECTION -->
      <section v-if="data.gallery && data.gallery.length > 0" class="py-16 px-6 sm:px-10 bg-[#fdfbf7] border-b border-[#f0e6dc]">
        <div class="text-center mb-10">
          <span class="text-[10px] font-serif font-semibold text-[#b86835] uppercase tracking-[0.25em]">MEMORI KAMI</span>
          <h2 class="font-serif text-2xl sm:text-3xl font-bold text-[#5c3e2e] mt-1">Galeri Foto</h2>
        </div>

        <div class="grid grid-cols-2 gap-3 max-w-md mx-auto">
          <div v-for="(photo, pIdx) in data.gallery" :key="pIdx"
               class="aspect-square rounded-2xl overflow-hidden bg-[#eee4da] shadow-sm border border-[#e4d4c5] group">
            <img :src="photo.url || photo" alt="Galeri" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
          </div>
        </div>
      </section>

      <!-- DIGITAL GIFTS / AMPLOP DIGITAL -->
      <section v-if="data.gifts && data.gifts.length > 0" class="py-16 px-6 sm:px-10 bg-white border-b border-[#f0e6dc]">
        <div class="text-center mb-10">
          <span class="text-[10px] font-serif font-semibold text-[#b86835] uppercase tracking-[0.25em]">TANDA KASIH</span>
          <h2 class="font-serif text-2xl sm:text-3xl font-bold text-[#5c3e2e] mt-1">Amplop Digital</h2>
          <p class="text-xs font-serif text-[#8c6b58] mt-2 max-w-xs mx-auto">
            Doa restu Anda merupakan karunia terindah bagi kami. Namun jika hendak memberi tanda kasih, dapat melalui:
          </p>
        </div>

        <div class="space-y-4 max-w-sm mx-auto">
          <div v-for="(gift, idx) in data.gifts" :key="idx"
               class="bg-[#fcf7ee] border border-[#ebdccb] rounded-2xl p-5 shadow-sm text-center">
            <div class="flex items-center justify-between mb-2">
              <span class="font-serif font-bold text-[#5c3e2e] text-sm">{{ gift.bank_name || 'BANK' }}</span>
              <span class="text-[10px] font-serif text-[#a47b63] uppercase tracking-wider">Transfer Online</span>
            </div>
            <div class="font-mono text-base sm:text-lg font-bold text-[#8d4f29] tracking-wider my-1">
              {{ gift.account_number }}
            </div>
            <p class="text-xs font-serif text-[#7d5642]">a.n. <span class="font-semibold">{{ gift.account_holder }}</span></p>

            <button @click="copyGift(gift.account_number, idx)"
                    class="mt-3 w-full bg-white hover:bg-[#faebd7] text-xs font-serif font-semibold py-2 rounded-xl border border-[#d6ba9f] text-[#8d4f29] transition-colors">
              <span v-if="copiedIdx !== idx">Salin Nomor Rekening</span>
              <span v-else class="text-[#10b981] font-bold">✓ Berhasil Disalin</span>
            </button>
          </div>
        </div>
      </section>

      <!-- RSVP & UCAPAN / DOA RESTU -->
      <section class="py-16 px-6 sm:px-10 bg-[#fdfbf7] border-b border-[#f0e6dc]">
        <div class="text-center mb-10">
          <span class="text-[10px] font-serif font-semibold text-[#b86835] uppercase tracking-[0.25em]">KONFIRMASI KEHADIRAN</span>
          <h2 class="font-serif text-2xl sm:text-3xl font-bold text-[#5c3e2e] mt-1">RSVP & Doa Restu</h2>
        </div>

        <div class="max-w-md mx-auto">
          <!-- RSVP Form -->
          <div class="bg-white p-6 rounded-3xl shadow-sm border border-[#eee4da] mb-8">
            <div v-if="successRsvpMessage" class="mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs rounded-xl font-serif text-center">
              {{ successRsvpMessage }}
            </div>

            <form @submit.prevent="submitRsvp" class="space-y-4">
              <div>
                <label class="block text-xs font-serif font-semibold text-[#5c3e2e] mb-1">Nama Lengkap</label>
                <input v-model="rsvpForm.name" type="text" required placeholder="Nama Anda"
                       class="w-full text-xs font-serif px-3.5 py-2.5 rounded-xl border border-[#d6ba9f] focus:outline-none focus:ring-2 focus:ring-[#b86835]">
              </div>

              <div>
                <label class="block text-xs font-serif font-semibold text-[#5c3e2e] mb-1">Konfirmasi Kehadiran</label>
                <select v-model="rsvpForm.rsvp_status"
                        class="w-full text-xs font-serif px-3.5 py-2.5 rounded-xl border border-[#d6ba9f] focus:outline-none focus:ring-2 focus:ring-[#b86835]">
                  <option value="attended">Hadir</option>
                  <option value="uncertain">Masih Ragu</option>
                  <option value="not_attended">Tidak Hadir</option>
                </select>
              </div>

              <div v-if="rsvpForm.rsvp_status === 'attended'">
                <label class="block text-xs font-serif font-semibold text-[#5c3e2e] mb-1">Jumlah Tamu Hadir</label>
                <input v-model.number="rsvpForm.guest_count" type="number" min="1" max="5"
                       class="w-full text-xs font-serif px-3.5 py-2.5 rounded-xl border border-[#d6ba9f] focus:outline-none focus:ring-2 focus:ring-[#b86835]">
              </div>

              <div>
                <label class="block text-xs font-serif font-semibold text-[#5c3e2e] mb-1">Doa & Ucapan Selamat</label>
                <textarea v-model="rsvpForm.message" rows="3" required placeholder="Tuliskan doa restu untuk kedua mempelai..."
                          class="w-full text-xs font-serif px-3.5 py-2.5 rounded-xl border border-[#d6ba9f] focus:outline-none focus:ring-2 focus:ring-[#b86835]"></textarea>
              </div>

              <button type="submit" :disabled="submittingRsvp"
                      class="w-full bg-[#b86835] hover:bg-[#a65b2a] text-white text-xs font-serif font-semibold py-3 rounded-xl shadow-md transition-all disabled:opacity-50">
                <span v-if="submittingRsvp">Mengirim...</span>
                <span v-else>Kirim Konfirmasi & Doa Restu</span>
              </button>
            </form>
          </div>

          <!-- Feed Ucapan -->
          <div class="space-y-3">
            <h4 class="text-xs font-serif font-bold text-[#5c3e2e] uppercase tracking-wider mb-3">Doa & Ucapan Restu ({{ wishesList.length }})</h4>
            <div v-if="wishesList.length === 0" class="text-center py-6 text-xs font-serif text-[#a47b63]">
              Belum ada ucapan. Jadilah yang pertama memberikan doa restu!
            </div>
            <div v-for="(w, wIdx) in wishesList" :key="wIdx"
                 class="bg-white p-4 rounded-2xl border border-[#eee4da] shadow-xs text-left">
              <div class="flex items-center justify-between mb-1">
                <span class="font-serif font-bold text-xs text-[#5c3e2e]">{{ w.name }}</span>
                <span class="text-[10px] font-serif text-[#a47b63]">{{ formatWishTime(w.created_at) }}</span>
              </div>
              <p class="text-xs font-serif text-[#634839] leading-relaxed">{{ w.message }}</p>
            </div>
          </div>
        </div>
      </section>

      <!-- CLOSING & FOOTER -->
      <footer class="py-16 px-6 text-center bg-white text-[#7d5642] border-t border-[#f0e6dc]">
        <div class="max-w-md mx-auto">
          <p class="text-xs font-serif leading-relaxed mb-4">
            {{ data.closing?.text || 'Merupakan suatu kehormatan dan kebahagiaan bagi kami apabila Bapak/Ibu/Saudara/i berkenan hadir untuk memberikan doa restu.' }}
          </p>
          <p class="font-cursive text-3xl text-[#9c5932] my-4">
            {{ data.party1?.name || 'Alex' }} & {{ data.party2?.name || 'Olivia' }}
          </p>
          <p class="text-xs font-serif font-semibold text-[#5c3e2e]">
            {{ data.closing?.signature || 'Keluarga Besar Kedua Mempelai' }}
          </p>

          <p class="text-[10px] text-[#a47b63] mt-12 font-serif">
            Dibuat dengan segenap cinta di <router-link to="/" class="font-bold text-[#b86835] hover:underline">Forkoway.com</router-link>
          </p>
        </div>
      </footer>

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

const audioSrc = computed(() => {
  return props.data?.music_url || props.data?.music_path || '';
});

const countdown = ref({ days: 0, hours: 0, minutes: 0, seconds: 0 });
let timerInterval = null;

const primaryEvent = computed(() => {
  return (props.events && props.events.length > 0) ? props.events[0] : null;
});

const primaryEventVenue = computed(() => {
  return primaryEvent.value ? (primaryEvent.value.venue_name + (primaryEvent.value.address ? ', ' + primaryEvent.value.address : '')) : '';
});

const targetDate = computed(() => {
  if (primaryEvent.value?.start_at) {
    return new Date(primaryEvent.value.start_at);
  }
  const d = new Date();
  d.setDate(d.getDate() + 14);
  return d;
});

// Date formatting for reference design block: "Sept | 17 | 2023"
const eventMonthShort = computed(() => {
  return targetDate.value.toLocaleDateString('en-US', { month: 'short' });
});

const eventDay = computed(() => {
  return targetDate.value.getDate();
});

const eventYear = computed(() => {
  return targetDate.value.getFullYear();
});

const eventDayName = computed(() => {
  return targetDate.value.toLocaleDateString('id-ID', { weekday: 'long' });
});

const eventTime = computed(() => {
  return targetDate.value.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }).replace('.', ':');
});

const formattedEventDate = computed(() => {
  return targetDate.value.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
});

const formatEventDateTime = (dt) => {
  if (!dt) return '';
  return new Date(dt).toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' }).replace('.', ':');
};

const formatWishTime = (dt) => {
  if (!dt) return '';
  return new Date(dt).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
};

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

const downloadCalendar = (ev) => {
  const title = encodeURIComponent(ev.title || 'Pernikahan');
  const details = encodeURIComponent(`Undangan Pernikahan ${props.data?.party1?.name || ''} & ${props.data?.party2?.name || ''}`);
  const location = encodeURIComponent(`${ev.venue_name || ''}, ${ev.address || ''}`);
  const start = new Date(ev.start_at).toISOString().replace(/-|:|\.\d+/g, '');
  const end = ev.end_at ? new Date(ev.end_at).toISOString().replace(/-|:|\.\d+/g, '') : start;
  const url = `https://calendar.google.com/calendar/render?action=TEMPLATE&text=${title}&dates=${start}/${end}&details=${details}&location=${location}`;
  window.open(url, '_blank');
};

const submitRsvp = async () => {
  try {
    submittingRsvp.value = true;
    const res = await api.post(`/invitations/public/${props.invitation.slug}/rsvp`, rsvpForm.value);
    successRsvpMessage.value = res.data.message || 'Terima kasih atas konfirmasi dan ucapan Anda!';
    if (res.data.guest) {
      wishesList.value.unshift({
        name: res.data.guest.name,
        message: res.data.guest.message || rsvpForm.value.message,
        created_at: new Date().toISOString()
      });
    }
    rsvpForm.value.message = '';
    setTimeout(() => {
      successRsvpMessage.value = null;
    }, 5000);
  } catch (err) {
    alert(err.response?.data?.message || 'Gagal mengirim RSVP. Silakan coba lagi.');
  } finally {
    submittingRsvp.value = false;
  }
};

onMounted(() => {
  updateCountdown();
  timerInterval = setInterval(updateCountdown, 1000);
});

onUnmounted(() => {
  if (timerInterval) clearInterval(timerInterval);
});
</script>

<style scoped>
.spin-slow {
  animation: spin 8s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.marble-pattern {
  background-image: radial-gradient(#ebe0d3 1px, transparent 1px), radial-gradient(#ebe0d3 1px, #fdfbf7 1px);
  background-size: 40px 40px;
  background-position: 0 0, 20px 20px;
}
</style>
