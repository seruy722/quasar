<template>
  <q-select
    filled
    :options="duplicateOptions"
    :label="label"
    :error-message="viewError()"
    :error="isError"
    :value="value"
    :dense="dense"
    use-input
    data-vue-component-name="SearchSelect"
    emit-value
    map-options
    @filter="filterFn"
    @input="inputEvent"
  >
    <template v-slot:prepend>
      <q-icon :name="icon" />
    </template>
  </q-select>
</template>

<script>
    import ErrorsServerMixin from 'src/mixins/ViewErrors';

    export default {
        name: 'SearchSelect',
        mixins: [ErrorsServerMixin],
        props: {
            value: {
                type: [String, Number],
                default: null,
            },
            options: {
                type: Array,
                default: () => [],
            },
            field: {
                type: String,
                default: '',
            },
            label: {
                type: String,
                default: '',
            },
            icon: {
                type: String,
                default: '',
            },
            dense: {
                type: Boolean,
                default: false,
            },
            dark: {
                type: Boolean,
                default: false,
            },
            errors: {
                type: Object,
                default: () => ({}),
            },
        },
        data() {
            return {
                duplicateOptions: [],
            };
        },
        created() {
            this.duplicateOptions = this.options;
        },
        methods: {
            inputEvent($event) {
                this.$emit('input', $event);
                this.changeErrors();
            },
            filterFn(val, update) {
                devlog.log('valSE', val);
                if (!val) {
                    update(() => {
                        this.duplicateOptions = this.options;
                    });
                    return;
                }

                update(() => {
                    const needle = val.toLowerCase();
                    this.duplicateOptions = this.options.filter((v) => v.label.toLowerCase()
                      .indexOf(needle) > -1);
                });
            },
        },
    };
</script>
