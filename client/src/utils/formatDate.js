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
    devlog.log('DDF', date);
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
