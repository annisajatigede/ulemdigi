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
              Editor Undangan: <span class="text-rose-600">{{ formData.party1?.name || safeData?.party1?.name || 'Momen' }}</span>
            </h2>
          </div>

          <div class="flex items-center gap-3">
            <a :href="previewUrl" target="_blank"
               class="bg-white hover:bg-stone-50 text-stone-700 border border-stone-200 text-xs font-bold px-4 py-2.5 rounded-xl shadow-sm transition-all flex items-center gap-1.5">
              <span>Buka Tab Baru ↗</span>
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
                6. Galeri ({{ galleryList.length }}/{{ maxPhotos }})
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

                <!-- Musik Latar -->
                <div>
                  <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-bold text-stone-700">Musik Latar Belakang</label>
                    <span v-if="musicSource !== 'none'" class="text-[11px] font-semibold text-emerald-600 flex items-center gap-1">
                      <span>✓ Musik Aktif</span>
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
                      <span>🔗</span>
                      <span>Link / Kustom</span>
                    </button>
                    <button type="button" @click="musicSource = 'none'"
                            :class="musicSource === 'none' ? 'bg-white shadow text-stone-900 font-bold' : 'text-stone-600 hover:text-stone-900 font-medium'"
                            class="flex-1 py-2 rounded-xl text-center transition-all flex items-center justify-center gap-1.5">
                      <span>🔇</span>
                      <span>Hening</span>
                    </button>
                  </div>

                  <!-- 1. PRESET MUSICS (Disediakan Admin) -->
                  <div v-show="musicSource === 'preset'" class="space-y-3">
                    <p class="text-[11px] text-stone-500">Pilih musik pengiring yang telah disiapkan admin untuk tema acara Anda:</p>

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

                  <!-- 2. CUSTOM UPLOAD / URL -->
                  <div v-show="musicSource === 'custom'" class="space-y-3">
                    <div class="p-4 bg-stone-50 border border-stone-200 rounded-2xl space-y-3">
                      <div>
                        <label class="block text-xs font-bold text-stone-700 mb-1">Tautan / URL Berkas MP3</label>
                        <input type="url" v-model="customMusicUrl" placeholder="https://example.com/audio/lagu.mp3"
                               class="w-full text-xs font-mono px-3.5 py-2.5 rounded-xl border border-stone-200">
                        <p class="text-[11px] text-stone-400 mt-1">Masukkan URL langsung ke file MP3 Anda (Google Drive, Dropbox, Cloud Storage, dll).</p>
                      </div>

                      <div v-if="customMusicUrl" class="pt-2 flex items-center justify-between text-xs">
                        <span class="text-emerald-700 font-medium truncate max-w-[200px]">✓ Link terisi</span>
                        <button type="button" @click="previewMusic(customMusicUrl)"
                                class="px-3 py-1 rounded-lg text-xs font-bold bg-white border border-stone-200 text-stone-700 hover:bg-stone-50">
                          <span v-if="playingMusicUrl !== customMusicUrl">▶ Tes Putar</span>
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

                <!-- Pihak 1 (Mempelai Pria/Wanita) -->
                <div class="p-5 bg-stone-50 rounded-2xl border border-stone-200 space-y-4">
                  <h4 class="text-xs font-extrabold uppercase tracking-wider text-rose-600">
                    {{ isKhitan ? 'Profil Anak' : (isGeneral ? 'Profil Penyelenggara' : 'Mempelai Pria') }}
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
                             placeholder="Contoh: Putra pertama dari">
                    </div>
                    <div>
                      <label class="block text-xs font-bold text-stone-700 mb-1">Nama Orang Tua</label>
                      <input type="text" v-model="formData.party1.parents"
                             class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200"
                             placeholder="Contoh: Bpk. Ahmad & Ibu Siti">
                    </div>
                  </div>

                  <!-- Upload & Preview Foto Pihak 1 -->
                  <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">Foto Profil</label>
                    
                    <div v-if="party1PhotoPreview || formData.party1.photo_path || safeData.party1?.photo_path" 
                         class="mb-3 flex items-center gap-3 p-3 bg-white rounded-2xl border border-stone-200">
                      <img :src="party1PhotoPreview || formData.party1.photo_path || safeData.party1?.photo_path" 
                           class="w-14 h-14 object-cover rounded-xl border border-rose-200 shadow-sm">
                      <div>
                        <span class="text-xs font-bold text-stone-800 block">Foto Terpasang</span>
                        <button type="button" @click="removeParty1Photo" 
                                class="text-[11px] font-bold text-rose-600 hover:text-rose-800 underline mt-0.5">
                          ✕ Hapus Foto
                        </button>
                      </div>
                    </div>

                    <input type="file" ref="party1PhotoInput" accept="image/*" @change="onParty1PhotoChange"
                           class="w-full text-xs text-stone-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-rose-50 file:text-rose-700 hover:file:bg-rose-100 cursor-pointer">
                  </div>
                </div>

                <!-- Pihak 2 (Hanya jika bukan general) -->
                <div v-if="!isGeneral" class="p-5 bg-stone-50 rounded-2xl border border-stone-200 space-y-4">
                  <h4 class="text-xs font-extrabold uppercase tracking-wider text-rose-600">
                    {{ isKhitan ? 'Keluarga / Orang Tua' : 'Mempelai Wanita' }}
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

                  <!-- Upload & Preview Foto Pihak 2 -->
                  <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">Foto Profil</label>
                    
                    <div v-if="party2PhotoPreview || formData.party2.photo_path || safeData.party2?.photo_path" 
                         class="mb-3 flex items-center gap-3 p-3 bg-white rounded-2xl border border-stone-200">
                      <img :src="party2PhotoPreview || formData.party2.photo_path || safeData.party2?.photo_path" 
                           class="w-14 h-14 object-cover rounded-xl border border-rose-200 shadow-sm">
                      <div>
                        <span class="text-xs font-bold text-stone-800 block">Foto Terpasang</span>
                        <button type="button" @click="removeParty2Photo" 
                                class="text-[11px] font-bold text-rose-600 hover:text-rose-800 underline mt-0.5">
                          ✕ Hapus Foto
                        </button>
                      </div>
                    </div>

                    <input type="file" ref="party2PhotoInput" accept="image/*" @change="onParty2PhotoChange"
                           class="w-full text-xs text-stone-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-rose-50 file:text-rose-700 hover:file:bg-rose-100 cursor-pointer">
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
                    <label class="block text-xs font-semibold text-stone-700 mb-1">Nama Tempat / Gedung *</label>
                    <input type="text" v-model="event.venue_name" required
                           class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200"
                           placeholder="Contoh: Gedung Graha Asri">
                  </div>

                  <div>
                    <label class="block text-xs font-semibold text-stone-700 mb-1">Alamat Lengkap *</label>
                    <textarea v-model="event.address" rows="2" required
                              class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200"
                              placeholder="Jl. Mawar No. 123..."></textarea>
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

              <!-- 6. TAB GALERI FOTO (MULTI UPLOAD + HAPUS) -->
              <div v-show="activeTab === 'galeri'" class="space-y-6">
                <div class="flex items-center justify-between">
                  <div>
                    <h3 class="text-base font-bold text-stone-900">Galeri Foto Kenangan</h3>
                    <p class="text-xs text-stone-500">Kapasitas paket: {{ galleryList.length }} / {{ maxPhotos }} foto.</p>
                  </div>
                </div>

                <div class="p-4 bg-stone-50 border border-stone-200 rounded-2xl space-y-2">
                  <label class="block text-xs font-bold text-stone-700">Unggah Foto (Bisa Pilih Banyak Sekaligus)</label>
                  <input type="file" ref="galleryFilesInput" multiple accept="image/*" @change="onGalleryFilesChange"
                         class="w-full text-xs text-stone-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-stone-900 file:text-white hover:file:bg-stone-800 cursor-pointer">
                  <p class="text-[11px] text-stone-400">Pilih satu atau beberapa foto sekaligus. Gambar akan dioptimasi otomatis agar memuat super cepat.</p>
                </div>

                <!-- Grid Preview Foto Galeri -->
                <div v-if="galleryList.length > 0" class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2">
                  <div v-for="(item, idx) in galleryList" :key="idx" 
                       class="relative group rounded-2xl overflow-hidden border border-stone-200 aspect-square bg-stone-100 shadow-sm">
                    <img :src="item.url || item" class="w-full h-full object-cover">
                    <!-- Tombol Hapus Selalu Terlihat -->
                    <button type="button" @click.stop="removeGalleryItem(idx)"
                            class="absolute top-2 right-2 bg-rose-600/95 hover:bg-rose-700 text-white text-xs font-bold w-6 h-6 rounded-full shadow-md flex items-center justify-center z-10 transition-transform active:scale-95 cursor-pointer"
                            title="Hapus foto ini">
                      ✕
                    </button>
                    <!-- Overlay Tombol Hapus Hover -->
                    <div class="absolute inset-0 bg-stone-900/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center p-2 pointer-events-none">
                      <span class="bg-rose-600 text-white text-[11px] font-bold px-3 py-1 rounded-xl shadow-lg flex items-center gap-1">
                        <span>🗑</span> Hapus Foto
                      </span>
                    </div>
                  </div>
                </div>
                <div v-else class="text-center py-8 text-stone-400 text-xs italic bg-stone-50 rounded-2xl border border-dashed border-stone-200">
                  Belum ada foto di galeri. Silakan pilih foto di atas.
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
                         placeholder="Contoh: Alex & Olivia Beserta Keluarga Besar">
                </div>
              </div>

              <div class="pt-6 border-t border-stone-200 flex items-center justify-between">
                <button type="button" @click="reloadIframe" class="text-xs font-semibold text-stone-500 hover:text-stone-800 flex items-center gap-1">
                  <span>🔄</span> Refresh Preview
                </button>
                <button type="submit" :disabled="saving"
                        class="bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs sm:text-sm px-8 py-3 rounded-2xl shadow-lg shadow-rose-600/25 transition-all disabled:opacity-50">
                  <span v-if="saving">Menyimpan...</span>
                  <span v-else>Simpan Perubahan Undangan</span>
                </button>
              </div>

            </form>
          </div>

          <!-- RIGHT COLUMN: LIVE MOBILE PREVIEW -->
          <div class="lg:col-span-5 sticky top-24">
            <div class="bg-white rounded-3xl border border-stone-200 shadow-xl p-4 flex flex-col items-center">
              <div class="w-full flex items-center justify-between pb-3 px-2 border-b border-stone-100 text-xs">
                <div class="flex items-center gap-2 font-bold text-stone-800">
                  <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                  <span>Live Mobile Preview</span>
                </div>
                <div class="flex items-center gap-2">
                  <button type="button" @click="reloadIframe" class="p-1 rounded hover:bg-stone-100 text-stone-500 text-xs">
                    🔄 Refresh
                  </button>
                </div>
              </div>

              <!-- Device Mockup Frame -->
              <div class="w-full max-w-[340px] aspect-[9/19] bg-stone-900 rounded-[44px] p-3 shadow-2xl mt-4 border-4 border-stone-800 relative">
                <div class="w-24 h-4 bg-stone-800 rounded-full mx-auto mb-2"></div>
                <div class="w-full h-[calc(100%-24px)] rounded-[32px] overflow-hidden bg-white">
                  <iframe
                    ref="previewIframe"
                    :src="previewUrl"
                    @load="onIframeLoaded"
                    class="w-full h-full border-0"
                    title="Live Preview Undangan"
                  ></iframe>
                </div>
              </div>
            </div>
          </div>

        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { useRoute } from 'vue-router';
