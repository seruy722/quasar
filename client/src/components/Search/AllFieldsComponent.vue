<template>
  <q-input
      v-model="model"
      :dense="$q.screen.xs || $q.screen.sm"
      data-vue-component-name="AllFieldsComponent"
      label="По всем полям"
      autofocus
  />
</template>
<script>
import { ref, watch } from 'vue';

export default {
  name: 'AllFieldsComponent',
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
      _.set(props, `values.${field}`, currentValue);
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
