export const SET_TASKS = ((state, data) => {
  state.tasks = data;
});

export const ADD_OR_UPDATE_TASK = ((state, data) => {
  const index = _.findIndex(state.tasks, { id: data.id });
  devlog.log('index', index);
  if (index === -1) {
    state.tasks.push(data);
  } else {
    state.tasks.splice(index, 1, data);
  }
});

export const DELETE_TASKS = ((state, data) => {
  _.forEach(data, (id) => {
    const index = _.findIndex(state.tasks, { id });
    if (index !== -1) {
      state.tasks.splice(index, 1);
    }
  });
});
