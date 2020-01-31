import { formatToDotDate } from 'src/utils/formatDate';
import { numberFormat } from 'src/utils/index';

export default async ({ Vue }) => {
  Vue.filter('filterFromSelectData', (val, data) => _.get(_.find(data, { value: val }), 'label'));
  Vue.filter('formatToDotDate', (val) => formatToDotDate(val));
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
    if (_.isString(val)) {
      return `+${val.slice(0, 2)} (${val.slice(2, 5)}) ${val.slice(5, 8)}-${val.slice(8, 10)}-${val.slice(10, 12)}`;
    }
    return val;
  });
};
