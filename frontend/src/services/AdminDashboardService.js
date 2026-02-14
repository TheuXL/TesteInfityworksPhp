import api from '../api/axios';

const AdminDashboardService = {
  getChartData() {
    return api.get('/api/v1/admin/dashboard');
  },
};
export default AdminDashboardService;
export { AdminDashboardService };
