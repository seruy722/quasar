export default {
  filters: {
    jsonDecodeThings(val) {
      try {
        return _.reduce(JSON.parse(val), (result, { label, value }) => {
          result[label] = value;
          return result;
        }, {});
      } catch (e) {
        devlog.error(`Неправильный формат JSON строки (функции jsonDecodeThings) - ${val}`);
      }
      return val;
    },
  },
};
