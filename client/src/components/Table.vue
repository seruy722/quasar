<template>
    <div
        data-vue-component-name="Table"
    >
        <q-table
            :data="tableDataS"
            :columns="tableData.columns"
            :visible-columns="tableData.visibleColumns"
            :pagination.sync="pagination"
            :selection="tableData.selection || 'multiple'"
            :dense="$q.screen.xs || $q.screen.sm"
            row-key="id"
            :selected.sync="selected"
            :filter="filter.value"
            :rows-per-page-options="[10, 20, 50, 100, 0]"
            separator="cell"
            :hide-bottom="tableData.hideBottom"
        >
            <template v-if="tableData.viewTop" v-slot:top="props">
                <div class="col-2 q-table__title">{{ tableData.title }}</div>

                <q-space />

                <q-select
                    v-model="tableData.visibleColumns"
                    multiple
                    borderless
                    dense
                    options-dense
                    :display-value="$t('columns')"
                    emit-value
                    map-options
                    :options="tableData.columns"
                    option-value="name"
                    style="min-width: 150px"
                />
                <q-space />

                <Search :filter="filter" />

                <q-space />
                <IconBtn
                    :iconBtnData="{
                        icon: props.inFullscreen ? 'fullscreen_exit' : 'fullscreen',
                        tooltip: props.inFullscreen ? $t('hide') : 'reveal'
                    }"
                    @iconBtnClick="props.toggleFullscreen"
                />
                <slot name="top-buttons"></slot>
            </template>

            <template v-if="tableData.viewBody" v-slot:body="props">
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
            // Table: () => import('src/components/Table.vue'),
        },
        props: {
            tableData: {
                type: Object,
                default: () => [],
            },
            tableDataS: {
                type: Array,
                default: () => [],
            },
        },
        data() {
            return {
                filter: {
                    value: '',
                },
                selected: [],
                pagination: {
                    rowsPerPage: 10,
                },
            };
        },
        watch: {
            selected(val) {
                this.tableData.selected = val;
            },
        },
        created() {
            this.$q.lang.table.recordsPerPage = this.$t('recordsPerPage');
            this.$q.lang.table.allRows = this.$t('all');
            this.$q.lang.table.noData = this.$t('noData');
            devlog.log('LANG_T', this.$q.lang.table);
        },
        // methods: {
        //     getSelectedString() {
        //         return this.selected.length === 0 ? '' : `${this.selected.length} record${this.selected.length > 1 ? 's' : ''} selected of ${this.data.length}`;
        //     },
        // },
    };
</script>
