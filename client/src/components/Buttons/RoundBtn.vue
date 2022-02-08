<template>
  <q-btn
      round
      :color="color || 'primary'"
      :label="label"
      :icon="icon"
      :loading="loading"
      data-vue-component-name="RoundBtn"
      @click.stop="$emit('round-btn-click')"
  >
    <q-tooltip
        v-if="tooltip"
        anchor="bottom middle"
        self="top middle"
        :offset="[10, 10]"
    >
      {{ tooltip }}
    </q-tooltip>
  </q-btn>
</template>

<script>
export default {
  name: 'RoundBtn',
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
  emits: ['round-btn-click'],
};
</script>
