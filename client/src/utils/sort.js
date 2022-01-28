const checkValue = (array) => _.isArray(array) && !_.isEmpty(array);
// СОРТИРОВКА МАССИВА ДАННЫХ КАК ЧИСЛА
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
// СОРТИРУЕТ МАССИВ ОБЬЕКТОВ ДАННЫЕ КАК ЧИСЛА
export const sortArrayCollection = ((array, field) => {
  devlog.log('ARRR', array);
  if (checkValue(array)) {
    return array.sort((a, b) => a[field].localeCompare(b[field], undefined, {
      numeric: true,
      sensitivity: 'base',
    }));
  }
  devlog.warn('Переменная ', array, ' не массив!');
  return array;
});
// СОРТИРУЕТ МАССИВ ИЛИ МАССИВ ОБЬЕКТОВ
export const sortCollection = (collection, field, order = 'asc') => _.orderBy(collection, field, [order]);
export const sortSuper = (data, sortBy, descending = false) => {
  data.sort((a, b) => {
    const x = descending ? b : a;
    const y = descending ? a : b;
    if (!_.isNaN(_.toNumber(a[sortBy])) && !_.isNaN(_.toNumber(b[sortBy]))) {
      return parseFloat(x[sortBy]) - parseFloat(y[sortBy]);
    }
    if (sortBy === 'created_at' || sortBy === 'updated_at') {
      return new Date(x[sortBy]) - new Date(y[sortBy]);
    }
    let num = 0;
    if (b[sortBy] > a[sortBy]) {
      num = 1;
    } else if (x[sortBy] < y[sortBy]) {
      num = -1;
    }
    return num;
  });
  return data;
};
