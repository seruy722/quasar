<template>
  <q-select
    :options="duplicateOptions"
    :label="label"
    :error-message="viewError()"
    :error="isError"
    :value="value"
    :dense="dense"
    :multiple="multiple"
    :clearable="clearable"
    use-input
    data-vue-component-name="SearchSelect"
    emit-value
    map-options
    @filter="filterFn"
    @update:model-value="inputEvent"
  >
    <template #prepend>
      <slot name="prepend" />
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
      type: [String, Number, Array],
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
    changeValue: {
      type: Boolean,
      default: false,
    },
    multiple: {
      type: Boolean,
      default: false,
    },
    clearable: {
      type: Boolean,
      default: false,
    },
    funcLoadData: {
      type: Function,
      default: null,
    },
    errors: {
      type: Object,
      default: () => ({}),
    },
  },
  emits: ['update:changeValue', 'input'],
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
      if (!this.changeValue) {
        this.$emit('update:changeValue', true);
      }
    },
    filterFn(val, update) {
      devlog.log('valSE', val);
      if (!_.isEmpty(this.options) && !val) {
        update(() => {
          this.duplicateOptions = this.options;
        });
      } else if (_.isEmpty(this.options) && _.isFunction(this.funcLoadData)) {
        // this.$store.dispatch('codes/setCodes').then(() => {
        //   update(() => {
        //     this.duplicateOptions = this.options;
        //   });
        // });
        devlog.log('funcLoadData', this.options);
        devlog.log('this.options', this.options);
        this.funcLoadData(this.$store)
          .then(() => {
            update(() => {
              this.duplicateOptions = this.options;
            });
          });
      } else {
        update(() => {
          const needle = val.toLowerCase();
          this.duplicateOptions = this.options.filter((v) => v.label.toLowerCase()
            .indexOf(needle) > -1);
        });
      }
    },
  },
};
</script>
