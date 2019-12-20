<template>
  <q-select
    use-input
    use-chips
    input-debounce="0"
    :value="value"
    :dense="dense"
    :options="filterOptions"
    data-vue-component-name="SelectChips"
    @new-value="createValue"
    @filter="filterFn"
    @input="$emit('input', $event)"
  />
</template>

<script>
    export default {
        name: 'SelectChips',
        props: {
            value: {
                type: [String, Number],
                default: 0,
            },
            options: {
                type: Array,
                default: () => [],
            },
            dense: {
                type: Boolean,
                default: false,
            },
        },
        data() {
            this.$_stringOptions = [];
            return {
                model: null,
                filterOptions: [],
            };
        },
        watch: {
            options: {
                handler: function set(val) {
                    this.$_stringOptions = _.clone(val);
                    this.filterOptions = val;
                },
                immediate: true,
            },
        },
        methods: {
            createValue(val, done) {
                if (val.length > 0) {
                    if (!this.$_stringOptions.includes(val)) {
                        this.$_stringOptions.push(val);
                    }
                    done(val, 'toggle');
                }
            },

            filterFn(val, update) {
                update(() => {
                    if (val === '') {
                        this.filterOptions = this.$_stringOptions;
                    } else {
                        const needle = val.toLowerCase();
                        this.filterOptions = this.$_stringOptions.filter(
                          (v) => v.toLowerCase()
                            .indexOf(needle) > -1,
                        );
                    }
                });
            },
        },
    };
</script>
