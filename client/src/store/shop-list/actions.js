import { getUrl } from 'src/tools/url';
import { axiosInstance } from 'boot/axios';

export const setShopsList = (async ({ commit }) => {
  try {
    const { data } = await axiosInstance.get(getUrl('shopsList'));
    commit('SET_SHOPS_LIST', data);
  } catch (e) {
    devlog.log(e);
  }
});
