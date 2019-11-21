import getFromSettings from 'src/tools/settings';

export const setCities = (({ commit }, data) => {
  data.unshift(getFromSettings('defaultSelectElement'));
  commit('SET_CITIES', data);
});
