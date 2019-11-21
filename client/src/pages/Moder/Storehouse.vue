<template>
    <div
        class="q-pa-md"
        data-vue-component-name="Storehouse"
    >
        <Table
            :table-data="storehouseTableSettings"
            :table-data-s="storehouseTableData"
        >
            <template v-slot:top-buttons>
                <AddEntryOnStorehouse />
                <Menu />

                <IconBtn
                    v-show="arrayToUpdate.length"
                    color="positive"
                    icon="save"
                    :tooltip="$t('save')"
                    @iconBtnClick="updateStorehouseData"
                />

                <IconBtn
                    v-show="storehouseTableSettings.selected.length || storehouseTableExpandSettings.selected.length"
                    color="negative"
                    icon="delete"
                    :tooltip="$t('delete')"
                    @iconBtnClick="deleteEntry()"
                />

                <IconBtn
                    color="positive"
                    icon="explicit"
                    :tooltip="$t('excel')"
                />
            </template>

            <template v-slot:inner-body="{props}">
                <q-tr
                    v-show="!props.row.destroyed"
                    :props="props"
                    :class="{table__tr_bold_text: props.row.brand }"
                >
                    <q-td auto-width>
                        <q-checkbox
                            v-model="props.selected"
                            :dense="$q.screen.xs || $q.screen.sm"
                        />
                    </q-td>

                    <q-td
                        key="code_client_name"
                        class="cursor-pointer"
                        :props="props"
                    >
                        {{ props.row.code_client_id | filterFromSelectData(clientCodes) }}
                        <Icon
                            :name="props.expand ? 'arrow_drop_up' : 'arrow_drop_down'"
                            :tooltip="props.expand ? 'hide' : 'reveal'"
                            class="cursor-pointer q-ml-lg"
                            @iconClick="props.expand = !props.expand"
                        />
                        <PopupEdit
                            :value.sync="props.row.code_client_id"
                            @addToSave="addToSaveMultiply(props.row.arr, props.row.code_client_id, 'code_client_id')"
                        >
                            <template v-slot:body>
                                <SelectWithSearchInput
                                    :value.sync="props.row.code_client_id"
                                    :options.sync="clientCodes"
                                    :search-options="clientCodes"
                                    :label="$t('category')"
                                    icon="people"
                                />
                            </template>
                        </PopupEdit>
                    </q-td>

                    <q-td
                        key="place"
                        :props="props"
                    >
                        {{ props.row.place }}
                    </q-td>

                    <q-td
                        key="kg"
                        :props="props"
                    >
                        {{ props.row.kg }}
                    </q-td>

                    <q-td
                        key="category_name"
                        class="cursor-pointer"
                        :props="props"
                    >
                        {{ props.row.category_id | filterFromSelectData(categories) }}
                        <PopupEdit
                            :value.sync="props.row.category_id"
                            @addToSave="addToSaveMultiply(props.row.arr, props.row.category_id, 'category_id')"
                        >
                            <template v-slot:body>
                                <SearchSelect
                                    v-model="props.row.category_id"
                                    :options="categories"
                                    :label="$t('category')"
                                    icon="house"
                                />
                            </template>
                        </PopupEdit>
                    </q-td>

                    <!--                    <q-td-->
                    <!--                        key="for_kg"-->
                    <!--                        :props="props"-->
                    <!--                        class="cursor-pointer"-->
                    <!--                    >-->
                    <!--                        {{ props.row.for_kg }}-->
                    <!--                        <PopupEdit-->
                    <!--                            :value.sync="props.row.for_kg"-->
                    <!--                            input-type="number"-->
                    <!--                            @addToSave="addToSave(props.row)"-->
                    <!--                        />-->
                    <!--                    </q-td>-->

                    <!--                    <q-td-->
                    <!--                        key="for_place"-->
                    <!--                        :props="props"-->
                    <!--                        class="cursor-pointer"-->
                    <!--                    >-->
                    <!--                        {{ props.row.for_place }}-->
                    <!--                        <PopupEdit-->
                    <!--                            :value.sync="props.row.for_place"-->
                    <!--                            input-type="number"-->
                    <!--                            @addToSave="addToSave(props.row)"-->
                    <!--                        />-->
                    <!--                    </q-td>-->

                    <!--                    <q-td-->
                    <!--                        key="created_at"-->
                    <!--                        :props="props"-->
                    <!--                    >-->
                    <!--                        {{ props.row.created_at | formatToDotDate}}-->
                    <!--                    </q-td>-->
                </q-tr>
                <q-tr
                    v-show="props.expand"
                    :props="props"
                >
                    <q-td colspan="100%">
                        <Table
                            :table-data="storehouseTableExpandSettings"
                            :table-data-s="props.row.arr"
                        >
                            <template v-slot:inner-body="{props}">
                                <q-tr
                                    :props="props"
                                    :class="{table__tr_bold_text: props.row.brand }"
                                >
                                    <q-td auto-width>
                                        <q-checkbox
                                            v-model="props.selected"
                                            :dense="$q.screen.xs || $q.screen.sm"
                                        />
                                    </q-td>

                                    <q-td
                                        key="code_place"
                                        class="cursor-pointer"
                                        :props="props"
                                    >
                                        {{ props.row.code_place }}
                                        <PopupEdit
                                            :value.sync="props.row.code_place"
                                            :mask="'###/###/###'"
                                            @addToSave="addToSave(props.row)"
                                        />
                                    </q-td>

                                    <q-td
                                        key="code_client_name"
                                        class="cursor-pointer"
                                        :props="props"
                                    >
                                        {{ props.row.code_client_id | filterFromSelectData(clientCodes) }}
                                        <PopupEdit
                                            :value.sync="props.row.code_client_id"
                                            @addToSave="addToSave(props.row)"
                                        >
                                            <template v-slot:body>
                                                <SelectWithSearchInput
                                                    :value.sync="props.row.code_client_id"
                                                    :options.sync="clientCodes"
                                                    :search-options="clientCodes"
                                                    ::label="$t('category')"
                                                    icon="people"
                                                />
                                            </template>
                                        </PopupEdit>
                                    </q-td>

                                    <q-td
                                        key="place"
                                        :props="props"
                                    >
                                        {{ props.row.place }}
                                    </q-td>

                                    <q-td
                                        key="kg"
                                        class="cursor-pointer"
                                        :props="props"
                                    >
                                        {{ props.row.kg }}
                                        <PopupEdit
                                            :value.sync="props.row.kg"
                                            input-type="number"
                                            @addToSave="addToSave(props.row)"
                                        />
                                    </q-td>

                                    <q-td
                                        key="category_name"
                                        class="cursor-pointer"
                                        :props="props"
                                    >
                                        {{ props.row.category_id | filterFromSelectData(categories) }}
                                        <PopupEdit
                                            :value.sync="props.row.category_id"
                                            @addToSave="addToSave(props.row)"
                                        >
                                            <template v-slot:body>
                                                <SearchSelect
                                                    v-model="props.row.category_id"
                                                    :options="categories"
                                                    :label="$t('category')"
                                                    icon="people"
                                                />
                                            </template>
                                        </PopupEdit>
                                    </q-td>

                                    <!--                                    <q-td-->
                                    <!--                                        key="for_kg"-->
                                    <!--                                        :props="props"-->
                                    <!--                                        class="cursor-pointer"-->
                                    <!--                                    >-->
                                    <!--                                        {{ props.row.for_kg }}-->
                                    <!--                                        <PopupEdit-->
                                    <!--                                            :value.sync="props.row.for_kg"-->
                                    <!--                                            input-type="number"-->
                                    <!--                                            @addToSave="addToSave(props.row)"-->
                                    <!--                                        />-->
                                    <!--                                    </q-td>-->

                                    <!--                                    <q-td-->
                                    <!--                                        key="for_place"-->
                                    <!--                                        :props="props"-->
                                    <!--                                        class="cursor-pointer"-->
                                    <!--                                    >-->
                                    <!--                                        {{ props.row.for_place }}-->
                                    <!--                                        <PopupEdit-->
                                    <!--                                            :value.sync="props.row.for_place"-->
                                    <!--                                            input-type="number"-->
                                    <!--                                            @addToSave="addToSave(props.row)"-->
                                    <!--                                        />-->
                                    <!--                                    </q-td>-->

                                    <!--                                    <q-td-->
                                    <!--                                        key="fax_id"-->
                                    <!--                                        :props="props"-->
                                    <!--                                        class="cursor-pointer"-->
                                    <!--                                    >-->
                                    <!--                                        {{ props.row.fax_id }}-->
                                    <!--                                        <PopupEdit-->
                                    <!--                                            :value.sync="props.row.fax_id"-->
                                    <!--                                            input-type="number"-->
                                    <!--                                            @addToSave="addToSave(props.row)"-->
                                    <!--                                        />-->
                                    <!--                                    </q-td>-->

                                    <!--                                    <q-td-->
                                    <!--                                        key="sum"-->
                                    <!--                                        :props="props"-->
                                    <!--                                    >-->
                                    <!--                                        {{ (props.row.for_kg * props.row.kg + props.row.for_place *-->
                                    <!--                                        props.row.place).toFixed(1) }}-->
                                    <!--                                    </q-td>-->

                                    <q-td
                                        key="notation"
                                        class="cursor-pointer"
                                        :props="props"
                                    >
                                        {{ props.row.notation }}
                                        <PopupEdit
                                            :value.sync="props.row.notation"
                                            @addToSave="addToSave(props.row)"
                                        />
                                    </q-td>

                                    <q-td
                                        key="shop"
                                        class="cursor-pointer"
                                        :props="props"
                                    >
                                        {{ props.row.shop }}
                                        <PopupEdit
                                            :value.sync="props.row.shop"
                                            @addToSave="addToSave(props.row)"
                                        />
                                    </q-td>

                                    <q-td
                                        key="things"
                                        class="cursor-pointer"
                                        :props="props"
                                    >
                                        {{ props.row.things | thingsFilter }}
                                        <PopupEdit
                                            :value.sync="props.row.things"
                                            @addToSave="addToSave(props.row)"
                                        >
                                            <template v-slot:body>
                                                <AddThings
                                                    :things.sync="props.row.things"
                                                    :show-dialog.sync="showDialog"
                                                >
                                                    <template v-slot:button>
                                                        <OutlineBtn
                                                            label="Открыть диалог"
                                                            color="info"
                                                            icon="cancel"
                                                            @clickOutlineBtn="showDialog = true"
                                                        />
                                                    </template>
                                                </AddThings>
                                            </template>
                                        </PopupEdit>
                                    </q-td>

                                    <q-td
                                        key="created_at"
                                        :props="props"
                                    >
                                        {{ props.row.created_at | formatToDotDate }}
                                    </q-td>
                                </q-tr>
                            </template>
                        </Table>
                    </q-td>
                </q-tr>
            </template>
            <template v-slot:inner-bottom-row>
                <div class="text-center">{{ `${ countSum(storehouseTableData, 'place')} мест |
                    ${countSum(storehouseTableData, 'kg')} кг` }}
                </div>
            </template>
        </Table>

        <div class="text-center text-bold text-uppercase q-mt-lg">Категории</div>
        <Table
            :table-data="storehouseTableCategoriesSettings"
            :table-data-s="storehouseCategoriesData"
        >
            <template v-slot:inner-body="{props}">
                <q-tr
                    :props="props"
                    :class="{table__tr_bold_text: props.row.name }"
                >
                    <q-td
                        key="name"
                        :props="props"
                    >
                        {{ props.row.name }}
                    </q-td>

                    <q-td
                        key="place"
                        :props="props"
                    >
                        {{ props.row.place }}
                    </q-td>

                    <q-td
                        key="kg"
                        :props="props"
                    >
                        {{ props.row.kg }}
                    </q-td>
                </q-tr>
            </template>
        </Table>
    </div>
