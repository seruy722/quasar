import { i18n } from 'src/boot/i18n';
import { get } from 'lodash';

const settings = {
  defaultSelectElement: {
    label: i18n.t('notChosen'),
    value: 0,
  },
  transportStatusOptions: [
    {
      label: i18n.t('notChosen'),
      value: 0,
      color: 'none',
    },
    {
      label: 'Погрузка',
      value: 1,
      color: 'primary',
    },
    {
      label: 'Ушло',
      value: 2,
      color: 'yellow_bg',
    },
    {
      label: 'Прибыл',
      value: 3,
      color: 'green',
    },
    {
      label: 'Задерживается',
      value: 4,
      color: 'accent',
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
  historyActionForIcon: {
    create: 'add',
    update: 'update',
    destroy: 'delete',
    move: 'move_to_inbox',
  },
};

export default function sett(value) {
  return get(settings, value);
}
