import api from '../api/axios';

const TeacherService = {
  getAll(params = {}) {
    return api.get('/api/v1/admin/teachers', { params });
  },

  getOne(id) {
    return api.get(`/api/v1/admin/teachers/${id}`);
  },

  create(data) {
    return api.post('/api/v1/admin/teachers', data);
  },

  update(id, data) {
    return api.put(`/api/v1/admin/teachers/${id}`, data);
  },

  delete(id) {
    return api.delete(`/api/v1/admin/teachers/${id}`);
  },
};
export default TeacherService;
export { TeacherService };
