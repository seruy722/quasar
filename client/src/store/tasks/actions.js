import { getUrl } from 'src/tools/url';
import { axiosInstance } from 'boot/axios';

export const fetchTasks = (({ commit }) => axiosInstance.get(getUrl('getTasks'))
  .then(({ data: { tasks } }) => {
    commit('SET_TASKS', tasks, ['created_at']);
  }));

export const addOrUpdateTask = (({ commit }, data) => {
  commit('ADD_OR_UPDATE_TASK', data);
});

export const deleteTasks = (({ commit }, data) => {
  commit('DELETE_TASKS', data);
});
