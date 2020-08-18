export const SET_DOCUMENTS = ((state, data) => {
  state.documents = data;
});

export const ADD_DOCUMENT = ((state, data) => {
  state.documents.push(data);
});
