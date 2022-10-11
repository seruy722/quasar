import { get } from 'lodash';

const settings = {
    defaultSelectElement: {
        label: 'Не выбрано',
        value: 0,
    },
    transportStatusOptions: [
        {
            label: 'На складе',
            value: -1,
            color: 'none',
        },
        {
            label: 'Не выбрано',
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
            label: 'В Украине',
            value: 6,
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
        {
            label: 'На складе',
            value: 5,
            color: 'accent',
        },
    ],
    sex: [
        {
            label: 'Не выбрано',
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
        {
            label: 'Обработка',
            color: 'info',
            value: 6,
        },
        {
            label: 'Отменен клиентом',
            color: 'grey',
            value: 7,
        },
    ],
    transferStatusClient: [
        {
            label: 'Не выбрано',
            color: '',
            value: 0,
        },
        {
            label: 'Обработка',
            color: 'black',
            value: 6,
        },
        {
            label: 'Отменен клиентом',
            color: 'grey',
            value: 7,
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
        move: 'sync_alt',
    },
    statusTask: [
        {
            label: 'Новая',
            value: 1,
            color: 'negative',
        },
        {
            label: 'Отработано',
            value: 2,
            color: 'lightgrey',
        },
        {
            label: 'Доработка',
            value: 3,
            color: 'pink',
        },
        {
            label: 'Отменена',
            value: 4,
            color: 'grey',
        },
        {
            label: 'Выполнено',
            value: 5,
            color: 'positive',
        },
    ],
    statusQuestion: [
        {
            label: 'Новый',
            value: 0,
            color: 'negative',
        },
        {
            label: 'Отработано',
            value: 1,
            color: 'lightgrey',
        },
        {
            label: 'Выполнено',
            value: 2,
            color: 'positive',
        },
    ],
    sectionTask: [
        {
            label: 'Карго',
            value: 1,
        },
        {
            label: 'Долги',
            value: 2,
        },
        {
            label: 'Переводы',
            value: 3,
        },
        {
            label: 'Факсы',
            value: 4,
        },
        {
            label: 'Склад',
            value: 5,
        },
    ],
};

export default function sett(value) {
    return get(settings, value);
}
