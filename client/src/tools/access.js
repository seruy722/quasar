export default function checkAccess(userAccess, menuAccess) {
  devlog.log('userAccessMENU', menuAccess);
  let yesRoleForAccess = false;
  let yesPermissionForAccess = false;
  if (menuAccess && userAccess) {
    const { roles: accessRoles, permissions: accessPermissions } = userAccess;
    const { roles, permissions } = menuAccess;
    if (!_.isEmpty(accessRoles)) {
      _.forEach(accessRoles, (role) => {
        if (_.includes(roles, role)) {
          devlog.error('includesRole', role);
          yesRoleForAccess = true;
        }
      });
    }
    if (!_.isEmpty(accessPermissions)) {
      _.forEach(accessPermissions, (permission) => {
        if (_.includes(permissions, permission)) {
          devlog.error('includesPermission', permission);
          yesPermissionForAccess = true;
        }
      });
    }
  }
  return yesRoleForAccess || yesPermissionForAccess;
}
