export const SET_CARGO = (state, data) => {
  state.cargo = data;
};
export const SET_CARGO_FO_SEARCH = (state, data) => {
  state.cargoForSearch = data;
};
export const SET_DEBTS_FO_SEARCH = (state, data) => {
  state.debtsForSearch = data;
};
export const UPDATE_CARGO_ENTRY = (state, data) => {
  const index = _.findIndex(state.cargo, { id: data.id });
  if (index !== -1 && state.currentCodeClientId === data.code_client_id) {
    state.cargo.splice(index, 1, data);
  } else if (state.currentCodeClientId !== data.code_client_id) {
    state.cargo.splice(index, 1);
  }
};
export const ADD_CARGO_ENTRY = (state, data) => {
  if (state.currentCodeClientId === data.code_client_id) {
    state.cargo.push(data);
  }
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

export const SET_DEBTS = (state, data) => {
  state.debts = data;
};

export const ADD_DEBT_ENTRY = (state, data) => {
  if (state.currentCodeClientId === data.code_client_id) {
    state.debts.push(data);
  }
};

export const UPDATE_DEBT_ENTRY = (state, data) => {
  const index = _.findIndex(state.debts, { id: data.id });
  if (index !== -1 && state.currentCodeClientId === data.code_client_id) {
    state.debts.splice(index, 1, data);
  } else if (state.currentCodeClientId !== data.code_client_id) {
    state.debts.splice(index, 1);
  }
};

export const DELETE_DEBT_ENTRY = ((state, data) => {
  _.forEach(data, (id) => {
    const index = _.findIndex(state.debts, { id });
    if (index !== -1) {
      state.debts.splice(index, 1);
    }
  });
});

export const SET_GENERAL_DATA = ((state, data) => {
  state.generalData = data;
});
export const UPDATE_OR_ADD_DEBT_ENTRY = ((state, data) => {
  const index = _.findIndex(state.debts, { id: data.id });
  if (index !== -1) {
    state.debts.splice(index, 1, data);
  } else {
    state.debts.push(data);
  }
});
