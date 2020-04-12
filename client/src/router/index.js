import Vue from 'vue';
import VueRouter from 'vue-router';

import routes from './routes';
import middlewarePipeline from './middlewarePipeline';

Vue.use(VueRouter);

/*
 * If not building with SSR mode, you can
 * directly export the Router instantiation
 */

export default function ({ store }) {
  const Router = new VueRouter({
    scrollBehavior: () => ({
      x: 0,
      y: 0,
    }),
    routes,

    // Leave these as is and change from quasar.conf.js instead!
    // quasar.conf.js -> build -> vueRouterMode
    // quasar.conf.js -> build -> publicPath
    mode: process.env.VUE_ROUTER_MODE,
    base: process.env.VUE_ROUTER_BASE,
  });

  Router.beforeEach((to, from, next) => {
    const { middleware } = to.meta;
    devlog.log('Middleware', middleware);
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
