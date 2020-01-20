import getFromSettings from 'src/tools/settings';

export default {
  methods: {
    statusColor(value) {
      const findLabel = _.find(getFromSettings('transferStatus'), { value }) || _.find(getFromSettings('transferStatus'), { label: value });
      return _.get(findLabel, 'color');
    },
  },
};
