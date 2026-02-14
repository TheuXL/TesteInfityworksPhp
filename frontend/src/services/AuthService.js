import api from '../api/axios';

const AuthService = {
  async getCsrfCookie() {
    await api.get('/sanctum/csrf-cookie');
  },

  async login(credentials) {
    const { data } = await api.post('/login', credentials);
    return data.user?.data ?? data.user;
  },

  async loginAluno(credentials) {
    const { data } = await api.post('/login/aluno', credentials);
    return data.user?.data ?? data.user;
  },

  async loginAdmin(credentials) {
    const { data } = await api.post('/login/admin', credentials);
    return data.user?.data ?? data.user;
  },

  async register(payload) {
    const { data } = await api.post('/register', payload);
    return data;
  },

  async registerAdmin(payload) {
    const { data } = await api.post('/api/v1/admin/register', payload);
    return data;
  },

  async logout() {
    await api.post('/logout');
  },

  async getUser() {
    const { data } = await api.get('/api/v1/user');
    return data.data ?? data;
  },
};
export default AuthService;
export { AuthService };
