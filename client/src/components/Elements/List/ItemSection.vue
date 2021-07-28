<template>
  <q-item-section
    :side="side"
    :avatar="avatar"
    :top="top"
    data-vue-component-name="ItemSection"
  >
    <slot />
  </q-item-section>
</template>

<script>
export default {
  name: 'ItemSection',
  props: {
    side: {
      type: Boolean,
      default: false,
    },
    avatar: {
      type: Boolean,
      default: false,
    },
    top: {
      type: Boolean,
      default: false,
    },
  },
};
</script>
