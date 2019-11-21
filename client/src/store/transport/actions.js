import { axiosInstance } from 'boot/axios';
import { getUrl } from 'src/tools/url';
import getFromSettings from 'src/tools/settings';

export const fetchTransports = (async ({ commit }) => {
  await axiosInstance.get(getUrl('transports'))
    .then(({ data }) => {
      data.transports.unshift(getFromSettings('defaultSelectElement'));
      commit('SET_TRANSPORTS', data.transports);
    })
    .catch((errors) => {
      devlog.error(errors);
    });
});
