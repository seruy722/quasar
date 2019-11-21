export const SET_CATEGORIES = ((state, data) => {
  state.categories = data;
});
export const ADD_CATEGORY = ((state, category) => {
  state.categories.push(category);
});
