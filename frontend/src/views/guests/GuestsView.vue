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
              <span class="text-xs font-bold text-rose-600 uppercase">{{ invitation?.slug }}</span>
            </div>
            <h2 class="font-extrabold text-2xl text-stone-900 leading-tight mt-1">
              Kelola Tamu Undangan & RSVP
            </h2>
          </div>

          <div class="flex items-center gap-2">
            <button @click="exportCsv"
                    class="bg-white hover:bg-stone-50 border border-stone-200 text-stone-800 text-xs font-bold px-4 py-2.5 rounded-xl shadow-sm transition-all flex items-center gap-1.5">
              <span>📥 Ekspor Data (CSV)</span>
            </button>
          </div>
        </div>
      </div>
    </header>

    <div v-if="loading" class="py-20 text-center text-stone-400 text-sm">
      Memuat daftar tamu dan analitik...
    </div>

    <div v-else class="py-10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        <!-- Status Message -->
        <div v-if="statusMessage" class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold rounded-2xl flex items-center justify-between">
          <span>{{ statusMessage }}</span>
          <button @click="statusMessage = null" class="text-emerald-600 font-bold hover:text-emerald-800">×</button>
        </div>

        <!-- 1. ANALITIK RSVP -->
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3">
          <div class="bg-white p-4 rounded-2xl border border-stone-200 shadow-sm text-center">
            <span class="text-[10px] font-bold text-stone-400 uppercase tracking-wider block">Total Undangan</span>
            <span class="text-2xl font-extrabold text-stone-900 mt-1 block">{{ analytics.total_invitations || 0 }}</span>
          </div>
          <div class="bg-white p-4 rounded-2xl border border-stone-200 shadow-sm text-center">
            <span class="text-[10px] font-bold text-stone-400 uppercase tracking-wider block">Estimasi Orang</span>
            <span class="text-2xl font-extrabold text-indigo-600 mt-1 block">{{ analytics.total_guests_estimated || 0 }}</span>
          </div>
          <div class="bg-white p-4 rounded-2xl border border-stone-200 shadow-sm text-center">
            <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider block">Pasti Hadir</span>
            <span class="text-2xl font-extrabold text-emerald-600 mt-1 block">{{ analytics.attended || 0 }}</span>
          </div>
          <div class="bg-white p-4 rounded-2xl border border-stone-200 shadow-sm text-center">
            <span class="text-[10px] font-bold text-rose-600 uppercase tracking-wider block">Tidak Hadir</span>
            <span class="text-2xl font-extrabold text-rose-600 mt-1 block">{{ analytics.not_attended || 0 }}</span>
          </div>
          <div class="bg-white p-4 rounded-2xl border border-stone-200 shadow-sm text-center">
            <span class="text-[10px] font-bold text-amber-600 uppercase tracking-wider block">Masih Ragu</span>
            <span class="text-2xl font-extrabold text-amber-600 mt-1 block">{{ analytics.maybe || 0 }}</span>
          </div>
          <div class="bg-white p-4 rounded-2xl border border-stone-200 shadow-sm text-center">
            <span class="text-[10px] font-bold text-stone-400 uppercase tracking-wider block">Belum Respon</span>
            <span class="text-2xl font-extrabold text-stone-500 mt-1 block">{{ analytics.pending || 0 }}</span>
          </div>
          <div class="bg-white p-4 rounded-2xl border border-stone-200 shadow-sm text-center">
            <span class="text-[10px] font-bold text-sky-600 uppercase tracking-wider block">Check-in Hadir</span>
            <span class="text-2xl font-extrabold text-sky-600 mt-1 block">{{ analytics.checked_in || 0 }}</span>
          </div>
        </div>

        <!-- Visual Attendance Progress Bar -->
        <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-sm">
          <div class="flex items-center justify-between text-xs font-semibold text-stone-600 mb-2">
            <span>Persentase Kehadiran Tamu</span>
            <span>{{ pctAttended }}% Konfirmasi Hadir</span>
          </div>
          <div class="h-3 w-full bg-stone-100 rounded-full overflow-hidden flex">
            <div :style="{ width: pctAttended + '%' }" class="bg-emerald-500 h-full" :title="'Hadir: ' + pctAttended + '%'"></div>
            <div :style="{ width: pctMaybe + '%' }" class="bg-amber-400 h-full" :title="'Ragu: ' + pctMaybe + '%'"></div>
            <div :style="{ width: pctNot + '%' }" class="bg-rose-500 h-full" :title="'Tidak Hadir: ' + pctNot + '%'"></div>
          </div>
          <div class="flex gap-4 text-[11px] text-stone-500 mt-2.5">
            <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Hadir</span>
            <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span> Ragu-ragu</span>
            <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span> Tidak Hadir</span>
          </div>
        </div>

        <!-- 2. TAMBAH TAMU BARU & TABEL -->
        <div class="bg-white rounded-3xl border border-stone-200 shadow-sm p-6 sm:p-8">
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
              <h3 class="text-base font-extrabold text-stone-900">Daftar Tamu & Tautan Personal</h3>
              <p class="text-xs text-stone-500">Kirim link undangan spesifik yang mencantumkan nama masing-masing tamu.</p>
            </div>

            <button type="button" @click="showAddModal = true"
                    class="bg-stone-900 hover:bg-stone-800 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-sm transition-all flex items-center gap-1.5 self-start">
              <span>+ Tambah Tamu Baru</span>
            </button>
          </div>

          <!-- MODAL TAMBAH TAMU -->
          <div v-if="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-stone-900/50 p-4">
            <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl">
              <h4 class="text-base font-bold text-stone-900 mb-4">Tambah Tamu Undangan</h4>
              <form @submit.prevent="createGuest" class="space-y-4">
                <div>
                  <label class="block text-xs font-bold text-stone-700 mb-1">Nama Tamu Undangan *</label>
                  <input type="text" v-model="newGuest.name" required placeholder="Contoh: Bpk. Joko Widodo & Rekan"
                         class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-stone-200 focus:ring-1 focus:ring-rose-500 focus:outline-none">
                </div>
                <div>
                  <label class="block text-xs font-bold text-stone-700 mb-1">No. WhatsApp / HP</label>
                  <input type="text" v-model="newGuest.phone" placeholder="Contoh: 08123456789 (opsional)"
                         class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-stone-200 focus:ring-1 focus:ring-rose-500 focus:outline-none">
                </div>
                <div>
                  <label class="block text-xs font-bold text-stone-700 mb-1">Perkiraan Jumlah Orang (Pax)</label>
                  <input type="number" v-model.number="newGuest.guest_count" min="1" max="20"
                         class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-stone-200 focus:ring-1 focus:ring-rose-500 focus:outline-none">
                </div>
                <div class="flex justify-end gap-2 pt-2">
                  <button type="button" @click="showAddModal = false" class="px-4 py-2 text-xs font-bold text-stone-500 hover:text-stone-700">Batal</button>
                  <button type="submit" :disabled="submittingGuest" class="bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs px-5 py-2.5 rounded-xl shadow-md disabled:opacity-50">
                    <span v-if="submittingGuest">Menyimpan...</span>
                    <span v-else>Simpan Tamu</span>
                  </button>
                </div>
              </form>
            </div>
          </div>

          <!-- FILTER & SEARCH -->
          <div class="flex flex-wrap gap-2 mb-6">
            <input type="text" v-model="searchQuery" @keyup.enter="applyFilter" placeholder="Cari nama atau nomor HP..."
                   class="text-xs px-3.5 py-2 rounded-xl border border-stone-200 w-64 focus:ring-1 focus:ring-rose-500">

            <select v-model="statusFilter" @change="applyFilter" class="text-xs px-3 py-2 rounded-xl border border-stone-200 focus:ring-1 focus:ring-rose-500">
              <option value="">Semua Status RSVP</option>
              <option value="attended">Hadir</option>
              <option value="maybe">Ragu-ragu</option>
              <option value="not_attended">Tidak Hadir</option>
              <option value="pending">Belum Merespons</option>
            </select>

            <button type="button" @click="applyFilter" class="bg-stone-900 text-white text-xs font-bold px-4 py-2 rounded-xl">Filter</button>
            <button v-if="searchQuery || statusFilter" type="button" @click="resetFilter" class="text-xs text-rose-600 hover:underline self-center ml-2">Reset</button>
          </div>

          <!-- TABEL TAMU -->
          <div v-if="guests.length === 0" class="text-xs text-stone-400 py-8 text-center">
            Belum ada daftar tamu yang cocok dengan pencarian.
          </div>

          <div v-else class="overflow-x-auto">
            <table class="w-full text-left text-xs">
              <thead class="bg-stone-50 text-stone-500 uppercase tracking-wider border-b border-stone-200">
                <tr>
                  <th class="py-3 px-4">Nama Tamu</th>
                  <th class="py-3 px-4">Kontak / HP</th>
                  <th class="py-3 px-4">Status RSVP</th>
                  <th class="py-3 px-4">Pax</th>
                  <th class="py-3 px-4">Check-in</th>
                  <th class="py-3 px-4">Link & Bagikan</th>
                  <th class="py-3 px-4 text-right">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-stone-100">
                <tr v-for="g in guests" :key="g.id">
                  <td class="py-3 px-4 font-bold text-stone-900">{{ g.name }}</td>
                  <td class="py-3 px-4 text-stone-500 font-mono">{{ g.phone || '-' }}</td>
                  <td class="py-3 px-4">
                    <span v-if="g.rsvp_status === 'attended'" class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px]">Hadir</span>
                    <span v-else-if="g.rsvp_status === 'not_attended'" class="px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 font-bold text-[10px]">Tidak Hadir</span>
                    <span v-else-if="g.rsvp_status === 'maybe'" class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 font-bold text-[10px]">Ragu-ragu</span>
                    <span v-else class="px-2 py-0.5 rounded-full bg-stone-100 text-stone-500 font-bold text-[10px]">Belum Respon</span>
                  </td>
                  <td class="py-3 px-4 font-semibold text-stone-700">{{ g.guest_count || 1 }} orang</td>
                  <td class="py-3 px-4">
                    <button type="button" @click="checkInGuest(g.id)"
                            class="px-2.5 py-1 rounded-lg text-[10px] font-bold transition-colors"
                            :class="g.checked_in_at ? 'bg-sky-100 text-sky-800' : 'bg-stone-100 hover:bg-stone-200 text-stone-600'">
                      {{ g.checked_in_at ? '✓ Check-in' : 'Check-in' }}
                    </button>
                  </td>
                  <td class="py-3 px-4">
                    <div class="flex items-center gap-1.5">
                      <button @click="copyGuestLink(g)"
                              class="bg-stone-100 hover:bg-stone-200 text-stone-700 px-2 py-1 rounded-lg text-[10px] font-semibold transition-colors">
                        <span v-if="copiedGuestId !== g.id">Salin Link</span>
                        <span v-else class="text-emerald-600 font-bold">✓ Tersalin</span>
                      </button>

                      <a v-if="getCleanPhone(g.phone)"
                         :href="getWhatsAppLink(g)"
                         target="_blank"
                         class="bg-emerald-600 hover:bg-emerald-700 text-white px-2 py-1 rounded-lg text-[10px] font-bold transition-colors">
                        Kirim WA
                      </a>
                    </div>
                  </td>
                  <td class="py-3 px-4 text-right">
                    <button type="button" @click="deleteGuest(g.id)" class="text-rose-500 hover:text-rose-700 font-bold text-xs">
                      Hapus
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- 3. FEED DOA & BUKU TAMU TERBARU -->
        <div class="bg-white rounded-3xl border border-stone-200 shadow-sm p-6 sm:p-8">
          <h3 class="text-base font-extrabold text-stone-900 mb-4">Doa & Ucapan Masuk ({{ wishes.length }})</h3>
          <div class="space-y-3 max-h-80 overflow-y-auto">
            <div v-for="wish in wishes" :key="wish.id" class="p-3.5 bg-stone-50 rounded-2xl border border-stone-100 flex items-start justify-between">
              <div>
                <span class="font-bold text-xs text-stone-900 block">{{ wish.name }}</span>
                <p class="text-xs text-stone-600 mt-1 leading-relaxed">{{ wish.message }}</p>
              </div>
              <span class="text-[10px] text-stone-400 shrink-0 ml-4">{{ formatRelativeTime(wish.created_at) }}</span>
            </div>
            <p v-if="wishes.length === 0" class="text-xs text-stone-400">Belum ada ucapan masuk dari tamu.</p>
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
const statusMessage = ref(null);
const invitation = ref(null);
const guests = ref([]);
const wishes = ref([]);
const analytics = ref({});

