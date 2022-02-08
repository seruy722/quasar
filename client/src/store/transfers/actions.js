// import JobQueue from 'src/utils/JobQueue';
import { getUrl } from 'src/tools/url';
import { axiosInstance } from 'boot/axios';
import {
    setFormatedDate,
    setMethodLabel,
    setStatusLabel,
} from 'src/utils/FrequentlyCalledFunctions';

export const fetchTransfers = (({ commit }) => axiosInstance.get(getUrl('transfers'))
  .then(({ data: { transfers } }) => {
      commit('SET_TRANSFERS', transfers.data);
      delete transfers.data;
      commit('SET_TRANSFERS_DATA', transfers);
  })
  .catch(() => {
      devlog.warn('Ошибка при запросе fetchTransfers');
  }));

export const fetchTransfersClient = (({ commit }) => axiosInstance.get(getUrl('transfersClient'))
  .then(({ data: { transfers } }) => {
      commit('SET_TRANSFERS_CLIENT', setMethodLabel(setStatusLabel(setFormatedDate(transfers, ['created_at', 'issued_by']))));
  })
  .catch(() => {
      devlog.warn('Ошибка при запросе fetchTransfers');
  }));

export const fetchNewAndChangedTransfers = (({
                                                 commit,
                                                 state,
                                             }) => {
    const id = _.get(_.first(state.transfers), 'id');
    const updatedAt = _.max(_.map(state.transfers, 'updated_at'));
    return axiosInstance.post(getUrl('getNewAndChangedTransfers'), {
        lastCreatedId: id,
        updatedAt,
    })
      .then(({ data: { transfers } }) => {
          commit('UPDATE_TRANSFERS', transfers);
      })
      .catch(() => {
          devlog.warn('Ошибка при запросе fetchTransfers');
      });
});

export const setTransfers = (({ commit }, data) => {
    commit('SET_TRANSFERS', data);
});

export const setSearchData = (({ commit }, data) => {
    devlog.log('setSearchData', data);
    commit('SET_SEARCH_DATA', data);
});

export const addTransfer = (({ commit }, data) => {
    commit('ADD_TRANSFER', data);
});

export const addTransferClient = (({ commit }, data) => {
    commit('ADD_TRANSFER_CLIENT', data);
});

export const updateTransfer = (({ commit }, data) => {
    commit('UPDATE_TRANSFER', data);
});

export const updateTransferClient = (({ commit }, data) => {
    commit('UPDATE_TRANSFER_CLIENT', data);
});
