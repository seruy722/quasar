<template>
  <q-btn
      :color="color"
      :label="label"
      :push="push"
      :loading="loading"
      :disable="disable"
      no-caps
      data-vue-component-name="BaseBtn"
      @click.stop="$emit('click-base-btn')"
  />
</template>

<script>
import { defineComponent } from 'vue';

export default defineComponent({
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
    disable: {
      type: Boolean,
      default: false,
    },
  },
  emits: ['click-base-btn'],
});
</script>