const searchQuery = ref('');
const statusFilter = ref('');

const showAddModal = ref(false);
const submittingGuest = ref(false);
const newGuest = ref({ name: '', phone: '', guest_count: 1 });

const copiedGuestId = ref(null);

const pctAttended = computed(() => {
  const total = Math.max(analytics.value.total_invitations || 0, 1);
  return Math.round(((analytics.value.attended || 0) / total) * 100);
});

const pctNot = computed(() => {
  const total = Math.max(analytics.value.total_invitations || 0, 1);
  return Math.round(((analytics.value.not_attended || 0) / total) * 100);
});

const pctMaybe = computed(() => {
  const total = Math.max(analytics.value.total_invitations || 0, 1);
  return Math.round(((analytics.value.maybe || 0) / total) * 100);
});

const fetchGuests = async () => {
  try {
    loading.value = true;
    const res = await api.get(`/invitations/${invitationId}/guests`, {
      params: {
        q: searchQuery.value || undefined,
        status: statusFilter.value || undefined
      }
    });

    invitation.value = res.data.invitation;
    guests.value = res.data.guests || [];
    analytics.value = res.data.analytics || {};
    wishes.value = res.data.invitation?.wishes || [];
  } catch (err) {
    console.error('Fetch guests error:', err);
  } finally {
    loading.value = false;
  }
};

