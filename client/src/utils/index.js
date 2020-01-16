export const countSumCollection = ((arr, key) => _.sumBy(arr, key));
export const round = ((num, precision = 1) => _.round(num, precision));
export const numberFormat = ((number) => new Intl.NumberFormat('ru-RU').format(_.toNumber(number)));
export const numberUnformat = ((number) => _.chain(number)
  .toString()
  .split(' ')
  .join('')
  .toNumber());

export const jsonDecodeThings = ((val) => {
  try {
    return _.reduce(JSON.parse(val), (result, { label, value }) => {
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
