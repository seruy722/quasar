<template>
  <q-select
    :filled="filled"
    :options="options"
    :label="label"
    :error-message="viewError()"
    :error="isError"
    :value="value"
    :dense="dense"
    :options-dense="optionsDense"
    :no-error-icon="noErrorIcon"
    data-vue-component-name="BaseSelect"
    emit-value
    map-options
    @input="inputEvent"
  >
    <template #prepend>
      <slot name="prepend" />
    </template>
  </q-select>
</template>

<script>
    import ErrorsServerMixin from 'src/mixins/ViewErrors';

    export default {
        name: 'BaseSelect',
        mixins: [ErrorsServerMixin],
        props: {
            value: {
                type: [String, Number],
                default: 0,
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
            optionsDense: {
                type: Boolean,
                default: false,
            },
            noErrorIcon: {
                type: Boolean,
                default: true,
            },
            filled: {
                type: Boolean,
                default: false,
            },
            changeValue: {
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
        methods: {
            inputEvent($event) {
                this.$emit('input', $event);
                this.changeErrors();
                if (!this.changeValue) {
                    this.$emit('update:changeValue', true);
                }
            },
        },
    };
</script>