import api from '../../api/client';
import { notifySuccess, alertError } from '../../utils/alert';
import { processImageToBase64 } from '../../utils/image';

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
const customMusicUrl = ref('');
const playingMusicUrl = ref(null);
let audioPlayer = null;

const events = ref([]);
const stories = ref([]);
const gifts = ref([]);

// Photo Previews & Management
const party1PhotoInput = ref(null);
const party1PhotoPreview = ref(null);
const party1PhotoRemoved = ref(false);

const party2PhotoInput = ref(null);
const party2PhotoPreview = ref(null);
const party2PhotoRemoved = ref(false);

// Gallery List
const galleryFilesInput = ref(null);
const galleryList = ref([]);

const previewIframe = ref(null);

const formData = ref({
  slug: '',
  template_id: null,
  theme_color: '#e11d48',
  rsvp_enabled: true,
  wishes_enabled: true,
  opening: { title: '', greeting: '', quote: '', quote_source: '' },
  party1: { name: '', full_name: '', child_order: '', parents: '', photo_path: '' },
  party2: { name: '', full_name: '', child_order: '', parents: '', photo_path: '' },
  closing: { text: '', signature: '' }
});

const isKhitan = computed(() => invitation.value?.category === 'khitan');
const isGeneral = computed(() => invitation.value?.category === 'general');
const maxPhotos = computed(() => invitation.value?.plan?.max_photos || 15);
const maxEvents = computed(() => invitation.value?.plan?.max_events || 3);
const previewUrl = computed(() => {
  const currentSlug = formData.value.slug || invitation.value?.slug || invitationId;
  return `/u/${currentSlug}?preview=1`;
});

