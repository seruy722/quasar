<template>
  <Icon
    name="event"
    class="cursor-pointer"
    size="md"
    data-vue-component-name="DateComponent"
  >
    <q-popup-proxy
      ref="qDateProxy"
      transition-show="scale"
      transition-hide="scale"
    >
      <q-date
        v-model="valueData"
        today-btn
        mask="DD-MM-YYYY"
        @update:model-value="$refs.qDateProxy.hide()"
      />
    </q-popup-proxy>
  </Icon>
</template>

<script>
import Icon from 'src/components/Elements/Icon.vue';
import { computed } from 'vue';

export default {
  name: 'DateComponent',
  components: {
    Icon,
  },
  props: {
    value: {
      type: String,
      default: '',
    },
    changeValue: {
      type: Boolean,
      default: false,
    },
  },
  emits: ['update:changeValue', 'update:value'],
  setup(props, { emit }) {
    const valueData = computed({
      get: () => props.value,
      set: (val) => {
        emit('update:value', val);
        emit('update:changeValue', true);
      },
    });

    return { valueData };
  },
};
</script>
