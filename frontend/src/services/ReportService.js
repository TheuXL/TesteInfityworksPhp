import api from '../api/axios';

const ReportService = {
  getCourseAges() {
    return api.get('/api/v1/admin/reports');
  },

  /**
   * Baixa o relatório completo em PDF (gráficos e dados dos alunos).
   * Retorna uma Promise que resolve quando o download for disparado.
   */
  async downloadPdf() {
    const { data } = await api.get('/api/v1/admin/reports/pdf', {
      responseType: 'blob',
    });
    const url = window.URL.createObjectURL(new Blob([data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', `relatorio-admin-${new Date().toISOString().slice(0, 10)}.pdf`);
    document.body.appendChild(link);
    link.click();
    link.remove();
    window.URL.revokeObjectURL(url);
  },
};
export default ReportService;
export { ReportService };