// Photo Handlers
const onParty1PhotoChange = async (e) => {
  const file = e.target.files?.[0];
  if (file) {
    try {
      const base64 = await processImageToBase64(file, 400, 400, 32000);
      if (base64) {
        party1PhotoPreview.value = base64;
        party1PhotoRemoved.value = false;
        formData.value.party1.photo_path = base64;
        sendLiveSync();
      }
    } catch (err) {
      console.warn('Failed to process photo 1:', err);
    }
  }
};

const removeParty1Photo = () => {
  party1PhotoPreview.value = '';
  party1PhotoRemoved.value = true;
  formData.value.party1.photo_path = '';
  if (safeData.value.party1) safeData.value.party1.photo_path = '';
  if (party1PhotoInput.value) party1PhotoInput.value.value = '';
  sendLiveSync();
};

const onParty2PhotoChange = async (e) => {
  const file = e.target.files?.[0];
  if (file) {
    try {
      const base64 = await processImageToBase64(file, 400, 400, 32000);
      if (base64) {
        party2PhotoPreview.value = base64;
        party2PhotoRemoved.value = false;
        formData.value.party2.photo_path = base64;
        sendLiveSync();
      }
    } catch (err) {
      console.warn('Failed to process photo 2:', err);
    }
  }
};

const removeParty2Photo = () => {
  party2PhotoPreview.value = '';
  party2PhotoRemoved.value = true;
  formData.value.party2.photo_path = '';
  if (safeData.value.party2) safeData.value.party2.photo_path = '';
  if (party2PhotoInput.value) party2PhotoInput.value.value = '';
  sendLiveSync();
};

