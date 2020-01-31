import { getUrl } from 'src/tools/url';
import { axiosInstance } from 'boot/axios';

export const getCategories = (({ commit }) => axiosInstance.get(getUrl('categories'))
  .then(({ data: { categories } }) => {
    commit('SET_CATEGORIES', categories);
  })
  .catch(() => {
    devlog.warn('Ошибка при запросе getCategories');
  }));

export const AddCategory = (({ commit }, category) => {
  commit('ADD_CATEGORY', category);
});
