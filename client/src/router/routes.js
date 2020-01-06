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
          title: 'Login',
        },
        component: () => import('pages/Auth/Login.vue'),
      },
    ],
  },
  // {
  //   path: '/admin',
  //   component: () => import('layouts/AdminLayout.vue'),
  //   children: [
  //     {
  //       path: 'transfers',
  //       name: 'transfers',
  //       meta: {
  //         title: 'transfers',
  //         middleware: [
  //           auth,
  //         ],
  //       },
  //       component: () => import('src/pages/Moder/Transfers.vue'),
  //     },
  //     // {
  //     //   path: 'storehouse',
  //     //   name: 'storehouse',
  //     //   meta: {
  //     //     title: 'storehouse',
  //     //     middleware: [
  //     //       auth,
  //     //     ],
  //     //   },
  //     //   component: () => import('pages/Moder/Storehouse.vue'),
  //     // },
  //     // {
  //     //   path: 'customers',
  //     //   name: 'customers',
  //     //   meta: {
  //     //     title: 'customers',
  //     //     middleware: [
  //     //       auth,
  //     //     ],
  //     //   },
  //     //   component: () => import('pages/Moder/Customers.vue'),
  //     // },
  //     // {
  //     //   path: 'profile',
  //     //   name: 'profile',
  //     //   meta: {
  //     //     title: 'profile',
  //     //     middleware: [
  //     //       auth,
  //     //     ],
  //     //   },
  //     //   component: () => import('pages/Profile.vue'),
  //     // },
  //     // {
  //     //   path: 'faxes',
  //     //   name: 'faxes',
  //     //   meta: {
  //     //     title: 'faxes',
  //     //     middleware: [
  //     //       auth,
  //     //     ],
  //     //   },
  //     //   component: () => import('pages/Faxes.vue'),
  //     // },
  //     // {
  //     //   path: 'faxes/:id',
  //     //   name: 'fax',
  //     //   meta: {
  //     //     title: 'fax',
  //     //     middleware: [
  //     //       auth,
  //     //     ],
  //     //   },
  //     //   component: () => import('pages/Fax.vue'),
  //     // },
  //     // {
  //     //   path: 'drafts',
  //     //   name: 'drafts',
  //     //   meta: {
  //     //     title: 'drafts',
  //     //     middleware: [
  //     //       auth,
  //     //     ],
  //     //   },
  //     //   component: () => import('pages/Drafts.vue'),
  //     // },
  //     // {
  //     //   path: 'search',
  //     //   name: 'search',
  //     //   meta: {
  //     //     title: 'search',
  //     //     middleware: [
  //     //       auth,
  //     //     ],
  //     //   },
  //     //   component: () => import('pages/Search.vue'),
  //     // },
  //   ],
  // },
  {
    path: '/moder',
    component: () => import('layouts/ModerLayout.vue'),
    children: [
      {
        path: 'transfers',
        name: 'transfers',
        meta: {
          title: 'transfers',
          middleware: [
            auth,
          ],
        },
        component: () => import('src/pages/Moder/Transfers.vue'),
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
      //   path: 'customers',
      //   name: 'customers',
      //   meta: {
      //     title: 'customers',
      //     middleware: [
      //       auth,
      //     ],
      //   },
      //   component: () => import('pages/Moder/Customers.vue'),
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
      // {
      //   path: 'faxes',
      //   name: 'faxes',
      //   meta: {
      //     title: 'faxes',
      //     middleware: [
      //       auth,
      //     ],
      //   },
      //   component: () => import('pages/Faxes.vue'),
      // },
      // {
      //   path: 'faxes/:id',
      //   name: 'fax',
      //   meta: {
      //     title: 'fax',
      //     middleware: [
      //       auth,
      //     ],
      //   },
      //   component: () => import('pages/Fax.vue'),
      // },
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
      //   },
      //   component: () => import('pages/Search.vue'),
      // },
    ],
  },
];

// Always leave this as last one
if (process.env.MODE !== 'ssr') {
  routes.push({
    path: '*',
    component: () => import('pages/Error404.vue'),
  });
}

export default routes;
