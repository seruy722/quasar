<template>
  <q-list
    :dense="dense"
    :bordered="bordered"
    :padding="padding"
    :separator="separator"
    data-vue-component-name="List"
    @click.stop="$emit('click-list')"
  >
    <slot />
  </q-list>
</template>

<script>
export default {
  name: 'List',
  props: {
    dense: {
      type: Boolean,
      default: false,
    },
    bordered: {
      type: Boolean,
      default: false,
    },
    padding: {
      type: Boolean,
      default: false,
    },
    separator: {
      type: Boolean,
      default: false,
    },
  },
  emits: ['click-list'],
};
</script>
