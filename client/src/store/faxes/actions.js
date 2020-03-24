import { getUrl } from 'src/tools/url';
import { axiosInstance } from 'boot/axios';
import { setFormatedDate } from 'src/utils/FrequentlyCalledFunctions';

export const fetchFaxes = (({ commit }) => axiosInstance.get(getUrl('faxes'))
  .then(({ data: { faxesList } }) => {
    commit('SET_FAXES', setFormatedDate(faxesList, ['departure_date', 'arrival_date', 'created_at']));
  })
  .catch(() => {
    devlog.warn('Ошибка при запросе fetchFaxes');
  }));

// export const setFaxes = (({ commit }, data) => {
//   commit('SET_FAXES', data);
// });

export const addFax = (({ commit }, data) => {
  commit('ADD_FAX', data);
});

export const updateFax = (({ commit }, data) => {
  commit('UPDATE_FAX', data);
});

export const deleteFaxes = (({ commit }, data) => {
  commit('DELETE_FAXES', data);
});

export const setCurrentFaxItem = (({ commit }, data) => {
  commit('SET_CURRENT_FAX_ITEM', data);
});

export const setFaxData = (({ commit }, data) => {
  commit('SET_FAX_DATA', data);
});

export const updateFaxData = (({ commit }, data) => {
  commit('UPDATE_FAX_DATA', data);
});

export const setFaxCategoriesData = (({ commit }, data) => {
  commit('SET_FAX_CATEGORIES_DATA', data);
});
