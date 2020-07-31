import auth from './middleware/auth';

const routes = [
  {
    path: '/',
    component: () => import('layouts/AuthLayout.vue'),
    children: [
      {
        path: '',
        name: 'login',
        meta: {
          title: 'Авторизация',
        },
        component: () => import('pages/Auth/Login.vue'),
      },
    ],
  },
  {
    path: '/index',
    component: () => import('layouts/StartLayout.vue'),
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
            roles: ['admin', 'moderator', 'assistant'],
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
        },
        component: () => import('src/pages/Index.vue'),
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
    ],
  },
];

// Always leave this as last one
// if (process.env.MODE !== 'ssr') {
routes.push({
  path: '*',
  component: () => import('pages/Error404.vue'),
});
// }

export default routes;
