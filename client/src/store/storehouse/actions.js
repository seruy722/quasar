export const setStorehouseData = (({ commit }, data) => {
    commit('SET_STOREHOUSE_DATA', data);
});

export const setStorehouseCategoriesData = (({ commit }, data) => {
    commit('SET_STOREHOUSE_CATEGORIES_DATA', data);
});

export const addToStorehouseData = (({ commit }, entry) => {
    commit('ADD_TO_STOREHOUSE_DATA', entry);
});
