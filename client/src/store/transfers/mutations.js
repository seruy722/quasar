export const SET_TRANSFERS = ((state, data) => {
  state.transfers = data;
});

export const SET_TRANSFERS_CLIENT = ((state, data) => {
  state.transfersClient = data;
});

export const ADD_TRANSFER = ((state, [elem]) => {
  state.transfers.unshift(elem);
});

export const ADD_TRANSFER_CLIENT = ((state, [elem]) => {
  state.transfersClient.unshift(elem);
});
export const UPDATE_TRANSFER = ((state, [elem]) => {
  const index = _.findIndex(state.transfers, { id: elem.id });
  state.transfers.splice(index, 1, elem);
});

export const UPDATE_TRANSFER_CLIENT = ((state, [elem]) => {
  const index = _.findIndex(state.transfersClient, { id: elem.id });
  state.transfersClient.splice(index, 1, elem);
});
