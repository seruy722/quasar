import { getUrl } from 'src/tools/url';
import { axiosInstance } from 'boot/axios';
import { sortArrayCollection } from 'src/utils/sort';

export const getCodes = (async ({ commit }) => {
  try {
    const { data } = await axiosInstance.get(getUrl('codeList'));
    const sorted = sortArrayCollection(data, 'label');
    sorted.unshift({
      label: 'Не выбрано',
      value: 0,
    });
    commit('SET_CODES', sorted);
  } catch (e) {
    devlog.log(e);
  }
});

export const addCode = (({ commit }, data) => {
  commit('ADD_CODE', data);
});
