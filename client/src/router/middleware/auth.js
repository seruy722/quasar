import access from 'src/tools/access';

// const accessFunction = ((data, store) => {
//   if (data) {
//     const { access: userAccess } = store.getters['auth/getUser'];
//     devlog.log('USER_MODEL', userAccess);
//     // const { roles: accessRoles, permissions: accessPermissions } = data;
//     // devlog.log('accessRoles', accessRoles, 'accessPermissions', accessPermissions);
//     return access(userAccess, data);
//   }
//   return true;
// });

export default function auth({
                               next,
                               store,
                               to: { path, meta: { accessData } },
                               router,
                             }) {
  let userAccess = _.get(store.getters['auth/getUser'], 'access');
  devlog.log('LOGIN_CALL_accessData', accessData);
  store.dispatch('auth/setToPath', path);
  if (!store.getters['auth/isUserAuth']) {
    devlog.log('LOGIN_CALL_getUserModel', _.get(router, 'app'));
    devlog.log('LOGIN_CALL_getUserModel_2', router.currentRoute);
    store.dispatch('auth/getUserModel')
      .then(() => {
        if (!userAccess) {
          userAccess = _.get(store.getters['auth/getUser'], 'access');
        }
        devlog.log('ACCESS_1', access(userAccess, accessData));
        devlog.log('ACCESS_1userAccess', userAccess);
        devlog.log('ACCESS_1accessData', accessData);
        if (accessData && !access(userAccess, accessData)) {
          devlog.log('EROORRVVVVVV_NEXT1');
          next({ name: 'login' });
        } else {
          next();
        }
        // devlog.log('EROORRVVVVVV_NEXT');
        // return next();
      })
      .catch(() => {
        devlog.log('LOGIN_CALL_catch');
        next({ name: 'login' });
      });
  } else {
    const accessFunc = access(userAccess, accessData);
    devlog.log('ACCESS_DATA', accessData);
    devlog.log('ACCESS_2', access(userAccess, accessData));
    devlog.log('ACCESS_PATH', path);
    if (!accessFunc) {
      // Promise.all([accessFunc])
      //   .then(() => {
      //     devlog.log('access', accessFunc);
      //     next({ name: 'storehouse' });
      //   });
      next(false);
    } else {
      next();
    }
  }
  devlog.log('NEXTROUTER', path);
  // next();
}
