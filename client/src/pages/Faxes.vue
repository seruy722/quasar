<template>
    <div
        data-vue-component-name="Faxes"
    >
        <div class="row">
            <DialogAddFax />
            <IconBtn
                v-if="arrayToUpdateFax.length"
                :icon-btn-data="{
                 color: 'positive',
                 icon: 'save',
                 tooltip: $t('save')
            }"
                class="q-ml-md"
                @iconBtnClick="updateFaxes"
            />
            <IconBtn
                v-if="faxesTableData.selected.length"
                :icon-btn-data="{
                 color: 'negative',
                 icon: 'delete',
                 tooltip: $t('delete')
            }"
                class="q-ml-md"
                @iconBtnClick="deleteFaxItems"
            />
        </div>

        <Table
            :table-data="faxesTableData"
            :table-data-s="faxesList"
        >
            <template v-slot:inner-body="{props}">
                <q-tr
                    :props="props"
                    :class="[props.row.delivered ? 'table__tr_green_bg' : 'table__tr_red_bg']"
                >
                    <q-td auto-width>
                        <q-checkbox
                            v-model="props.selected"
                            :dense="$q.screen.xs || $q.screen.sm"
                        />
                    </q-td>
                    <q-td
                        key="name"
                        :props="props"
                        class="cursor-pointer"
                    >
                        {{ props.row.name }}
                        <PopupEdit
                            :value.sync="props.row.name"
                            :popup-settings="{type: 'text'}"
                            @addToSave="addToUpdateArray(props.row, 'arrayToUpdateFax')"
                        />
                        <Icon
                            name="send"
                            tooltip="follow"
                            class="q-ml-lg cursor-pointer"
                            @iconClick="$router.push({name: 'fax',params: { id: props.row.id }})"
                        />
                    </q-td>

                    <q-td
                        key="departure_date"
                        :props="props"
                        class="cursor-pointer"
                    >
                        {{ props.row.departure_date | formatDate }}
                        <PopupEdit
                            :value.sync="props.row.departure_date"
                            @addToSave="addToUpdateArray(props.row, 'arrayToUpdateFax')"
                        >
                            <template v-slot:body>
<!--                                <DatePicker-->
<!--                                    :value.sync="props.row.departure_date"-->
<!--                                    :input-data="datePickerData"-->
<!--                                    :errors="{}"-->
<!--                                />-->
                            </template>
                        </PopupEdit>
                    </q-td>

                    <q-td
                        key="delivered"
                        :props="props"
                        class="cursor-pointer"
                    >
                        {{ props.row.delivered | viewYesNo }}
                        <PopupEdit
                            :value.sync="props.row.delivered"
                            @addToSave="addToUpdateArray(props.row, 'arrayToUpdateFax')"
                        >
                            <template v-slot:body>
                                <BaseSelect
                                    :value.sync="props.row.delivered"
                                    :options="deliveredData"
                                    :search-options="deliveredData"
                                    :input-data="{label: 'Прибыл', icon:'people'}"
                                />
                            </template>
                        </PopupEdit>
                    </q-td>

                    <q-td
                        key="transport_name"
                        :props="props"
                        class="cursor-pointer"
                    >
                        {{ props.row.transport_id | filterFromSelectData(transport) }}
                        <PopupEdit
                            :value.sync="props.row.transport_id"
                            @addToSave="addToUpdateArray(props.row, 'arrayToUpdateFax')"
                        >
                            <template v-slot:body>
                                <SelectWithSearchInput
                                    :value.sync="props.row.transport_id"
                                    :options.sync="transport"
                                    :search-options="transport"
                                    :input-data="selectTransportData"
                                />
                            </template>
                        </PopupEdit>
                    </q-td>

                    <q-td
                        key="transporter_name"
                        :props="props"
                        class="cursor-pointer"
                    >
                        {{ props.row.transporter_id | filterFromSelectData(transporters) }}
                        <PopupEdit
                            :value="props.row.transporter_id"
                            @addToSave="addToUpdateArray(props.row, 'arrayToUpdateFax')"
                        >
                            <template v-slot:body>
                                <SelectWithSearchInput
                                    :value.sync="props.row.transporter_id"
                                    :options.sync="transporters"
                                    :search-options="transporters"
                                    :input-data="selectTransportData"
                                />
                            </template>
                        </PopupEdit>
                    </q-td>

                    <q-td
                        key="user_name"
                        :props="props"
                    >
                        {{ props.row.user_name }}
                    </q-td>
                </q-tr>
            </template>
        </Table>
    </div>
</template>

