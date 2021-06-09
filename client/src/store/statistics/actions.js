import { getUrl } from 'src/tools/url';
import { axiosInstance } from 'boot/axios';

export const fetchStatistics = (({ commit }) => {
  const res = axiosInstance.get(getUrl('getStatistics'));
  res.then(({ data }) => {
    commit('SET_STATISTICS', data);
  });
  return res;
});

export const addStatistic = (({ commit }, data) => {
  devlog.log('addStatistic', data);
  commit('ADD_STATISTIC', data);
});
