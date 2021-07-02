<template>
  <q-btn
    :color="color"
    :label="label"
    :push="push"
    :loading="loading"
    no-caps
    data-vue-component-name="BaseBtn"
    @click.stop="$emit('click-base-btn')"
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
    loading: {
      type: Boolean,
      default: false,
    },
  },
  emits: ['click-base-btn'],
};
</script>
