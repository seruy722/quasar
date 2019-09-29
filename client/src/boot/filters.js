export default async ({ Vue }) => {
    Vue.filter('filterFromSelectData', (val, data) => _.get(_.find(data, { value: val }), 'label'));
};
