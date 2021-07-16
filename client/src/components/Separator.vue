<template>
  <q-separator
    data-vue-component-name="Separator"
    :color="color"
  />
</template>

<script>
export default {
  name: 'Separator',
  props: {
    color: {
      type: String,
      default: 'grey',
    },
  },
};
</script>
