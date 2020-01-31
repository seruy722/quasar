import { getUrl } from 'src/tools/url';
import { axiosInstance } from 'boot/axios';

export const setThingsList = (({ commit }) => axiosInstance.get(getUrl('thingsList'))
  .then(({ data }) => {
    commit('SET_THINGS_LIST', data);
  })
  .catch(() => {
    devlog.warn('Ошибка - setThingsList');
  }));
