<template>
  <q-fab-action
    :color="color"
    :icon="icon"
    data-vue-component-name="FabAction"
    @click="$emit('fab-action-click')"
  />
</template>

<script>
export default {
  name: 'FabAction',
  props: {
    color: {
      type: String,
      default: 'primary',
    },
    icon: {
      type: String,
      default: 'add',
    },
  },
  emits: ['fab-action-click'],
};
</script>
