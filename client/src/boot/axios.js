import axios from 'axios';
import { getUrl } from 'src/tools/url';
import { LocalStorage } from 'quasar';
import { getLSKey } from 'src/tools/lsKeys';

// devlog.log('navigator.connection.type', navigator.connection.type);
const axiosInstance = axios.create(getUrl('axiosData'));
export default ({ app }) => {
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

  axiosInstance.interceptors.response.use((response) => response, (error) => Promise.reject(error));

  app.config.globalProperties.$axios = axiosInstance;
};

export { axiosInstance };
