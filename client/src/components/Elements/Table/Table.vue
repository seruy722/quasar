<template>
  <div
    data-vue-component-name="Table"
  >
    <q-table
      :data="tableData"
      :columns="tableProperties.columns"
      :visible-columns="tableReactiveProperties.visibleColumns"
      :pagination.sync="pagination"
      :selection="tableProperties.selection || 'multiple'"
      :grid="$q.screen.xs"
      dense
      row-key="id"
      :selected.sync="tableReactiveProperties.selected"
      :filter="search"
      :filter-method="filterMethod"
      :rows-per-page-options="[10, 20, 50, 100, 0]"
      :separator="tableProperties.separator || 'cell'"
      :hide-bottom="tableProperties.hideBottom"
      :virtual-scroll-sticky-start="48"
      class="my-sticky-virtscroll-table"
      virtual-scroll
    >
      <template
        v-if="!tableProperties.hideTop"
        v-slot:top="props"
      >
        <div class="col-2 q-table__title">{{ title }}</div>

        <q-space />

        <q-select
          v-model="tableReactiveProperties.visibleColumns"
          multiple
          borderless
          dense
          options-dense
          :display-value="$t('columns')"
          emit-value
          map-options
          :options="tableProperties.columns"
          option-value="name"
          style="min-width: 150px"
        />
        <q-space />

        <Search v-model="search" />

        <q-space />
        <IconBtn
          :icon="props.inFullscreen ? 'fullscreen_exit' : 'fullscreen'"
          :tooltip="$t(props.inFullscreen ? 'hide' : 'reveal')"
          @iconBtnClick="props.toggleFullscreen"
        />
        <slot name="top-buttons"></slot>
      </template>

      <template v-slot:body="props">
        <slot name="inner-body" :props="props"></slot>
      </template>

      <template v-slot:bottom-row>
        <q-tr>
          <q-td colspan="100%">
            <slot name="inner-bottom-row"></slot>
          </q-td>
        </q-tr>
      </template>
    </q-table>
  </div>
</template>

<script>
    export default {
        name: 'Table',
        components: {
            Search: () => import('src/components/Search.vue'),
            IconBtn: () => import('src/components/Buttons/IconBtn.vue'),
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
            selected: {
                type: Array,
                default: () => [],
            },
        },
        data() {
            return {
                search: '',
                pagination: {
                    rowsPerPage: 20,
                },
            };
        },
        created() {
            devlog.log('LANG_TA', this.$q.lang);
        },
        methods: {
            filterMethod(rows, terms, cols, cellValue) {
                // devlog.log('rows', rows);
                // devlog.log('cols', cols);
                // devlog.log('cellValue', cellValue);
                // devlog.log('terms', terms);
                const lowerTerms = terms ? terms.toLowerCase() : '';
                return rows.filter(
                  (row) => cols.some((col) => (`${cellValue(col, row)} `).toLowerCase()
                    .indexOf(lowerTerms) !== -1),
                );
            },
        },
    };
</script>

<style lang="stylus">
  .my-sticky-virtscroll-table
    .q-table__middle
      max-height: 600px

    .q-table__top,
    .q-table__bottom,
    thead tr:first-child th
      background-color: #fff

    thead tr:first-child th
      position: sticky
      top: 0
      text-transform uppercase
      font-weight: bold
      opacity: 1
      z-index: 1
</style>
