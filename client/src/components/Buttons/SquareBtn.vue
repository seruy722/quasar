<template>
  <q-btn
      :color="color || 'primary'"
      :label="label"
      :icon="icon"
      :loading="loading"
      data-vue-component-name="SquareBtn"
      @click.stop="$emit('square-btn-click')"
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
export default {
  name: 'SquareBtn',
  props: {
    color: {
      type: String,
      default: '',
    },
    label: {
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
    loading: {
      type: Boolean,
      default: false,
    },
  },
  emits: ['square-btn-click'],
};
</script>
