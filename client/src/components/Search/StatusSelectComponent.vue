<template>
  <div data-vue-component-name="StatusSelectComponent">
    <SearchSelect
        v-model="model"
        label="Выберите статус"
        :dense="$q.screen.xs || $q.screen.sm"
        :func-load-data="fetchClientList"
        :options="clientsOptions"
    />
  </div>
</template>
<script>
import { defineAsyncComponent, ref, computed, watch } from 'vue';
import { useStore } from 'vuex';

export default {
  name: 'StatusSelectComponent',
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
    const model = ref(null);
    const store = useStore();
    const clientsOptions = computed(() => store.getters['statuses/getStatuses']);
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
        return _.isEmpty(clientsOptions.value) ? store.dispatch('statuses/fetchStatuses') : new Promise(() => {
        });
      },
    };
  },
};
</script>
