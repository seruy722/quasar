import { getUrl } from 'src/tools/url';
import { axiosInstance } from 'boot/axios';

export const getCodes = (async ({ commit }) => {
  try {
    const { data } = await axiosInstance.get(getUrl('codeList'));
    data.unshift({
      label: 'Не выбрано',
      value: 0,
    });
    commit('SET_CODES', data);
  } catch (e) {
    devlog.log(e);
  }
});

export const addCode = (({ commit }, data) => {
  commit('ADD_CODE', data);
});
