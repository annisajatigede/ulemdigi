<template>
  <div class="min-h-screen bg-stone-50 pb-20">
    <!-- Header -->
    <header class="bg-white border-b border-stone-200 sticky top-16 z-30 shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <div>
            <div class="flex items-center gap-2">
              <router-link to="/dashboard" class="text-xs text-stone-500 hover:text-stone-800 font-semibold">← Dashboard</router-link>
              <span class="text-stone-300">/</span>
              <span class="text-xs font-bold text-rose-600 uppercase">{{ invitation?.category || 'WEDDING' }}</span>
            </div>
            <h2 class="font-extrabold text-2xl text-stone-900 leading-tight mt-1">
              Editor Undangan: <span class="text-rose-600">{{ safeData?.party1?.name || 'Momen' }}</span>
            </h2>
          </div>

          <div class="flex items-center gap-3">
            <a :href="`/u/${invitation?.slug}?preview=1`" target="_blank"
               class="bg-white hover:bg-stone-50 text-stone-700 border border-stone-200 text-xs font-bold px-4 py-2.5 rounded-xl shadow-sm transition-all flex items-center gap-1.5">
              <span>Lihat Tab Baru ↗</span>
            </a>
            <button type="button" @click="saveChanges" :disabled="saving"
                    class="bg-rose-600 hover:bg-rose-700 text-white text-xs sm:text-sm font-bold px-6 py-2.5 rounded-xl shadow-md shadow-rose-600/25 transition-all disabled:opacity-50">
              <span v-if="saving">Menyimpan...</span>
              <span v-else>Simpan Perubahan</span>
            </button>
          </div>
        </div>
      </div>
    </header>

    <!-- Content -->
    <div v-if="loading" class="py-20 text-center text-stone-400 text-sm">
      Memuat data editor undangan...
    </div>

    <div v-else class="py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Status Message -->
        <div v-if="statusMessage" class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold rounded-2xl flex items-center justify-between">
          <span>{{ statusMessage }}</span>
          <button @click="statusMessage = null" class="text-emerald-600 font-bold hover:text-emerald-800">×</button>
        </div>

        <!-- Errors Message -->
        <div v-if="errorMessage" class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm font-semibold rounded-2xl flex items-center justify-between">
          <span>{{ errorMessage }}</span>
          <button @click="errorMessage = null" class="text-rose-600 font-bold hover:text-rose-800">×</button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

          <!-- LEFT COLUMN: SECTION FORMS -->
          <div class="lg:col-span-7 bg-white rounded-3xl border border-stone-200 shadow-sm p-6 sm:p-8">
            <!-- SECTION TABS -->
            <div class="flex overflow-x-auto space-x-2 border-b border-stone-200 pb-3 mb-6 scrollbar-none">
              <button type="button" @click="activeTab = 'dasar'"
                      :class="activeTab === 'dasar' ? 'bg-stone-900 text-white' : 'bg-stone-100 text-stone-600 hover:bg-stone-200'"
                      class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-colors">
                1. Dasar & Tema
              </button>
              <button type="button" @click="activeTab = 'pembuka'"
                      :class="activeTab === 'pembuka' ? 'bg-stone-900 text-white' : 'bg-stone-100 text-stone-600 hover:bg-stone-200'"
                      class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-colors">
                2. Pembuka
              </button>
              <button type="button" @click="activeTab = 'mempelai'"
                      :class="activeTab === 'mempelai' ? 'bg-stone-900 text-white' : 'bg-stone-100 text-stone-600 hover:bg-stone-200'"
                      class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-colors">
                3. {{ isKhitan ? 'Anak & Orang Tua' : (isGeneral ? 'Penyelenggara' : 'Mempelai') }}
              </button>
              <button type="button" @click="activeTab = 'acara'"
                      :class="activeTab === 'acara' ? 'bg-stone-900 text-white' : 'bg-stone-100 text-stone-600 hover:bg-stone-200'"
                      class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-colors">
                4. Acara
              </button>
              <button type="button" @click="activeTab = 'kisah'"
                      :class="activeTab === 'kisah' ? 'bg-stone-900 text-white' : 'bg-stone-100 text-stone-600 hover:bg-stone-200'"
                      class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-colors">
                5. Kisah / Cerita
              </button>
              <button type="button" @click="activeTab = 'galeri'"
                      :class="activeTab === 'galeri' ? 'bg-stone-900 text-white' : 'bg-stone-100 text-stone-600 hover:bg-stone-200'"
                      class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-colors">
                6. Galeri ({{ (safeData.gallery || []).length }}/{{ maxPhotos }})
              </button>
              <button type="button" @click="activeTab = 'amplop'"
                      :class="activeTab === 'amplop' ? 'bg-stone-900 text-white' : 'bg-stone-100 text-stone-600 hover:bg-stone-200'"
                      class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-colors">
                7. Amplop Digital
              </button>
              <button type="button" @click="activeTab = 'penutup'"
                      :class="activeTab === 'penutup' ? 'bg-stone-900 text-white' : 'bg-stone-100 text-stone-600 hover:bg-stone-200'"
                      class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-colors">
                8. RSVP & Penutup
              </button>
            </div>

            <form @submit.prevent="saveChanges" class="space-y-6">

              <!-- 1. TAB DASAR & TEMA -->
              <div v-show="activeTab === 'dasar'" class="space-y-6">
                <h3 class="text-base font-bold text-stone-900">Pengaturan Dasar & Tema</h3>

                <div>
                  <label class="block text-xs font-bold text-stone-700 mb-1">Tautan / Slug Undangan *</label>
                  <div class="flex items-center">
                    <span class="bg-stone-100 text-stone-500 text-xs px-3 py-2.5 rounded-l-xl border border-r-0 border-stone-200 font-mono">
                      /u/
                    </span>
                    <input type="text" v-model="formData.slug" required
                           class="w-full text-xs font-mono px-3 py-2.5 rounded-r-xl border border-stone-200 focus:ring-1 focus:ring-rose-500 focus:outline-none">
                  </div>
                  <p class="text-[11px] text-stone-400 mt-1">Hanya huruf kecil, angka, dan tanda hubung (-).</p>
                </div>

                <div>
                  <label class="block text-xs font-bold text-stone-700 mb-2">Desain Template Digunakan</label>
                  <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    <label v-for="tmpl in templates" :key="tmpl.id" class="cursor-pointer">
                      <input type="radio" v-model="formData.template_id" :value="tmpl.id" class="peer sr-only">
                      <div class="p-3 rounded-xl border-2 peer-checked:border-rose-600 peer-checked:bg-rose-50/40 text-center transition-all border-stone-200 hover:border-stone-300">
                        <div class="font-bold text-xs text-stone-800">{{ tmpl.name }}</div>
                        <span class="text-[10px] text-stone-500 capitalize">{{ tmpl.category }}</span>
                      </div>
                    </label>
                  </div>
                </div>

                <div>
                  <label class="block text-xs font-bold text-stone-700 mb-1">Warna Aksen Tema</label>
                  <div class="flex items-center gap-3">
                    <input type="color" v-model="formData.theme_color"
                           class="w-10 h-10 p-0.5 rounded-xl border border-stone-200 cursor-pointer">
                    <span class="text-xs font-mono text-stone-600">{{ formData.theme_color }}</span>
                  </div>
                </div>

                <div>
                  <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-bold text-stone-700">Musik Latar Belakang</label>
                    <span v-if="safeData.music_path" class="text-[11px] font-semibold text-emerald-600 flex items-center gap-1">
                      <span>✓ Aktif:</span>
                      <span class="font-bold text-stone-800">{{ safeData.music_title || 'Audio Aktif' }}</span>
                    </span>
                    <span v-else class="text-[11px] font-semibold text-stone-400">🔇 Tanpa Musik</span>
                  </div>

                  <!-- Music Source Selector Tabs -->
                  <div class="bg-stone-100 p-1 rounded-2xl flex gap-1 mb-4 text-xs">
                    <button type="button" @click="musicSource = 'preset'"
                            :class="musicSource === 'preset' ? 'bg-white shadow text-rose-600 font-bold' : 'text-stone-600 hover:text-stone-900 font-medium'"
                            class="flex-1 py-2 rounded-xl text-center transition-all flex items-center justify-center gap-1.5">
                      <span>🎵</span>
                      <span>Pilihan Admin</span>
                    </button>
                    <button type="button" @click="musicSource = 'custom'"
                            :class="musicSource === 'custom' ? 'bg-white shadow text-rose-600 font-bold' : 'text-stone-600 hover:text-stone-900 font-medium'"
                            class="flex-1 py-2 rounded-xl text-center transition-all flex items-center justify-center gap-1.5">
                      <span>📤</span>
                      <span>Upload Sendiri</span>
                    </button>
                    <button type="button" @click="musicSource = 'none'"
                            :class="musicSource === 'none' ? 'bg-white shadow text-stone-900 font-bold' : 'text-stone-600 hover:text-stone-900 font-medium'"
                            class="flex-1 py-2 rounded-xl text-center transition-all flex items-center justify-center gap-1.5">
                      <span>🔇</span>
                      <span>Tanpa Musik</span>
                    </button>
                  </div>

                  <!-- 1. PRESET MUSICS (Disediakan Admin) -->
                  <div v-show="musicSource === 'preset'" class="space-y-3">
                    <p class="text-[11px] text-stone-500">Pilih musik pengiring yang telah disiapkan admin untuk tema acara Anda. Klik tombol putar untuk mendengarkan cuplikan audio:</p>

                    <div class="space-y-2 max-h-64 overflow-y-auto pr-1">
                      <label v-for="music in presetMusics" :key="music.id"
                             class="block p-3 rounded-2xl border transition-all cursor-pointer relative hover:border-rose-200"
                             :class="selectedPresetId == music.id ? 'border-rose-500 bg-rose-50/50 ring-1 ring-rose-500' : 'border-stone-200 bg-white'">
                        <div class="flex items-center justify-between gap-3">
                          <div class="flex items-center gap-3">
                            <input type="radio" v-model="selectedPresetId" :value="music.id" class="text-rose-600 focus:ring-rose-500 border-stone-300">
                            <div>
                              <div class="font-bold text-xs text-stone-900 flex items-center gap-1.5">
                                <span>{{ music.title }}</span>
                                <span class="text-[9px] px-1.5 py-0.5 rounded-full font-bold uppercase"
                                      :class="music.category === 'wedding' ? 'bg-rose-100 text-rose-700' :
                                              (music.category === 'khitan' ? 'bg-amber-100 text-amber-700' :
                                              (music.category === 'general' ? 'bg-indigo-100 text-indigo-700' : 'bg-stone-100 text-stone-700'))">
                                  {{ music.category }}
                                </span>
                              </div>
                              <div class="text-[11px] text-stone-400 font-medium">{{ music.artist || 'Pustaka Musik' }}</div>
                            </div>
                          </div>

                          <!-- Audio Preview Button -->
                          <button type="button" @click.stop="previewMusic(music.audio_url || music.file_path)"
                                  class="shrink-0 p-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5"
                                  :class="playingMusicUrl === (music.audio_url || music.file_path) ? 'bg-rose-600 text-white shadow-sm' : 'bg-stone-100 text-stone-700 hover:bg-stone-200'">
                            <span v-if="playingMusicUrl !== (music.audio_url || music.file_path)">▶ Putar</span>
                            <span v-else>⏸ Jeda</span>
                          </button>
                        </div>
                      </label>
                    </div>
                  </div>

                  <!-- 2. CUSTOM UPLOAD -->
                  <div v-show="musicSource === 'custom'" class="space-y-3">
                    <div class="p-4 bg-stone-50 border border-stone-200 rounded-2xl space-y-2">
                      <p class="text-xs text-stone-700 font-semibold">Unggah berkas lagu Anda sendiri:</p>
                      <input type="file" ref="customMusicFile" accept="audio/*"
                             class="block w-full text-xs text-stone-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-rose-50 file:text-rose-700 hover:file:bg-rose-100">
                      <p class="text-[11px] text-stone-400">Format yang didukung: MP3, WAV, OGG, M4A. Maksimal 15MB.</p>

                      <div v-if="safeData.music_path && safeData.music_source === 'custom'" class="mt-3 pt-3 border-t border-stone-200 flex items-center justify-between text-xs">
                        <span class="text-emerald-700 font-medium truncate max-w-[200px]">✓ Berkas: {{ safeData.music_title }}</span>
                        <button type="button" @click="previewMusic(safeData.music_url)"
                                class="px-3 py-1 rounded-lg text-xs font-bold bg-white border border-stone-200 text-stone-700 hover:bg-stone-50">
                          <span v-if="playingMusicUrl !== safeData.music_url">▶ Tes Putar</span>
                          <span v-else>⏸ Jeda</span>
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- 3. NONE (TANPA MUSIK) -->
                  <div v-show="musicSource === 'none'" class="p-4 bg-stone-50 border border-stone-200 rounded-2xl text-xs text-stone-600">
                    <p class="font-semibold text-stone-800 mb-1">🔇 Mode Hening</p>
                    <p class="text-[11px] text-stone-500">Undangan akan ditampilkan tanpa musik latar belakang.</p>
                  </div>
                </div>
              </div>

              <!-- 2. TAB PEMBUKA -->
              <div v-show="activeTab === 'pembuka'" class="space-y-5">
                <h3 class="text-base font-bold text-stone-900">Bagian Pembuka (Opening)</h3>

                <div>
                  <label class="block text-xs font-bold text-stone-700 mb-1">Judul Undangan / Acara *</label>
                  <input type="text" v-model="formData.opening.title" required
                         class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200 focus:ring-1 focus:ring-rose-500">
                </div>

                <div>
                  <label class="block text-xs font-bold text-stone-700 mb-1">Salam Pembuka</label>
                  <input type="text" v-model="formData.opening.greeting"
                         class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200 focus:ring-1 focus:ring-rose-500">
                </div>

                <div>
                  <label class="block text-xs font-bold text-stone-700 mb-1">
                    {{ isKhitan ? 'Kutipan / Doa Khitanan' : (isGeneral ? 'Kutipan / Kata Mutiara' : 'Kutipan Ayat / Doa Pernikahan') }}
                  </label>
                  <textarea v-model="formData.opening.quote" rows="3"
                            class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200 focus:ring-1 focus:ring-rose-500"></textarea>
                </div>

                <div>
                  <label class="block text-xs font-bold text-stone-700 mb-1">Sumber Kutipan / Dalil (Opsional)</label>
                  <input type="text" v-model="formData.opening.quote_source"
                         class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200 focus:ring-1 focus:ring-rose-500"
                         placeholder="Contoh: QS. Ar-Rum: 21">
                </div>
              </div>

              <!-- 3. TAB TOKOH UTAMA / MEMPELAI -->
              <div v-show="activeTab === 'mempelai'" class="space-y-6">
                <h3 class="text-base font-bold text-stone-900">
                  {{ isKhitan ? 'Informasi Anak yang Dikhitan & Orang Tua' : (isGeneral ? 'Informasi Penyelenggara' : 'Informasi Kedua Mempelai') }}
                </h3>

                <!-- Pihak 1 -->
                <div class="p-5 bg-stone-50 rounded-2xl border border-stone-200 space-y-4">
                  <h4 class="text-xs font-extrabold uppercase tracking-wider text-rose-600">
                    {{ isKhitan ? 'Profil Anak' : (isGeneral ? 'Profil Penyelenggara' : 'Mempelai Wanita') }}
                  </h4>

                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                      <label class="block text-xs font-bold text-stone-700 mb-1">Nama Panggilan *</label>
                      <input type="text" v-model="formData.party1.name" required
                             class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200">
                    </div>
                    <div>
                      <label class="block text-xs font-bold text-stone-700 mb-1">Nama Lengkap & Gelar *</label>
                      <input type="text" v-model="formData.party1.full_name" required
                             class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200">
                    </div>
                  </div>

                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                      <label class="block text-xs font-bold text-stone-700 mb-1">Urutan Kelahiran</label>
                      <input type="text" v-model="formData.party1.child_order"
                             class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200"
                             placeholder="Contoh: Putri pertama dari">
                    </div>
                    <div>
                      <label class="block text-xs font-bold text-stone-700 mb-1">Nama Orang Tua</label>
                      <input type="text" v-model="formData.party1.parents"
                             class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200"
                             placeholder="Contoh: Bpk. Ahmad & Ibu Siti">
                    </div>
                  </div>

                  <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">Foto Profil</label>
                    <input type="file" ref="party1PhotoFile" accept="image/*" class="text-xs text-stone-500">
                    <div v-if="safeData.party1?.photo_path" class="mt-2 flex items-center gap-2">
                      <img :src="safeData.party1.photo_path" class="w-10 h-10 object-cover rounded-full border">
                      <span class="text-[11px] text-stone-500">Foto terpasang</span>
                    </div>
                  </div>
                </div>

                <!-- Pihak 2 (Hanya jika bukan general) -->
                <div v-if="!isGeneral" class="p-5 bg-stone-50 rounded-2xl border border-stone-200 space-y-4">
                  <h4 class="text-xs font-extrabold uppercase tracking-wider text-rose-600">
                    {{ isKhitan ? 'Keluarga / Orang Tua' : 'Mempelai Pria' }}
                  </h4>

                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                      <label class="block text-xs font-bold text-stone-700 mb-1">Nama Panggilan</label>
                      <input type="text" v-model="formData.party2.name"
                             class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200">
                    </div>
                    <div>
                      <label class="block text-xs font-bold text-stone-700 mb-1">Nama Lengkap & Gelar</label>
                      <input type="text" v-model="formData.party2.full_name"
                             class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200">
                    </div>
                  </div>

                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                      <label class="block text-xs font-bold text-stone-700 mb-1">Urutan Kelahiran</label>
                      <input type="text" v-model="formData.party2.child_order"
                             class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200">
                    </div>
                    <div>
                      <label class="block text-xs font-bold text-stone-700 mb-1">Nama Orang Tua</label>
                      <input type="text" v-model="formData.party2.parents"
                             class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200">
                    </div>
                  </div>

                  <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">Foto Profil</label>
                    <input type="file" ref="party2PhotoFile" accept="image/*" class="text-xs text-stone-500">
                    <div v-if="safeData.party2?.photo_path" class="mt-2 flex items-center gap-2">
                      <img :src="safeData.party2.photo_path" class="w-10 h-10 object-cover rounded-full border">
                      <span class="text-[11px] text-stone-500">Foto terpasang</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- 4. TAB ACARA (MULTI ACARA) -->
              <div v-show="activeTab === 'acara'" class="space-y-6">
                <div class="flex items-center justify-between">
                  <div>
                    <h3 class="text-base font-bold text-stone-900">Rangkaian Acara</h3>
                    <p class="text-xs text-stone-500">Maksimal {{ maxEvents }} acara sesuai paket Anda.</p>
                  </div>
                  <button type="button" @click="addEvent" :disabled="events.length >= maxEvents"
                          class="text-xs bg-stone-900 hover:bg-stone-800 disabled:opacity-40 text-white font-bold px-3 py-1.5 rounded-xl">
                    + Tambah Acara
                  </button>
                </div>

                <div v-for="(event, index) in events" :key="index" class="p-5 bg-stone-50 rounded-2xl border border-stone-200 space-y-3 relative">
                  <div class="flex justify-between items-center">
                    <span class="font-bold text-xs uppercase tracking-wider text-rose-600">Acara #{{ index + 1 }}</span>
                    <button type="button" @click="removeEvent(index)" v-show="events.length > 1"
                            class="text-rose-600 hover:text-rose-800 text-xs font-bold">Hapus</button>
                  </div>

                  <div>
                    <label class="block text-xs font-semibold text-stone-700 mb-1">Nama Acara *</label>
                    <input type="text" v-model="event.title" required
                           class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200"
                           placeholder="Contoh: Akad Nikah / Resepsi">
                  </div>

                  <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <div class="sm:col-span-2">
                      <label class="block text-xs font-semibold text-stone-700 mb-1">Waktu Mulai *</label>
                      <input type="datetime-local" v-model="event.start_at" required
                             class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200">
                    </div>
                    <div>
                      <label class="block text-xs font-semibold text-stone-700 mb-1">Zona Waktu</label>
                      <select v-model="event.timezone" class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200">
                        <option value="WIB">WIB</option>
                        <option value="WITA">WITA</option>
                        <option value="WIT">WIT</option>
                      </select>
                    </div>
                  </div>

                  <div>
                    <label class="block text-xs font-semibold text-stone-700 mb-1">Nama Gedung / Tempat *</label>
                    <input type="text" v-model="event.venue_name" required
                           class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200"
                           placeholder="Contoh: Masjid Agung / Hotel Grand Santika">
                  </div>

                  <div>
                    <label class="block text-xs font-semibold text-stone-700 mb-1">Alamat Lengkap</label>
                    <input type="text" v-model="event.address"
                           class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200"
                           placeholder="Jl. Merdeka No. 45, Jakarta Selatan">
                  </div>

                  <div>
                    <label class="block text-xs font-semibold text-stone-700 mb-1">Link Google Maps (URL)</label>
                    <input type="url" v-model="event.maps_url"
                           class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200"
                           placeholder="https://maps.app.goo.gl/...">
                  </div>
                </div>
              </div>

              <!-- 5. TAB KISAH / TIMELINE -->
              <div v-show="activeTab === 'kisah'" class="space-y-6">
                <div class="flex items-center justify-between">
                  <div>
                    <h3 class="text-base font-bold text-stone-900">Perjalanan & Kisah Cinta / Timeline</h3>
                    <p class="text-xs text-stone-500">Momen berharga yang ingin dibagikan.</p>
                  </div>
                  <button type="button" @click="addStory"
                          class="text-xs bg-stone-900 hover:bg-stone-800 text-white font-bold px-3 py-1.5 rounded-xl">
                    + Tambah Momen
                  </button>
                </div>

                <div v-for="(story, index) in stories" :key="index" class="p-4 bg-stone-50 rounded-2xl border border-stone-200 space-y-2">
                  <div class="flex justify-between items-center">
                    <span class="text-xs font-bold text-rose-600">Momen #{{ index + 1 }}</span>
                    <button type="button" @click="removeStory(index)" class="text-rose-600 text-xs font-bold">Hapus</button>
                  </div>
                  <div class="grid grid-cols-3 gap-2">
                    <input type="text" v-model="story.year" placeholder="Tahun / Momen" class="text-xs px-3 py-2 rounded-xl border border-stone-200">
                    <input type="text" v-model="story.title" placeholder="Judul Momen" class="col-span-2 text-xs px-3 py-2 rounded-xl border border-stone-200">
                  </div>
                  <textarea v-model="story.description" rows="2" placeholder="Ceritakan kisah singkatnya..." class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200"></textarea>
                </div>
              </div>

              <!-- 6. TAB GALERI FOTO -->
              <div v-show="activeTab === 'galeri'" class="space-y-6">
                <div class="flex items-center justify-between">
                  <div>
                    <h3 class="text-base font-bold text-stone-900">Galeri Foto</h3>
                    <p class="text-xs text-stone-500">Batas paket: {{ maxPhotos }} foto.</p>
                  </div>
                </div>

                <div>
                  <label class="block text-xs font-bold text-stone-700 mb-1">Unggah Foto Baru (Bisa Multi-Pilih)</label>
                  <input type="file" ref="galleryPhotosFiles" multiple accept="image/*" class="text-xs text-stone-500">
                  <p class="text-[11px] text-stone-400 mt-1">Maksimal 3MB per foto. Format: JPG, PNG, WEBP.</p>
                </div>

                <div class="grid grid-cols-3 sm:grid-cols-4 gap-3 pt-3">
                  <div v-for="(gItem, gIdx) in safeData.gallery || []" :key="gIdx"
                       class="aspect-square rounded-2xl overflow-hidden border border-stone-200 relative group">
                    <img :src="gItem.url || gItem" class="w-full h-full object-cover">
                    <label class="absolute inset-0 bg-stone-900/60 opacity-0 group-hover:opacity-100 flex items-center justify-center cursor-pointer transition-opacity text-white text-xs font-bold">
                      <input type="checkbox" :value="gIdx" v-model="removeGalleryIndexes" class="mr-1">
                      Hapus
                    </label>
                  </div>
                </div>
              </div>

              <!-- 7. TAB AMPLOP DIGITAL -->
              <div v-show="activeTab === 'amplop'" class="space-y-6">
                <div class="flex items-center justify-between">
                  <div>
                    <h3 class="text-base font-bold text-stone-900">Amplop Digital & Rekening</h3>
                    <p class="text-xs text-stone-500">Tamu dapat menyalin nomor rekening untuk mengirim kado.</p>
                  </div>
                  <button type="button" @click="addGift"
                          class="text-xs bg-stone-900 hover:bg-stone-800 text-white font-bold px-3 py-1.5 rounded-xl">
                    + Tambah Rekening
                  </button>
                </div>

                <div v-for="(gift, index) in gifts" :key="index" class="p-4 bg-stone-50 rounded-2xl border border-stone-200 space-y-2">
                  <div class="flex justify-between items-center">
                    <span class="text-xs font-bold text-rose-600">Rekening #{{ index + 1 }}</span>
                    <button type="button" @click="removeGift(index)" class="text-rose-600 text-xs font-bold">Hapus</button>
                  </div>
                  <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <input type="text" v-model="gift.bank_name" placeholder="Bank (BCA / Mandiri / GoPay)" class="text-xs px-3 py-2 rounded-xl border border-stone-200">
                    <input type="text" v-model="gift.account_number" placeholder="Nomor Rekening / No HP" class="text-xs px-3 py-2 rounded-xl border border-stone-200 font-mono">
                    <input type="text" v-model="gift.account_holder" placeholder="Atas Nama (Pemilik)" class="text-xs px-3 py-2 rounded-xl border border-stone-200">
                  </div>
                </div>
              </div>

              <!-- 8. TAB RSVP & PENUTUP -->
              <div v-show="activeTab === 'penutup'" class="space-y-6">
                <h3 class="text-base font-bold text-stone-900">Fitur RSVP & Teks Penutup</h3>

                <div class="space-y-3">
                  <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" v-model="formData.rsvp_enabled" class="rounded border-stone-300 text-rose-600 focus:ring-rose-500">
                    <span class="text-xs font-semibold text-stone-700">Aktifkan Formulir Konfirmasi Kehadiran (RSVP)</span>
                  </label>

                  <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" v-model="formData.wishes_enabled" class="rounded border-stone-300 text-rose-600 focus:ring-rose-500">
                    <span class="text-xs font-semibold text-stone-700">Aktifkan Buku Tamu & Doa Restu Publik</span>
                  </label>
                </div>

                <div class="pt-4 border-t border-stone-100">
                  <label class="block text-xs font-bold text-stone-700 mb-1">Teks Penutup</label>
                  <textarea v-model="formData.closing.text" rows="3"
                            class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200"></textarea>
                </div>

                <div>
                  <label class="block text-xs font-bold text-stone-700 mb-1">Nama Penutup / Tanda Tangan</label>
                  <input type="text" v-model="formData.closing.signature"
                         class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200"
                         placeholder="Contoh: Keluarga Besar Kami">
                </div>
              </div>

              <div class="mt-8 pt-6 border-t border-stone-200 flex justify-end">
                <button type="submit" :disabled="saving"
                        class="bg-rose-600 hover:bg-rose-700 text-white text-xs sm:text-sm font-bold px-8 py-3 rounded-2xl shadow-lg shadow-rose-600/30 transition-all disabled:opacity-50">
                  <span v-if="saving">Menyimpan Semua Data...</span>
                  <span v-else>Simpan Semua Perubahan Undangan</span>
                </button>
              </div>
            </form>
          </div>

          <!-- RIGHT COLUMN: MOBILE FRAME LIVE PREVIEW -->
          <div class="lg:col-span-5 sticky top-28">
            <div class="text-center mb-3 flex items-center justify-between">
              <span class="text-xs font-extrabold uppercase text-stone-500 tracking-wider">Live Mobile Preview</span>
              <button type="button" @click="reloadIframe"
                      class="text-xs text-rose-600 hover:text-rose-800 font-bold flex items-center gap-1">
                <span>🔄 Muat Ulang Pratinjau</span>
              </button>
            </div>

            <!-- Smartphone Bezel Mockup -->
            <div class="mx-auto w-[320px] sm:w-[360px] h-[640px] bg-stone-900 rounded-[45px] p-3 shadow-2xl border-4 border-stone-800 relative flex flex-col overflow-hidden">
              <!-- Speaker & Camera notch -->
              <div class="absolute top-5 left-1/2 -translate-x-1/2 w-28 h-4 bg-stone-950 rounded-full z-20 flex items-center justify-center">
                <div class="w-2.5 h-2.5 rounded-full bg-stone-800 mr-2"></div>
                <div class="w-8 h-1 rounded-full bg-stone-800"></div>
              </div>

              <!-- Iframe Screen -->
              <div class="w-full h-full rounded-[35px] overflow-hidden bg-white relative pt-5">
                <iframe ref="previewIframe"
                        :src="previewUrl"
                        class="w-full h-full border-0"></iframe>
              </div>

              <!-- Home bar indicator -->
              <div class="absolute bottom-2 left-1/2 -translate-x-1/2 w-28 h-1 bg-stone-600 rounded-full z-20"></div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import api from '../../api/client';

