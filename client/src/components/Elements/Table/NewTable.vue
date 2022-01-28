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
      :pagination="pagination"
      :rows-per-page-options="[0]"
      selection="multiple"
      data-vue-component="NewTable"
      :selected="selected"
      @selection="onSelection"
      @virtual-scroll="onScroll"
  >
    <template #body="props">
      <slot
          name="inner-body"
          :props="props"
      />
    </template>
  </q-table>
</template>

<script>
import { ref, computed, nextTick } from 'vue';
import { useStore } from 'vuex';
import { axiosInstance } from 'boot/axios';

export default {
  name: 'NewTable',
  props: {
    columns: {
      type: Array,
      require: true,
      default: () => [],
    },
  },
  setup() {
    const selected = ref([]);
    const store = useStore();
    devlog.log('store', store);
    const loading = ref(false);

    const rows = computed(() => store.getters['transfers/getTransfers']);
    devlog.log('rows', rows);

    return {
      rows,
      selected,
      loading,

      pagination: { rowsPerPage: 0 },
      onSelection(val) {
        devlog.log('onSelection', val);
      },

      getSelectedString(val) {
        devlog.log('getSelectedString', val);
      },

      onScroll({ to, ref }) {
        const lastIndex = rows.value.length - 1;

        if (loading.value !== true && to === lastIndex) {
          loading.value = true;
          const data = store.getters['transfers/getTransfersData'];
          if (data.next_page_url) {
            axiosInstance.get(data.next_page_url)
                .then(({ data: { transfers } }) => {
                  store.commit('transfers/ADD_TRANSFERS', transfers.data);
                  delete transfers.data;
                  store.commit('transfers/SET_TRANSFERS_DATA', transfers);
                  setTimeout(() => {
                    nextTick(() => {
                      ref.refresh();
                      loading.value = false;
                    });
                  }, 500);
                  devlog.log('transfers', transfers);
                });
          } else if (!data.next_page_url || data.to !== data.total) {
            store.dispatch('transfers/fetchTransfers')
                .finally(() => {
                  loading.value = false;
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
