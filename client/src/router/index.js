import {
  createRouter,
  createMemoryHistory,
  createWebHistory,
  createWebHashHistory,
} from 'vue-router';
import routes from './routes';
import middlewarePipeline from './middlewarePipeline';

export default function initStore({ store }) {
  // eslint-disable-next-line no-nested-ternary
  const createHistory = process.env.SERVER
    ? createMemoryHistory
    : process.env.VUE_ROUTER_MODE === 'history' ? createWebHistory : createWebHashHistory;

  const Router = createRouter({
    scrollBehavior: () => ({
      left: 0,
      top: 0,
    }),
    routes,

    // Leave this as is and make changes in quasar.conf.js instead!
    // quasar.conf.js -> build -> vueRouterMode
    // quasar.conf.js -> build -> publicPath
    history: createHistory(process.env.MODE === 'ssr' ? undefined : process.env.VUE_ROUTER_BASE),
  });

  Router.beforeEach((to, from, next) => {
    const { middleware } = to.meta;
    devlog.log('Middleware', middleware);
    devlog.warn('Middleware_TO_ROUTER', to);
    devlog.warn('Middleware_TO_ROUTERnext', next);
    if (!middleware) {
      return next();
    }

    const context = {
      to,
      from,
      next,
      store,
      router: Router,
    };

    return middleware[0]({
      ...context,
      next: middlewarePipeline(context, middleware, 1),
    });
  });

  return Router;
}