</template>

<script>
    import { getUrl } from 'src/tools/url';
    import showNotif from 'src/mixins/showNotif';
    import ExportDataMixin from 'src/mixins/ExportData';
    import FaxesMixin from 'src/mixins/Faxes';

    export default {
        name: 'Storehouse',
        components: {
            Table: () => import('src/components/Table.vue'),
            AddEntryOnStorehouse: () => import('src/components/Dialogs/AddEntryOnStorehouse.vue'),
            Icon: () => import('src/components/Buttons/Icons/Icon.vue'),
            IconBtn: () => import('src/components/Buttons/IconBtn.vue'),
            PopupEdit: () => import('src/components/PopupEdit.vue'),
            SelectWithSearchInput: () => import('src/components/Elements/SelectWithSearchInput.vue'),
            SearchSelect: () => import('src/components/Elements/SearchSelect.vue'),
            AddThings: () => import('src/components/Dialogs/AddThings.vue'),
            OutlineBtn: () => import('src/components/Buttons/OutlineBtn.vue'),
            Menu: () => import('src/components/Menu.vue'),
        },
        mixins: [showNotif, ExportDataMixin, FaxesMixin],
        data() {
            return {
                showDialog: false,
                storehouseTableSettings: {
                    title: this.$t('storehouse'),
                    viewBody: true,
                    viewTop: true,
                    hideBottom: false,
                    selected: [],
                    columns: [
                        // {
                        //     name: 'code_place',
                        //     label: this.$t('code'),
                        //     align: 'center',
                        //     field: 'code_place',
                        //     sortable: true,
                        // },
                        {
                            name: 'code_client_name',
                            label: this.$t('client'),
                            field: 'code_client_name',
                            align: 'center',
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
                            name: 'category_name',
                            label: this.$t('category'),
                            field: 'category_name',
                            align: 'center',
                            sortable: true,
                        },
                        // {
                        //     name: 'for_kg',
                        //     label: this.$t('forKg'),
                        //     field: 'for_kg',
                        //     align: 'center',
                        //     sortable: true,
                        // },
                        // {
                        //     name: 'for_place',
                        //     label: this.$t('forPlace'),
                        //     field: 'for_place',
                        //     align: 'center',
                        //     sortable: true,
                        // },
                        // {
                        //     name: 'sum',
                        //     label: this.$t('sum'),
                        //     field: 'sum',
                        //     align: 'center',
                        //     sortable: true,
                        // },
                        // {
                        //     name: 'notation',
                        //     label: this.$t('notation'),
                        //     field: 'notation',
                        //     align: 'center',
                        //     sortable: true,
                        // },
                        // {
                        //     name: 'things',
                        //     label: this.$t('things'),
                        //     field: 'things',
                        //     align: 'center',
                        //     sortable: true,
                        // },
                        // {
                        //     name: 'created_at',
                        //     label: this.$t('date'),
                        //     field: 'created_at',
                        //     align: 'center',
                        //     sortable: true,
                        // },
                    ],
                    visibleColumns: ['code_client_name', 'place', 'kg', 'category_name'],
                },
                storehouseTableExpandSettings: {
                    title: this.$t('storehouse'),
                    viewBody: true,
                    viewTop: true,
                    hideBottom: false,
                    selected: [],
                    columns: [
                        {
                            name: 'code_place',
                            label: this.$t('code'),
                            align: 'center',
                            field: 'code_place',
                            sortable: true,
                        },
                        {
                            name: 'code_client_name',
                            label: this.$t('client'),
                            field: 'code_client_name',
                            align: 'center',
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
                            name: 'category_name',
                            label: this.$t('category'),
                            field: 'category_name',
                            align: 'center',
                            sortable: true,
                        },
                        // {
                        //     name: 'for_kg',
                        //     label: this.$t('forKg'),
                        //     field: 'for_kg',
                        //     align: 'center',
                        //     sortable: true,
                        // },
                        // {
                        //     name: 'for_place',
                        //     label: this.$t('forPlace'),
                        //     field: 'for_place',
                        //     align: 'center',
                        //     sortable: true,
                        // },
                        // {
                        //     name: 'fax_id',
                        //     label: this.$t('fax'),
                        //     field: 'fax_id',
                        //     align: 'center',
                        //     sortable: true,
                        // },
                        // {
                        //     name: 'sum',
                        //     label: this.$t('sum'),
                        //     field: 'sum',
                        //     align: 'center',
                        //     sortable: true,
                        // },
                        {
                            name: 'notation',
                            label: this.$t('notation'),
                            field: 'notation',
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
                            name: 'things',
                            label: this.$t('things'),
                            field: 'things',
                            align: 'center',
                            sortable: true,
                        },
                        {
                            name: 'created_at',
                            label: this.$t('date'),
                            field: 'created_at',
                            align: 'center',
                            sortable: true,
                        },
                    ],
                    visibleColumns: ['code_place', 'code_client_name', 'place', 'kg', 'category_name', 'notation', 'shop', 'things', 'created_at'],
                },
                storehouseTableCategoriesSettings: {
                    title: this.$t('storehouse'),
                    viewBody: false,
                    viewTop: false,
                    hideBottom: true,
                    selection: 'none',
                    selected: [],
                    columns: [
                        {
                            name: 'name',
                            label: this.$t('category'),
                            align: 'center',
                            field: 'name',
                            sortable: false,
                        },
                        {
                            name: 'place',
                            label: this.$t('place'),
                            field: 'place',
                            align: 'center',
                            sortable: false,
                        },
                        {
                            name: 'kg',
                            label: this.$t('kg'),
                            field: 'kg',
                            align: 'center',
                            sortable: false,
                        },
                    ],
                    visibleColumns: ['name', 'place', 'kg'],
                },
                storehouseTableCategories: [],
                arrayToUpdate: [],
                arrayToUpdateTransporterPriceData: [],
                storehouseTableData: [],
            };
        },
        computed: {
            storehouseData() {
                return this.$store.getters['storehouse/getStorehouseData'];
            },
            categories() {
                return this.$store.getters['category/getCategories'];
            },
            clientCodes() {
                return this.$store.getters['clientCodes/getCodes'];
            },
            storehouseCategoriesData() {
                return this.$store.getters['storehouse/getStorehouseCategoriesData'];
            },
        },
        watch: {
            storehouseData: {
                handler: function set(val) {
                    this.storehouseTableData = _.cloneDeep(val);
                },
                immediate: true,
            },
        },
        created() {
            this.getStorehouseTableData(1);
            this.getCategories();
            this.getClientCode();
        },
        methods: {
            async getStorehouseTableData(id) {
                if (_.isEmpty(this.storehouseData)) {
                    this.$q.loading.show();
                    try {
                        const { data } = await this.$axios.get(`${getUrl('storehouseData')}/${id}`);
                        // this.$store.dispatch('storehouse/setStorehouseData', this.prepareFaxTableData(data.storehouseData));
                        this.$store.dispatch('storehouse/setStorehouseData', data.storehouseData);
                            devlog.log('GRB', _.groupBy(data.storehouseData, 'category_name'));
                        data.categories.push(this.sumObjectForCategories(data.categories));
                        this.$store.dispatch('storehouse/setStorehouseCategoriesData', data.categories);
                        this.$q.loading.hide();
                    } catch (e) {
                        devlog.log(e);
                        this.$q.loading.hide();
                    }
                }
            },
            addToSaveMultiply(arr, value, key) {
                _.forEach(arr, (item) => {
                    item[key] = value;
                    this.addToUpdateArray(item, 'arrayToUpdate');
                });
            },
            exportFaxData(fax) {
                devlog.log('EXPORT');
                this.exportDataToExcel(getUrl('exportFaxData'), {
                    faxID: fax.id,
                    transporterID: fax.transporter_id,
                }, 'epo.xlsx');
            },
            addToSave(value) {
                if (_.isArray(value)) {
                    _.forEach(value, (item) => {
                        this.addToUpdateArray(item, 'arrayToUpdate');
                    });
                } else if (_.isObject(value)) {
                    this.addToUpdateArray(value, 'arrayToUpdate');
                }
            },
            deleteOptionalParameters(params) {
                _.forEach(this.arrayToUpdate, (item) => {
                    _.forEach(params, (elem) => {
                        if (!item[elem]) {
                            delete item[elem];
                        }
                    });
                });
            },
            async updateStorehouseData() {
                this.deleteOptionalParameters(['things']);
                this.$q.loading.show();
                await this.$axios.post(getUrl('updateStorehouseData'), this.arrayToUpdate)
                  .then(({ data }) => {
                      devlog.log('DSFF', data);
                      // this.$store.dispatch('storehouse/setStorehouseData', this.prepareFaxTableData(data.storehouseData));
                      this.$store.dispatch('storehouse/setStorehouseData', data.storehouseData);
                      data.categories.push(this.sumObjectForCategories(data.categories));
                      this.$store.dispatch('storehouse/setStorehouseCategoriesData', data.categories);
                      this.arrayToUpdate = [];
                      this.$q.loading.hide();
                      this.showNotif('success', 'Данные успешно обновлены.', 'center');
                  })
                  .catch(() => {
                      this.$q.loading.hide();
                  });
            },
            deleteEntry() {
                if (!_.isEmpty(this.storehouseTableSettings.selected)) {
                    _.forEach(this.storehouseTableSettings.selected, (item) => {
                        _.forEach(item.arr, (elem) => {
                            elem.destroyed = true;
                        });
                        this.addToSave(item.arr);
                    });
                    this.storehouseTableSettings.selected = [];
                } else if (!_.isEmpty(this.storehouseTableExpandSettings.selected)) {
                    _.forEach(this.storehouseTableSettings.selected, (item) => {
                        item.destroyed = true;
                        this.addToSave(item);
                    });
                }
                this.updateStorehouseData();
            },
        },
    };
</script>
