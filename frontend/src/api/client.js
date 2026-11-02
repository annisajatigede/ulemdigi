import axios from 'axios';

const apiBase = import.meta.env.VITE_API_BASE_URL 
  ? import.meta.env.VITE_API_BASE_URL.replace(/\/$/, '') + '/api'
  : '/api';

const api = axios.create({
  baseURL: apiBase,
  withCredentials: true,
  headers: {
    'Accept': 'application/json',
  },
});

api.interceptors.request.use((config) => {
  const token = localStorage.getItem('kawinan_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response && error.response.status === 401) {
      localStorage.removeItem('kawinan_token');
      localStorage.removeItem('kawinan_user');
    }
    return Promise.reject(error);
  }
);

export default api;
