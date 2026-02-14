import api from '../api/axios';

const StudentService = {
  getAll(filters = {}) {
    return api.get('/api/v1/admin/students', { params: filters });
  },

  getOne(id) {
    return api.get(`/api/v1/admin/students/${id}`);
  },

  create(data) {
    return api.post('/api/v1/admin/students', data);
  },

  update(id, data) {
    return api.put(`/api/v1/admin/students/${id}`, data);
  },

  delete(id) {
    return api.delete(`/api/v1/admin/students/${id}`);
  },
};
export default StudentService;
export { StudentService };