const applyFilter = () => {
  fetchGuests();
};

const resetFilter = () => {
  searchQuery.value = '';
  statusFilter.value = '';
  fetchGuests();
};

const createGuest = async () => {
  try {
    submittingGuest.value = true;
    await api.post(`/invitations/${invitationId}/guests`, newGuest.value);
    showAddModal.value = false;
    newGuest.value = { name: '', phone: '', guest_count: 1 };
    statusMessage.value = 'Tamu berhasil ditambahkan ke daftar!';
    await fetchGuests();
  } catch (err) {
    alert(err.response?.data?.message || 'Gagal menambahkan tamu');
  } finally {
    submittingGuest.value = false;
  }
};

const deleteGuest = async (guestId) => {
  if (!confirm('Hapus tamu ini dari daftar?')) return;
  try {
    await api.delete(`/invitations/${invitationId}/guests/${guestId}`);
    statusMessage.value = 'Tamu berhasil dihapus dari daftar.';
    await fetchGuests();
  } catch (err) {
    alert(err.response?.data?.message || 'Gagal menghapus tamu');
  }
};

const checkInGuest = async (guestId) => {
  try {
    await api.post(`/invitations/${invitationId}/guests/${guestId}/checkin`);
    await fetchGuests();
  } catch (err) {
    alert(err.response?.data?.message || 'Gagal melakukan check-in');
  }
};

