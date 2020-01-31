import { countSumCollection } from 'src/utils/index';
import { fullDate } from 'src/utils/formatDate';
import getFromSettings from 'src/tools/settings';
// ДОП ФУНКЦИИ
/**
 * Возвращает итоговый обьект для категорий
 * @param arr
 * @return {{name: string, place: *, kg: *}}
 */
const sumObjectForCategories = (arr) => ({
  name: '',
  kg: countSumCollection(arr, 'kg'),
  place: countSumCollection(arr, 'place'),
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
export const setCategoriesStoreHouseData = (data) => {
  const uniq = _.uniqBy(_.map(data, ({ category_name: categoryName, category_id: categoryID }) => ({
    name: categoryName,
    id: categoryID,
  })), 'name');

  const arr = [];

  _.forEach(uniq, ({ name, id }) => {
    arr.push({
      name,
      place: countSumCollection(_.filter(data, { category_id: id }), ({ place }) => place),
      kg: countSumCollection(_.filter(data, { category_id: id }), ({ kg: kilo }) => kilo),
    });
  });

  // _.orderBy(arr, 'kg', 'desc');
  arr.push(sumObjectForCategories(arr));

  return arr;
};
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
 * @return {*}
 */
export const setFormatedDate = (data) => {
  if (_.isArray(data)) {
    _.forEach(data, (item) => {
      item.created_at = fullDate(item.created_at);
      if (_.has(item, 'issued_by')) {
        _.set(item, 'issued_by', fullDate(_.get(item, 'issued_by')));
      }
    });
  } else if (_.isObject(data)) {
    data.created_at = fullDate(data.created_at);
    if (_.has(data, 'issued_by')) {
      _.set(data, 'issued_by', fullDate(_.get(data, 'issued_by')));
    }
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
