import { axiosInstance } from 'boot/axios';
import { getUrl } from 'src/tools/url';

export const setUser = (({ commit }, data) => {
    commit('SET_USER', data);
});

export const getUserModel = (async ({ commit }) => {
    try {
        const { data } = await axiosInstance.get(getUrl('userModel'));
        devlog.log('DDS', data);
        commit('SET_USER', data);
        return true;
    } catch (e) {
        return false;
    }
});

export const setToPath = (({ commit }, value) => {
    commit('SET_TO_PATH', value);
});
