const checkValue = (array) => _.isArray(array);

export const sortArray = ((array) => {
  if (checkValue(array)) {
    return array.sort((a, b) => a.localeCompare(b, undefined, {
      numeric: true,
      sensitivity: 'base',
    }));
  }
  devlog.warn('Переменная ', array, ' не массив!');
  return [];
});

export const sortArrayCollection = ((array, field) => {
  if (checkValue(array)) {
    return array.sort((a, b) => a[field].localeCompare(b[field], undefined, {
      numeric: true,
      sensitivity: 'base',
    }));
  }
  devlog.warn('Переменная ', array, ' не массив!');
  return [];
});

export const sortCollection = (collection, field, order = 'asc') => _.orderBy(collection, field, [order]);
