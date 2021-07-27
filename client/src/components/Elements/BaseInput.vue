<template>
  <q-input
    :dark="dark"
    :type="type"
    :label="label"
    :dense="dense"
    :autofocus="autofocus"
    :value="value"
    :error-message="viewError()"
    :error="isError"
    :no-error-icon="noErrorIcon"
    clearable
    clear-icon="clear"
    :color="color"
    :filled="filled"
    :mask="mask"
    :unmasked-value="unmaskedValue"
    :readonly="readonly"
    :disable="disable"
    bottom-slots
    :input-style="{fontWeight: 'bold'}"
    data-vue-component="BaseInput"
    @update:model-value="inputEvent"
    @keyup="$emit('on-key-up', $event)"
    @focus="focus"
  >
    <template #append>
      <slot name="append" />
    </template>

    <template #prepend>
      <slot name="prepend" />
    </template>
  </q-input>
</template>

<script>
import ViewErrorsMixin from 'src/mixins/ViewErrors';

export default {
  name: 'BaseInput',
  mixins: [ViewErrorsMixin],
  props: {
    value: {
      type: [String, Number],
      default: '',
    },
    type: {
      type: String,
      default: 'text',
    },
    color: {
      type: String,
      default: 'primary',
    },
    field: {
      type: String,
      default: '',
    },
    mask: {
      type: String,
      default: '',
    },
    label: {
      type: String,
      default: '',
    },
    dense: {
      type: Boolean,
      default: false,
    },
    unmaskedValue: {
      type: Boolean,
      default: false,
    },
    dark: {
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
    filled: {
      type: Boolean,
      default: false,
    },
    autofocus: {
      type: Boolean,
      default: false,
    },
    noErrorIcon: {
      type: Boolean,
      default: true,
    },
    changeValue: {
      type: Boolean,
      default: false,
    },
    errors: {
      type: Object,
      default: () => ({}),
    },
  },
  emits: ['update:changeValue', 'input', 'on-key-up'],
  watch: {
    value(val) {
      if (val) {
        this.changeErrors();
      }
    },
  },
  methods: {
    focus(event) {
      if (_.isFunction(_.get(event, 'target.select'))) {
        event.target.select();
      }
    },
    inputEvent($event) {
      this.$emit('input', $event);
      devlog.log('INNNNN', $event);
      this.changeErrors();
      if (!this.changeValue) {
        this.$emit('update:changeValue', true);
      }
    },
  },
};
</script>
