const accessFunction = ((data, store, routeName) => {
  if (data) {
    const { roles, permissions } = store.getters['auth/getUser'];
    devlog.log('USER_MODEL', roles, permissions);
    const { roles: accessRoles, permissions: accessPermissions } = data;
    devlog.log('accessRoles', accessRoles, 'accessPermissions', accessPermissions);
    let yesRoleForAccess = false;
    let yesPermissionForAccess = false;
    if (!_.isEmpty(accessRoles)) {
      _.forEach(accessRoles, (role) => {
        if (_.includes(roles, role)) {
          devlog.error('ERROR');
          yesRoleForAccess = true;
        }
      });
    }
    if (!_.isEmpty(accessPermissions)) {
      _.forEach(accessPermissions, (permission) => {
        if (_.includes(permissions, permission)) {
          devlog.error('ERROR');
          yesPermissionForAccess = true;
        }
      });
    }

    if (!yesRoleForAccess && !yesPermissionForAccess) {
      devlog.error('routeName', routeName);
      // if (routeName !== 'storehouse') {
      return false;
      // }
    }
  }
  return true;
});

export default function auth({
                               next,
                               store,
                               to: { path, name: routeName, meta: { accessData } },
                               router,
                             }) {
  devlog.log('AUTRFG', path);
  devlog.log('name', routeName);
  devlog.log('accessData', accessData);
  store.dispatch('auth/setToPath', path);
  if (!store.getters['auth/isUserAuth']) {
    devlog.log('LOGIN_CALL_getUserModel', router);
    store.dispatch('auth/getUserModel')
      .then(() => {
        accessFunction(accessData, store, routeName, router);
      })
      .catch(() => {
        devlog.log('LOGIN_CALL_catch');
        return router.push({ name: 'login' });
      });
  }

  const access = accessFunction(accessData, store, routeName, router);
  if (!access) {
    Promise.all([access])
      .then(() => {
        devlog.log('access', access);
        return router.push({ name: 'storehouse' });
      });
  }

  return next();
}
