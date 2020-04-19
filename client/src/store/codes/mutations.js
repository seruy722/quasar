const setCitiesAndPhones = ((obj, array) => {
  obj.cities = _.compact(_.uniq(_.map(array, 'city_name')));
  obj.phones = _.compact(_.uniq(_.map(array, 'phone')));
  return obj;
});
export const SET_CODES = ((state, data) => {
  state.codes = data;
});

export const SET_CODES_WITH_CUSTOMERS = ((state, data) => {
  devlog.log('SDF', data);
  state.codesWithCustomers = data;
});

export const ADD_CODE = ((state, data) => {
  if (data.code) {
    state.codes.push(data.code);
  }

  if (data.codeWithCustomers) {
    state.codesWithCustomers.push(data.codeWithCustomers);
  }
});

export const ADD_CODE_TO_CODE_WITH_CUSTOMERS = ((state, data) => {
  const find = _.find(state.codesWithCustomers, { id: _.toNumber(data.code_id) });
  devlog.log('FIND_D', find);
  devlog.log('FIND_data', data);
  if (find) {
    find.customers.push(data);
    setCitiesAndPhones(find, find.customers);
  }
});

export const UPDATE_CUSTOMER = ((state, data) => {
  const find = _.find(state.codesWithCustomers, { id: _.toNumber(data.code_id) });
  devlog.log('FIND', find);
  if (find && !_.isEmpty(find.customers)) {
    const index = _.findIndex(find.customers, { id: data.id });
    devlog.log('INDEX', index, _.get(find.customers[index], 'code_id'), data.code_id);
    if (index !== -1) {
      find.customers.splice(index, 1, data);
    } else {
      find.customers.push(data);
    }
  } else if (find) {
    devlog.log('INDEX_PUSH', data.code_id);
    find.customers.push(data);
  }
  if (find) {
    setCitiesAndPhones(find, find.customers);
  }
});

export const DELETE_CUSTOMER = ((state, data) => {
  const find = _.find(state.codesWithCustomers, { id: _.toNumber(data.code_id) });
  devlog.log('FIND', find);
  if (find && !_.isEmpty(find.customers)) {
    const index = _.findIndex(find.customers, { id: data.id });
    devlog.log('INDEX', index);
    if (index !== -1) {
      find.customers.splice(index, 1);
      setCitiesAndPhones(find, find.customers);
    }
  }
});
