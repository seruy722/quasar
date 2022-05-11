<template>
  <q-input
      v-model="model"
      type="number"
      label="Сумма"
      autofocus
      data-vue-component-name="SumComponent"
      :dense="$q.screen.xs || $q.screen.sm"
  />
</template>

<script>
import { ref, watch } from 'vue';

export default {
  name: 'SumComponent',
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
    const { field } = props;

    watch(model, (currentValue) => {
      _.set(props, `values.${field}`, _.toNumber(currentValue));
    });

    if (props.values[field]) {
      model.value = _.clone(props.values[field]);
    } else {
      _.set(props, `values.${field}`, model.value);
    }

    return {
      model,
    };
  },
};
</script>
