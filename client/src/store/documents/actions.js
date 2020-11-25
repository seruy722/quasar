import { getUrl } from 'src/tools/url';
import { axiosInstance } from 'boot/axios';

export const fetchDocuments = (({ commit }) => {
  const res = axiosInstance.get(getUrl('getDocumentsComments'));
  res.then(({ data: { documents } }) => {
    commit('SET_DOCUMENTS', documents);
  });
  return res;
});

export const addDocument = (({ commit }, data) => {
  commit('ADD_DOCUMENT', data);
});
