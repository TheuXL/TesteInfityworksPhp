import api from '../api/axios';

const ReportService = {
  getCourseAges() {
    return api.get('/api/v1/admin/reports');
  },
};
export default ReportService;
export { ReportService };
