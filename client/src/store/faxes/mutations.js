export const SET_FAXES = ((state, data) => {
  state.faxes = data;
});

export const ADD_FAX = ((state, data) => {
  if (_.isEmpty(state.faxes)) {
    state.faxes.push(data);
  } else {
    state.faxes.unshift(data);
  }
});

export const UPDATE_FAX = ((state, data) => {
  const index = _.findIndex(state.faxes, { id: data.id });
  if (index !== -1) {
    state.faxes.splice(index, 1, data);
  }
});

export const UPDATE_FAX_DATA = ((state, data) => {
  const index = _.findIndex(state.faxData, { id: data.id });
  if (index !== -1) {
    state.faxData.splice(index, 1, data);
  }
});

export const DELETE_FAXES = ((state, data) => {
  _.forEach(data, (id) => {
    const index = _.findIndex(state.faxes, { id });
    if (index !== -1) {
      state.faxes.splice(index, 1);
    }
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
