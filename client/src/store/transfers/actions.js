export const setTransfers = (({ commit }, data) => {
  commit('SET_TRANSFERS', data);
});

export const addTransfer = (({ commit }, data) => {
  commit('ADD_TRANSFER', data);
});

export const updateTransfer = (({ commit }, data) => {
  commit('UPDATE_TRANSFER', data);
});
