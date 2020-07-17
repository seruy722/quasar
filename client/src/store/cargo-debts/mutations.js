export const SET_CARGO = (state, data) => {
  state.cargo = data;
};
export const UPDATE_CARGO_ENTRY = (state, data) => {
  const index = _.findIndex(state.cargo, { id: data.id });
  if (index !== -1) {
    state.cargo.splice(index, 1, data);
  }
};
export const ADD_CARGO_ENTRY = (state, data) => {
  state.cargo.push(data);
};

export const DELETE_CARGO_ENTRY = ((state, data) => {
  _.forEach(data, (id) => {
    const index = _.findIndex(state.cargo, { id });
    if (index !== -1) {
      state.cargo.splice(index, 1);
    }
  });
});

export const SET_CURRENT_CODE_CLIENT_ID = ((state, id) => {
  state.currentCodeClientId = id;
});
