import { i18n } from 'src/boot/i18n';
import _ from 'lodash';

const settings = {
  defaultSelectElement: {
    label: i18n.t('notChosen'),
    value: 0,
  },
  transportOptionsData: [
    {
      label: i18n.t('notChosen'),
      value: 0,
    },
    {
      label: 'Погрузка',
      value: 1,
    },
    {
      label: 'Ушло',
      value: 2,
    },
    {
      label: 'Прибыл',
      value: 3,
    },
  ],
  sex: [
    {
      label: i18n.t('notChosen'),
      value: 0,
    },
    {
      label: 'Женский',
      value: 1,
    },
    {
      label: 'Мужской',
      value: 2,
    },
  ],
  transferStatus: [
    {
      label: 'Не выбрано',
      color: '',
      value: 0,
    },
    {
      label: 'Вопрос',
      color: 'warning',
      value: 1,
    },
    {
      label: 'Не выдан',
      color: 'red',
      value: 2,
    },
    {
      label: 'Выдано',
      color: 'positive',
      value: 3,
    },
    {
      label: 'Отменен',
      color: 'grey',
      value: 4,
    },
    {
      label: 'Возврат',
      color: 'info',
      value: 5,
    },
  ],
  transferMethod: [
    {
      label: 'Не выбрано',
      value: 0,
    },
    {
      label: 'Деньги',
      value: 1,
    },
    {
      label: 'Товар деньги',
      value: 2,
    },
  ],
};

export default function (value) {
  return _.get(settings, value);
}
