<template>
  <div class="min-h-screen flex flex-col bg-[#fafafa]">
    <!-- Navbar (Hidden on public invitation pages) -->
    <Navbar v-if="!isPublicInvitation" />

    <!-- Main View Content -->
    <main class="flex-1">
      <router-view />
    </main>
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import Navbar from './components/Navbar.vue';
import { useAuthStore } from './stores/auth';

const route = useRoute();
const authStore = useAuthStore();

const isPublicInvitation = computed(() => {
  return route.name === 'invitation.show' || route.name === 'invitation.guest';
});

onMounted(() => {
  authStore.fetchUser();
});
</script>
