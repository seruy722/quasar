export const SET_PERMISSIONS = ((state, data) => {
  state.permissions = data;
});

export const ADD_PERMISSION = ((state, data) => {
  state.permissions.unshift(data);
});

export const DELETE_PERMISSION = ((state, id) => {
  const index = _.findIndex(state.permissions, { id });
  if (index !== -1) {
    state.permissions.splice(index, 1);
  }
});
