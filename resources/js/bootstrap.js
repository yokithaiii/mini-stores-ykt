import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const token = window.localStorage?.getItem('admin_token');
if (token) {
    window.axios.defaults.headers.common.Authorization = `Bearer ${token}`;
}
