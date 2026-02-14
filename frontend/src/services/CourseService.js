import api from '../api/axios';

const CourseService = {
  getAll(params = {}) {
    return api.get('/api/v1/admin/courses', { params });
  },

  getOne(id) {
    return api.get(`/api/v1/admin/courses/${id}`);
  },

  create(data) {
    return api.post('/api/v1/admin/courses', data);
  },

  update(id, data) {
    return api.put(`/api/v1/admin/courses/${id}`, data);
  },

  delete(id) {
    return api.delete(`/api/v1/admin/courses/${id}`);
  },
};
export default CourseService;
export { CourseService };
