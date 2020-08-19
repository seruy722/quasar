import { getUrl } from 'src/tools/url';
import { axiosInstance } from 'boot/axios';

export const fetchDocuments = (({ commit }) => axiosInstance.get(getUrl('getDocumentsComments'))
  .then(({ data: { documents } }) => {
    commit('SET_DOCUMENTS', documents);
  }));

export const addDocument = (({ commit }, data) => {
  commit('ADD_DOCUMENT', data);
});
