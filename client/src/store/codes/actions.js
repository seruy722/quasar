import { getUrl } from 'src/tools/url';
import { axiosInstance } from 'boot/axios';

export const setCodes = (({ commit }) => axiosInstance.get(getUrl('codeList'))
  .then(({ data: { codeList } }) => {
    commit('SET_CODES', codeList);
  })
  .catch((e) => {
    devlog.error('Ошибка запроса - codeList', e);
  }));

export const addCode = (({ commit }, data) => {
  commit('ADD_CODE', data);
});
