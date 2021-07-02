<template>
  <q-btn
    flat
    round
    :color="color"
    :icon="icon"
    :disable="disable"
    :size="size"
    :dense="dense"
    data-vue-component-name="IconBtn"
    @click="$emit('icon-btn-click')"
  >
    <q-tooltip>{{ tooltip }}</q-tooltip>
  </q-btn>
</template>

<script>
export default {
  name: 'IconBtn',
  props: {
    color: {
      type: String,
      default: 'primary',
    },
    icon: {
      type: String,
      default: 'add_box',
    },
    disable: {
      type: Boolean,
      default: false,
    },
    dense: {
      type: Boolean,
      default: false,
    },
    tooltip: {
      type: String,
      default: 'add',
    },
    size: {
      type: String,
      default: 'md',
    },
  },
  emits: ['icon-btn-click'],
};
</script>
