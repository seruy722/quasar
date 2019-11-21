export const SET_STOREHOUSE_DATA = ((state, data) => {
    state.storehouseData = data;
});

export const SET_STOREHOUSE_CATEGORIES_DATA = ((state, data) => {
    state.storehouseCategoriesData = data;
});

export const ADD_TO_STOREHOUSE_DATA = ((state, entry) => {
    state.storehouseData.push(entry);
});