// Gallery Handlers
const onGalleryFilesChange = async (e) => {
  const files = Array.from(e.target.files || []);
  for (const file of files) {
    if (galleryList.value.length >= maxPhotos.value) break;
    try {
      const base64 = await processImageToBase64(file, 600, 600, 32000);
      if (base64) {
        galleryList.value.push({ url: base64 });
      }
    } catch (err) {
      console.warn('Failed to process gallery file:', err);
    }
  }
  if (galleryFilesInput.value) galleryFilesInput.value.value = '';
  sendLiveSync();
};

const removeGalleryItem = (index) => {
  galleryList.value.splice(index, 1);
  sendLiveSync();
};

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

    // Photo preview initialization
    party1PhotoPreview.value = safeData.value.party1?.photo_path || '';
    party1PhotoRemoved.value = !safeData.value.party1?.photo_path;
    party2PhotoPreview.value = safeData.value.party2?.photo_path || '';
    party2PhotoRemoved.value = !safeData.value.party2?.photo_path;

    // Gallery List
    galleryList.value = (safeData.value.gallery || []).map(g => ({ url: g.url || g }));

    // Events
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
    customMusicUrl.value = safeData.value.music_url || safeData.value.music_path || '';
    
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
    const targetSlug = formData.value.slug || invitation.value?.slug || invitationId;
    previewIframe.value.src = `/u/${targetSlug}?preview=1&_t=${Date.now()}`;
  }
};

let syncTimer = null;
const sendLiveSync = () => {
  if (syncTimer) clearTimeout(syncTimer);
  syncTimer = setTimeout(() => {
    try {
      if (!previewIframe.value || !previewIframe.value.contentWindow) return;

      let finalMusicPath = safeData.value?.music_path || '';
      if (musicSource.value === 'preset') {
        const p = presetMusics.value.find(m => m.id == selectedPresetId.value);
        if (p) finalMusicPath = p.audio_url || p.file_path;
      } else if (musicSource.value === 'custom') {
        finalMusicPath = customMusicUrl.value;
      }

      let p1Photo = formData.value.party1?.photo_path || safeData.value?.party1?.photo_path || '';
      if (party1PhotoPreview.value) p1Photo = party1PhotoPreview.value;
      if (party1PhotoRemoved.value) p1Photo = '';

      let p2Photo = formData.value.party2?.photo_path || safeData.value?.party2?.photo_path || '';
      if (party2PhotoPreview.value) p2Photo = party2PhotoPreview.value;
      if (party2PhotoRemoved.value) p2Photo = '';

      const syncPayload = {
        type: 'LIVE_PREVIEW_SYNC',
        template_id: formData.value.template_id,
        category: invitation.value?.category,
        data: {
          opening: { ...formData.value.opening },
          party1: {
            ...formData.value.party1,
            photo_path: p1Photo
          },
          party2: {
            ...formData.value.party2,
            photo_path: p2Photo
          },
          closing: { ...formData.value.closing },
          theme_color: formData.value.theme_color,
          music_path: finalMusicPath,
          music_url: finalMusicPath,
          music_source: musicSource.value,
          rsvp_enabled: formData.value.rsvp_enabled,
          wishes_enabled: formData.value.wishes_enabled,
          events: events.value,
          story: stories.value,
          gifts: gifts.value,
          gallery: galleryList.value
        },
        events: events.value
      };

      const targetSlug = formData.value.slug || invitation.value?.slug || invitationId;
      try {
        localStorage.setItem('preview_draft_' + targetSlug, JSON.stringify(syncPayload.data));
        localStorage.setItem('preview_draft_template_' + targetSlug, formData.value.template_id);
      } catch (e) {}

      previewIframe.value.contentWindow.postMessage(syncPayload, '*');
    } catch (e) {
      console.warn('Sync postMessage warning:', e);
    }
  }, 60);
};

