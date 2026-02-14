import { defineStore } from 'pinia';
import api from '../api/axios';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    loading: false,
    userFetched: false,
  }),

  getters: {
    isAuthenticated: (state) => !!state.user,
    isAdmin: (state) => state.user?.role === 'admin',
    isStudent: (state) => state.user?.role === 'student',
    userName: (state) => state.user?.name ?? '',
  },

  actions: {
    async fetchUser() {
      if (this.userFetched) return this.user;
      this.loading = true;
      try {
        const { data } = await api.get('/api/v1/user');
        const raw = data?.data ?? data;
        this.user = raw && typeof raw === 'object' && (raw.id != null || raw.role != null) ? raw : null;
        return this.user;
      } catch {
        this.user = null;
        return null;
      } finally {
        this.loading = false;
        this.userFetched = true;
      }
    },

    async login(credentials) {
      const { data } = await api.post('/login', credentials);
      this.user = data.user?.data ?? data.user;
      return this.user;
    },

    async logout() {
      await api.post('/logout');
      this.user = null;
      this.userFetched = false;
    },

    async init() {
      if (this.user) return this.user;
      return this.fetchUser();
    },

    setUser(user) {
      const raw = user?.data ?? user;
      this.user = raw && typeof raw === 'object' && (raw.id != null || raw.role != null) ? raw : null;
    },
  },
});
