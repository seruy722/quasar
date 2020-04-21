export const SET_TRANSFERS = ((state, data) => {
  state.transfers = data;
});

export const ADD_TRANSFER = ((state, [elem]) => {
  state.transfers.unshift(elem);
});
export const UPDATE_TRANSFER = ((state, [elem]) => {
  const index = _.findIndex(state.transfers, { id: elem.id });
  state.transfers.splice(index, 1, elem);
});
