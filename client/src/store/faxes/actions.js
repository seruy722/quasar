export const setFaxes = (({ commit }, data) => {
    commit('SET_FAXES', data);
});

export const addFax = (({ commit }, data) => {
    commit('ADD_FAX', data);
});

export const deleteFaxes = (({ commit }, data) => {
    commit('DELETE_FAXES', data);
});


export const setCurrentFaxItem = (({ commit }, data) => {
    commit('SET_CURRENT_FAX_ITEM', data);
});

export const setFaxData = (({ commit }, data) => {
    commit('SET_FAX_DATA', data);
});

export const setFaxCategoriesData = (({ commit }, data) => {
    commit('SET_FAX_CATEGORIES_DATA', data);
});
