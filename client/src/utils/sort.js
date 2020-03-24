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
