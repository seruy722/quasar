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
      :filter="search"
      :filter-method="filterMethod"
      :rows-per-page-options="[10, 20, 50, 100, 0]"
      :separator="tableProperties.separator || 'cell'"
      :hide-bottom="tableProperties.hideBottom"
      :virtual-scroll-sticky-start="48"
      :sort-method="customSort"
      class="my-sticky-virtscroll-table"
      virtual-scroll
      data-vue-component-name="Table"
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

export default {
  name: 'Table',
  components: {
    Search: defineAsyncComponent(() => import('src/components/Search.vue')),
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
        rowsPerPage: 20,
      },
      searchField: 'Все',
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
    // const el = document.querySelector('.my-sticky-virtscroll-table >.q-table__middle');
    // el.setAttribute('style', `max-height: ${document.documentElement.clientHeight - 100}px`);
    // devlog.log('el', el);
    this.searchOptionsFields = _.map(_.get(this.tableProperties, 'columns'), ({
                                                                                label,
                                                                                name,
                                                                              }) => _.assign({}, {
      label,
      value: name,
    }));
    this.$nextTick(() => {
      this.searchOptionsFields.unshift({
        label: 'Все',
        value: 'Все',
      });
    });
  },
  methods: {
    filterMethod(rows, terms, cols, cellValue) {
      const newCols = this.searchField === 'Все' ? cols : _.filter(cols, { name: this.searchField });
      devlog.log('newCols', newCols);
      const lowerTerms = terms ? terms.toLowerCase() : '';
      const result = rows.filter(
          (row) => newCols.some((col) => (`${cellValue(col, row)} `).toLowerCase()
              .indexOf(lowerTerms) !== -1),
      );
      devlog.log('resultSerarch', result);
      // this.$emit('update:searchData', result);
      return result;
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

<style lang="scss">
.my-sticky-virtscroll-table .q-table__middle {
  max-height: 385px;
}

.q-table__top .q-table__bottom thead tr:first-child th {
  background-color: #fff;
}

thead tr:first-child th {
  position: sticky;
  top: 0;
  text-transform: uppercase;
  font-weight: bold;
  opacity: 1;
  z-index: 1;
}
</style>
