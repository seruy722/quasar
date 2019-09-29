<template>
    <div
        data-vue-component-name="Search"
    >
        <q-card>
            <q-card-section>
                <div class="text-h6">Поиск данных по таблицам</div>

                <SelectWithSearchInput
                    :input-data="inputData"
                    :options.sync="options"
                    :search-options="searchOptions"
                    :value.sync="inputData.value"
                    class="q-gutter-md row"
                    @selectSearchInput="getClientData(inputData.value)"
                />
            </q-card-section>
            <q-card-section>
                <q-tabs
                    v-model="tab"
                    indicator-color="yellow"
                    class="bg-primary text-white shadow-2"
                >
                    <q-tab name="cargo" icon="mail" label="КАРГО">
                        <Badge :badgeData="{value: badges.cargo}" />
                    </q-tab>
                    <q-tab name="debts" icon="alarm" label="ДОЛГИ">
                        <Badge :badgeData="{value: badges.debts}" />
                    </q-tab>
                    <q-tab name="warehouse" icon="movie" label="СКЛАД">
                        <Badge :badgeData="{value: badges.warehouse}" />
                    </q-tab>
                    <q-tab name="faxes" icon="movie" label="ФАКСЫ">
                        <Badge :badgeData="{value: badges.faxes}" />
                    </q-tab>
                </q-tabs>

                <q-separator />

                <q-tab-panels v-model="tab" animated>
                    <q-tab-panel name="cargo">
                        <Table
                            :table-data-s="cargoTableData.data"
                            :table-data="cargoTableData">
                            <template v-slot:inner-bottom-row>
                                <div>
                                    <div v-if="cargoTableData.selected.length">
                                        Сумма: {{ calcSelected }}
                                    </div>
                                    <div v-else>
                                        Сумма: {{ calcData }}
                                    </div>
                                </div>

                            </template>
                        </Table>
                    </q-tab-panel>

                    <q-tab-panel name="debts">
                        <Table
                            :table-data-s="debtsTableData.data"
                            :tableData="debtsTableData"
                        >

                        </Table>
                    </q-tab-panel>

                    <q-tab-panel name="warehouse">
                        <Table
                            :table-data-s="skladTableData.data"
                            :tableData="skladTableData"
                        >

                        </Table>
                    </q-tab-panel>
                    <q-tab-panel name="faxes">
                        <Table
                            :table-data-s="faxesTableData.data"
                            :tableData="faxesTableData"
                        >
                            <template v-slot:inner-body="{props}">
                                <q-tr :props="props"
                                      :class="[props.row.delivered ? 'table__tr_green_bg' : 'table__tr_red_bg']">
                                    <q-td auto-width>
                                        <q-checkbox v-model="props.selected" :dense="$q.screen.xs || $q.screen.sm" />
                                    </q-td>

                                    <q-td key="fax" :props="props">
                                        {{ props.row.fax }}
                                        <Icon
                                            :name="props.expand ? 'arrow_drop_up' : 'arrow_drop_down'"
                                            :size="$q.screen.xs"
                                            :tooltip="props.expand ? 'hide' : 'reveal'"
                                            @iconClick="props.expand = !props.expand"
                                        />
                                    </q-td>
                                    <q-td key="place" :props="props">{{ props.row.place }}</q-td>
                                    <q-td key="kg" :props="props">{{ props.row.kg }}</q-td>
                                    <q-td key="category" :props="props">{{ props.row.category }}</q-td>
                                    <q-td key="for_kg" :props="props">{{ props.row.for_kg }}</q-td>
                                    <q-td key="for_place" :props="props">{{ props.row.for_place }}</q-td>
                                    <q-td key="notation" :props="props">{{ props.row.notation }}</q-td>
                                </q-tr>
                                <q-tr v-show="props.expand">
                                    <q-td colspan="100%">
                                        <q-table
                                            title="Treats"
                                            :data="faxesTableData.data"
                                            :columns="faxesTableData.columns"
                                            row-key="name"
                                        />
                                    </q-td>
                                </q-tr>
                            </template>
                        </Table>
                    </q-tab-panel>
                </q-tab-panels>
            </q-card-section>
        </q-card>
    </div>
