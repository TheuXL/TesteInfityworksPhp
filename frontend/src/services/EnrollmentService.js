import api from '../api/axios';

const EnrollmentService = {
  getAll(params = {}) {
    return api.get('/api/v1/admin/enrollments', { params });
  },

  getCreateData() {
    return api.get('/api/v1/admin/enrollments/create');
  },

  getOne(id) {
    return api.get(`/api/v1/admin/enrollments/${id}`);
  },

  create(data) {
    return api.post('/api/v1/admin/enrollments', data);
  },

  update(id, data) {
    return api.put(`/api/v1/admin/enrollments/${id}`, data);
  },

  delete(id) {
    return api.delete(`/api/v1/admin/enrollments/${id}`);
  },
};
export default EnrollmentService;
export { EnrollmentService };
