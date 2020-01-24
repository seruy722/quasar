import { getUrl } from 'src/tools/url';
// import {callFunction} from 'src/utils/index';

export default {
  methods: {
    getCategories(callBack) {
      if (_.isEmpty(this.$store.getters['category/getCategories'])) {
        this.$store.dispatch('category/getCategories')
          .then(() => {
            if (_.isFunction(callBack)) {
              callBack();
            }
          })
          .catch(() => {
            devlog.log('ERROR in getCategories');
          });
      }
    },
    getClientCodes(callBack) {
      if (_.isEmpty(this.$store.getters['clientCodes/getCodes'])) {
        this.$store.dispatch('clientCodes/getCodes')
          .then(() => {
            if (_.isFunction(callBack)) {
              callBack();
            }
          });
      }
    },
    getThingsList(callBack) {
      if (_.isEmpty(this.$store.getters['thingsList/getThingsList'])) {
        this.$store.dispatch('thingsList/setThingsList')
          .then(() => {
            if (_.isFunction(callBack)) {
              callBack();
            }
          });
      }
    },
    getShopsList(callBack) {
      if (_.isEmpty(this.$store.getters['shopsList/getShopsList'])) {
        this.$store.dispatch('shopsList/setShopsList')
          .then(() => {
            if (_.isFunction(callBack)) {
              callBack();
            }
          });
      }
    },
    async getCities(callBack) {
      if (_.isEmpty(this.$store.getters['cities/getCities'])) {
        await this.$axios.get(getUrl('cities'))
          .then(({ data }) => {
            this.$store.dispatch('cities/setCities', data)
              .then(() => {
                if (_.isFunction(callBack)) {
                  callBack();
                }
              });
          });
      }
    },
    getTransports(callBack) {
      if (_.isEmpty(this.$store.getters['transport/getTransports'])) {
        this.$store.dispatch('transport/fetchTransports')
          .then(() => {
            callBack();
          });
      }
    },
    getTransporters(callBack) {
      if (_.isEmpty(this.$store.getters['transporter/getTransporters'])) {
        this.$store.dispatch('transporter/fetchTransporters')
          .then(() => {
            callBack();
          });
      }
    },
    setDefaultData(data) {
      _.forEach(data, (item) => {
        _.set(item, 'value', item.default);
      });
    },
  },
};
