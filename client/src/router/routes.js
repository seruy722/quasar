import auth from './middleware/auth';

const routes = [
  {
    path: '/',
    component: () => import('src/layouts/AuthLayout.vue'),
    children: [
      {
        path: '',
        name: 'login',
        meta: {
          title: 'Авторизация',
        },
        component: () => import('src/pages/Auth/Index.vue'),
      },
      {
        path: '/register-client',
        name: 'register-client',
        redirect: { name: 'login' },
        meta: {
          title: 'Регистрация клиента',
        },
        component: () => import('src/pages/Auth/RegisterClient.vue'),
      },
      {
        path: '/password-recovery',
        redirect: { name: 'login' },
        name: 'password-recovery',
        meta: {
          title: 'Восстановление доступа',
        },
        component: () => import('src/pages/Auth/PasswordRecovery'),
      },
    ],
  },
  {
    path: '/index',
    component: () => import('src/layouts/StartLayout.vue'),
    children: [
      {
        path: '',
        name: 'index',
        meta: {
          title: 'Главная',
          middleware: [
            auth,
          ],
          accessData: {
            roles: ['admin', 'moderator', 'assistant', 'client'],
            permissions: [],
          },
        },
        component: () => import('src/pages/Index.vue'),
      },
    ],
  },
  {
    path: '/assistant',
    component: () => import('layouts/AssistantLayout.vue'),
    children: [
      {
        path: 'cargo-debts',
        name: 'cargo-debts-assistant',
        meta: {
          title: 'Карго Долги',
          middleware: [
            auth,
          ],
          accessData: {
            roles: ['admin', 'assistant'],
            permissions: [],
          },
        },
        component: () => import('src/pages/Assistant/CargoDebtsAssistant.vue'),
      },
    ],
  },
  {
    path: '/admin',
    component: () => import('layouts/AdminLayout.vue'),
    children: [
      {
        path: 'access',
        name: 'access',
        meta: {
          title: 'Доступы',
          middleware: [
            auth,
          ],
          accessData: {
            roles: ['admin'],
            permissions: [],
          },
        },
        component: () => import('src/pages/Admin/Access.vue'),
      },
      // {
      //   path: 'storehouse',
      //   name: 'storehouse',
      //   meta: {
      //     title: 'storehouse',
      //     middleware: [
      //       auth,
      //     ],
      //   },
      //   component: () => import('pages/Moder/Storehouse.vue'),
      // },
      // {
      //   path: 'profile',
      //   name: 'profile',
      //   meta: {
      //     title: 'profile',
      //     middleware: [
      //       auth,
      //     ],
      //   },
      //   component: () => import('pages/Profile.vue'),
      // },
      {
        path: 'admin-faxes',
        name: 'admin-faxes',
        meta: {
          title: 'Факсы админа',
          middleware: [
            auth,
          ],
          accessData: {
            roles: ['admin'],
            permissions: [],
          },
        },
        component: () => import('src/pages/Admin/Faxes.vue'),
      },
      {
        path: 'admin-faxes/:id',
        name: 'admin-fax',
        meta: {
          title: 'Факсы админа',
          middleware: [
            auth,
          ],
          accessData: {
            roles: ['admin'],
            permissions: [],
          },
        },
        component: () => import('src/pages/Admin/Fax.vue'),
      },
      // {
      //   path: 'drafts',
      //   name: 'drafts',
      //   meta: {
      //     title: 'drafts',
      //     middleware: [
      //       auth,
      //     ],
      //   },
      //   component: () => import('pages/Drafts.vue'),
      // },
      // {
      //   path: 'search',
      //   name: 'search',
      //   meta: {
      //     title: 'search',
      //     middleware: [
      //       auth,
      //     ],
      //     accessData: {
      //       roles: ['admin', 'moder'],
      //       permissions: [],
      //     },
      //   },
      //   component: () => import('pages/Search.vue'),
      // },
    ],
  },
  {
    path: '/moder',
    component: () => import('layouts/ModerLayout.vue'),
    children: [
      {
        path: 'transfers',
        name: 'transfers',
        meta: {
          title: 'Переводы',
          middleware: [
            auth,
          ],
          accessData: {
            roles: ['admin', 'transfers'],
            permissions: ['view transfers list'],
          },
        },
        component: () => import('src/pages/Moder/Transfers.vue'),
      },
      {
        path: 'storehouse',
        name: 'storehouse',
        meta: {
          title: 'Склад',
          middleware: [
            auth,
          ],
          accessData: {
            roles: ['admin', 'storehouse'],
            permissions: ['view storehouse data'],
          },
        },
        component: () => import('pages/Moder/Storehouse.vue'),
      },
      {
        path: 'codes',
        name: 'codes',
        meta: {
          title: 'Кода',
          middleware: [
            auth,
          ],
          accessData: {
            roles: ['admin', 'codes'],
            permissions: ['view codes list'],
          },
        },
        component: () => import('pages/Moder/Codes.vue'),
      },
      {
        path: 'codes-prices',
        name: 'codes-prices',
        meta: {
          title: 'Цены по кодам',
          middleware: [
            auth,
          ],
          accessData: {
            roles: ['admin', 'codes-prices'],
            permissions: ['get-codes-prices'],
          },
        },
        component: () => import('pages/Moder/CodePrice.vue'),
      },
      {
        path: 'faxes',
        name: 'faxes',
        meta: {
          title: 'Факсы',
          middleware: [
            auth,
          ],
          accessData: {
            roles: ['admin', 'fax'],
            permissions: ['view faxes list'],
          },
        },
        component: () => import('pages/Faxes.vue'),
      },
      {
        path: 'faxes/:id',
        name: 'fax',
        meta: {
          title: 'Факс',
          middleware: [
            auth,
          ],
          accessData: {
            roles: ['admin', 'fax'],
            permissions: ['view fax data'],
          },
        },
        component: () => import('pages/Fax.vue'),
      },
      {
        path: 'drafts',
        name: 'drafts',
        meta: {
          title: 'Черновики',
          middleware: [
            auth,
          ],
          accessData: {
            roles: ['admin'],
            permissions: [],
          },
        },
        component: () => import('pages/Drafts.vue'),
      },
      {
        path: 'search',
        name: 'search',
        meta: {
          title: 'Поиск',
          middleware: [
            auth,
          ],
          accessData: {
            roles: ['admin'],
            permissions: ['view search page'],
          },
        },
        component: () => import('pages/Search.vue'),
      },
      {
        path: 'cargo-debts',
        name: 'cargo-debts',
        meta: {
          title: 'Карго Долги',
          middleware: [
            auth,
          ],
          accessData: {
            roles: ['admin'],
            permissions: ['general-cargo-data'],
          },
        },
        component: () => import('pages/CargoDebts.vue'),
      },
      {
        path: 'payment-arrears',
        name: 'payment-arrears',
        meta: {
          title: 'Задолженность',
          middleware: [
            auth,
          ],
          accessData: {
            roles: ['admin'],
            permissions: ['general-cargo-data'],
          },
        },
        component: () => import('pages/PaymentArrears.vue'),
      },
      {
        path: 'questions',
        name: 'questions',
        meta: {
          title: 'Вопросы',
          middleware: [
            auth,
          ],
          accessData: {
            roles: ['admin', 'questions'],
            permissions: [],
          },
        },
        component: () => import('pages/Moder/Questions.vue'),
      },
      {
        path: 'tasks',
        name: 'tasks',
        meta: {
          title: 'Задачи',
          middleware: [
            auth,
          ],
          accessData: {
            roles: ['admin'],
            permissions: [],
          },
        },
        component: () => import('pages/Moder/Tasks.vue'),
      },
      {
        path: 'tasks/:id',
        name: 'task',
        meta: {
          title: 'Задача',
          middleware: [
            auth,
          ],
          accessData: {
            roles: ['admin'],
            permissions: [],
          },
        },
        component: () => import('pages/Moder/Task.vue'),
      },
      {
        path: 'documents',
        name: 'documents',
        meta: {
          title: 'Документы',
          middleware: [
            auth,
          ],
          accessData: {
            roles: ['admin', 'documents', 'comments'],
            permissions: [],
          },
        },
        component: () => import('pages/Moder/Documents.vue'),
      },
      {
        path: 'statistics',
        name: 'statistics',
        meta: {
          title: 'Статистика',
          middleware: [
            auth,
          ],
          accessData: {
            roles: ['admin'],
            permissions: ['view statistics'],
          },
        },
        component: () => import('pages/Moder/Statistics.vue'),
      },
      {
        path: 'info',
        name: 'info',
        meta: {
          title: 'Инфо',
          middleware: [
            auth,
          ],
          accessData: {
            roles: ['admin'],
            permissions: ['view info'],
          },
        },
        component: () => import('src/pages/Moder/Info.vue'),
      },
      {
        path: 'client-cargo-debts',
        name: 'client-cargo-debts',
        meta: {
          title: 'Карго и долги',
          middleware: [
            auth,
          ],
          accessData: {
            roles: ['admin', 'client'],
            permissions: [],
          },
        },
        component: () => import('pages/Client/CargoDebtsClient.vue'),
      },
      {
        path: 'client-transfers',
        name: 'client-transfers',
        meta: {
          title: 'Переводы',
          middleware: [
            auth,
          ],
          accessData: {
            roles: ['admin', 'client'],
            permissions: [],
          },
        },
        component: () => import('pages/Client/TransfersClient.vue'),
      },
    ],
  },
  // Always leave this as last one,
  // but you can also remove it
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/Error404.vue'),
  },
];

export default routes;
