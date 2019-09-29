import { axiosInstance } from 'boot/axios';
import { getUrl } from 'src/tools/url';

export const fetchTransports = (async ({ commit, getters }) => {
    if (_.isEmpty(getters.getTransports)) {
        await axiosInstance.get(getUrl('transports'))
          .then(({ data }) => {
              commit('SET_TRANSPORTS', data.transports);
          })
          .catch((errors) => {
              devlog.error(errors);
          });
    }
});