const route = useRoute();
const invitationId = route.params.id;

const loading = ref(true);
const saving = ref(false);
const statusMessage = ref(null);
const errorMessage = ref(null);

const invitation = ref(null);
const safeData = ref({});
const templates = ref([]);
const presetMusics = ref([]);

const activeTab = ref('dasar');
const musicSource = ref('preset');
const selectedPresetId = ref(null);
const playingMusicUrl = ref(null);
let audioPlayer = null;

const events = ref([]);
const stories = ref([]);
const gifts = ref([]);
const removeGalleryIndexes = ref([]);

const customMusicFile = ref(null);
const party1PhotoFile = ref(null);
const party2PhotoFile = ref(null);
const galleryPhotosFiles = ref(null);
const previewIframe = ref(null);

const formData = ref({
  slug: '',
  template_id: null,
  theme_color: '#e11d48',
  rsvp_enabled: true,
  wishes_enabled: true,
  opening: { title: '', greeting: '', quote: '', quote_source: '' },
  party1: { name: '', full_name: '', child_order: '', parents: '', instagram: '' },
  party2: { name: '', full_name: '', child_order: '', parents: '', instagram: '' },
  closing: { text: '', signature: '' }
});

const isKhitan = computed(() => invitation.value?.category === 'khitan');
const isGeneral = computed(() => invitation.value?.category === 'general');
const maxPhotos = computed(() => invitation.value?.plan?.max_photos || 15);
const maxEvents = computed(() => invitation.value?.plan?.max_events || 3);
const previewUrl = computed(() => invitation.value?.slug ? `/u/${invitation.value.slug}?preview=1` : '');

