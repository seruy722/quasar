import { getUrl } from 'src/tools/url';
import { axiosInstance } from 'boot/axios';

export const getCargoDebts = (({ commit }, clientId) => axiosInstance.get(`${getUrl('allCargoData')}/${clientId}`)
  .then(async ({ data: { answer } }) => {
    const { combineCargoData, setFormatedDate } = await import('src/utils/FrequentlyCalledFunctions');
    commit('SET_CARGO', _.orderBy(setFormatedDate(combineCargoData(answer), ['created_at'])), 'id', 'desc');
  })
  .catch(() => {
    devlog.warn('Ошибка при запросе getCargoDebts');
  }));

export const updateCargoEntry = (async ({ commit }, data) => {
  const { setFormatedDate } = await import('src/utils/FrequentlyCalledFunctions');
  commit('UPDATE_CARGO_ENTRY', setFormatedDate(data, ['created_at']));
  commit('sort');
});

export const deleteCargoEntry = (({ commit }, data) => {
  commit('DELETE_CARGO_ENTRY', data);
});
