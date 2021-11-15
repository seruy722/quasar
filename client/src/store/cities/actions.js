import { getUrl } from 'src/tools/url';
import { axiosInstance } from 'boot/axios';

export const setCities = (({ commit }) => {
  const answer = axiosInstance.get(getUrl('cities'));
  answer.then(({ data }) => {
    commit('SET_CITIES', data);
  })
    .catch(() => {
      devlog.warn('Ошибка при запросе setCities');
    });

  return answer;
});

