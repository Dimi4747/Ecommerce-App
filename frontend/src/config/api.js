import axios from 'axios';

// Configuration de base pour axios
const API_BASE_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000';

// Instance axios configurée
const apiClient = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  withCredentials: false,
});

// Intercepteur pour gérer les erreurs globalement
apiClient.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response) {
      // Le serveur a répondu avec un code d'erreur
      console.error('Erreur API:', error.response.status, error.response.data);
    } else if (error.request) {
      // La requête a été faite mais pas de réponse
      console.error('Pas de réponse du serveur:', error.request);
    } else {
      // Erreur lors de la configuration de la requête
      console.error('Erreur de configuration:', error.message);
    }
    return Promise.reject(error);
  }
);

// Endpoints API
export const API_ENDPOINTS = {
  // Orders
  orders: {
    list: '/api/orders',
    show: (id) => `/api/orders/${id}`,
    create: '/api/orders',
    update: (id) => `/api/orders/${id}`,
    delete: (id) => `/api/orders/${id}`,
    statistics: '/api/orders/statistics',
  },
  // Products
  products: {
    list: '/api/products',
    show: (id) => `/api/products/${id}`,
    create: '/api/products',
    update: (id) => `/api/products/${id}`,
    delete: (id) => `/api/products/${id}`,
  },
  // Customers
  customers: {
    list: '/api/customers',
    show: (id) => `/api/customers/${id}`,
    create: '/api/customers',
    update: (id) => `/api/customers/${id}`,
    delete: (id) => `/api/customers/${id}`,
  },
};

export default apiClient;
