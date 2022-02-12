<template>
  <q-table
      v-model:selected="selected"
      class="my-sticky-dynamic"
      dense
      :rows="rows"
      :columns="columns"
      :loading="loading"
      row-key="id"
      virtual-scroll
      :virtual-scroll-item-size="48"
      :virtual-scroll-sticky-size-start="48"
      :grid="$q.screen.lt.sm"
      :pagination="pagination"
      :rows-per-page-options="[0]"
      selection="multiple"
      data-vue-component="NewTable"
      :selected="selected"
      @virtual-scroll="onScroll"
  >
    <template #top>
      <slot name="top-buttons" />
      <ExportBtn
          url="exportTransfers"
          title="Переводы"
          :ids="selected.map((item)=>item.id)"
      />
      <SearchDialog :columns="columns" />

      <DialogChooseDate2
          :values="selected"
      />

    </template>

    <template #item="props">
      <slot
          name="inner-item"
          :props="props"
      />
    </template>

    <template #body="props">
      <slot
          name="inner-body"
          :props="props"
      />
    </template>
  </q-table>
</template>

<script>
import {
  ref,
  nextTick,
  onMounted,
} from 'vue';
import { useStore } from 'vuex';
import { axiosInstance } from 'boot/axios';
import SearchDialog from 'src/components/Search/SearchDialog.vue';
import ExportBtn from 'src/components/Buttons/ExportBtn.vue';
import DialogChooseDate2 from 'src/components/Dialogs/DialogChooseDate2.vue';

export default {
  name: 'NewTable',
  components: {
    SearchDialog,
    ExportBtn,
    DialogChooseDate2,
  },
  props: {
    columns: {
      type: Array,
      require: true,
      default: () => [],
    },
    rows: {
      type: Array,
      require: true,
      default: () => [],
    },
  },
  setup(props) {
    const selected = ref([]);
    const store = useStore();
    const loading = ref(false);

    onMounted(() => {
      if (_.isEmpty(props.rows)) {
        store.dispatch('transfers/fetchTransfers')
            .finally(() => {
              loading.value = false;
            });
      }
    });

    return {
      selected,
      loading,

      pagination: { rowsPerPage: 0 },

      onScroll(details) {
        const data = store.getters['transfers/getTransfersData'];
        const lastIndex = props.rows.length - 1;

        if (loading.value !== true && details.to === lastIndex && _.isEmpty(store.getters['transfers/getSearchData'])) {
          loading.value = true;
          if (data.next_page_url) {
            axiosInstance.get(data.next_page_url)
                .then(({ data: { transfers } }) => {
                  store.commit('transfers/ADD_TRANSFERS', transfers.data);
                  delete transfers.data;
                  store.commit('transfers/SET_TRANSFERS_DATA', transfers);
                  setTimeout(() => {
                    nextTick(() => {
                      details.ref.refresh();
                      loading.value = false;
                    });
                  }, 500);
                });
          }
        }
      },
    };
  },
};
</script>

<style lang="sass">
.my-sticky-dynamic
  height: 410px

  .q-table__top,
  .q-table__bottom,
  thead tr:first-child th
    background-color: #fff

  thead tr th
    position: sticky
    z-index: 1

  thead tr:last-child th
    top: 48px

  thead tr:first-child th
    top: 0
</style>
