// import JobQueue from 'src/utils/JobQueue';

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

export const updateTransfer = (({ commit }, data) => {
  commit('UPDATE_TRANSFER', data);
});
