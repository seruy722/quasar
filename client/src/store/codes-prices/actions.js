export const setCodesPrices = (({ commit }, data) => {
  commit('SET_CODES_PRICES', data);
});

export const addNewCodePrice = (({ commit }, newData) => {
  commit('ADD_NEW_CODE_PRICE', newData);
});

export const deleteCodePrice = (({ commit }, data) => {
  commit('DELETE_CODE_PRICE', data);
});

export const updateCodePrice = (({ commit }, data) => {
  commit('UPDATE_CODE_PRICE', data);
});
