import axios from 'axios';
import { getUrl } from 'src/tools/url';
import { LocalStorage, Notify } from 'quasar';
import { getLSKey } from 'src/tools/lsKeys';

// devlog.log('navigator.connection.type', navigator.connection.type);
const axiosInstance = axios.create(getUrl('axiosData'));
export default ({ Vue, router }) => {
  axiosInstance.interceptors.request.use((request) => {
    const token = LocalStorage.getItem(getLSKey('authToken'));
    // devlog.log('token', token);
    if (token) {
      request.headers.Authorization = `Bearer ${token}`;
    }

    const locale = LocalStorage.getItem(getLSKey('lang'));
    devlog.log('loc', locale);
    if (!_.isEmpty(locale)) {
      request.headers.Locale = locale.value;
    }

    return request;
  });

  axiosInstance.interceptors.response.use((response) => response, (error) => {
    // const token = Cookies.get(getLSKey('authToken'));
    const { status } = error.response;
    devlog.log('response_status', status);
    // devlog.log('token_Axios', token);
    // devlog.log('error', error.response);
    // if (_.isUndefined(status)) {
    //     store.commit('settings/setModal', true);
    // }
    if (status === 403) {
      Notify.create({
        position: 'center',
        message: 'У Вас нет доступа к этому ресурсу.',
        color: 'red',
      });
    } else if (status === 403) {
      router.push({ name: 'login' });
    }
    // if ((status === 401 && !token) || (status === 401 && token === 'undefined')) {
    //     devlog.log(':YES');
    //     router.push({ name: 'index' });
    // } else if (status === 401 && token) {
    // store.dispatch('innerLoading/setInner', true);
    // store.dispatch('auth/fetchUser')
    //     .then((response) => {
    //         store.dispatch('innerLoading/setInner', false);
    //         if (!response) {
    //             router.push({ name: 'index' });
    //         }
    //     });
    // }

    return Promise.reject(error);
  });
  Vue.prototype.$axios = axiosInstance;
};

export { axiosInstance };