const fetchEditorData = async () => {
  try {
    loading.value = true;
    const res = await api.get(`/editor/${invitationId}`);
    invitation.value = res.data.invitation;
    safeData.value = res.data.safeData || {};
    templates.value = res.data.templates || [];
    presetMusics.value = res.data.presetMusics || [];

    // Sync formData
    formData.value.slug = invitation.value.slug || '';
    formData.value.template_id = invitation.value.template_id;
    formData.value.theme_color = safeData.value.theme_color || '#e11d48';
    formData.value.rsvp_enabled = safeData.value.rsvp_enabled !== false;
    formData.value.wishes_enabled = safeData.value.wishes_enabled !== false;

    formData.value.opening = { ...safeData.value.opening };
    formData.value.party1 = { ...safeData.value.party1 };
    formData.value.party2 = { ...safeData.value.party2 };
    formData.value.closing = { ...safeData.value.closing };

    events.value = (res.data.eventsData || []).map(ev => ({
      title: ev.title || '',
      start_at: ev.start_at || '',
      end_at: ev.end_at || '',
      timezone: ev.timezone || 'WIB',
      venue_name: ev.venue_name || '',
      address: ev.address || '',
      maps_url: ev.maps_url || ''
    }));

    stories.value = [...(safeData.value.story || [])];
    gifts.value = [...(safeData.value.gifts || [])];

    musicSource.value = safeData.value.music_source || (safeData.value.music_path ? 'preset' : 'preset');
    
    // Find preset music ID if match
    if (safeData.value.music_path) {
      const match = presetMusics.value.find(m => m.file_path === safeData.value.music_path);
      if (match) selectedPresetId.value = match.id;
    }
  } catch (err) {
    errorMessage.value = err.response?.data?.message || 'Gagal memuat data editor';
  } finally {
    loading.value = false;
  }
};

