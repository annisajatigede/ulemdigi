import { defineStore } from 'pinia';
import api from '../api/client';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('kawinan_user') || 'null'),
    token: localStorage.getItem('kawinan_token') || null,
    loading: false,
  }),
  getters: {
    isAuthenticated: (state) => !!state.user && !!state.token,
    isAdmin: (state) => state.user && state.user.role === 'admin',
  },
  actions: {
    async login(email, password) {
      this.loading = true;
      try {
        const response = await api.post('/auth/login', { email, password });
        this.user = response.data.user;
        this.token = response.data.token;
        localStorage.setItem('kawinan_token', this.token);
        localStorage.setItem('kawinan_user', JSON.stringify(this.user));
        return response.data;
      } finally {
        this.loading = false;
      }
    },
    async register(payload) {
      this.loading = true;
      try {
        const response = await api.post('/auth/register', payload);
        this.user = response.data.user;
        this.token = response.data.token;
        localStorage.setItem('kawinan_token', this.token);
        localStorage.setItem('kawinan_user', JSON.stringify(this.user));
        return response.data;
      } finally {
        this.loading = false;
      }
    },
    async fetchUser() {
      if (!this.token) return null;
      try {
        const response = await api.get('/auth/me');
        this.user = response.data.user;
        localStorage.setItem('kawinan_user', JSON.stringify(this.user));
        return this.user;
      } catch (err) {
        this.user = null;
        this.token = null;
        localStorage.removeItem('kawinan_token');
        localStorage.removeItem('kawinan_user');
        return null;
      }
    },
    async logout() {
      try {
        await api.post('/auth/logout');
      } catch (e) {
        // Ignore
      }
      this.user = null;
      this.token = null;
      localStorage.removeItem('kawinan_token');
      localStorage.removeItem('kawinan_user');
    },
  },
});
