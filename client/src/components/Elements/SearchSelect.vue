<template>
  <q-select
    v-model="modelValue"
    :options="duplicateOptions"
    :label="label"
    :error-message="viewError()"
    :error="isError"
    :dense="dense"
    :multiple="multiple"
    :clearable="clearable"
    use-input
    input-debounce="0"
    :behavior="$q.platform.is.ios ? 'dialog' : 'menu'"
    data-vue-component-name="SearchSelect"
    emit-value
    map-options
    @filter="filterFn"
    @update:model-value="$emit('change', $event)"
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
  emits: ['update:changeValue', 'input', 'update:value', 'change'],
  data() {
    return {
      duplicateOptions: [],
    };
  },
  computed: {
    modelValue: {
      get: function get() {
        return this.value;
      },
      set: function set(val) {
        this.$emit('update:value', val);
        this.inputEvent();
      },
    },
  },
  created() {
    this.duplicateOptions = this.options;
  },
  methods: {
    inputEvent() {
      this.changeErrors();
      if (!this.changeValue) {
        this.$emit('update:changeValue', true);
      }
    },
    filterFn(val, update) {
      if (!_.isEmpty(this.options) && !val) {
        update(() => {
          this.duplicateOptions = this.options;
        });
      } else if (_.isEmpty(this.options) && _.isFunction(this.funcLoadData)) {
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
