export const SET_ROLES = ((state, data) => {
  state.roles = data;
});

export const ADD_ROLE = ((state, data) => {
  state.roles.unshift(data);
});

export const DELETE_ROLE = ((state, id) => {
  const index = _.findIndex(state.roles, { id });
  if (index !== -1) {
    state.roles.splice(index, 1);
  }
});
