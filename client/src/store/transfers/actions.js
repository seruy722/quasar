import { getUrl } from 'src/tools/url';
import { axiosInstance } from 'boot/axios';
import {
    setFormatedDate,
    setMethodLabel,
    setStatusLabel,
} from 'src/utils/FrequentlyCalledFunctions';

export const fetchTransfers = (({ commit }) => axiosInstance.get(getUrl('transfers'))
  .then(({ data: { transfers: { data } } }) => {
      commit('SET_TRANSFERS', data);
  })
  .catch(() => {
      devlog.warn('Ошибка при запросе fetchTransfers');
  }));

export const setTransfers = (({ commit }, data) => {
    commit('SET_TRANSFERS', data);
});

export const addTransfer = (({ commit }, data) => {
    devlog.log('addTransfer', data);
    commit('ADD_TRANSFER', data);
});

export const updateTransfer = (({ commit }, data) => {
    commit('UPDATE_TRANSFER', data);
});

// CLIENTS
export const fetchTransfersClient = (({ commit }) => axiosInstance.get(getUrl('transfersClient'))
  .then(({ data: { transfers } }) => {
      commit('SET_TRANSFERS_CLIENT', setMethodLabel(setStatusLabel(setFormatedDate(transfers, ['created_at', 'issued_by']))));
  })
  .catch(() => {
      devlog.warn('Ошибка при запросе fetchTransfersClient');
  }));

export const addTransferClient = (({ commit }, data) => {
    commit('ADD_TRANSFER_CLIENT', data);
});

export const updateTransferClient = (({ commit }, data) => {
    commit('UPDATE_TRANSFER_CLIENT', data);
});
