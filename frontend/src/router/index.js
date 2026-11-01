import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const routes = [
  {
    path: '/',
    name: 'home',
    component: () => import('../views/HomeView.vue'),
  },
  {
    path: '/login',
    name: 'login',
    component: () => import('../views/auth/LoginView.vue'),
  },
  {
    path: '/register',
    name: 'register',
    component: () => import('../views/auth/RegisterView.vue'),
  },
  {
    path: '/forgot-password',
    name: 'forgot-password',
    component: () => import('../views/auth/ForgotPasswordView.vue'),
  },
  {
    path: '/dashboard',
    name: 'dashboard',
    component: () => import('../views/dashboard/DashboardView.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/checkout/select',
    name: 'checkout.select',
    component: () => import('../views/checkout/SelectView.vue'),
  },
  {
    path: '/checkout/pay/:id',
    name: 'checkout.pay',
    component: () => import('../views/checkout/PayView.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/checkout/success/:id',
    name: 'checkout.success',
    component: () => import('../views/checkout/SuccessView.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/editor/:id',
    name: 'editor',
    component: () => import('../views/editor/EditorView.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/invitations/:id/guests',
    name: 'guests',
    component: () => import('../views/guests/GuestsView.vue'),
    meta: { requiresAuth: true },
  },
  // Public Invitations
  {
    path: '/u/:slug',
    name: 'invitation.show',
    component: () => import('../views/public/PublicInvitationDispatcher.vue'),
  },
  {
    path: '/u/:slug/:token',
    name: 'invitation.guest',
    component: () => import('../views/public/PublicInvitationDispatcher.vue'),
  },
  // Admin Routes
  {
    path: '/admin',
    name: 'admin.dashboard',
    component: () => import('../views/admin/AdminDashboard.vue'),
    meta: { requiresAdmin: true },
  },
  {
    path: '/admin/users',
    name: 'admin.users',
    component: () => import('../views/admin/AdminUsers.vue'),
    meta: { requiresAdmin: true },
  },
  {
    path: '/admin/templates',
    name: 'admin.templates',
    component: () => import('../views/admin/AdminTemplates.vue'),
    meta: { requiresAdmin: true },
  },
  {
    path: '/admin/musics',
    name: 'admin.musics',
    component: () => import('../views/admin/AdminMusics.vue'),
    meta: { requiresAdmin: true },
  },
  {
    path: '/admin/plans',
    name: 'admin.plans',
    component: () => import('../views/admin/AdminPlans.vue'),
    meta: { requiresAdmin: true },
  },
  {
    path: '/admin/transactions',
    name: 'admin.transactions',
    component: () => import('../views/admin/AdminTransactions.vue'),
    meta: { requiresAdmin: true },
  },
  {
    path: '/admin/settings',
    name: 'admin.settings',
    component: () => import('../views/admin/AdminSettings.vue'),
    meta: { requiresAdmin: true },
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior() {
    return { top: 0 };
  },
});

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();

  if (to.meta.requiresAuth || to.meta.requiresAdmin) {
    if (!authStore.isAuthenticated) {
      await authStore.fetchUser();
    }

    if (!authStore.isAuthenticated) {
      return next({ name: 'login', query: { redirect: to.fullPath } });
    }

    if (to.meta.requiresAdmin && !authStore.isAdmin) {
      return next({ name: 'dashboard' });
    }
  }

  next();
});

export default router;
