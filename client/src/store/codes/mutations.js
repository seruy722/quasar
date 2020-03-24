export const SET_CODES = ((state, data) => {
  state.codes = data;
});

export const SET_CODES_WITH_CUSTOMERS = ((state, data) => {
  devlog.log('SDF', data);
  state.codesWithCustomers = data;
});

export const ADD_CODE = ((state, data) => {
  state.codes.push(data.code);
  state.codesWithCustomers.push(data.codeWithCustomers);
});

export const ADD_CODE_TO_CODE_WITH_CUSTOMERS = ((state, data) => {
  const find = _.find(state.codesWithCustomers, { id: data.code_id });
  devlog.log('FIND_D', find);
  devlog.log('FIND_data', data);
  if (find) {
    find.customers.push(data);
  }
});

export const DELETE_CUSTOMER = ((state, data) => {
  const find = _.find(state.codesWithCustomers, { id: data.code_id });
  devlog.log('FIND', find);
  if (find && !_.isEmpty(find.customers)) {
    const index = _.findIndex(find.customers, { id: data.id });
    devlog.log('INDEX', index);
    if (index !== -1) {
      find.customers.splice(index, 1);
    }
  }
});
