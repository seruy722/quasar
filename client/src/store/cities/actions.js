import { getUrl } from 'src/tools/url';
import { axiosInstance } from 'boot/axios';

export const setCities = (({ commit }) => axiosInstance.get(getUrl('cities'))
  .then(({ data }) => {
    commit('SET_CITIES', data);
  })
  .catch(() => {
    devlog.warn('Ошибка при запросе setCities');
  }));
