<template>
  <q-dialog
      v-model="modelData"
      :persistent="persistent"
      :maximized="maximized"
      :transition-show="transitionShow"
      :transition-hide="transitionHide"
      data-vue-component-name="DialogComponent"
  >
    <slot />
  </q-dialog>
</template>

<script>
import { computed } from 'vue';

export default {
  name: 'DialogComponent',
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
  setup(props, { emit }) {
    const modelData = computed({
      get: function getValue() {
        return props.dialog;
      },
      set: function setValue(newValue) {
        emit('update:dialog', newValue);
      },
    });

    return {
      modelData,
    };
  },
};
</script>
