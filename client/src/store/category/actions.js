import { getUrl } from 'src/tools/url';
import { axiosInstance } from 'boot/axios';

export const getCategories = (async ({ commit }) => {
  try {
    const { data } = await axiosInstance.get(getUrl('categories'));
    commit('SET_CATEGORIES', data.categories);
  } catch (e) {
    devlog.log(e);
  }
});

export const AddCategory = (({ commit }, category) => {
  commit('ADD_CATEGORY', category);
});
