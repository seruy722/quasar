import validator from 'validator';

export default {
  data: () => ({
    errorsData: {
      errors: {},
    },
  }),
  methods: {
    checkErrors(data, callback) {
      devlog.log('CH_DATA', data);
      const errors = {};
      _.forEach(data, (item, key) => {
        devlog.log('CH', key);
        if (this.checkRequire(item)) {
          errors[key] = [item.requireError];
        } else if (item.rules) {
          _.forEach(item.rules, ({ name, error, options }) => {
            if (!validator[name](item.value, options)) {
              errors[key] = [error];
            }
          });
        }
      });

      if (!_.isEmpty(errors)) {
        this.setErrors(errors);
      } else if (_.isFunction(callback)) {
        callback(data);
      }
    },
    setErrors(errors) {
      this.errorsData.errors = errors;
    },
    checkRequire({ require, value }) {
      return require && !value;
    },
  },
};
