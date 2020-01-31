import { axiosInstance } from 'boot/axios';
import { getUrl } from 'src/tools/url';
// import getFromSettings from 'src/tools/settings';

export const fetchTransporters = (({ commit }) => axiosInstance.get(getUrl('transporters'))
  .then(({ data: { transporters } }) => {
    commit('SET_TRANSPORTERS', transporters);
  })
  .catch((errors) => {
    devlog.error(errors);
  }));


export const setTransporterPrice = (({ commit }, data) => {
  commit('SET_TRANSPORTER_PRICE', data);
});

export const AddTransporter = (({ commit }, transporter) => {
  commit('ADD_TRANSPORTER', transporter);
});
