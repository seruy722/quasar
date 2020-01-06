<template>
  <q-checkbox
    :value="value"
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
        },
    };
</script>