const addEvent = () => {
  if (events.value.length < maxEvents.value) {
    const defaultDate = new Date();
    defaultDate.setDate(defaultDate.getDate() + 14);
    events.value.push({
      title: 'Resepsi Acara',
      start_at: defaultDate.toISOString().slice(0, 16),
      end_at: '',
      timezone: 'WIB',
      venue_name: '',
      address: '',
      maps_url: ''
    });
  }
};

const removeEvent = (index) => {
  if (events.value.length > 1) {
    events.value.splice(index, 1);
  }
};

const addStory = () => {
  stories.value.push({ year: '', title: '', description: '' });
};

const removeStory = (index) => {
  stories.value.splice(index, 1);
};

const addGift = () => {
  gifts.value.push({ bank_name: 'BCA', account_number: '', account_holder: '' });
};

const removeGift = (index) => {
  gifts.value.splice(index, 1);
};

const previewMusic = (url) => {
  if (!url) return;
  if (playingMusicUrl.value === url) {
    if (audioPlayer) audioPlayer.pause();
    playingMusicUrl.value = null;
    return;
  }

  if (audioPlayer) audioPlayer.pause();
  audioPlayer = new Audio(url);
  playingMusicUrl.value = url;
  audioPlayer.play().catch(e => {
    console.log('Audio playback error', e);
    playingMusicUrl.value = null;
  });
  audioPlayer.onended = () => {
    playingMusicUrl.value = null;
  };
};

