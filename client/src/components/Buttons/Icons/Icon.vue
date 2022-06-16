<template>
  <q-icon
      data-vue-component-name="IconComponent"
      :size="size"
      :name="name"
      @click.stop="$emit('icon-click')"
  >
    <q-tooltip>{{ tooltip }}</q-tooltip>
  </q-icon>
</template>

<script>
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'IconComponent',
  props: {
    name: {
      type: String,
      default: 'done_outline',
    },
    size: {
      type: String,
      default: 'xs',
    },
    tooltip: {
      type: String,
      default: 'reveal',
    },
  },
  emits: ['icon-click'],
});
</script>
