<template>
    <div
        data-vue-component-name="DialogAddFax"
    >
        <Dialog :dialog.sync="dialogUploadFaxData">

            <template v-slot:body>
                <q-card-section>
                    <div
                        v-for="(input, index) in inputs"
                        :key="index"
                    >
                        <SelectWithSearchInput
                            v-if="input.type==='select'"
                            :input-data="input"
                            :search-options="input.searchOptions"
                            :options="input.options"
                            :errors="errorsData"
                            :value.sync="input.value"
                        />
                        <DatePicker
                            v-else-if="input.type==='date'"
                            :input-data="input"
                            :value.sync="input.value"
                            :errors="errorsData"
                        />
                        <BaseInput
                            v-else
                            :input-data.sync="input"
                            :errors="errorsData"
                        />
                    </div>
                </q-card-section>

                <q-card-section>
                    <UploadFileToServer :upload-data="uploadFaxData" />
                </q-card-section>

                <q-separator />

                <q-card-actions align="right">
                    <OutlineBtn
                        :btn-data="cancelBtnData"
                        @clickOutlineBtn="dialogUploadFaxData.value = false"
                    />
                    <OutlineBtn
                        :btn-data="btnData"
                        @clickOutlineBtn="saveFaxOnServer"
                    />
                </q-card-actions>
            </template>
        </Dialog>
    </div>
</template>

<script>
    import { getUrl } from 'src/tools/url';
    import showNotif from 'src/mixins/showNotif';
    import { mapGetters } from 'vuex';

    export default {
        name: 'DialogAddFax',
        components: {
            Dialog: () => import('src/components/Dialogs/Dialog.vue'),
            UploadFileToServer: () => import('src/components/Upload/UploadFileToServer.vue'),
            OutlineBtn: () => import('src/components/Buttons/OutlineBtn.vue'),
            BaseInput: () => import('src/components/Elements/BaseInput.vue'),
            SelectWithSearchInput: () => import('src/components/Elements/SelectWithSearchInput.vue'),
            DatePicker: () => import('src/components/Elements/DatePicker.vue'),
        },
        mixins: [showNotif],
        data() {
            this.$_emptyInputs = [
                {
                    type: 'date',
                    value: '',
                    field: 'departure_date',
                    icon: 'person',
                    autofocus: true,
                    placeholder: this.$t('date'),
                },
                {
                    type: 'select',
                    field: 'transporter_id',
                    icon: 'local_shipping',
                    lang: 'transporter',
                    options: [],
                    objectOptions: [],
                    value: null,
                    label: this.$t('code.one'),
                    selected: [],
                },
                {
                    type: 'text',
                    value: '',
                    field: 'name',
                    icon: 'list',
                    lang: 'fax',
                    autofocus: true,
                },
                {
                    type: 'select',
                    value: null,
                    field: 'transport_id',
                    icon: 'wc',
                    options: [],
                    objectOptions: [],
                    label: this.$t('transport'),
                },
            ];
            return {
                uploadFaxData: {
                    url: getUrl('uploadFaxData'),
                    accept: '.xlsx, .xls, .csv',
                    faxID: 0,
                    hideUploadButton: true,
                },
                btnData: {
                    title: 'add',
                },
                cancelBtnData: {
                    title: 'close',
                    color: 'red',
                },
                dialogUploadFaxData: {
                    title: 'Добавление факса',
                    value: false,
                    labelBtn: 'Загрузить файл',
                    persistent: true,
                    minWidth: '350px',
                },
                errorsData: {
                    errors: {},
                },
                inputs: [
                    {
                        type: 'date',
                        value: '',
                        field: 'departure_date',
                        icon: 'person',
                        autofocus: true,
                        placeholder: this.$t('date'),
                    },
                    {
                        type: 'select',
                        field: 'transporter_id',
                        icon: 'local_shipping',
                        lang: 'transporter',
                        searchOptions: [],
                        options: [],
                        value: null,
                        label: this.$t('code.one'),
                    },
                    {
                        type: 'text',
                        value: '',
                        field: 'name',
                        icon: 'list',
                        lang: 'fax',
                        autofocus: true,
                    },
                    {
                        type: 'select',
                        value: null,
                        field: 'transport_id',
                        icon: 'wc',
                        label: this.$t('transport'),
                        searchOptions: [],
                        options: [],
                    },
                ],
            };
        },
        computed: {
            ...mapGetters({
                transporters: 'transporter/getTransporters',
                transport: 'transport/getTransports',
            }),
        },
        watch: {
            'uploadFaxData.faxID': function faxId(val) {
                if (!val) {
                    this.dialogUploadFaxData.value = false;
                    this.$q.loading.hide();
                    this.showNotif('success', 'Факс успешно добавлен.', 'center');
                }
            },
            transporters(val) {
                const transporter = _.find(this.inputs, { field: 'transporter_id' });
                transporter.searchOptions = val;
                transporter.options = val;
            },
            transport(val) {
                const transport = _.find(this.inputs, { field: 'transport_id' });
                transport.options = val;
                transport.searchOptions = val;
            },
        },
        created() {
            this.$store.dispatch('transporter/fetchTransporters');
            this.$store.dispatch('transport/fetchTransports');
        },
        methods: {
            collectData() {
                return _.reduce(this.inputs, (result, item) => {
                    if (item.type === 'date') {
                        result[item.field] = this.formatDate(item.value);
                    } else {
                        result[item.field] = item.value;
                    }

                    return result;
                }, {});
            },
            saveFaxOnServer() {
                if (this.uploadFaxData.canUpload) {
                    this.$q.loading.show();
                    devlog.log('SDS', this.collectData());
                    this.$axios.post(getUrl('addFax'), this.collectData())
                      .then(({ data }) => {
                          this.uploadFaxData.faxID = data.faxID;
                          this.$store.dispatch('faxes/addFax', data.fax);
                      })
                      .catch(({ response }) => {
                          this.errorsData.errors = _.get(response, 'data.errors');
                          this.$q.loading.hide();
                      });
                } else {
                    this.showNotif('warning', 'Добавте файл!', 'center');
                }
            },
            formatDate(date) {
                return date.split('.')
                  .reverse()
                  .join('/');
            },
        },
    };
</script>
