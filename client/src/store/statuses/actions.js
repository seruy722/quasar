import { getUrl } from 'src/tools/url';
import { axiosInstance } from 'boot/axios';

export const fetchStatuses = ({ commit }) => {
    const res = axiosInstance.get(getUrl('getStatuses'));
    res.then(({ data }) => {
        commit('SET_STATUSES', data);
    });

    return res;
};
