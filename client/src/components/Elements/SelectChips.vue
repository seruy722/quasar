<template>
  <q-select
    use-input
    use-chips
    input-debounce="0"
    :value="value"
    :dense="dense"
    :options="filterOptions"
    :label="label"
    :input-style="{fontWeight: 'bold'}"
    :error-message="viewError()"
    :error="isError"
    :readonly="readonly"
    :disable="disable"
    data-vue-component-name="SelectChips"
    @new-value="createValue"
    @filter="filterFn"
    @input="inputEvent"
  />
</template>

<script>
import ErrorsServerMixin from 'src/mixins/ViewErrors';

export default {
  name: 'SelectChips',
  mixins: [ErrorsServerMixin],
  props: {
    value: {
      type: [String, Number],
      default: 0,
    },
    label: {
      type: String,
      default: '',
    },
    options: {
      type: Array,
      default: () => [],
    },
    dense: {
      type: Boolean,
      default: false,
    },
    changeValue: {
      type: Boolean,
      default: false,
    },
    readonly: {
      type: Boolean,
      default: false,
    },
    disable: {
      type: Boolean,
      default: false,
    },
    field: {
      type: String,
      default: '',
    },
    funcLoadData: {
      type: Function,
      default: () => {
      },
    },
    errors: {
      type: Object,
      default: () => ({}),
    },
  },
  emits: ['update:changeValue', 'input'],
  data() {
    return {
      filterOptions: [],
    };
  },
  created() {
    this.filterOptions = this.options;
  },
  methods: {
    inputEvent($event) {
      this.$emit('input', $event);
      this.changeErrors();
      if (!this.changeValue) {
        this.$emit('update:changeValue', true);
      }
    },
    createValue(val, done) {
      devlog.log('createValue', val);
      if (val.length > 0) {
        if (!this.filterOptions.includes(val)) {
          this.filterOptions.push(val);
        }
        done(val, 'toggle');
      }
    },

    filterFn(val, update) {
      if (!_.isEmpty(this.options) && !val) {
        update(() => {
          this.filterOptions = this.options;
        });
      } else if (_.isEmpty(this.options) && _.isFunction(this.funcLoadData)) {
        devlog.log('E_2');
        this.funcLoadData(this.$store)
          .then(() => {
            update(() => {
              this.filterOptions = this.options;
            });
          });
      } else {
        update(() => {
          const needle = val.toLowerCase();
          this.filterOptions = this.options.filter((v) => v.toLowerCase()
            .indexOf(needle) > -1);
        });
      }
    },
  },
};
</script>
