import { getUrl } from 'src/tools/url';
import { axiosInstance } from 'boot/axios';

export const setShopsList = (async ({ commit }) => axiosInstance.get(getUrl('shopsList'))
  .then(({ data }) => {
    commit('SET_SHOPS_LIST', data);
  })
  .catch(() => {
    devlog.error('Ошибка запроса - setShopsList');
  }));