const onIframeLoaded = () => {
  sendLiveSync();
};

watch([
  formData,
  events,
  stories,
  gifts,
  galleryList,
  party1PhotoPreview,
  party2PhotoPreview,
  selectedPresetId,
  customMusicUrl,
  musicSource
], () => {
  sendLiveSync();
}, { deep: true });

const onMessageFromIframe = (event) => {
  if (event.data?.type === 'IFRAME_READY') {
    sendLiveSync();
  }
};

const saveChanges = async () => {
  try {
    saving.value = true;
    errorMessage.value = null;
    statusMessage.value = null;

    let finalParty1Photo = formData.value.party1.photo_path || safeData.value.party1?.photo_path || '';
    if (party1PhotoPreview.value) finalParty1Photo = party1PhotoPreview.value;
    if (party1PhotoRemoved.value) finalParty1Photo = '';

    let finalParty2Photo = formData.value.party2.photo_path || safeData.value.party2?.photo_path || '';
    if (party2PhotoPreview.value) finalParty2Photo = party2PhotoPreview.value;
    if (party2PhotoRemoved.value) finalParty2Photo = '';

    let finalMusicPath = safeData.value.music_path || '';
    if (musicSource.value === 'preset') {
      const p = presetMusics.value.find(m => m.id == selectedPresetId.value);
      if (p) finalMusicPath = p.audio_url || p.file_path;
    } else if (musicSource.value === 'custom') {
      finalMusicPath = customMusicUrl.value;
    } else {
      finalMusicPath = '';
    }

    const payload = {
      slug: formData.value.slug,
      template_id: formData.value.template_id,
      theme_color: formData.value.theme_color,
      music_source: musicSource.value,
      music_path: finalMusicPath,
      rsvp_enabled: formData.value.rsvp_enabled,
      wishes_enabled: formData.value.wishes_enabled,
      data: {
        theme_color: formData.value.theme_color,
        music_source: musicSource.value,
        music_path: finalMusicPath,
        music_url: finalMusicPath,
        rsvp_enabled: formData.value.rsvp_enabled,
        wishes_enabled: formData.value.wishes_enabled,
        opening: formData.value.opening,
        party1: {
          ...formData.value.party1,
          photo_path: finalParty1Photo
        },
        party2: {
          ...formData.value.party2,
          photo_path: finalParty2Photo
        },
        closing: formData.value.closing,
        events: events.value,
        story: stories.value,
        gifts: gifts.value,
        gallery: galleryList.value
      },
      events: events.value
    };

    const res = await api.put(`/editor/${invitationId}`, payload);

    statusMessage.value = res.data.message || 'Perubahan undangan berhasil disimpan!';
    notifySuccess('Perubahan Berhasil Disimpan!');

    // Update local state
    if (res.data.invitation) {
      invitation.value = res.data.invitation;
      safeData.value = res.data.invitation.data || payload.data;
    }

    // Auto reload Live Mobile Preview
    reloadIframe();
  } catch (err) {
    const msg = err.response?.data?.message || err.message || 'Gagal menyimpan perubahan undangan.';
    errorMessage.value = msg;
    await alertError('Gagal Menyimpan', msg);
  } finally {
    saving.value = false;
  }
};

onMounted(() => {
  fetchEditorData();
  window.addEventListener('message', onMessageFromIframe);
});

onUnmounted(() => {
  window.removeEventListener('message', onMessageFromIframe);
});
</script>
