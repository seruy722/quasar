<template>
  <div data-vue-component-name="MethodsSelectComponent">
    <SearchSelect
        v-model="model"
        label="Выберите метод"
        :dense="$q.screen.xs || $q.screen.sm"
        :options="clientsOptions"
    />
  </div>
</template>

<script>
import getFromSettings from 'src/tools/settings';
import { defineAsyncComponent, ref, watch } from 'vue';

export default {
  name: 'MethodsSelectComponent',
  components: {
    SearchSelect: defineAsyncComponent(() => import('src/components/Elements/SearchSelect.vue')),
  },
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
    const clientsOptions = getFromSettings('transferMethod');
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
      clientsOptions,
    };
  },
};
</script>
