import { axiosInstance } from 'boot/axios';
import { getUrl } from 'src/tools/url';
// import getFromSettings from 'src/tools/settings';

export const fetchTransports = (({ commit }) => axiosInstance.get(getUrl('transports'))
  .then(({ data: { transports } }) => {
    commit('SET_TRANSPORTS', transports);
  })
  .catch((errors) => {
    devlog.error(errors);
  }));
