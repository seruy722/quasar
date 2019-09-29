<template>
    <div
        data-vue-component-name="Fax"
    >
        <Table
            :table-data="faxDataTable"
            :table-data-s="faxDataList"
        >
            <template v-slot:top-buttons>
                <IconBtn
                    :icon-btn-data="{
                        color: 'positive',
                        icon: 'save',
                        tooltip: 'save',
                        disable: !arrayToUpdate.length
                    }"
                    @iconBtnClick="updateFaxData"
                />

                <IconBtn
                    :icon-btn-data="{
                        color: 'positive',
                        icon: 'explicit',
                        tooltip: 'excel'
                    }"
                    @iconBtnClick="exportFaxData(currentFaxItem)"
                />

                <IconBtn
                    :icon-btn-data="{
                        color: 'primary',
                        icon: 'update',
                        tooltip: 'update'
                    }"
                    @iconBtnClick="updateAllPriceInFaxData(currentFaxItem.id)"
                />
            </template>

            <template v-slot:inner-body="{props}">
                <q-tr :props="props">
                    <q-td auto-width>
                        <q-checkbox
                            v-model="props.selected"
                            :dense="$q.screen.xs || $q.screen.sm"
                        />
                    </q-td>

                    <q-td
                        key="customer_code"
                        :props="props"
                    >
                        {{ props.row.customer_code }}
                        <Icon
                            :name="props.expand ? 'arrow_drop_up' : 'arrow_drop_down'"
                            :tooltip="props.expand ? 'hide' : 'reveal'"
                            class="cursor-pointer"
                            @iconClick="props.expand = !props.expand"
                        />
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
                        key="category"
                        :props="props"
                    >
                        {{ props.row.category }}
                    </q-td>

                    <q-td
                        key="for_kg"
                        :props="props"
                        class="cursor-pointer"
                    >
                        {{ props.row.for_kg }}
                        <PopupEdit
                            :value.sync="props.row.for_kg"
                            :popup-settings="{type: 'number'}"
                            @addToSave="addToSaveMultiply(props.row.arr, props.row.for_kg, 'for_kg')"
                        />
                    </q-td>

                    <q-td
                        key="for_place"
                        :props="props"
                        class="cursor-pointer"
                    >
                        {{ props.row.for_place }}
                        <PopupEdit
                            :value.sync="props.row.for_place"
                            :popup-settings="{type: 'number'}"
                            @addToSave="addToSaveMultiply(props.row.arr, props.row.for_place, 'for_place')"
                        />
                    </q-td>

                    <q-td
                        key="sum"
                        :props="props"
                    >
                        {{ (props.row.for_kg * props.row.kg + props.row.for_place * props.row.place).toFixed(1) }}
                    </q-td>
                </q-tr>
                <q-tr
                    v-show="props.expand"
                    :props="props"
                >
                    <q-td colspan="100%">
                        <Table
                            :table-data="faxExpandDataTable"
                            :table-data-s="props.row.arr"
                        >
                            <template v-slot:inner-body="{props}">
                                <q-tr :props="props">
                                    <q-td auto-width>
                                        <q-checkbox
                                            v-model="props.selected"
                                            :dense="$q.screen.xs || $q.screen.sm"
                                        />
                                    </q-td>

                                    <q-td
                                        key="code"
                                        :props="props"
                                        class="cursor-pointer"
                                    >
                                        {{ props.row.code }}
                                        <PopupEdit
                                            :value.sync="props.row.code"
                                            @addToSave="addToUpdateArray(props.row, 'arrayToUpdate')"
                                        />
                                    </q-td>

                                    <q-td
                                        key="code_id"
                                        :props="props"
                                        class="cursor-pointer"
                                    >
                                        {{ props.row.code_id | filterFromSelectData(customerCodes) }}
                                        <PopupEdit
                                            :value.sync="props.row.code_id"
                                            @addToSave="addToUpdateArray(props.row, 'arrayToUpdate')"
                                        >
                                            <template v-slot:body>
                                                <SelectWithSearchInput
                                                    :value.sync="props.row.code_id"
                                                    :options="customerCodes"
                                                    :search-options="customerCodes"
                                                    :input-data="selectCustomerCode"
                                                />
                                            </template>
                                        </PopupEdit>
                                    </q-td>

                                    <q-td
                                        key="place"
                                        :props="props"
                                        class="cursor-pointer"
                                    >
                                        {{ props.row.place }}
                                        <PopupEdit
                                            :value.sync="props.row.place"
                                            :popup-settings="{type: 'number'}"
                                            @addToSave="addToUpdateArray(props.row, 'arrayToUpdate')"
                                        />
                                    </q-td>

                                    <q-td
                                        key="kg"
                                        :props="props"
                                        class="cursor-pointer"
                                    >
                                        {{ props.row.kg }}
                                        <PopupEdit
                                            :value.sync="props.row.kg"
                                            :popup-settings="{type: 'number'}"
                                            @addToSave="addToUpdateArray(props.row, 'arrayToUpdate')"
                                        />
                                    </q-td>

                                    <q-td
                                        key="for_kg"
                                        :props="props"
                                        class="cursor-pointer"
                                    >
                                        {{ props.row.for_kg }}
                                        <PopupEdit
                                            :value.sync="props.row.for_kg"
                                            :popup-settings="{type: 'number'}"
                                            @addToSave="addToUpdateArray(props.row, 'arrayToUpdate')"
                                        />
                                    </q-td>

                                    <q-td
                                        key="for_place"
                                        :props="props"
                                        class="cursor-pointer"
                                    >
                                        {{ props.row.for_place }}
                                        <PopupEdit
                                            :value.sync="props.row.for_place"
                                            :popup-settings="{type: 'number'}"
                                            @addToSave="addToUpdateArray(props.row, 'arrayToUpdate')"
                                        />
                                    </q-td>

                                    <q-td
                                        key="category_id"
                                        :props="props"
                                        class="cursor-pointer"
                                    >
                                        {{ props.row.category_id | filterFromSelectData(categories) }}
                                        <PopupEdit
                                            :value.sync="props.row.category_id"
                                            @addToSave="addToUpdateArray(props.row, 'arrayToUpdate')"
                                        >
                                            <template v-slot:body>
                                                <SelectWithSearchInput
                                                    :value.sync="props.row.category_id"
                                                    :options="categories"
                                                    :search-options="categories"
                                                    :input-data="selectCategoryData"
                                                />
                                            </template>
                                        </PopupEdit>
                                    </q-td>

                                    <q-td
                                        key="shop"
                                        :props="props"
                                        class="cursor-pointer"
                                    >
                                        {{ props.row.shop }}
                                        <PopupEdit
                                            :value.sync="props.row.shop"
                                            @addToSave="addToUpdateArray(props.row, 'arrayToUpdate')"
                                        />
                                    </q-td>

                                    <q-td
                                        key="notation"
                                        :props="props"
                                        class="cursor-pointer"
                                    >
                                        {{ props.row.notation }}
                                        <PopupEdit
                                            :value.sync="props.row.notation"
                                            @addToSave="addToUpdateArray(props.row, 'arrayToUpdate')"
                                        />
                                    </q-td>


                                    <q-td
                                        key="things"
                                        :props="props"
                                        class="cursor-pointer"
                                    >
                                        {{ props.row.things }}
                                        <PopupEdit
                                            :value.sync="props.row.things"
                                        />
                                    </q-td>
                                </q-tr>
                                <!--                                <q-tr v-show="props.expand" :props="props">-->
                                <!--                                    <q-td colspan="100%">-->
                                <!--                                    </q-td>-->
                                <!--                                </q-tr>-->
                            </template>
                        </Table>
                    </q-td>
                </q-tr>
            </template>
            <template v-slot:inner-bottom-row>
                <Table
                    :table-data="faxCategoriesTable"
                    :table-data-s="faxCategoriesDataList"
                >
                    <template v-slot:inner-body="{props}">
                        <q-tr :props="props">
                            <q-td
                                key="category_id"
                                :props="props"
                            >
                                {{ props.row.category_id | filterFromSelectData(categories) }}
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
                                key="for_kg"
                                :props="props"
                                class="cursor-pointer"
                            >
                                {{ props.row.for_kg }}
                                <PopupEdit
                                    :value.sync="props.row.for_kg"
                                    :popup-settings="{type: 'number'}"
                                    @addToSave="addToUpdateArray(props.row, 'arrayToUpdateTransporterPriceData')"
                                />
                            </q-td>

                            <q-td
                                key="sum"
                                :props="props"
                            >
                                {{ (props.row.for_kg * props.row.kg).toFixed(1) }}
                            </q-td>
                        </q-tr>
                        <q-tr
                            v-show="props.expand"
                            :props="props"
                        >
                            <q-td colspan="100%">
                                <Table
                                    :table-data="faxExpandDataTable"
                                    :table-data-s="props.row.arr"
                                >
                                    <template v-slot:inner-body="{props}">
                                        <q-tr :props="props">
                                            <q-td auto-width>
                                                <q-checkbox
                                                    v-model="props.selected"
                                                    :dense="$q.screen.xs || $q.screen.sm"
                                                />
                                            </q-td>

                                            <q-td
                                                key="code"
                                                :props="props"
                                                class="cursor-pointer"
                                            >
                                                {{ props.row.code }}
                                                <PopupEdit
                                                    :value.sync="props.row.code"
                                                    @addToSave="addToUpdateArray(props.row, 'arrayToUpdate')"
                                                />
                                            </q-td>

                                            <q-td
                                                key="code_id"
                                                :props="props"
                                                class="cursor-pointer"
                                            >
                                                {{ props.row.code_id | filterFromSelectData(customerCodes) }}
                                                <PopupEdit
                                                    :value.sync="props.row.code_id"
                                                    @addToSave="addToUpdateArray(props.row, 'arrayToUpdate')"
                                                >
                                                    <template v-slot:body>
                                                        <SelectWithSearchInput
                                                            :value.sync="props.row.code_id"
                                                            :options="customerCodes"
                                                            :search-options="customerCodes"
                                                            :input-data="selectCustomerCode"
                                                        />
                                                    </template>
                                                </PopupEdit>
                                            </q-td>

                                            <q-td
                                                key="place"
                                                :props="props"
                                                class="cursor-pointer"
                                            >
                                                {{ props.row.place }}
                                                <PopupEdit
                                                    :value.sync="props.row.place"
                                                    :popup-settings="{type: 'number'}"
                                                    @addToSave="addToUpdateArray(props.row, 'arrayToUpdate')"
                                                />
                                            </q-td>

                                            <q-td
                                                key="kg"
                                                :props="props"
                                                class="cursor-pointer"
                                            >
                                                {{ props.row.kg }}
                                                <PopupEdit
                                                    :value.sync="props.row.kg"
                                                    :popup-settings="{type: 'number'}"
                                                    @addToSave="addToUpdateArray(props.row, 'arrayToUpdate')"
                                                />
                                            </q-td>

                                            <q-td
                                                key="for_kg"
                                                :props="props"
                                                class="cursor-pointer"
                                            >
                                                {{ props.row.for_kg }}
                                                <PopupEdit
                                                    :value.sync="props.row.for_kg"
                                                    :popup-settings="{type: 'number'}"
                                                    @addToSave="addToUpdateArray(props.row, 'arrayToUpdate')"
                                                />
                                            </q-td>

                                            <q-td
                                                key="for_place"
                                                :props="props"
                                                class="cursor-pointer"
                                            >
                                                {{ props.row.for_place }}
                                                <PopupEdit
                                                    :value.sync="props.row.for_place"
                                                    :popup-settings="{type: 'number'}"
                                                    @addToSave="addToUpdateArray(props.row, 'arrayToUpdate')"
                                                />
                                            </q-td>

                                            <q-td
                                                key="category_id"
                                                :props="props"
                                                class="cursor-pointer"
                                            >
                                                {{ props.row.category_id | filterFromSelectData(categories) }}
                                                <PopupEdit
                                                    :value.sync="props.row.category_id"
                                                    @addToSave="addToUpdateArray(props.row, 'arrayToUpdate')"
                                                >
                                                    <template v-slot:body>
                                                        <SelectWithSearchInput
                                                            :value.sync="props.row.category_id"
                                                            :options.sync="categories"
                                                            :search-options="categories"
                                                            :input-data="selectCategoryData"
                                                        />
                                                    </template>
                                                </PopupEdit>
                                            </q-td>

                                            <q-td
                                                key="shop"
                                                :props="props"
                                                class="cursor-pointer"
                                            >
                                                {{ props.row.shop }}
                                                <PopupEdit
                                                    :value.sync="props.row.shop"
                                                    @addToSave="addToUpdateArray(props.row, 'arrayToUpdate')"
                                                />
                                            </q-td>

                                            <q-td
                                                key="notation"
                                                :props="props"
                                                class="cursor-pointer"
                                            >
                                                {{ props.row.notation }}
                                                <PopupEdit
                                                    :value.sync="props.row.notation"
                                                    @addToSave="addToUpdateArray(props.row, 'arrayToUpdate')"
                                                />
                                            </q-td>


                                            <q-td
                                                key="things"
                                                :props="props"
                                                class="cursor-pointer"
                                            >
                                                {{ props.row.things }}
                                                <PopupEdit
                                                    :value.sync="props.row.things"
                                                />
                                            </q-td>
                                        </q-tr>
                                        <!--                                <q-tr v-show="props.expand" :props="props">-->
                                        <!--                                    <q-td colspan="100%">-->
                                        <!--                                    </q-td>-->
                                        <!--                                </q-tr>-->
                                    </template>

                                </Table>
                            </q-td>
                        </q-tr>
                    </template>
                    <template v-slot:inner-bottom-row>
                        <IconBtn
                            v-if="arrayToUpdateTransporterPriceData.length"
                            :icon-btn-data="{
                                color: 'positive',
                                icon: 'save',
                                tooltip: 'save'
                            }"
                            @iconBtnClick="updateTransporterPriceData"
                        />
                    </template>
                </Table>
            </template>
        </Table>
    </div>
