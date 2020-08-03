export const SET_USER = ((state, data) => {
  state.user = data;
});

export const SET_TO_PATH = ((state, value) => {
  state.toPath = value;
});

export const LOGOUT = ((state) => {
  state.user = null;
  state.toPath = null;
});

export const SET_USERS_WITH_ROLES_AND_PERMISSIONS = ((state, array) => {
  state.usersWithRolesAndPermissions = array;
});

export const CHECK_USER_ACCESS = ((state, data) => {
  // const userRoles = _.get(state, 'user.roles');
  // const userPermissions = _.get(state, 'user.roles');
  _.forEach(data.roles, ({ role }) => {
    devlog.log('ROLE', role);
  });
  return true;
});

export const REMOVE_USER_DATA = ((state) => {
  state.user = null;
  state.toPath = '';
  state.usersWithRolesAndPermissions = [];
});

export const SET_USERS_LIST = ((state, data) => {
  state.usersList = data;
});
