import { defineStore } from 'pinia';
import {
  loginWithEmail,
  registerWithEmail,
  loginWithGoogle,
  logoutUser,
  subscribeAuthState,
  getUserProfile
} from '../firebase/services/authService';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('forkoway_user') || 'null'),
    token: localStorage.getItem('forkoway_token') || (localStorage.getItem('forkoway_user') ? 'firebase-session' : null),
    loading: false,
    initialized: false,
  }),
  getters: {
    isAuthenticated: (state) => !!state.user,
    isAdmin: (state) => {
      if (!state.user) return false;
      const email = state.user.email?.toLowerCase() || '';
      return state.user.role === 'admin' || email.includes('admin') || email.includes('annisajatigede');
    },
  },
  actions: {
    async login(email, password) {
      this.loading = true;
      try {
        const { user, profile } = await loginWithEmail(email, password);
        const token = await user.getIdToken();
        this.token = token;
        this.user = {
          id: user.uid,
          uid: user.uid,
          email: user.email,
          name: profile?.name || user.displayName || user.email?.split('@')[0],
          role: profile?.role || (email.toLowerCase().includes('admin') ? 'admin' : 'user')
        };
        localStorage.setItem('forkoway_token', this.token);
        localStorage.setItem('forkoway_user', JSON.stringify(this.user));
        return { user: this.user, token: this.token };
      } finally {
        this.loading = false;
      }
    },

    async register(payload) {
      this.loading = true;
      try {
        const { user, profile } = await registerWithEmail(payload.email, payload.password, payload.name);
        const token = await user.getIdToken();
        this.token = token;
        this.user = {
          id: user.uid,
          uid: user.uid,
          email: user.email,
          name: profile?.name || payload.name,
          role: profile?.role || 'user'
        };
        localStorage.setItem('forkoway_token', this.token);
        localStorage.setItem('forkoway_user', JSON.stringify(this.user));
        return { user: this.user, token: this.token };
      } finally {
        this.loading = false;
      }
    },

    async loginGoogle() {
      this.loading = true;
      try {
        const { user, profile } = await loginWithGoogle();
        const token = await user.getIdToken();
        this.token = token;
        this.user = {
          id: user.uid,
          uid: user.uid,
          email: user.email,
          name: profile?.name || user.displayName || 'Pengguna Forkoway',
          role: profile?.role || (user.email?.toLowerCase().includes('admin') ? 'admin' : 'user')
        };
        localStorage.setItem('forkoway_token', this.token);
        localStorage.setItem('forkoway_user', JSON.stringify(this.user));
        return { user: this.user, token: this.token };
      } finally {
        this.loading = false;
      }
    },

    async fetchUser() {
      return this.user;
    },

    async logout() {
      await logoutUser();
      this.user = null;
      this.token = null;
      localStorage.removeItem('forkoway_token');
      localStorage.removeItem('forkoway_user');
      localStorage.removeItem('kawinan_token');
      localStorage.removeItem('kawinan_user');
    },

    initAuth() {
      if (this.initialized) return;
      this.initialized = true;

      subscribeAuthState(async (firebaseUser, profile) => {
        if (firebaseUser) {
          const token = await firebaseUser.getIdToken();
          this.token = token;
          this.user = {
            id: firebaseUser.uid,
            uid: firebaseUser.uid,
            email: firebaseUser.email,
            name: profile?.name || firebaseUser.displayName || firebaseUser.email?.split('@')[0],
            role: profile?.role || (firebaseUser.email?.toLowerCase().includes('admin') ? 'admin' : 'user')
          };
          localStorage.setItem('forkoway_token', this.token);
          localStorage.setItem('forkoway_user', JSON.stringify(this.user));
        } else {
          // If no firebase session
          if (this.token === 'firebase-session') {
            this.user = null;
            this.token = null;
            localStorage.removeItem('forkoway_token');
            localStorage.removeItem('forkoway_user');
          }
        }
      });
    }
  },
});
