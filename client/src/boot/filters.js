import validator from 'validator';
import { formatToDotDate } from 'src/utils/formatDate';

export default async ({ Vue }) => {
    Vue.filter('filterFromSelectData', (val, data) => _.get(_.find(data, { value: val }), 'label'));
    Vue.filter('formatToDotDate', (val) => formatToDotDate(val));
    Vue.filter('thingsFilter', (val) => {
        if (val && validator.isJSON(val)) {
            const things = JSON.parse(val);
            return _.reduce(things, (result, item) => {
                result[item.title] = item.count;

                return result;
            }, {});
        }
        return val;
    });
};
