import getFromSettings from 'src/tools/settings';

export const countSumCollection = ((arr, key) => _.sumBy(arr, key));
export const round = ((num, precision = 1) => _.round(num, precision));
export const numberFormat = ((number) => new Intl.NumberFormat('ru-RU').format(_.toNumber(number)));
export const statusColor2 = ((value) => {
    const findLabel = _.find(getFromSettings('transferStatus'), { value }) || _.find(getFromSettings('transferStatus'), { label: value });
    return _.get(findLabel, 'color');
});
export const numberUnformat = ((number) => _.chain(number)
  .toString()
  .split(' ')
  .join('')
  .toNumber());

export const jsonDecodeThings = ((val) => {
    try {
        return _.reduce(JSON.parse(val), (result, {
            label,
            value,
        }) => {
            result[label] = value;
            return result;
        }, {});
    } catch (e) {
        devlog.error(`Неправильный формат JSON строки (функции jsonDecodeThings) - ${val}`);
    }
    return val;
});

export const callFunction = ((func, args) => {
    if (_.isFunction(func)) {
        func(args);
    }
});

export const createSpecialLink = ((href, special) => {
    const link = document.createElement('a');
    link.href = `${special}:${href}`;
    return link;
});

export const thingsFilter = ((val) => {
    if (val) {
        try {
            return _.reduce(JSON.parse(val), (str, {
                title,
                count,
            }) => {
                str += `${title}: ${count}; `;
                return str;
            }, '');
        } catch (e) {
            return val;
        }
    }
    return val;
});

export const phoneNumberFilter = ((val) => {
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

export const statusFilter = ((value) => {
    const options = getFromSettings('transportStatusOptions');
    return _.get(_.find(options, { value }), 'label');
});

export const optionsFilter = ((id, categories) => {
    const find = _.find(categories, { value: id });
    if (find) {
        return find.label;
    }
    return id;
});

export const statusColor = (value) => {
    devlog.log('VAAA', value);
    const findLabel = _.find(getFromSettings('transferStatus'), { value }) || _.find(getFromSettings('transferStatus'), { label: value });
    return _.get(findLabel, 'color');
};
