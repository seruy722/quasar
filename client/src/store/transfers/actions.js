export const setTransfers = (({ commit }, data) => {
  // const size = 10;
  // if (!_.isEmpty(data)) {
  //   _.forEach(_.chunk(data, _.size(data) / size), (item, index) => {
  //     setTimeout(() => {
  //       devlog.log('INDEX', index);
  //       commit('SET_TRANSFERS', item);
  //     }, 3000);
  //   });
  // }
  commit('SET_TRANSFERS', data);
});

export const addTransfer = (({ commit }, data) => {
  commit('ADD_TRANSFER', data);
});

export const updateTransfer = (({ commit }, data) => {
  commit('UPDATE_TRANSFER', data);
});
