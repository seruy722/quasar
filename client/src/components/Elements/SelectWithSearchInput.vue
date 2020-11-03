<template>
  <q-select
    v-model="modelData"
    filled
    use-input
    input-debounce="0"
    :label="label"
    emit-value
    map-options
    :options="localOptions"
    :value="value"
    :dense="dense"
    :error-message="viewError()"
    :error="isError"
    data-vue-component-name="SelectWithSearchInput"
    @filter="filterFn"
    @input="inputEvent"
  >
    <template #prepend>
      <q-icon :name="icon" />
    </template>
    <template #no-option>
      <q-item>
        <q-item-section class="text-grey">
          Нет результатов
        </q-item-section>
      </q-item>
    </template>
  </q-select>
</template>

<script>
    import ViewErrorsMixin from 'src/mixins/ViewErrors';

    export default {
        name: 'SelectWithSearchInput',
        mixins: [ViewErrorsMixin],
        props: {
            value: {
                type: [String, Number],
                default: '',
            },
            options: {
                type: Array,
                default: () => [],
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
            field: {
                type: String,
                default: '',
            },
            errors: {
                type: Object,
                default: () => ({}),
            },
        },
        data: () => ({
            localOptions: [],
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
        watch: {
            localOptions(val) {
                devlog.log(val);
            },
        },
        created() {
            this.localOptions = this.options;
        },
        methods: {
            filterFn(val, update) {
                devlog.log('valSE', val);
                if (!val) {
                    update(() => {
                        this.localOptions = this.options;
                        // this.$emit('update:options', this.options);
                        // this.options = this.options;
                    });
                    return;
                }

                update(() => {
                    const needle = val.toLowerCase();
                    // devlog.log('NEEDLE', this.options.filter(v => v.label.toLowerCase()
                    //   .indexOf(needle) > -1));
                    // this.$emit('update:options', this.options.filter(v => v.label.toLowerCase()
                    //   .indexOf(needle) > -1));
                    // this.$emit('update:options', this.options.filter(v => v.label.toLowerCase()
                    //   .indexOf(needle) > -1));
                    this.localOptions = this.options.filter((v) => v.label.toLowerCase()
                      .indexOf(needle) > -1);
                });
            },
            inputEvent($event) {
                this.changeErrors();
                this.$emit('input', $event);
            },
        },
    };
</script>
