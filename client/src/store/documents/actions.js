import { getUrl } from 'src/tools/url';
import { axiosInstance } from 'boot/axios';
import { fullDate } from 'src/utils/formatDate';

export const fetchDocuments = (({ commit }) => axiosInstance.get(getUrl('getDocumentsComments'))
  .then(({ data: { documents } }) => {
    commit('SET_DOCUMENTS', _.map(documents, (doc) => {
      doc.formatDate = fullDate(doc.created_at);
      return doc;
    }));
  }));

export const addDocument = (({ commit }, data) => {
  commit('ADD_DOCUMENT', data);
});
