<template>
  <div data-vue-component-name="UserSelectComponent">
    <SearchSelect
        v-model="model"
        label="Выберите пользователя"
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
  name: 'UserSelectComponent',
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
    const clientsOptions = computed(() => store.getters['auth/getUsersList']);
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
        return _.isEmpty(clientsOptions.value) ? store.dispatch('auth/fetchUsersList') : new Promise(() => {
        });
      },
    };
  },
};
</script>
