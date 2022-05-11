<template>
  <q-select
      v-model="model"
      :options="options"
      data-vue-component-name="PaidFieldComponent"
      :dense="$q.screen.xs || $q.screen.sm"
      label="Оплачен"
      emit-value
      map-options
  />
</template>

<script>
import { ref, watch } from 'vue';

export default {
  name: 'PaidFieldComponent',
  props: {
    values: {
      type: Object,
      default: () => ({}),
    },
    field: {
      type: String,
      default: null,
    },
  },
  setup(props) {
    const model = ref(null);
    watch(model, (currentValue) => {
      _.set(props, `values.${props.field}`, _.toNumber(currentValue));
    });

    if (props.values[props.field]) {
      model.value = _.clone(props.values[props.field]);
    } else {
      _.set(props, `values.${props.field}`, model.value);
    }

    return {
      model,
      options: [
        {
          label: 'Да',
          value: 1,
        },
        {
          label: 'Нет',
          value: 0,
        },
      ],
    };
  },
};
</script>
