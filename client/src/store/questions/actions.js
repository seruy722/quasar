import { getUrl } from 'src/tools/url';
import { axiosInstance } from 'boot/axios';

export const fetchQuestions = (({ commit }) => axiosInstance.get(getUrl('getQuestions'))
  .then(({ data: { questions } }) => {
    commit('SET_QUESTIONS', questions);
  }));
export const addQuestion = (({ commit }, data) => {
  commit('ADD_QUESTION', data);
});
export const deleteQuestions = (({ commit }, data) => {
  commit('DELETE_QUESTIONS', data);
});
export const updateQuestion = (({ commit }, data) => {
  commit('UPDATE_QUESTION', data);
});
