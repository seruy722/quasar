<template>
  <div
      data-vue-component-name="SearchDialog"
  >
    <RoundBtn
        color="primary"
        icon="search"
        tooltip="Поиск"
        :loading="loading"
        @round-btn-click="showDialog = true"
    />
    <RoundBtn
        v-show="searchValues[searchField]"
        color="negative"
        icon="clear"
        tooltip="Очистить"
        @round-btn-click="clearSearchData"
    />
    <DialogComponent
        :dialog="showDialog"
        :persistent="true"
        @keyup.enter="filterMethod(searchValues)"
    >
      <q-card style="min-width: 320px;width: 100%;max-width: 500px;">
        <q-card-section class="bg-grey q-mb-sm">
          <BaseSelect
              v-model="searchField"
              label="В каком поле искать"
              style="min-width: 210px;padding-bottom: 0;"
              dense
              options-dense
              :options="searchOptionsFields"
          />

          <component
              :is="getComponent"
              :values="searchValues"
              :field="searchField"
          />
        </q-card-section>

        <Separator />

        <q-card-actions align="right">
          <BaseBtn
              label="Очистить"
              color="secondary"
              :dense="$q.screen.xs || $q.screen.sm"
              @click-base-btn="clearSearchData"
          />

          <BaseBtn
              label="Закрыть"
              color="negative"
              :dense="$q.screen.xs || $q.screen.sm"
              @click-base-btn="showDialog = false"
          />

          <BaseBtn
              label="Найти"
              color="positive"
              :dense="$q.screen.xs || $q.screen.sm"
              @click-base-btn="filterMethod(searchValues)"
          />
        </q-card-actions>
      </q-card>
    </DialogComponent>
  </div>
</template>

<script>
import { defineAsyncComponent, ref, computed } from 'vue';
import { getUrl } from 'src/tools/url';
import { axiosInstance } from 'boot/axios';
import { useStore } from 'vuex';

export default {
  name: 'SearchDialog',
  components: {
    DialogComponent: defineAsyncComponent(() => import('src/components/Dialogs/DialogComponent.vue')),
    BaseBtn: defineAsyncComponent(() => import('src/components/Buttons/BaseBtn.vue')),
    Separator: defineAsyncComponent(() => import('src/components/Separator.vue')),
    BaseSelect: defineAsyncComponent(() => import('src/components/Elements/BaseSelect.vue')),
    RoundBtn: defineAsyncComponent(() => import('src/components/Buttons/RoundBtn.vue')),
    ClientsSelectComponent: defineAsyncComponent(() => import('src/components/Search/ClientsSelectComponent.vue')),
    StatusSelectComponent: defineAsyncComponent(() => import('src/components/Search/StatusSelectComponent.vue')),
    DateComponent: defineAsyncComponent(() => import('src/components/Search/DateComponent.vue')),
    DateComponent2: defineAsyncComponent(() => import('src/components/Search/DateComponent2.vue')),
    MethodsSelectComponent: defineAsyncComponent(() => import('src/components/Search/MethodsSelectComponent.vue')),
    SumComponent: defineAsyncComponent(() => import('src/components/Search/SumComponent.vue')),
    PaidFieldComponent: defineAsyncComponent(() => import('src/components/Search/PaidFieldComponent.vue')),
    NotationFieldComponent: defineAsyncComponent(() => import('src/components/Search/NotationFieldComponent.vue')),
    AllFieldsComponent: defineAsyncComponent(() => import('src/components/Search/AllFieldsComponent.vue')),
    ReceiverNameFieldComponent: defineAsyncComponent(() => import('src/components/Search/ReceiverNameFieldComponent.vue')),
    ReceiverPhoneFieldComponent: defineAsyncComponent(() => import('src/components/Search/ReceiverPhoneFieldComponent.vue')),
  },
  props: {
    loading: {
      type: Boolean,
      default: false,
    },
    columns: {
      type: Array,
      default: () => ([]),
    },
  },
  emits: ['update:loading'],
  setup(props, { emit }) {
    const showDialog = ref(false);
    const store = useStore();
    const searchField = ref('all');
    const optionsFields = _.map(props.columns, ({
                                                  label,
                                                  name,
                                                }) => _.assign({}, {
      label,
      value: name,
    }));
    optionsFields.unshift({ label: 'По всем полям', value: 'all' });
    const searchOptionsFields = optionsFields;
    const searchValues = {
      all: null,
      client_name: null,
      status: null,
      issued_by: null,
      created_at: null,
      method: null,
      sum: null,
      paid: null,
      notation: null,
      receiver_name: null,
      receiver_phone: null,
      user_name: null,
    };

    const filterMethod = ((val) => {
      if (val.paid === 0 || !_.isEmpty(_.pickBy(val, (v) => !!v))) {
        showDialog.value = false;
        emit('update:loading', true);
        const { value } = searchField;
        axiosInstance.post(getUrl('transfersSearch'), {
          value: val[value],
          field: value,
        })
            .then(({ data: { transfers } }) => {
              store.dispatch('transfers/setTransfers', transfers);
              store.commit('transfers/SET_TRANSFERS_DATA', {});
              emit('update:loading', false);
            });
      } else {
        devlog.log('Нет данных');
      }
      devlog.log('VVV', val);
    });

    const clearSearchData = (() => {
      searchField.value = 'all';
      searchValues[searchField.value] = null;
      emit('update:loading', true);
      showDialog.value = false;
      store.dispatch('transfers/fetchTransfers')
          .finally(() => {
            emit('update:loading', false);
          });
    });

    const getComponent = computed(() => {
      if (searchField.value === 'client_name') {
        return 'ClientsSelectComponent';
      }
      if (searchField.value === 'status') {
        return 'StatusSelectComponent';
      }
      if (searchField.value === 'issued_by') {
        return 'DateComponent';
      }
      if (searchField.value === 'created_at') {
        return 'DateComponent2';
      }
      if (searchField.value === 'method') {
        return 'MethodsSelectComponent';
      }
      if (searchField.value === 'sum') {
        return 'SumComponent';
      }
      if (searchField.value === 'paid') {
        return 'PaidFieldComponent';
      }
      if (searchField.value === 'notation') {
        return 'NotationFieldComponent';
      }
      if (searchField.value === 'all') {
        return 'AllFieldsComponent';
      }
      if (searchField.value === 'receiver_name') {
        return 'ReceiverNameFieldComponent';
      }
      if (searchField.value === 'receiver_phone') {
        return 'ReceiverPhoneFieldComponent';
      }
      return '';
    });

    return {
      searchField,
      searchOptionsFields,
      filterMethod,
      showDialog,
      clearSearchData,
      getComponent,
      searchValues,
      key(ev) {
        devlog.log('ev', ev);
      },
    };
  },
};
</script>
