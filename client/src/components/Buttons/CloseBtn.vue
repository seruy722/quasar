<template>
  <q-btn
      :color="color || 'negative'"
      :icon="icon"
      data-vue-component-name="SquareBtn"
      @click.stop="$emit('close-btn-click')"
  >
    <q-tooltip
        v-if="tooltip"
        anchor="bottom middle"
        self="top middle"
        :offset="[10, 10]"
    >
      {{ tooltip }}
    </q-tooltip>
    <slot />
  </q-btn>
</template>

<script>
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'SquareBtn',
  props: {
    color: {
      type: String,
      default: '',
    },
    icon: {
      type: String,
      default: '',
    },
    tooltip: {
      type: String,
      default: '',
    },
  },
  emits: ['close-btn-click'],
});
</script>