</template>

<script>
    import { getUrl } from 'src/tools/url';
    // import { sortArrayCollection } from 'src/utils/sort';

    export default {
        name: 'Search',
        components: {
            Table: () => import('src/components/Table.vue'),
            SelectWithSearchInput: () => import('src/components/Elements/SelectWithSearchInput.vue'),
            Icon: () => import('src/components/Buttons/Icons/Icon.vue'),
            Badge: () => import('src/components/Elements/Badge.vue'),
        },
        data() {
            return {
                options: [],
                searchOptions: [],
                badges: {
                    cargo: 0,
                    debts: 0,
                    warehouse: 0,
                    faxes: 0,
                },
                inputData: {
                    options: [],
                    objectOptions: [],
                    value: null,
                    label: this.$t('codes'),
                    selected: [],
                },
                tab: 'cargo',
                cargoTableData: {
                    data: [],
                    title: '',
                    viewBody: false,
                    viewTop: true,
                    selected: [],
                    columns: [
                        {
                            name: 'created_at',
                            required: true,
                            label: this.$t('date'),
                            align: 'center',
                            field: 'created_at',
                            sortable: true,
                        },
                        {
                            name: 'type',
                            label: this.$t('type'),
                            field: 'type',
                            align: 'center',
                            sortable: true,
                        },
                        {
                            name: 'sum',
                            label: this.$t('sum'),
                            field: 'sum',
                            align: 'center',
                            sortable: true,
                        },
                        {
                            name: 'notation',
                            label: this.$t('notation'),
                            field: 'notation',
                            align: 'center',
                            sortable: true,
                        },
                    ],
                    visibleColumns: ['created_at', 'type', 'sum', 'notation'],
                },
                debtsTableData: {
                    data: [],
                    title: '',
                    viewBody: false,
                    viewTop: true,
                    selected: [],
                    columns: [
                        {
                            name: 'created_at',
                            required: true,
                            label: this.$t('date'),
                            align: 'center',
                            field: 'created_at',
                            sortable: true,
                        },
                        {
                            name: 'type',
                            label: this.$t('type'),
                            field: 'type',
                            align: 'center',
                            sortable: true,
                        },
                        {
                            name: 'sum',
                            label: this.$t('sum'),
                            field: 'sum',
                            align: 'center',
                            sortable: true,
                        },
                        {
                            name: 'notation',
                            label: this.$t('notation'),
                            field: 'notation',
                            align: 'center',
                            sortable: true,
                        },
                    ],
                    visibleColumns: ['created_at', 'type', 'sum', 'notation'],
                },
                skladTableData: {
                    data: [],
                    title: this.$t('warehouse'),
                    viewBody: false,
                    viewTop: true,
                    selected: [],
                    columns: [
                        {
                            name: 'code',
                            label: this.$t('code.one'),
                            align: 'center',
                            field: 'code',
                            sortable: true,
                        },
                        {
                            name: 'client',
                            label: this.$t('customer'),
                            align: 'center',
                            field: 'client',
                            sortable: true,
                        },
                        {
                            name: 'place',
                            label: this.$t('place'),
                            field: 'place',
                            align: 'center',
                            sortable: true,
                        },
                        {
                            name: 'kg',
                            label: this.$t('kg'),
                            field: 'kg',
                            align: 'center',
                            sortable: true,
                        },
                        {
                            name: 'category',
                            label: this.$t('category'),
                            field: 'category',
                            align: 'center',
                            sortable: true,
                        },
                        {
                            name: 'shop',
                            label: this.$t('shop'),
                            field: 'shop',
                            align: 'center',
                            sortable: true,
                        },
                        {
                            name: 'city',
                            label: this.$t('city'),
                            field: 'city',
                            align: 'center',
                            sortable: true,
                        },
                        {
                            name: 'notation',
                            label: this.$t('notation'),
                            field: 'notation',
                            align: 'center',
                            sortable: true,
                        },
                        {
                            name: 'things',
                            label: this.$t('things'),
                            field: 'things',
                            align: 'center',
                            sortable: true,
                        },
                    ],
                    visibleColumns: ['code', 'client', 'place', 'kg', 'category', 'shop', 'city', 'notation', 'things'],
                },
                faxesTableData: {
                    data: [],
                    title: this.$t('warehouse'),
                    viewBody: true,
                    viewTop: true,
                    selected: [],
                    columns: [
                        {
                            name: 'fax',
                            label: this.$t('fax'),
                            align: 'center',
                            field: 'fax',
                            sortable: true,
                        },
                        {
                            name: 'place',
                            label: this.$t('place'),
                            field: 'place',
                            align: 'center',
                            sortable: true,
                        },
                        {
                            name: 'kg',
                            label: this.$t('kg'),
                            field: 'kg',
                            align: 'center',
                            sortable: true,
                        },
                        {
                            name: 'category',
                            label: this.$t('category'),
                            field: 'category',
                            align: 'center',
                            sortable: true,
                        },
                        {
                            name: 'for_kg',
                            label: this.$t('for_kg'),
                            field: 'for_kg',
                            align: 'center',
                            sortable: true,
                        },
                        {
                            name: 'for_place',
                            label: this.$t('for_place'),
                            field: 'for_place',
                            align: 'center',
                            sortable: true,
                        },
                        {
                            name: 'notation',
                            label: this.$t('notation'),
                            field: 'notation',
                            align: 'center',
                            sortable: true,
                        },
                    ],
                    visibleColumns: ['fax', 'place', 'kg', 'category', 'for_kg', 'for_place', 'notation'],
                },
            };
        },
        computed: {
            calcData() {
                return this.calc(this.cargoTableData.data, 'sum');
            },
            calcSelected() {
                return this.calc(this.cargoTableData.selected, 'sum');
            },
        },
        created() {
            this.fetchClientList();
        },
        methods: {
            fetchClientList() {
                this.$q.loading.show();
                this.$axios.get(getUrl('codeList'))
                  .then(({ data }) => {
                      devlog.log('RESPON', data);
                      // const sorted = sortArrayCollection(data.data, 'label');
                      // const clone = _.cloneDeep(data);
                      this.searchOptions = data;
                      this.options = data;
                      this.$q.loading.hide();
                  })
                  .catch(() => {
                      this.$q.loading.hide();
                  });
            },
            async getClientData(codeID) {
                devlog.log('INPUTDATA', codeID);
                this.$q.loading.show();
                const tableTitle = _.find(this.searchOptions, { value: codeID }).label;
                this.cargoTableData.title = tableTitle;
                this.debtsTableData.title = tableTitle;
                await this.$axios.get(`${getUrl('clientData')}/${codeID}`)
                  .then(({ data }) => {
                      this.cargoTableData.data = data.cargo;
                      this.debtsTableData.data = data.debts;
                      this.skladTableData.data = data.warehouse;
                      this.faxesTableData.data = this.prepareFaxesTableData(data.faxes);
                      this.badges.cargo = data.sumCargo;
                      this.badges.debts = data.sumDebts;
                      this.badges.warehouse = data.warehouse.length;
                      this.badges.faxes = data.faxesCount;
                      this.$q.loading.hide();
                  })
                  .catch((errors) => {
                      this.$q.loading.hide();
                      devlog.log(errors);
                  });
            },
            calc(arr, value) {
                return _.sumBy(arr, value);
            },
            prepareFaxesTableData(data) {
                const newArr = [];
                _.forEach(data, (value) => {
                    const obj = {};
                    if (_.isArray(value)) {
                        const first = _.first(value);
                        _.assign(obj, first[0]);
                        obj.arr = first;
                        obj.kg = _.sumBy(first, 'kg');
                        obj.place = _.sumBy(first, 'place');
                        newArr.push(obj);
                    } else if (_.isObject(value)) {
                        _.forEach(value, (item) => {
                            devlog.log('value', item);
                            const obj2 = {};
                            _.assign(obj2, item[0]);
                            obj2.arr = item;
                            obj2.kg = _.sumBy(item, 'kg');
                            obj2.place = _.sumBy(item, 'place');
                            newArr.push(obj2);
                        });
                    }
                });
                devlog.log('newArr', newArr);
                return newArr;
            },
        },
    };
</script>
