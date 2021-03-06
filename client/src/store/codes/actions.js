import { getUrl } from 'src/tools/url';
import { axiosInstance } from 'boot/axios';
// import { sortArrayCollection } from 'src/utils/sort';
import { setFormatedDate } from 'src/utils/FrequentlyCalledFunctions';

export const setCodes = (({ commit }) => axiosInstance.get(getUrl('codeList'))
  .then(({ data: { codeList } }) => {
    commit('SET_CODES', codeList);
  })
  .catch((e) => {
    devlog.error('Ошибка запроса - setCodes', e);
  }));

export const addCode = (({ commit }, data) => {
  commit('ADD_CODE', data);
});

export const deleteCustomer = (({ commit }, data) => {
  commit('DELETE_CUSTOMER', data);
});

export const setCodesWithCustomers = (({ commit }) => axiosInstance.get(getUrl('codes'))
  .then(({ data: { data: codesData } }) => {
    const sortCodesData = codesData.sort((a, b) => parseInt(a.code, 10) - parseInt(b.code, 10));
    _.forEach(sortCodesData, (item) => {
      if (!_.isEmpty(item.customers)) {
        item.city = _.join(_.map(item.customers, 'city_name'), '; ');
      }
      item.user_name = _.get(item, 'user.name');
    });
    commit('SET_CODES_WITH_CUSTOMERS', setFormatedDate(sortCodesData, ['created_at', 'updated_at']));
  })
  .catch((e) => {
    devlog.error('Ошибка запроса - setCodesWithCustomers', e);
  }));

export const addCustomerToCodeWithCustomers = (({ commit }, data) => {
  commit('ADD_CODE_TO_CODE_WITH_CUSTOMERS', data);
});
