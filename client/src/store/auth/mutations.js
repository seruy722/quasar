export const SET_USER = ((state, data) => {
  state.user = data;
});

export const SET_TO_PATH = ((state, value) => {
  state.toPath = value;
});
export const LOGOUT = ((state) => {
  state.user = null;
  state.toPath = null;
});
