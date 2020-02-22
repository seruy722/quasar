import { getUrl } from 'src/tools/url';
import { axiosInstance } from 'boot/axios';

export const fetchStorehouseTableData = (({ commit }) => axiosInstance.get(`${getUrl('storehouseData')}/${1}`)
  .then(({ data }) => {
    commit('SET_STOREHOUSE_DATA', data);
  })
  .catch(() => {
    devlog.warn('Ошибка при запросе fetchStorehouseTableData');
  }));

// export const setStorehouseData = (({ commit }, data) => {
//   commit('SET_STOREHOUSE_DATA', data);
// });

export const setStorehouseCategoriesData = (({ commit }, data) => {
  devlog.log('setStorehouseCategoriesData', data);
  commit('SET_STOREHOUSE_CATEGORIES_DATA', data);
});

export const addToStorehouseData = (({ commit }, entry) => {
  commit('ADD_TO_STOREHOUSE_DATA', entry);
});

export const updateStorehouseData = (({ commit }, entry) => {
  commit('UPDATE_STOREHOUSE_DATA', entry);
});

export const destroyStorehouseData = (({ commit }, arr) => {
  commit('DESTROY_STOREHOUSE_DATA', arr);
});
