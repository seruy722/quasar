export const getUser = (state => state.user);
export const isUserAuth = (state => !_.isEmpty(state.user));
export const getToPath = (state => state.toPath);
