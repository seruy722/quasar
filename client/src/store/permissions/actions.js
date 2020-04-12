import { getUrl } from 'src/tools/url';
import { axiosInstance } from 'boot/axios';
// import { sortArrayCollection } from 'src/utils/sort';
// import { setFormatedDate } from 'src/utils/FrequentlyCalledFunctions';

export const fetchPermissions = (({ commit }) => axiosInstance.get(getUrl('permissionList'))
  .then(({ data }) => {
    devlog.log('Perm', data);
    commit('SET_PERMISSIONS', data);
  })
  .catch((e) => {
    devlog.error('Ошибка запроса - fetchRoles', e);
  }));

export const addPermission = (({ commit }, role) => {
  commit('ADD_PERMISSION', role);
});

export const deletePermission = (({ commit }, id) => {
  commit('DELETE_PERMISSION', id);
});
