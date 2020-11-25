import { getUrl } from 'src/tools/url';
import { axiosInstance } from 'boot/axios';

export const fetchTasks = (({ commit }) => {
  const res = axiosInstance.get(getUrl('getTasks'));
  res.then(({ data: { tasks } }) => {
    commit('SET_TASKS', tasks);
  });
  return res;
});

export const addOrUpdateTask = (({ commit }, data) => {
  commit('ADD_OR_UPDATE_TASK', data);
});

export const deleteTasks = (({ commit }, data) => {
  commit('DELETE_TASKS', data);
});

export const fetchTaskComments = (({ commit }, id) => {
  const res = axiosInstance.get(`${getUrl('getTaskComments')}/${id}`);
  res.then(({ data: { comments } }) => {
    commit('SET_TASK_COMMENTS', comments);
  });
  return res;
});

export const addTaskComment = (({ commit }, comment) => {
  commit('ADD_TASK_COMMENT', comment);
});

export const updateTaskComment = (({ commit }, data) => {
  commit('UPDATE_TASK_COMMENT', data);
});

export const deleteTaskComments = (({ commit }, data) => {
  commit('DELETE_TASK_COMMENTS', data);
});
