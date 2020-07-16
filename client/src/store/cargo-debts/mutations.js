export const SET_CARGO = (state, data) => {
  state.cargo = data;
};
export const UPDATE_CARGO_ENTRY = (state, data) => {
  const index = _.findIndex(state.cargo, { id: data.id });
  if (index !== -1) {
    state.cargo.splice(index, 1, data);
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

export const sort = (async (state) => {
  const { toDate } = await import('src/utils/formatDate');
  state.cargo.sort((a, b) => new Date(toDate(b.created_at)) - new Date(toDate(a.created_at)));
});
