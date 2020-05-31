import { getUrl } from 'src/tools/url';
import { axiosInstance } from 'boot/axios';

export const fetchDeliveryMethodsList = (async ({ commit }) => axiosInstance.get(getUrl('deliveryMethodsList'))
  .then(({ data }) => {
    data.unshift({
      label: 'Не выбрано',
      value: 0,
    });
    commit('SET_DELIVERY_METHODS_LIST', data);
  })
  .catch(() => {
    devlog.error('Ошибка запроса - fetchDeliveryMethodsList');
  }));