const reloadIframe = () => {
  if (previewIframe.value) {
    previewIframe.value.src = previewIframe.value.src;
  }
};

const saveChanges = async () => {
  try {
    saving.value = true;
    errorMessage.value = null;
    statusMessage.value = null;

    const body = new FormData();
    body.append('slug', formData.value.slug);
    body.append('template_id', formData.value.template_id);
    body.append('theme_color', formData.value.theme_color);
    body.append('music_source', musicSource.value);
    if (selectedPresetId.value) {
      body.append('preset_music_id', selectedPresetId.value);
    }
    body.append('rsvp_enabled', formData.value.rsvp_enabled ? '1' : '0');
    body.append('wishes_enabled', formData.value.wishes_enabled ? '1' : '0');

    // JSON objects / arrays
    body.append('opening', JSON.stringify(formData.value.opening));
    body.append('party1', JSON.stringify(formData.value.party1));
    body.append('party2', JSON.stringify(formData.value.party2));
    body.append('closing', JSON.stringify(formData.value.closing));
    body.append('events', JSON.stringify(events.value));
    body.append('story', JSON.stringify(stories.value));
    body.append('gifts', JSON.stringify(gifts.value));

    // Removed gallery items
    removeGalleryIndexes.value.forEach(idx => {
      body.append('remove_gallery_index[]', idx);
    });

    // File uploads
    if (party1PhotoFile.value?.files?.[0]) {
      body.append('party1_photo', party1PhotoFile.value.files[0]);
    }
    if (party2PhotoFile.value?.files?.[0]) {
      body.append('party2_photo', party2PhotoFile.value.files[0]);
    }
    if (customMusicFile.value?.files?.[0]) {
      body.append('custom_music', customMusicFile.value.files[0]);
    }
    if (galleryPhotosFiles.value?.files) {
      Array.from(galleryPhotosFiles.value.files).forEach(file => {
        body.append('gallery_photos', file);
      });
    }

    const res = await api.put(`/editor/${invitationId}`, body, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });

    statusMessage.value = res.data.message || 'Perubahan undangan berhasil disimpan!';
    removeGalleryIndexes.value = [];
    await fetchEditorData();
    reloadIframe();
  } catch (err) {
    errorMessage.value = err.response?.data?.message || 'Gagal menyimpan perubahan undangan.';
  } finally {
    saving.value = false;
  }
};

onMounted(() => {
  fetchEditorData();
});
</script>
