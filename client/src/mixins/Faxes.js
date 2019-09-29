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
    },
};
