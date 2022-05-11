<template>
  <div data-vue-component-name="ClientsSelectComponent">
    <SearchSelect
        v-model="model"
        label="Выберите код клиента"
        :dense="$q.screen.xs || $q.screen.sm"
        :func-load-data="fetchClientList"
        :options="clientsOptions"
    />
  </div>
</template>
<script>
import {
  defineAsyncComponent,
  ref,
  computed,
  watch,
} from 'vue';
import { useStore } from 'vuex';

export default {
  name: 'ClientsSelectComponent',
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
    const store = useStore();
    const model = ref(null);
    const clientsOptions = computed(() => store.getters['codes/getCodes']);
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
      fetchClientList() {
        return _.isEmpty(clientsOptions.value) ? store.dispatch('codes/setCodes') : new Promise(() => {
        });
      },
    };
  },
};
</script>
