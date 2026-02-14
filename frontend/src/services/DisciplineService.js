import api from '../api/axios';

const DisciplineService = {
  getAll(params = {}) {
    return api.get('/api/v1/admin/disciplines', { params });
  },

  getOne(id) {
    return api.get(`/api/v1/admin/disciplines/${id}`);
  },

  create(data) {
    return api.post('/api/v1/admin/disciplines', data);
  },

  update(id, data) {
    return api.put(`/api/v1/admin/disciplines/${id}`, data);
  },

  delete(id) {
    return api.delete(`/api/v1/admin/disciplines/${id}`);
  },
};
export default DisciplineService;
export { DisciplineService };
