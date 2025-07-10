import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Configuration CSRF pour Laravel
let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

// Configuration des intercepteurs Axios
window.axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response?.status === 419) {
            // CSRF token mismatch
            window.location.reload();
        }
        
        if (error.response?.status === 422) {
            // Erreurs de validation
            console.log('Erreurs de validation:', error.response.data.errors);
        }
        
        return Promise.reject(error);
    }
); 