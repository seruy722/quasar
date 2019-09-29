<template>
    <div
        data-vue-component-name="SelectWithSearchInput"
    >
        <q-select
            v-model="modelData"
            filled
            use-input
            input-debounce="0"
            :label="inputData.label"
            emit-value
            map-options
            :options="opt"
            :error-message="viewError()"
            :error="isError"
            @filter="filterFn"
            @input="inputEvent"
        >
            <template v-slot:prepend>
                <q-icon :name="inputData.icon" />
            </template>
            <template v-slot:no-option>
                <q-item>
                    <q-item-section class="text-grey">
                        Нет результатов
                    </q-item-section>
                </q-item>
            </template>
        </q-select>
    </div>
</template>

<script>
    import ErrorsServerMixin from 'src/mixins/ViewErrors';

    export default {
        name: 'SelectWithSearchInput',
        mixins: [ErrorsServerMixin],
        props: {
            value: {
                type: [String, Number],
                default: '',
            },
            options: {
                type: Array,
                default: () => [],
            },
            searchOptions: {
                type: Array,
                default: () => [],
            },
            inputData: {
                type: Object,
                default: () => ({}),
            },
            errors: {
                type: Object,
                default: () => ({}),
            },
        },
        data: () => ({
            opt: [],
        }),
        computed: {
            modelData: {
                get: function getData() {
                    return this.value;
                },
                set: function setData(newValue) {
                    this.$emit('update:value', newValue);
                },
            },
        },
        created() {
            this.opt = this.searchOptions;
        },
        methods: {
            filterFn(val, update) {
                devlog.log('valSE', val);
                if (!val) {
                    update(() => {
                        this.opt = this.searchOptions;
                        // this.$emit('update:options', this.searchOptions);
                        // this.options = this.searchOptions;
                    });
                    return;
                }

                update(() => {
                    const needle = val.toLowerCase();
                    // devlog.log('NEEDLE', this.searchOptions.filter(v => v.label.toLowerCase()
                    //   .indexOf(needle) > -1));
                    // this.$emit('update:options', this.searchOptions.filter(v => v.label.toLowerCase()
                    //   .indexOf(needle) > -1));
                    // this.$emit('update:options', this.searchOptions.filter(v => v.label.toLowerCase()
                    //   .indexOf(needle) > -1));
                    this.opt = this.searchOptions.filter(v => v.label.toLowerCase()
                      .indexOf(needle) > -1);
                });
            },
            inputEvent() {
                this.changeErrors();
                this.$emit('selectSearchInput');
            },
        },
    };
</script>
