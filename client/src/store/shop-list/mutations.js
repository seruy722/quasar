export const SET_SHOPS_LIST = ((state, data) => {
  state.shopsList = _.compact(data);
});
