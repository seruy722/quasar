export const SET_THINGS_LIST = ((state, data) => {
  state.thingsList = _.compact(data);
});
