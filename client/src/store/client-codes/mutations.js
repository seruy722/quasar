export const SET_CODES = ((state, data) => {
  state.codes = data;
});

export const ADD_CODE = ((state, data) => {
  state.codes.push(data);
});
