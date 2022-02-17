<template>
  <div data-vue-component-name="SearchDialog">
    <RoundBtn
        color="primary"
        icon="search"
        tooltip="Поиск"
        :loading="loading"
        @round-btn-click="showDialog = true"
    />
    <RoundBtn
        v-show="search && !loading"
        color="negative"
        icon="clear"
        tooltip="Очистить"
        @round-btn-click="clearSearchData"
    />
    <Dialog
        :dialog="showDialog"
        title="Удаление доступов"
        :persistent="true"
    >
      <q-card style="min-width: 320px;width: 100%;max-width: 500px;">
        <q-card-section class="bg-grey q-mb-sm">
          <Search
              ref="searchInput"
              v-model="search"
          />

          <BaseSelect
              v-model="searchField"
              label="В каком поле искать"
              style="min-width: 210px;padding-bottom: 0;"
              dense
              options-dense
              :options="searchOptionsFields"
          />

        </q-card-section>

        <Separator />

        <q-card-actions align="right">
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
              @click-base-btn="filterMethod(search)"
          />
        </q-card-actions>
      </q-card>
    </Dialog>
  </div>
</template>

<script>
import { defineAsyncComponent, ref, onBeforeUnmount } from 'vue';
import { getUrl } from 'src/tools/url';
import { axiosInstance } from 'boot/axios';
import { useStore } from 'vuex';

export default {
  name: 'SearchDialog',
  components: {
    Dialog: defineAsyncComponent(() => import('src/components/Dialogs/Dialog.vue')),
    BaseBtn: defineAsyncComponent(() => import('src/components/Buttons/BaseBtn.vue')),
    Separator: defineAsyncComponent(() => import('src/components/Separator.vue')),
    Search: defineAsyncComponent(() => import('src/components/Search/Search.vue')),
    BaseSelect: defineAsyncComponent(() => import('src/components/Elements/BaseSelect.vue')),
    RoundBtn: defineAsyncComponent(() => import('src/components/Buttons/RoundBtn.vue')),
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
    const searchInput = ref(null);
    const search = ref(null);
    const searchField = ref('');
    const searchOptionsFields = _.map(props.columns, ({
                                                        label,
                                                        name,
                                                      }) => _.assign({}, {
      label,
      value: name,
    }));

    const filterMethod = ((val) => {
      if (val) {
        showDialog.value = false;
        emit('update:loading', true);
        axiosInstance.post(getUrl('transfersSearch'), {
          value: _.toLower(val),
          field: searchField.value,
        })
            .then(({ data: { transfers } }) => {
              emit('update:loading', false);
              if (!_.isEmpty(transfers)) {
                store.dispatch('transfers/setTransfers', transfers);
                store.commit('transfers/SET_TRANSFERS_DATA', {});
              }
            });
      } else {
        searchInput.value.$el.focus();
      }
    });

    const clearSearchData = (() => {
      search.value = null;
      searchField.value = '';
      emit('update:loading', true);
      store.dispatch('transfers/fetchTransfers')
          .finally(() => {
            emit('update:loading', false);
          });
    });

    return {
      searchInput,
      search,
      searchField,
      searchOptionsFields,
      filterMethod,
      showDialog,
      clearSearchData,
    };
  },
};
</script>
