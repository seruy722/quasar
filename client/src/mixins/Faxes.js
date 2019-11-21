export default {
    methods: {
        addToUpdateArray(item, key) {
            const duplicateIndex = _.findIndex(this[key], { id: item.id });
            if (duplicateIndex !== -1) {
                this[key].splice(duplicateIndex, 1, item);
            } else {
                this[key].push(item);
            }
        },
        prepareFaxTableData(data) {
            const newArr = [];
            _.forEach(data, (value) => {
                if (_.isObject(value)) {
                    _.forEach(value, (item) => {
                        _.forEach(item, (elem) => {
                            const obj2 = {};
                            _.assign(obj2, _.first(elem), {
                                arr: elem,
                                kg: this.countSum(elem, 'kg'),
                                place: this.countSum(elem, 'place'),
                                sum: 0,
                            });
                            newArr.push(obj2);
                        });
                    });
                }
            });
            // devlog.log('newArr', newArr);
            return newArr;
        },
        countSum(arr, key) {
            return _.sumBy(arr, key);
        },
        getCategories() {
            if (_.isEmpty(this.$store.getters['category/getCategories'])) {
                this.$store.dispatch('category/getCategories');
            }
        },
        sumObjectForCategories(arr) {
            return {
                name: '',
                kg: this.countSum(arr, 'kg'),
                place: this.countSum(arr, 'place'),
            };
        },
        getClientCode() {
            if (_.isEmpty(this.$store.getters['clientCodes/getCodes'])) {
                this.$store.dispatch('clientCodes/getCodes');
            }
        },
    },
};
