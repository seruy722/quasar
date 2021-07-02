<template>
  <q-dialog
    v-model="modelData"
    :persistent="persistent"
    :maximized="maximized"
    :transition-show="transitionShow"
    :transition-hide="transitionHide"
    data-vue-component-name="Dialog"
  >
    <slot />
  </q-dialog>
</template>

<script>
export default {
  name: 'Dialog',
  props: {
    dialog: {
      type: Boolean,
      default: false,
    },
    persistent: {
      type: Boolean,
      default: false,
    },
    maximized: {
      type: Boolean,
      default: false,
    },
    transitionShow: {
      type: String,
      default: 'scale',
    },
    transitionHide: {
      type: String,
      default: 'scale',
    },
  },
  emits: ['update:dialog'],
  computed: {
    modelData: {
      get: function getValue() {
        return this.dialog;
      },
      set: function setValue(newValue) {
        this.$emit('update:dialog', newValue);
      },
    },
  },
};
</script>
