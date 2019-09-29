import { getUrl } from 'src/tools/url';
import { axiosInstance } from 'boot/axios';

export const getCodes = (async ({ commit }) => {
    try {
        const { data } = await axiosInstance.get(getUrl('codeList'));
        commit('SET_CODES', data.data);
    } catch (e) {
        devlog.log(e);
    }
});
