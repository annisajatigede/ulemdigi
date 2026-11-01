<template>
  <div v-if="loading" class="min-h-screen bg-stone-100 flex items-center justify-center p-6 text-stone-500 text-sm">
    <div class="text-center">
      <div class="w-10 h-10 border-4 border-rose-500 border-t-transparent rounded-full animate-spin mx-auto mb-3"></div>
      <p>Memuat undangan digital...</p>
    </div>
  </div>

  <div v-else-if="error" class="min-h-screen bg-stone-50 flex items-center justify-center p-6 text-center">
    <div class="max-w-md w-full bg-white p-8 rounded-3xl shadow-xl border border-stone-200">
      <h3 class="text-lg font-bold text-stone-800 mb-2">Undangan Tidak Ditemukan</h3>
      <p class="text-xs text-stone-500 mb-6">{{ error }}</p>
      <router-link to="/" class="inline-block bg-stone-900 text-white text-xs font-bold px-5 py-2.5 rounded-xl shadow-md">
        Kembali ke Beranda
      </router-link>
    </div>
  </div>

  <!-- Not Published State -->
  <NotPublished v-else-if="isNotPublished" :invitation="invitation" />

  <!-- Expired State -->
  <Expired v-else-if="isExpired" :invitation="invitation" />

  <!-- Dynamic Template Component -->
  <component
    v-else
    :is="resolvedTemplateComponent"
    :invitation="invitation"
    :data="invitationData"
    :events="events"
    :wishes="wishes"
    :recipientName="recipientName"
    :isPersonal="isPersonal"
    :guest="guest"
    :previewMode="previewMode"
  />
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import api from '../../api/client';

import NotPublished from './NotPublished.vue';
import Expired from './Expired.vue';
import WeddingFloral from './WeddingFloral.vue';
import KhitanCeria from './KhitanCeria.vue';
import GeneralModern from './GeneralModern.vue';
import WeddingIslami from './WeddingIslami.vue';

const route = useRoute();
const slug = route.params.slug;
const token = route.params.token;
const previewMode = ref(route.query.preview === '1' || route.query.preview === 'true');

const loading = ref(true);
const error = ref(null);
const isNotPublished = ref(false);
const isExpired = ref(false);

const invitation = ref(null);
const template = ref(null);
const invitationData = ref({});
const events = ref([]);
const wishes = ref([]);
const recipientName = ref('Tamu Undangan');
const isPersonal = ref(false);
const guest = ref(null);

const resolvedTemplateComponent = computed(() => {
  const viewName = (template.value?.blade_view || '').toLowerCase();
  if (viewName.includes('khitan_ceria') || invitation.value?.category === 'khitan') {
    return KhitanCeria;
  }
  if (viewName.includes('general_modern') || invitation.value?.category === 'general') {
    return GeneralModern;
  }
  if (viewName.includes('wedding_islami')) {
    return WeddingIslami;
  }
  return WeddingFloral;
});

const loadInvitation = async () => {
  try {
    loading.value = true;
    error.value = null;

    const endpoint = token
      ? `/invitations/public/${slug}/${token}`
      : `/invitations/public/${slug}`;

    const res = await api.get(endpoint, {
      params: {
        to: route.query.to || undefined,
        preview: previewMode.value ? '1' : undefined
      }
    });

    if (res.data.not_published) {
      isNotPublished.value = true;
      invitation.value = res.data.invitation;
      return;
    }

    if (res.data.expired) {
      isExpired.value = true;
      invitation.value = res.data.invitation;
      return;
    }

    invitation.value = res.data.invitation;
    template.value = res.data.template;
    invitationData.value = res.data.data || {};
    events.value = res.data.events || [];
    wishes.value = res.data.wishes || [];
    recipientName.value = res.data.recipientName || 'Tamu Undangan';
    isPersonal.value = !!res.data.isPersonal;
    guest.value = res.data.guest || null;
  } catch (err) {
    error.value = err.response?.data?.message || 'Undangan tidak ditemukan atau terjadi kesalahan server.';
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  loadInvitation();
});
</script>
