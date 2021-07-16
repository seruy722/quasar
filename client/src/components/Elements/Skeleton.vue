<template>
  <q-skeleton
    data-vue-component-name="Skeleton"
    :type="type"
    :tag="tag"
    :animation="animation"
    :width="width"
  />
</template>

<script>
export default {
  name: 'Skeleton',
  props: {
    type: {
      type: String,
      default: 'rect',
    },
    tag: {
      type: String,
      default: 'div',
    },
    animation: {
      type: String,
      default: 'wave',
    },
    width: {
      type: String,
      default: '',
    },
  },
};
</script>
