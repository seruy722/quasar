<template>
  <q-page-sticky
    :position="position"
    :offset="offset"
    data-vue-component-name="PageSticky"
  >
    <slot />
  </q-page-sticky>
</template>

<script>
export default {
  name: 'PageSticky',
  props: {
    position: {
      type: String,
      default: 'bottom-right',
    },
    offset: {
      type: Array,
      default: () => [18, 18],
    },
  },
};
</script>
