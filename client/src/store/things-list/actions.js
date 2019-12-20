import { getUrl } from 'src/tools/url';
import { axiosInstance } from 'boot/axios';

export const setThingsList = (async ({ commit }) => {
  try {
    const { data } = await axiosInstance.get(getUrl('thingsList'));
    commit('SET_THINGS_LIST', data);
  } catch (e) {
    devlog.log(e);
  }
});
