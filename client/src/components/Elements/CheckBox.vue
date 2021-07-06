<template>
  <q-checkbox
    :value="value"
    :label="label"
    dense
    data-vue-component-name="CheckBox"
    @input="$emit('input', $event)"
  />
</template>

<script>
    export default {
        name: 'CheckBox',
        props: {
            value: {
                type: Boolean,
                default: false,
            },
            label: {
                type: String,
                default: '',
            },
        },
    };
</script>
