<template>
  <div
    data-vue-component-name="RoundBtn"
  >
    <q-btn
      unelevated
      rounded
      :color="roundBtnData.color || 'primary'"
      :label="roundBtnData.label"
      @click.stop="$emit('round-btn-click')"
    />
  </div>
</template>

<script>
export default {
  name: 'RoundBtn',
  props: {
    roundBtnData: {
      type: Object,
      default: () => ({}),
    },
  },
};
</script>
