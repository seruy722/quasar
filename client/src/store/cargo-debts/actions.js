import { getUrl } from 'src/tools/url';
import { axiosInstance } from 'boot/axios';

export const getCargoDebts = (({ commit }, clientId) => axiosInstance.get(`${getUrl('allCargoData')}/${clientId}`)
  .then(({ data: { cargo, debts } }) => {
    commit('SET_CARGO', cargo);
    commit('SET_DEBTS', debts);
  })
  .catch(() => {
    devlog.warn('Ошибка при запросе getCargoDebts');
  }));

export const updateCargoEntry = (({ commit }, data) => {
  commit('UPDATE_CARGO_ENTRY', data);
});

export const addCargoEntry = (({ commit }, data) => {
  commit('ADD_CARGO_ENTRY', data);
});

export const deleteCargoEntry = (({ commit }, data) => {
  commit('DELETE_CARGO_ENTRY', data);
});

export const setCurrentCodeClientId = (({ commit }, id) => {
  commit('SET_CURRENT_CODE_CLIENT_ID', id);
});

export const addDebtEntry = (({ commit }, data) => {
  commit('ADD_DEBT_ENTRY', data);
});
export const updateDebtEntry = (({ commit }, data) => {
  commit('UPDATE_DEBT_ENTRY', data);
});

export const deleteDebtEntry = (({ commit }, data) => {
  commit('DELETE_DEBT_ENTRY', data);
});
