<template>
  <q-input
      dense
      debounce="500"
      clearable
      clear-icon="close"
      :value="value"
      placeholder="Поиск"
      data-vue-component-name="SearchComponent"
      @input="$emit('input', $event)"
      @focus="focusInput"
      @blur="blurInput"
  >
    <template #append>
      <q-icon
          name="search"
          :color="iconColor"
      />
    </template>
  </q-input>
</template>

<script>
import { ref } from 'vue';

export default {
  name: 'SearchComponent',
  props: {
    value: {
      type: String,
      default: '',
    },
  },
  emits: ['input'],
  setup() {
    const iconColor = ref('');

    const focusInput = (() => {
      iconColor.value = 'primary';
    });
    const blurInput = (() => {
      iconColor.value = '';
    });

    return {
      text: ref(''),
      iconColor,
      focusInput,
      blurInput,
    };
  },
};
</script>
