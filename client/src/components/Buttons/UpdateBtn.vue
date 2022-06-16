<template>
  <q-btn
      round
      color="primary"
      icon="sync"
      :loading="loading"
      data-vue-component-name="UpdateBtn"
      @click="refresh"
  >
    <q-tooltip
        anchor="bottom middle"
        self="top middle"
        :offset="[10, 10]"
    >
      Обновить
    </q-tooltip>
  </q-btn>
</template>

<script>
import { ref, defineComponent } from 'vue';

export default defineComponent({
  name: 'UpdateBtn',
  props: {
    func: {
      type: Function,
      default: null,
    },
  },
  setup(props) {
    const loading = ref(false);
    return {
      loading,
      refresh() {
        if (_.isFunction(props.func)) {
          loading.value = true;
          props.func()
              .finally(() => {
                loading.value = false;
              });
        }
      },
    };
  },
});
</script>
