export const SET_CODES_PRICES = ((state, data) => {
  state.codesPrices = data;
});

export const ADD_NEW_CODE_PRICE = ((state, data) => {
  const arr = state.codesPrices[data.code_client_name];
  if (arr) {
    const index = _.findIndex(arr, {
      code_id: data.code_id,
      category_id: data.category_id,
    });
    if (index === -1) {
      arr.push(data);
    } else {
      arr.splice(index, 1, data);
    }
  } else {
    state.codesPrices[data.code_client_name] = [data];
  }
});

export const DELETE_CODE_PRICE = ((state, data) => {
  const arr = state.codesPrices[data.code_client_name];
  const index = _.findIndex(arr, {
    code_id: data.code_id,
    category_id: data.category_id,
  });
  if (index !== -1) {
    arr.splice(index, 1);
  }
});

export const UPDATE_CODE_PRICE = ((state, data) => {
  const arr = state.codesPrices[data.code_client_name];
  const index = _.findIndex(arr, { id: data.id });
  if (index !== -1) {
    arr.splice(index, 1, data);
  }
});
