<template>
  <q-table
      v-model:selected="selected"
      class="my-sticky-dynamic"
      dense
      :rows="rows"
      :columns="columns"
      :loading="loading"
      row-key="id"
      :pagination="pagination"
      :rows-per-page-options="[0]"
      selection="multiple"
      data-vue-component="NewTable"
      :grid="$q.screen.xs || $q.screen.sm || grid"
      :style="`height: ${tableHeight}px`"
      :selected="selected"
  >
    <template #top>
      <slot name="top-buttons" />
      <div class="row q-gutter-sm q-ml-sm">
        <ExportBtn
            url="exportTransfers"
            title="Переводы"
            :ids="selected.map((item)=>item.id)"
        />
        <SearchDialog
            v-model:loading="loading"
            :columns="columns"
        />

        <DialogChooseDate2
            v-model:values="selected"
        />
      </div>
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

    <template #loading>
      <q-inner-loading
          showing
          color="primary"
      />
    </template>
  </q-table>
</template>

<script>
import {
  ref,
  onMounted,
  computed,
  defineComponent,
} from 'vue';
import { useStore } from 'vuex';
import SearchDialog from 'src/components/Search/SearchDialog.vue';
import ExportBtn from 'src/components/Buttons/ExportBtn.vue';
import DialogChooseDate2 from 'src/components/Dialogs/DialogChooseDate2.vue';
import { useQuasar } from 'quasar';

export default defineComponent({
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
    grid: {
      type: Boolean,
      default: false,
    },
  },
  setup(props) {
    const selected = ref([]);
    const store = useStore();
    const loading = ref(false);
    const $q = useQuasar();

    onMounted(() => {
      if (_.isEmpty(props.rows)) {
        loading.value = true;
        store.dispatch('transfers/fetchTransfers')
            .finally(() => {
              loading.value = false;
            });
      }
    });
    const tableHeight = computed(() => $q.screen.height - 50);

    return {
      selected,
      loading,
      tableHeight,

      pagination: { rowsPerPage: 50 },
    };
  },
});
</script>

<style lang="sass">
.my-sticky-dynamic
  .q-table__grid-content
    height: 100px
    overflow: auto

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