</template>

<script>
    import { getUrl } from 'src/tools/url';
    import { mapGetters } from 'vuex';
    import showNotif from 'src/mixins/showNotif';
    import ExportDataMixin from 'src/mixins/ExportData';
    import FaxesMixin from 'src/mixins/Faxes';

    export default {
        name: 'Fax',
        components: {
            Table: () => import('src/components/Table.vue'),
            Icon: () => import('src/components/Buttons/Icons/Icon.vue'),
            IconBtn: () => import('src/components/Buttons/IconBtn.vue'),
            PopupEdit: () => import('src/components/PopupEdit.vue'),
            SelectWithSearchInput: () => import('src/components/Elements/SelectWithSearchInput.vue'),
        },
        mixins: [showNotif, ExportDataMixin, FaxesMixin],
        data() {
            return {
                selectCategoryData: {
                    label: this.$t('categories'),
                    icon: 'people',
                },
                selectCustomerCode: {
                    label: this.$t('customer'),
                    icon: 'people',
                },
                faxDataList: [],
                faxDataTable: {
                    title: this.$t('warehouse'),
                    viewBody: true,
                    viewTop: true,
                    hideBottom: false,
                    selected: [],
                    columns: [
                        {
                            name: 'customer_code',
                            label: this.$t('customer'),
                            align: 'center',
                            field: 'customer_code',
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
                            name: 'sum',
                            label: this.$t('sum'),
                            field: 'sum',
                            align: 'center',
                            sortable: true,
                        },
                    ],
                    visibleColumns: ['customer_code', 'place', 'kg', 'category', 'for_kg', 'for_place', 'sum'],
                },
                faxExpandDataTable: {
                    title: this.$t('warehouse'),
                    viewBody: true,
                    viewTop: true,
                    hideBottom: false,
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
                            name: 'code_id',
                            label: this.$t('customer'),
                            align: 'center',
                            field: 'code_id',
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
                            name: 'category_id',
                            label: this.$t('category'),
                            field: 'category_id',
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
                    visibleColumns: ['code', 'code_id', 'place', 'kg', 'for_kg', 'for_place', 'category_id', 'shop', 'notation', 'things'],
                },
                faxCategoriesTable: {
                    title: this.$t('warehouse'),
                    viewBody: true,
                    viewTop: false,
                    hideBottom: true,
                    selection: 'none',
                    selected: [],
                    columns: [
                        {
                            name: 'category_id',
                            label: this.$t('category'),
                            align: 'center',
                            field: 'category_id',
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
                            name: 'for_kg',
                            label: this.$t('for_kg'),
                            field: 'for_kg',
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
                    ],
                    visibleColumns: ['category_id', 'place', 'kg', 'for_kg', 'sum'],
                },
                // transporterCategoriesDataList: [],
                faxCategoriesDataList: [],
                arrayToUpdate: [],
                arrayToUpdateTransporterPriceData: [],
            };
        },
        computed: {
            ...mapGetters({
                categories: 'category/getCategories',
                customerCodes: 'customer/getCodes',
                currentFaxItem: 'faxes/getCurrentFaxItem',
                faxData: 'faxes/getFaxData',
                faxCategoriesData: 'faxes/getFaxCategoriesData',
                transporterPriceData: 'transporter/getTransporterPrice',
            }),
        },
        watch: {
            faxData(val) {
                this.faxDataList = _.cloneDeep(val);
            },
            faxCategoriesData(val) {
                this.faxCategoriesDataList = _.cloneDeep(val);
            },
        },
        created() {
            this.getFaxData(this.$route.params.id);
            // this.getCategories();
            // this.getCustomerCode();
            // this.faxDataList = _.cloneDeep(this.faxData);
            // this.faxCategoriesDataList = _.cloneDeep(this.faxCategoriesData);
        },
        methods: {
            async updateFaxData() {
                this.$q.loading.show();
                await this.$axios.post(getUrl('updateFaxData'), this.arrayToUpdate)
                  .then(({ data }) => {
                      devlog.log('SDF', data);
                      this.$store.dispatch('faxes/setFaxData', this.prepareFaxTableData(data.faxData));
                      this.$store.dispatch('faxes/setFaxCategoriesData', this.prepareFaxCategoriesData(this.faxData, this.transporterPriceData, _.get(this.currentFaxItem, 'transporter_id')));
                      // this.$store.dispatch('transporter/setTransporterPrice', data.transporterPriceData);
                      // this.faxData = this.prepareFaxTableData(data.faxData);
                      // this.prepareTransporterPriceData(this.faxData, data.transporterPriceData, data.transporterID);
                      this.arrayToUpdate = [];
                      this.$q.loading.hide();
                      this.showNotif('success', 'Данные успешно обновлены.', 'center');
                  })
                  .catch((errors) => {
                      this.$q.loading.hide();
                      devlog.log('EDR', errors);
                      this.showNotif('error', 'Произошла ошибка. Обновите пожалуйста страницу.', 'center');
                  });
            },
            async updateTransporterPriceData() {
                this.$q.loading.show();
                await this.$axios.post(getUrl('updateTransporterPriceData'), this.arrayToUpdateTransporterPriceData)
                  .then(({ data }) => {
                      // devlog.log('SDF', data);
                      this.$store.dispatch('transporter/setTransporterPrice', data.transporterPriceData);
                      // this.$store.dispatch('transporter/setTransporterPrice', this.prepareTransporterPriceData(this.faxData, data.transporterPriceData, _.get(this.currentFaxItem, 'transporter_id')));
                      this.$store.dispatch('faxes/setFaxCategoriesData', this.prepareFaxCategoriesData(this.faxData, data.transporterPriceData, _.get(this.currentFaxItem, 'transporter_id')));
                      // this.prepareTransporterPriceData(this.faxData, data.transporterPriceData, data.transporterID);
                      this.arrayToUpdateTransporterPriceData = [];
                      this.$q.loading.hide();
                      this.showNotif('success', 'Данные успешно обновлены.', 'center');
                  })
                  .catch((errors) => {
                      this.$q.loading.hide();
                      devlog.log('EDR', errors);
                      this.showNotif('error', 'Произошла ошибка. Обновите пожалуйста страницу.', 'center');
                  });
            },
            async getFaxData(id) {
                if (this.currentFaxItem.id !== id) {
                    this.$q.loading.show();
                    try {
                        const { data } = await this.$axios.get(`${getUrl('faxData')}/${id}`);
                        this.faxDataList = await this.prepareFaxTableData(data.faxData);
                        this.faxCategoriesDataList = await this.prepareFaxCategoriesData(this.faxData, this.transporterPriceData, _.get(data.fax, 'transporter_id'));
                        // await this.$store.dispatch('faxes/setFaxData', this.prepareFaxTableData(data.faxData));
                        // // this.faxData = this.prepareFaxTableData(data.faxData);
                        // await this.$store.dispatch('faxes/setCurrentFaxItem', data.fax);
                        // await this.$store.dispatch('transporter/setTransporterPrice', data.transporterPriceData);
                        // // this.prepareTransporterPriceData(this.faxData, data.transporterPriceData, _.get(this.currentFaxItem, 'transporter_id'));
                        // await this.$store.dispatch('faxes/setFaxCategoriesData', this.prepareFaxCategoriesData(this.faxData, this.transporterPriceData, _.get(this.currentFaxItem, 'transporter_id')));
                        // this.transporterCategoriesDataList = this.prepareTransporterPriceData(this.faxData, this.transporterPriceData, _.get(this.currentFaxItem, 'transporter_id'));
                        this.$q.loading.hide();
                    } catch (e) {
                        devlog.log(e);
                        this.$q.loading.hide();
                    }
                }
            },
            async updateAllPriceInFaxData(id) {
                this.$q.loading.show();
                await this.$axios.get(`${getUrl('updateAllPriceInFaxData')}/${id}`)
                  .then(({ data }) => {
                      this.$store.dispatch('faxes/setFaxData', this.prepareFaxTableData(data.faxData));
                      this.$q.loading.hide();
                      this.showNotif('success', 'Данные успешно обновлены.', 'center');
                  })
                  .catch(() => {
                      this.$q.loading.hide();
                      this.showNotif('error', 'Произошла ошибка. Обновите пожалуйста страницу.', 'center');
                  });
            },
            getCategories() {
                if (_.isEmpty(this.categories)) {
                    this.$store.dispatch('category/getCategories');
                }
            },
            getCustomerCode() {
                if (_.isEmpty(this.customerCodes)) {
                    this.$store.dispatch('customer/getCodes');
                }
            },
            countSum(arr, key) {
                return _.sumBy(arr, key);
            },
            prepareFaxCategoriesData(data, price, transporterID) {
                // devlog.log('PRICE', price);
                const categoriesArr = [];
                _.forEach(data, (item) => {
                    const elem = _.find(categoriesArr, { category_id: item.category_id });
                    const priceForCategory = _.find(price, { category_id: item.category_id });
                    if (elem) {
                        elem.place += item.place;
                        elem.kg += item.kg;
                        elem.sum += item.kg * elem.for_kg;
                    } else {
                        const obj = {};
                        _.assign(obj, {
                            id: item.id,
                            category_id: item.category_id,
                            transporter_id: transporterID,
                            place: item.place,
                            kg: item.kg,
                            for_kg: 0,
                            sum: 0,
                        });
                        // devlog.log('priceForCategory', priceForCategory);
                        // devlog.log('item.category_id', item.category_id);
                        if (priceForCategory) {
                            obj.for_kg = priceForCategory.for_kg;
                        }

                        categoriesArr.push(obj);
                    }
                });

                return categoriesArr;
            },
            prepareFaxTableData(data) {
                const newArr = [];
                _.forEach(data, (value) => {
                    if (_.isObject(value)) {
                        _.forEach(value, (item) => {
                            _.forEach(item, (elem) => {
                                const obj2 = {};
                                _.assign(obj2, _.first(elem), {
                                    arr: elem,
                                    kg: this.countSum(elem, 'kg'),
                                    place: this.countSum(elem, 'place'),
                                    sum: 0,
                                });
                                newArr.push(obj2);
                            });
                        });
                    }
                });
                // devlog.log('newArr', newArr);
                return newArr;
            },
            addToSaveMultiply(arr, price, key) {
                _.forEach(arr, (item) => {
                    item[key] = price;
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
        },
    };
</script>
