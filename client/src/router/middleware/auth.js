export default function auth({ next, store, to }) {
  devlog.log('AUTRFG', to);
  store.dispatch('auth/setToPath', to.path);
  if (!store.getters['auth/isUserAuth']) {
    devlog.log('LOGIN_CALL_getUserModel');
    store.dispatch('auth/getUserModel')
      .catch(() => {
        devlog.log('LOGIN_CALL_catch');
        return next({
          name: 'login',
        });
      });
  }

  return next();
}
