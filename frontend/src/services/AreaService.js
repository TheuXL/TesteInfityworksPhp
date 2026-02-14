import api from '../api/axios';

const AreaService = {
  getAll(params = {}) {
    return api.get('/api/v1/admin/areas', { params });
  },

  getOne(id) {
    return api.get(`/api/v1/admin/areas/${id}`);
  },

  create(data) {
    return api.post('/api/v1/admin/areas', data);
  },

  update(id, data) {
    return api.put(`/api/v1/admin/areas/${id}`, data);
  },

  delete(id) {
    return api.delete(`/api/v1/admin/areas/${id}`);
  },
};
export default AreaService;
export { AreaService };
