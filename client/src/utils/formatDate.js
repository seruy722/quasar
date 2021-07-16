import {
  formatISO,
  isDate,
  format,
  set,
  getMinutes,
  getHours,
  getSeconds,
} from 'date-fns';

/**
 * Проверяет дату на валидность
 * @param input
 * @returns boolean
 */
export const checkDate = ((input) => _.isString(input) && !!new Date(input).getTime());

export const today = () => new Date()
  .toLocaleDateString()
  .split('.')
  .reverse()
  .join('/');

/**
 * Преобразует дату к формату: 1 января 2018
 * @param date
 * @returns string
 */
export const formatToDayMonthYear = ((date) => {
  if (checkDate(date)) {
    return new Intl.DateTimeFormat('ru',
      {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
      }).format(new Date(date))
      .replace(/ г./u, '');
  }
  return date;
});

/**
 * Преобразует дату к формату: 25.05
 * @param date
 * @returns string
 */
export const formatToDayMonth = ((date) => {
  if (checkDate(date)) {
    return new Intl.DateTimeFormat('ru',
      {
        day: '2-digit',
        month: '2-digit',
      }).format(new Date(date));
  }
  return date;
});

/**
 * Преобразует дату к формату: 25 января
 * @param date
 * @returns string
 */
export const formatToDayMonthLong = ((date) => {
  if (checkDate(date)) {
    return new Intl.DateTimeFormat('ru',
      {
        day: 'numeric',
        month: 'long',
      }).format(new Date(date));
  }
  return date;
});

/**
 * Преобразует дату к формату: 19:00
 * @param date
 * @returns string
 */
export const formatToHoursMinutes = ((date) => {
  if (checkDate(date)) {
    return new Intl.DateTimeFormat('ru',
      {
        hour: '2-digit',
        minute: '2-digit',
      }).format(new Date(date));
  }
  return date;
});

/**
 * Преобразует дату к формату: 09.08.2019
 * @param date
 * @returns string
 */
export const formatToDotDate = ((date) => {
  if (checkDate(date)) {
    return new Intl.DateTimeFormat('ru',
      {
        day: 'numeric',
        month: 'numeric',
        year: 'numeric',
      }).format(new Date(date));
  }
  return date;
});

/**
 * Преобразует дату в количество дней, часов, минут, секунд
 * @param date (разница во времени)
 * @type Object || Boolean
 */
export const formatTimer = ((date) => {
  date = _.isFinite(date) ? date : Date.parse(date);
  let result = false;
  if (!_.isNaN(date)) {
    const seconds = Math.floor((date / 1000) % 60);
    const minutes = Math.floor((date / 1000 / 60) % 60);
    const hours = Math.floor((date / (1000 * 60 * 60)) % 24);
    const days = Math.floor(date / (1000 * 60 * 60 * 24));
    result = {
      days,
      hours,
      minutes,
      seconds,
    };
  }
  return result;
});

/**
 * Преобразует дату к формату: 2019/08
 * @param date
 * @returns string
 */
export const formatToYear = ((date) => {
  if (checkDate(date)) {
    return new Intl.DateTimeFormat('ru',
      {
        year: 'numeric',
      }).format(new Date(date));
  }
  return date;
});
/**
 * Преобразовывает дату формата 2020-02-01T14:35:46+00:00 в 01-02-2020 14:35:46
 * @type {Function}
 */
export const fullDate = ((date) => {
  if (isDate(new Date(date)) && date) {
    return format(new Date(date), 'dd-MM-yyyy HH:mm:ss');
  }
  return date;
});

/**
 * Преобразовывает дату формата 31-01-2020 16:35:21 или Fri Jan 31 2020 13:43:56 GMT+0200 (Восточная Европа, стандартное время) в 2020-01-31 16:35:46
 * @param str
 * @return {string|*}
 */
export const toDate = (str) => {
  // devlog.log('STR_DATE', str);
  if (str && _.size(str) === 19) {
    const [date, time] = _.split(str, ' ');
    const parseDate = _.join(_.reverse(_.split(date, '-')), '-');
    // devlog.log('time', time);
    return `${parseDate} ${time}`;
  }
  if (_.trim(str) && isDate(new Date(str))) {
    toDate(fullDate(str));
  }

  return str;
};

/**
 * Преобразовывает дату формата 31-01-2020 16:35:21 в 2020-01-31T16:35:46+02:00
 * @type {Function}
 */
export const isoDate = ((str) => {
  const date = toDate(str);
  if (isDate(new Date(date)) && date) {
    devlog.log('F_I_D', formatISO(new Date(date)));
    return formatISO(new Date(date));
  }
  return null;
});

/**
 * Добавляет текущее время к строке 2020.02.02
 * @type {Function}
 */
export const addTime = ((date) => {
  if (date) {
    const dateNow = new Date();
    return set(new Date(date), {
      hours: getHours(dateNow),
      minutes: getMinutes(dateNow),
      seconds: getSeconds(dateNow),
    });
  }
  return date;
});

/**
 * Разворачивает дату формата 02-02-2020 в 2020-02-02
 * @type {Function}
 */
export const reverseDate = ((date) => {
  if (date) {
    return date.split('-')
      .reverse()
      .join('-');
  }
  return date;
});

/**
 * Принимает дату вызова new Date();
 * @param date // Fri Jan 31 2020 13:43:56 GMT+0200 (Восточная Европа, стандартное время)
 * @return {string|*} // 2020-01-31 13:43:56
 */
export const formatToMysql = (date) => {
  if (isDate(date) && date) {
    return format(new Date(date), 'yyyy-MM-dd HH:mm:ss');
  }
  return date;
};

/**
 * Преобразует дату к формату: 2019/08/21
 * @param date
 * @returns string
 */
export const formatToPickerDate = ((date) => {
  if (_.isString(date) && date) {
    return date.split('.')
      .reverse()
      .join('/');
  }
  return date;
});
/**
 * Преобразует дату в число без учета времени
 * @type {function(*=): number}
 */
export const getDateWithoutTime = ((date) => Math.floor(new Date(date).getTime() / 86400000));

export const getTimeZone = (() => {
  const timeZoneOffset = new Date().getTimezoneOffset();
  return timeZoneOffset < 0 ? `+0${(timeZoneOffset / 60) * -1}:00` : `-0${(timeZoneOffset / 60)}:00`;
});
