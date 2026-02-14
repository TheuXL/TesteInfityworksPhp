import api from '../api/axios';

const AlunoService = {
  getDashboardChart() {
    return api.get('/api/v1/aluno/dashboard');
  },

  getProfile() {
    return api.get('/api/v1/aluno/profile');
  },

  updateProfile(data) {
    return api.put('/api/v1/aluno/profile', data);
  },
};
export default AlunoService;
export { AlunoService };
