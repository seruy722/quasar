import { countSumCollection } from 'src/utils/index';
import { fullDate } from 'src/utils/formatDate';
import getFromSettings from 'src/tools/settings';
import { uid } from 'quasar';
// ДОП ФУНКЦИИ
/**
 * Возвращает итоговый обьект для категорий
 * @param arr
 * @return {{name: string, place: *, kg: *}}
 */
const sumObjectForCategories = (arr) => ({
  name: '',
  for_kg: null,
  kg: countSumCollection(arr, 'kg'),
  place: countSumCollection(arr, 'place'),
  sum: countSumCollection(arr, 'sum'),
});

// TRANSFERS
/**
 * Устанавливает название метода по его id
 * @param value
 * @return {*}
 */
export const setMethodLabel = (value) => {
  _.forEach(value, (item) => {
    const findLabel = _.find(getFromSettings('transferMethod'), { value: _.toNumber(item.method) });
    if (findLabel) {
      _.set(item, 'method_label', _.get(findLabel, 'label'));
    }
  });
  return value;
};
/**
 * Устанавливает название статуса по его id
 * @param value
 * @return {*}
 */
export const setStatusLabel = (value) => {
  _.forEach(value, (item) => {
    const findLabel = _.find(getFromSettings('transferStatus'), { value: _.toNumber(item.status) });
    if (findLabel) {
      _.set(item, 'status_label', _.get(findLabel, 'label'));
    }
  });
  return value;
};
/**
 * Обнуляет свойство changeValue
 * @param data
 */
export const setChangeValue = (data) => {
  _.forEach(data, (elem) => {
    if (_.get(elem, 'changeValue')) {
      _.set(elem, 'changeValue', false);
    }
  });
};
// STOREHOUSEDATA
export const setCategoriesStoreHouseData = (data, transporterPrices) => {
  const uniq = _.uniqBy(_.map(data, ({ category_name: categoryName, category_id: categoryID, fax_id: faxId }) => ({
    name: categoryName,
    id: categoryID,
    faxId,
  })), 'name');

  const arr = [];

  _.forEach(uniq, ({ name, id, faxId }) => {
    const kg = countSumCollection(_.filter(data, { category_id: id }), ({ kg: kilo }) => kilo);
    let forKg = 0;
    const findForKg = _.find(transporterPrices, { category_id: id });
    if (findForKg) {
      forKg = findForKg.category_price;
    }
    arr.push({
      name,
      place: countSumCollection(_.filter(data, { category_id: id }), ({ place }) => place),
      kg,
      for_kg: forKg,
      sum: kg * forKg,
      category_id: id,
      fax_id: faxId,
      uid: uid(),
    });
  });

  arr.push(sumObjectForCategories(arr));

  return arr;
};

/**
 * Комбинирует одинаковые данные по code_client_id и category_name
 * @type {function(*=): []}
 */
export const combineStoreHouseData = ((data) => {
  const clients = _.uniq(_.map(data, 'code_client_id'));
  const clientsCategories2 = [];
  _.forEach(clients, (item) => {
    clientsCategories2.push(_.chain(data)
      .filter({ code_client_id: item })
      .groupBy('category_name')
      .mapValues((values) => _.chain(values)
        .groupBy('for_kg')
        .mapValues((val) => _.chain(val)
          .groupBy('for_place')
          .value())
        .value())
      .value());
  });
  const result = [];
  _.forEach(clientsCategories2, (elem) => {
    _.forEach(elem, (el) => {
      _.forEach(_.values(el), (arr) => {
        _.forEach((arr), (arr2) => {
          devlog.log('ARR_EKL', arr2);
          result.push(_.assign({}, _.first(arr2), {
            kg: _.sumBy(arr2, 'kg'),
            place: _.sumBy(arr2, 'place'),
            arr: arr2,
          }));
        });
      });
    });
  });
  return result;
});

/**
 * Получение и запись всех категорий во vuex
 * @param store
 * @return {boolean|*}
 */
