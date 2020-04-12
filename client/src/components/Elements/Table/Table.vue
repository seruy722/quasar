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
      :grid="$q.screen.lt.sm"
      dense
      row-key="id"
      :selected.sync="tableReactiveProperties.selected"
      :filter="search"
      :filter-method="filterMethod"
      :rows-per-page-options="[10, 20, 50, 100, 0]"
      :separator="tableProperties.separator || 'cell'"
      :hide-bottom="tableProperties.hideBottom"
      :virtual-scroll-sticky-start="48"
      :sort-method="customSort"
      class="my-sticky-virtscroll-table"
      virtual-scroll
    >
      <template
        v-if="!tableProperties.hideTop"
        v-slot:top="props"
      >
        <div class="col-2 q-table__title q-mr-lg">{{ title }}</div>

        <q-space />

<!--        <q-select-->
<!--          v-model="tableReactiveProperties.visibleColumns"-->
<!--          multiple-->
<!--          borderless-->
<!--          dense-->
<!--          options-dense-->
<!--          :display-value="$t('columns')"-->
<!--          emit-value-->
<!--          map-options-->
<!--          :options="tableProperties.columns"-->
<!--          option-value="name"-->
<!--          style="min-width: 150px"-->
<!--        />-->
<!--        <q-space />-->

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
          @iconBtnClick="props.toggleFullscreen"
        />
        <slot name="top-buttons"></slot>
      </template>

      <template v-slot:body="props">
        <slot name="inner-body" :props="props"></slot>
      </template>

      <template v-slot:item="props">
        <slot name="inner-item" :props="props"></slot>
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
            BaseSelect: () => import('src/components/Elements/BaseSelect.vue'),
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
        },
        data() {
            return {
                search: '',
                pagination: {
                    rowsPerPage: 20,
                },
                searchField: 'Все',
                searchOptionsFields: [],
            };
        },
        created() {
            // const el = document.querySelector('.my-sticky-virtscroll-table >.q-table__middle');
            // el.setAttribute('style', `max-height: ${document.documentElement.clientHeight - 100}px`);
            // devlog.log('el', el);
            this.searchOptionsFields = _.map(_.get(this.tableProperties, 'columns'), ({ label, name }) => _.assign({}, {
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
                // devlog.log('rows', rows);
                // devlog.log('cols', cols);
                // devlog.log('cellValue', cellValue);
                // devlog.log('terms', terms);
                // devlog.log('this.searchField', this.searchField === 'Все');
                const newCols = this.searchField === 'Все' ? cols : _.filter(cols, { name: this.searchField });
                devlog.log('newCols', newCols);
                const lowerTerms = terms ? terms.toLowerCase() : '';
                return rows.filter(
                  (row) => newCols.some((col) => (`${cellValue(col, row)} `).toLowerCase()
                    .indexOf(lowerTerms) !== -1),
                );
            },
            customSort(rows, sortBy, descending) {
                const data = [...rows];
                devlog.log('sortBy', sortBy);

                if (sortBy) {
                    data.sort((a, b) => {
                        const x = descending ? b : a;
                        const y = descending ? a : b;
                        // devlog.log('A', a[sortBy]);
                        // devlog.log('B', b[sortBy]);
                        // devlog.log('descending', descending);
                        if (!_.isNaN(_.toNumber(a[sortBy])) && !_.isNaN(_.toNumber(b[sortBy]))) {
                            devlog.log('STRING_SORT', _.toNumber(a[sortBy]));
                            return parseFloat(x[sortBy]) - parseFloat(y[sortBy]);
                        }
                        let num = 0;
                        if (x[sortBy] > y[sortBy]) {
                            num = 1;
                        } else if (x[sortBy] < y[sortBy]) {
                            num = -1;
                        }
                        devlog.log('NUM', num);
                        return num;
                        // return x[sortBy] > y[sortBy] ? 1 : x[sortBy] < y[sortBy] ? -1 : 0;
                    });
                }
                return data;
            },
        },
    };
</script>

<style lang="stylus">
  .my-sticky-virtscroll-table
    .q-table__middle
      max-height: 385px

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
