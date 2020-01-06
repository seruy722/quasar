<template>
  <q-btn
    :color="color"
    :label="label"
    :push="push"
    data-vue-component-name="BaseBtn"
    @click="$emit('clickBaseBtn')"
  />
</template>

<script>
    export default {
        name: 'BaseBtn',
        props: {
            color: {
                type: String,
                default: 'primary',
            },
            label: {
                type: String,
                default: 'Ok',
            },
            push: {
                type: Boolean,
                default: false,
            },
        },
    };
</script>
