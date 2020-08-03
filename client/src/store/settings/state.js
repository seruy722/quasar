export default {
  locale: '',
  menu: [
    {
      title: 'Главная',
      field: 'index',
      icon: 'house',
      access: {
        roles: ['admin', 'moderator', 'assistant'],
        permissions: [],
      },
    },
    {
      title: 'Карго Долги',
      field: 'cargo-debts',
      icon: 'assignment',
      access: {
        roles: ['admin'],
        permissions: ['general-cargo-data'],
      },
    },
    {
      title: 'Карго Долги',
      field: 'cargo-debts-assistant',
      icon: 'assignment',
      access: {
        roles: ['assistant'],
        permissions: [],
      },
    },
    {
      title: 'Склад',
      field: 'storehouse',
      icon: 'store',
      access: {
        roles: ['admin', 'storehouse'],
        permissions: ['view storehouse data'],
      },
    },
    {
      title: 'Кода',
      field: 'codes',
      icon: 'people',
      access: {
        roles: ['admin', 'codes'],
        permissions: ['view codes list'],
      },
    },
    {
      title: 'Цены по кодам',
      field: 'codes-prices',
      icon: 'money',
      access: {
        roles: ['admin', 'codes-prices'],
        permissions: ['get-codes-prices'],
      },
    },
    {
      title: 'Факсы',
      field: 'faxes',
      icon: 'library_books',
      access: {
        roles: ['admin', 'fax'],
        permissions: ['view faxes list'],
      },
    },
    {
      title: 'Факсы админа',
      field: 'admin-faxes',
      icon: 'library_add_check',
      access: {
        roles: ['admin'],
        permissions: [],
      },
    },
    {
      title: 'Переводы',
      field: 'transfers',
      icon: 'import_export',
      access: {
        roles: ['admin', 'transfers'],
        permissions: ['view transfers list'],
      },
    },
    {
      title: 'Задачи',
      field: 'tasks',
      icon: 'business',
      access: {
        roles: ['admin'],
        permissions: [],
      },
    },
    {
      title: 'Черновик',
      field: 'drafts',
      icon: 'drafts',
      access: {
        roles: ['admin'],
        permissions: [],
      },
    },
    {
      title: 'Поиск',
      field: 'search',
      icon: 'search',
      access: {
        roles: ['admin'],
        permissions: ['view search page'],
      },
    },
    {
      title: 'Доступы',
      field: 'access',
      icon: 'settings',
      access: {
        roles: ['admin'],
        permissions: ['view access list'],
      },
    },
    {
      title: 'Выход',
      field: 'exit',
      icon: 'exit_to_app',
      access: {
        roles: [],
        permissions: ['exit app'],
      },
    },
  ],
};