const getGuestUrl = (guest) => {
  return `${window.location.origin}/u/${invitation.value?.slug}/${guest.personal_token}`;
};

const copyGuestLink = (guest) => {
  navigator.clipboard.writeText(getGuestUrl(guest));
  copiedGuestId.value = guest.id;
  setTimeout(() => {
    copiedGuestId.value = null;
  }, 2500);
};

const getCleanPhone = (phone) => {
  if (!phone) return '';
  let clean = phone.replace(/[^0-9]/g, '');
  if (clean.startsWith('0')) {
    clean = '62' + clean.slice(1);
  }
  return clean;
};

const getWhatsAppLink = (guest) => {
  const phone = getCleanPhone(guest.phone);
  const link = getGuestUrl(guest);
  const text = encodeURIComponent(
    `Kepada Yth. ${guest.name},\n\nTanpa mengurangi rasa hormat, perkenankan kami mengundang Bapak/Ibu/Saudara/i untuk menghadiri acara kami.\n\nBerikut tautan undangan online lengkap:\n${link}\n\nMerupakan suatu kehormatan dan kebahagiaan bagi kami apabila berkenan hadir dan memberikan doa restu. Terima kasih.`
  );
  return `https://api.whatsapp.com/send?phone=${phone}&text=${text}`;
};

const exportCsv = () => {
  window.open(`/api/invitations/${invitationId}/guests/export`, '_blank');
};

const formatRelativeTime = (dateStr) => {
  if (!dateStr) return '';
  const date = new Date(dateStr);
  return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' });
};

onMounted(() => {
  fetchGuests();
});
</script>
