<template>
  <q-table
      v-model:pagination="pagination"
      v-model:selected="selected"
      :rows="tableData"
      :columns="tableProperties.columns"
      :visible-columns="tableReactiveProperties.visibleColumns"
      :selection="tableProperties.selection || 'multiple'"
      :grid="$q.screen.lt.sm || grid"
      :loading="loading"
      dense
      row-key="id"
      :rows-per-page-options="[0]"
      :separator="tableProperties.separator || 'cell'"
      :hide-bottom="tableProperties.hideBottom"
      :virtual-scroll-item-size="48"
      :virtual-scroll-sticky-start="48"
      :sort-method="customSort"
      class="my-sticky-dynamic"
      virtual-scroll
      data-vue-component-name="TableVirtualScrolling"
      @virtual-scroll="onScroll"
  >
    <template
        v-if="!tableProperties.hideTop"
        #top="props"
    >
      <div class="col-4 q-mr-md text-bold">
        {{ title }}
      </div>

      <q-space />

      <Search v-model="search" />

      <BaseSelect
          v-model="searchField"
          label="Поле"
          style="min-width: 110px;padding-bottom: 0;"
          dense
          options-dense
          :options="searchOptionsFields"
      />

      <q-btn @click="filterMethod(search)">Найти</q-btn>

      <q-space />
      <IconBtn
          :icon="props.inFullscreen ? 'fullscreen_exit' : 'fullscreen'"
          :tooltip="$t(props.inFullscreen ? 'hide' : 'reveal')"
          @icon-btn-click="props.toggleFullscreen"
      />
      <slot name="top-buttons" />
    </template>

    <template #body="props">
      <slot
          name="inner-body"
          :props="props"
      />
    </template>

    <template #item="props">
      <slot
          name="inner-item"
          :props="props"
      />
    </template>

    <template #bottom-row>
      <q-tr>
        <q-td colspan="100%">
          <slot name="inner-bottom-row" />
        </q-td>
      </q-tr>
    </template>
  </q-table>
</template>

<script>
import { defineAsyncComponent } from 'vue';
import { sortSuper } from 'src/utils/sort.js';
import { debounce } from 'quasar';
import { getUrl } from 'src/tools/url';

export default {
  name: 'TableVirtualScrolling',
  components: {
    Search: defineAsyncComponent(() => import('src/components/Search/Search.vue')),
    IconBtn: defineAsyncComponent(() => import('src/components/Buttons/IconBtn.vue')),
    BaseSelect: defineAsyncComponent(() => import('src/components/Elements/BaseSelect.vue')),
  },
  props: {
    title: {
      type: String,
      default: '',
    },
    tableProperties: {
      type: Object,
      default: () => ({}),
    },
    tableReactiveProperties: {
      type: Object,
      default: () => ({}),
    },
    tableData: {
      type: Array,
      default: () => [],
    },
    searchData: {
      type: Array,
      default: () => [],
    },
    grid: {
      type: Boolean,
      default: false,
    },
    dataSearch: {
      type: Object,
      default: () => ({}),
    },
    loading: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      search: null,
      pagination: {
        rowsPerPage: 0,
      },
      searchField: '',
      searchOptionsFields: [],
    };
  },
  computed: {
    selected: {
      get: function get() {
        return this.tableReactiveProperties.selected;
      },
      set: function set(val) {
        _.set(this.tableReactiveProperties, 'selected', val);
      },
    },
  },
  created() {
    devlog.log('dataSearch', this.dataSearch);
    if (!_.isEmpty(this.dataSearch)) {
      this.search = this.dataSearch.value;
      this.searchField = this.dataSearch.field;
    }
    this.searchOptionsFields = _.map(_.get(this.tableProperties, 'columns'), ({
                                                                                label,
                                                                                name,
                                                                              }) => _.assign({}, {
      label,
      value: name,
    }));

    this.onScroll = debounce(this.onScroll, 300);
  },
  methods: {
    onScroll({ to, ref }) {
      const lastIndex = this.tableData.length - 1;
      if (this.loading !== true && lastIndex > 0 && to === lastIndex && !this.search) {
        _.set(this, 'loading', true);
        const data = this.$store.getters['transfers/getTransfersData'];
        if (data.next_page_url) {
          this.$axios.get(data.next_page_url)
              .then(({ data: { transfers } }) => {
                this.$store.commit('transfers/ADD_TRANSFERS', transfers.data);
                delete transfers.data;
                this.$store.commit('transfers/SET_TRANSFERS_DATA', transfers);
                setTimeout(() => {
                  this.$nextTick(() => {
                    ref.refresh();
                    _.set(this, 'loading', false);
                  });
                }, 500);
                devlog.log('transfers', transfers);
              });
        }
      }
    },
    filterMethod(val) {
      if (val) {
        _.set(this, 'loading', true);
        devlog.log('this.searchOptionsFields', _.get(this.tableProperties, 'columns'));
        this.$axios.post(getUrl('transfersSearch'), {
          value: _.toLower(val),
          field: this.searchField,
        })
            .then(({ data: { transfers } }) => {
              this.$store.commit('transfers/SET_TRANSFERS', transfers);
              devlog.log('transfers_S', transfers);
              _.set(this, 'loading', false);
            });
      }
    },
    customSort(rows, sortBy, descending) {
      const data = [...rows];
      devlog.log('sortBy', sortBy);

      if (sortBy) {
        sortSuper(data, sortBy, descending);
      }
      return data;
    },
  },
};
</script>

<style lang="sass">
.my-sticky-dynamic
  /* height or max-height is important */
  height: 385px

  .q-table__top,
  .q-table__bottom,
  thead tr:first-child th /* bg color is important for th; just specify one */
    background-color: #fff

  thead tr th
    position: sticky
    z-index: 1
  /* this will be the loading indicator */
  thead tr:last-child th
    /* height of all previous header rows */
    top: 48px
  thead tr:first-child th
    top: 0
</style>
