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
