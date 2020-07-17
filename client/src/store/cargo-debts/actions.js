import { getUrl } from 'src/tools/url';
import { axiosInstance } from 'boot/axios';

export const getCargoDebts = (({ commit }, clientId) => axiosInstance.get(`${getUrl('allCargoData')}/${clientId}`)
  .then(async ({ data: { answer } }) => {
    const { setFormatedDate } = await import('src/utils/FrequentlyCalledFunctions');
    commit('SET_CARGO', setFormatedDate(answer, ['created_at']));
  })
  .catch(() => {
    devlog.warn('Ошибка при запросе getCargoDebts');
  }));

export const updateCargoEntry = (async ({ commit }, data) => {
  const { setFormatedDate } = await import('src/utils/FrequentlyCalledFunctions');
  commit('UPDATE_CARGO_ENTRY', setFormatedDate(data, ['created_at']));
});

export const addCargoEntry = (async ({ commit }, data) => {
  const { setFormatedDate } = await import('src/utils/FrequentlyCalledFunctions');
  commit('ADD_CARGO_ENTRY', setFormatedDate(data, ['created_at']));
});

export const deleteCargoEntry = (({ commit }, data) => {
  commit('DELETE_CARGO_ENTRY', data);
});

export const setCurrentCodeClientId = (({ commit }, id) => {
  commit('SET_CURRENT_CODE_CLIENT_ID', id);
});
