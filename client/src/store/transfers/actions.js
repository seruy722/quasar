// import JobQueue from 'src/utils/JobQueue';
import { getUrl } from 'src/tools/url';
import { axiosInstance } from 'boot/axios';
import { setFormatedDate, setMethodLabel, setStatusLabel } from 'src/utils/FrequentlyCalledFunctions';

export const fetchTransfers = (({ commit }) => axiosInstance.get(getUrl('transfers'))
  .then(({ data: { transfers } }) => {
    commit('SET_TRANSFERS', setMethodLabel(setStatusLabel(setFormatedDate(transfers, ['created_at', 'issued_by']))));
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

export const setTransfers = (({ commit }, data) => {
  commit('SET_TRANSFERS', data);
  // let size = 5;
  // if (_.size(data) >= 60) {
  //   size = 30;
  // }
  // const queue = new JobQueue();
  // // devlog.log('queue', queue);
  // _.forEach(_.chunk(data, size), (chunk) => {
  //   queue.addJob((done) => {
  //     requestAnimationFrame(() => {
  //       commit('SET_TRANSFERS', chunk);
  //       // devlog.log('RF', chunk);
  //       done();
  //     });
  //   });
  // });
  //
  // queue.start();
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