export const getCategories = (store) => {
  if (_.isEmpty(store.getters['category/getCategories'])) {
    return store.dispatch('category/getCategories');
  }
  return true;
};
/**
 * Получение и запись всех кодов клиентов во vuex
 * @param store
 * @return {boolean|*}
 */
export const getClientCodes = (store) => {
  if (_.isEmpty(store.getters['codes/getCodes'])) {
    return store.dispatch('codes/setCodes');
  }
  return true;
};
/**
 * Получение и запись описи вложения во vuex
 * @param store
 * @return {boolean|*}
 */
export const getThingsList = (store) => {
  if (_.isEmpty(store.getters['thingsList/getThingsList'])) {
    return store.dispatch('thingsList/setThingsList');
  }
  return true;
};
/**
 * Получение и запись списка названий магазинов во vuex
 * @param store
 * @return {boolean|*}
 */
export const getShopsList = (store) => {
  if (_.isEmpty(store.getters['shopsList/getShopsList'])) {
    return store.dispatch('shopsList/setShopsList');
  }
  return true;
};
/**
 * Получение и запись списка городов украины во vuex
 * @param store
 * @return {boolean|*}
 */
export const getCities = (store) => {
  if (_.isEmpty(store.getters['cities/getCities'])) {
    return store.dispatch('cities/setCities');
  }
  return true;
};
/**
 * Получение и запись списка видов транспорта во vuex
 * @param store
 * @return {boolean|*}
 */
export const getTransports = (store) => {
  if (_.isEmpty(store.getters['transport/getTransports'])) {
    return store.dispatch('transport/fetchTransports');
  }
  return true;
};
/**
 * Получение и запись списка перевожчиков во vuex
 * @param store
 * @return {boolean|*}
 */
export const getTransporters = (store) => {
  if (_.isEmpty(store.getters['transporter/getTransporters'])) {
    return store.dispatch('transporter/fetchTransporters');
  }
  return true;
};
/**
 * Получение и запись списка перевожчиков во vuex
 * @param store
 * @return {boolean|*}
 */
export const getStorehouseTableData = (store) => {
  if (_.isEmpty(store.getters['storehouse/getStorehouseData'])) {
    return store.dispatch('storehouse/fetchStorehouseTableData');
  }
  return true;
};
/**
 * Получение и запись списка факсов во vuex
 * @param store
 * @return {boolean|*}
 */
export const getFaxes = (store) => {
  if (_.isEmpty(store.getters['faxes/getFaxes'])) {
    return store.dispatch('faxes/fetchFaxes');
  }
  return true;
};
/**
 * Устанавливает значения по умолчанию
 * @param data
 */
export const setDefaultData = (data) => {
  if (_.isObject(data) || _.isArray(data)) {
    _.forEach(data, (item) => {
      _.set(item, 'value', item.default);
    });
  }
};
/**
 * Форматирует даты из 2020-01-31 16:25:13 в 30-01-2020 16:25:13
 * @param data
 * @param fields
 * @return {*}
 */
export const setFormatedDate = (data, fields = []) => {
  if (_.isArray(data)) {
    _.forEach(data, (item) => {
      _.forEach(fields, (field) => {
        if (item[field]) {
          item[field] = fullDate(item[field]);
        }
      });
    });
  } else if (_.isObject(data)) {
    _.forEach(fields, (field) => {
      if (data[field]) {
        data[field] = fullDate(data[field]);
      }
    });
  }

  return data;
};
/**
 * Подготавливает данные для компонентов истории
 * @param cols
 * @param data
 * @return {{cols: *, historyData: *}}
 */
export const prepareHistoryData = (cols, data) => ({
  cols: _.reduce(cols, (result, { label, name }) => {
    result[name] = label;
    return result;
  }, {}),
  historyData: _.reduce(data, (result, item) => {
    result.push(_.assign(item, JSON.parse(item.history_data)));
    return result;
  }, []),
});
