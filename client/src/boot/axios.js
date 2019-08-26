import axios from 'axios';

// devlog.log('DD', getSettings('axiosData.axiosDefaultData'));
const axiosInstance = axios.create({
    headers: {
        Accept: 'application/json',
    },
    baseURL: 'http://sp.com.ua',
});
export default ({ Vue }) => {
    // Request interceptor
    // axiosInstance.interceptors.request.use((request) => {
    //     devlog.log('navigator.connection.type', navigator.connection.type);
    //     if (navigator.connection.type === 'none') {
    //         devlog.log('none');
    //         store.commit('settings/setModal', true);
    //     }
    //     const token = LocalStorage.getItem(getVariables('authToken'));
    //     if (token) {
    //         request.headers.Authorization = `Bearer ${token}`;
    //     }
    //     return request;
    // });

    // axiosInstance.interceptors.response.use(response => response, (error) => {
    //     const token = LocalStorage.getItem(getVariables('authToken'));
    //     const { status } = error.response || {};
    //     devlog.log('response_status', status);
    //     devlog.log('token_Axios', token);
    //     devlog.log('error', error.response);
    //     if (_.isUndefined(status)) {
    //         store.commit('settings/setModal', true);
    //     }
    //     if ((status === 401 && !token) || (status === 401 && token === 'undefined')) {
    //         devlog.log(':YES');
    //         router.push({ name: 'index' });
    //     } else if (status === 401 && token) {
    //         store.dispatch('innerLoading/setInner', true);
    //         store.dispatch('auth/fetchUser')
    //             .then((response) => {
    //                 store.dispatch('innerLoading/setInner', false);
    //                 if (!response) {
    //                     router.push({ name: 'index' });
    //                 }
    //             });
    //     }
    //
    //     return Promise.reject(error);
    // });
    Vue.prototype.$axios = axiosInstance;
};

export { axiosInstance };
