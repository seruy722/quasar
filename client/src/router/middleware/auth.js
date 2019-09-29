export default function auth({ next, store, to }) {
    devlog.log('AUTRFG', to);
    store.dispatch('auth/setToPath', to.path);
    if (!store.getters['auth/isUserAuth']) {
        return next({
            name: 'login',
        });
    }

    return next();
}
