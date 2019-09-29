export const SET_FAXES = ((state, data) => {
    state.faxes = data;
});

export const ADD_FAX = ((state, data) => {
    state.faxes.unshift(data);
});

export const DELETE_FAXES = ((state, data) => {
    _.forEach(data, (id) => {
        const index = _.findIndex(state.faxes, { id });
        state.faxes.splice(index, 1);
    });
});

export const SET_CURRENT_FAX_ITEM = ((state, data) => {
    state.currentFaxItem = data;
});

export const SET_FAX_DATA = ((state, data) => {
    state.faxData = data;
});

export const SET_FAX_CATEGORIES_DATA = ((state, data) => {
    state.faxCategoriesData = data;
});
