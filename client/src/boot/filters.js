import { formatToDotDate, fullDate } from 'src/utils/formatDate';
import { numberFormat } from 'src/utils/index';

export default async ({ Vue }) => {
  Vue.filter('filterFromSelectData', (val, data) => _.get(_.find(data, { value: val }), 'label'));
  Vue.filter('formatToDotDate', (val) => formatToDotDate(val));
  Vue.filter('formatToDashDate', (val) => fullDate(val));
  Vue.filter('thingsFilter', (val) => {
    if (val) {
      try {
        return _.reduce(JSON.parse(val), (str, { title, count }) => {
          str += `${title}: ${count}; `;
          return str;
        }, '');
      } catch (e) {
        return val;
      }
    }
    return val;
  });
  Vue.filter('numberFormatFilter', (val) => numberFormat(val));
  Vue.filter('phoneNumberFilter', (val) => {
    const maskPhone = ((value) => `+${value.slice(0, 2)} (${value.slice(2, 5)}) ${value.slice(5, 8)}-${value.slice(8, 10)}-${value.slice(10, 12)}`);
    if (_.isString(val)) {
      return maskPhone(val);
    }
    if (_.isArray(val)) {
      if (!_.isEmpty(val)) {
        return _.map(val, (phone) => maskPhone(phone));
      }
      return '';
    }
    return val;
  });
};
