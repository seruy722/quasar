import { getUrl } from 'src/tools/url';
import { axiosInstance } from 'boot/axios';
// import { sortArrayCollection } from 'src/utils/sort';
import { setFormatedDate } from 'src/utils/FrequentlyCalledFunctions';

export const setCodes = (({ commit }) => axiosInstance.get(getUrl('codeList'))
  .then(({ data: { codeList } }) => {
    const collator = new Intl.Collator(undefined, {
      numeric: true,
      sensitivity: 'base',
    });
    codeList.sort((a, b) => collator.compare(a.label, b.label));
    commit('SET_CODES', codeList);
  })
  .catch((e) => {
    devlog.error('Ошибка запроса - setCodes', e);
  }));

export const addCode = (({ commit }, data) => {
  commit('ADD_CODE', data);
});

export const deleteCustomer = (({ commit }, data) => axiosInstance.get(`${getUrl('destroyCustomerEntry')}/${data.id}`)
  .then(() => {
    commit('DELETE_CUSTOMER', data);
  })
  .catch((e) => {
    devlog.error('Ошибка запроса - setCodes', e);
  }));
export const deleteCustomerFromStore = (({ commit }, data) => {
  commit('DELETE_CUSTOMER', data);
});

export const updateCustomer = (({ commit }, data) => {
  commit('UPDATE_CUSTOMER', data);
});

export const setCodesWithCustomers = (({ commit }) => axiosInstance.get(getUrl('codes'))
  .then(({ data: { codesData } }) => {
    // const sortCodesData = codesData.sort((a, b) => parseInt(a.code, 10) - parseInt(b.code, 10));
    // _.forEach(sortCodesData, (item) => {
    //   if (!_.isEmpty(item.customers)) {
    //     item.city = _.join(_.map(item.customers, 'city_name'), '; ');
    //   }
    //   item.user_name = _.get(item, 'user.name');
    // });
    commit('SET_CODES_WITH_CUSTOMERS', setFormatedDate(codesData, ['created_at']));
  })
  .catch((e) => {
    devlog.error('Ошибка запроса - setCodesWithCustomers', e);
  }));

export const addCustomerToCodeWithCustomers = (({ commit }, data) => {
  commit('ADD_CODE_TO_CODE_WITH_CUSTOMERS', data);
});

export const setCodesAssistant = (({ commit }, data) => {
  commit('SET_CODES_ASSISTANT', data);
});