<script>
    import { getUrl } from 'src/tools/url';
    import { mapGetters } from 'vuex';
    import FaxesMixin from 'src/mixins/Faxes';
    import { formatToDotDate } from 'src/utils/formatDate';

    export default {
        name: 'Faxes',
        components: {
            Table: () => import('src/components/Elements/Table/Table.vue'),
            DialogAddFax: () => import('src/components/Dialogs/DialogAddFax.vue'),
            Icon: () => import('src/components/Buttons/Icons/Icon.vue'),
            IconBtn: () => import('src/components/Buttons/IconBtn.vue'),
            PopupEdit: () => import('src/components/PopupEdit.vue'),
            SelectWithSearchInput: () => import('src/components/Elements/SelectWithSearchInput.vue'),
            BaseSelect: () => import('src/components/Elements/BaseSelect.vue'),
            // DatePicker: () => import('src/components/Elements/DatePicker.vue'),
        },
        filters: {
            viewYesNo(val) {
                return val ? 'Да' : 'Нет';
            },
            formatDate(val) {
                if (val && _.includes(val, '/')) {
                    return formatToDotDate(val);
                }
                return val;
            },
        },
        mixins: [FaxesMixin],
        data() {
            return {
                deliveredData: [
                    {
                        label: 'ДА',
                        value: 1,
                    },
                    {
                        label: 'НЕТ',
                        value: 0,
                    },
                ],
                faxesTableData: {
                    data: this.faxes || [],
                    title: this.$t('storehouse'),
                    viewBody: true,
                    viewTop: true,
                    selected: [],
                    columns: [
                        {
                            name: 'name',
                            label: this.$t('fax'),
                            align: 'center',
                            field: 'name',
                            sortable: true,
                        },
                        {
                            name: 'departure_date',
                            label: this.$t('departureDate'),
                            align: 'center',
                            field: 'departure_date',
                            sortable: true,
                        },
                        {
                            name: 'delivered',
                            label: this.$t('arrived'),
                            field: 'delivered',
                            align: 'center',
                            sortable: true,
                        },
                        {
                            name: 'transport_name',
                            label: this.$t('transport'),
                            field: 'transport_name',
                            align: 'center',
                            sortable: true,
                        },
                        {
                            name: 'transporter_name',
                            label: this.$t('transporter'),
                            field: 'transporter_name',
                            align: 'center',
                            sortable: true,
                        },
                        {
                            name: 'user_name',
                            label: this.$t('user.one'),
                            field: 'user_name',
                            align: 'center',
                            sortable: true,
                        },
                    ],
                    visibleColumns: ['name', 'departure_date', 'delivered', 'transport_name', 'transporter_name', 'user_name'],
                },
                arrayToUpdateFax: [],
                selectTransportData: {
                    label: this.$t('transport'),
                    icon: 'people',
                },
                datePickerData: {
                    value: null,
                    placeholder: 'Дата отправки',
                },
                faxesList: [],
            };
        },
        computed: {
            ...mapGetters({
                faxes: 'faxes/getFaxes',
                transporters: 'transporter/getTransporters',
                transport: 'transport/getTransports',
            }),
        },
        watch: {
            faxes(val) {
                this.faxesList = _.cloneDeep(val);
            },
        },
        created() {
            devlog.log('FDSA', this.$store.getters['auth/isUserAuth']);
            this.getFaxesList();
            this.faxesList = _.cloneDeep(this.faxes);
        },
        methods: {
            goToFaxData(item) {
                this.$router.push({
                    name: 'fax',
                    params: { id: item.id },
                });
            },
            async updateFaxes() {
                this.$q.loading.show();
                try {
                    const { data } = await this.$axios.post(getUrl('updateFaxes'), this.arrayToUpdateFax);
                    this.$store.dispatch('faxes/setFaxes', data.faxesList);
                    this.arrayToUpdateFax = [];
                    this.$q.loading.hide();
                } catch (e) {
                    this.$q.loading.hide();
                    devlog.error('e');
                }
            },
            getFaxesList() {
                if (_.isEmpty(this.faxes)) {
                    this.$q.loading.show();
                    this.$axios.get(getUrl('faxes'))
                      .then(({ data }) => {
                          this.$store.dispatch('faxes/setFaxes', data.faxesList);
                          this.$q.loading.hide();
                      })
                      .catch((errors) => {
                          this.$q.loading.hide();
                          devlog.log('errors', errors);
                      });
                }
            },
            deleteFaxItems() {
                this.$q.loading.show();
                // devlog.log('ID', this.faxesTableData.selected);
                const selectedIds = _.map(this.faxesTableData.selected, 'id');
                this.$axios.post(getUrl('deleteFax'), { ids: selectedIds })
                  .then(({ data }) => {
                      if (data.status) {
                          this.$q.loading.hide();
                          this.faxesTableData.selected = [];
                          this.deleteItemsFromStore(selectedIds);
                          this.showNotif('success', 'Данные успешно удалены.', 'center');
                      }
                  })
                  .catch((errors) => {
                      this.$q.loading.hide();
                      devlog.log(errors);
                  });
            },
            deleteItemsFromStore(arr) {
                this.$store.dispatch('faxes/deleteFaxes', arr);
            },
        },
    };
</script>
