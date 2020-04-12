import { axiosInstance } from 'boot/axios';
import { getUrl } from 'src/tools/url';

export const setUser = (({ commit }, data) => {
  commit('SET_USER', data);
});

export const getUserModel = (({ commit }) => axiosInstance.get(getUrl('userModel'))
  .then(({ data }) => {
    commit('SET_USER', data);
  }));

export const setToPath = (({ commit }, value) => {
  commit('SET_TO_PATH', value);
});

export const logout = (({ commit }) => {
  commit('LOGOUT');
});

export const setUsersWithRolesAndPermissions = (({ commit }, array) => {
  commit('SET_USERS_WITH_ROLES_AND_PERMISSIONS', array);
});

export const userHaveAccess = ((data) => {
  // const userRoles = _.get(state, 'user.roles');
  // const userPermissions = _.get(state, 'user.roles');
  // devlog.log('userRoles', userRoles);
  // devlog.log('userPermissions', userPermissions);
  devlog.log('data', data);
  return true;
});
