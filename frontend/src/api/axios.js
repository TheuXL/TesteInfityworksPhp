import axios from 'axios';

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || '',
  withCredentials: true,
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  },
});

/**
 * Interceptor de resposta: tratamento global de erros.
 * Não redireciona aqui para evitar loop (ERR_TOO_MANY_REDIRECTS); o router guard já envia para /login.
 * - 401/403: limpa storage e rejeita (componentes/guard tratam).
 * - 422: normaliza erros de validação em error.validationErrors.
 */
api.interceptors.response.use(
  (response) => response,
  (error) => {
    const status = error.response?.status;
    const data = error.response?.data;

    if (status === 401 || status === 403) {
      if (typeof window !== 'undefined') {
        try {
          localStorage.removeItem('user');
          sessionStorage.clear();
        } catch (_) {}
      }
      const err = Object.assign(error, {
        message: data?.message || (status === 401 ? 'Não autenticado.' : 'Acesso não autorizado.'),
      });
      return Promise.reject(err);
    }

    if (status === 422 && data?.errors) {
      const normalized = Object.assign(error, {
        validationErrors: data.errors,
        message: data.message || 'Erro de validação.',
      });
      return Promise.reject(normalized);
    }

    return Promise.reject(error);
  }
);

export default api;
