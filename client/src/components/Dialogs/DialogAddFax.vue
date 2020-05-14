<template>
  <Dialog
    :dialog.sync="show"
    title="Fax"
    :persistent="true"
    data-vue-component-name="DialogAddFax"
  >
    <Card style="min-width: 320px;width: 100%;max-width: 500px;">
      <CardSection class="row justify-between bg-grey q-mb-sm">
        <span class="text-h6">{{ entryData.row ? 'Редактирование' : 'Новый факс' }}</span>
        <div>
          <IconBtn
            v-if="entryData.row"
            dense
            icon="history"
            tooltip="История"
            @iconBtnClick="entryData.historyFunc(entryData.row.id, entryData.cols)"
          />
          <IconBtn
            dense
            icon="clear"
            tooltip="Закрыть"
            @iconBtnClick="close(faxData)"
          />
        </div>
      </CardSection>
      <CardSection>
        <BaseInput
          v-model.trim="faxData.name.value"
          :label="faxData.name.label"
          :type="faxData.name.type"
          :autofocus="true"
          :field="faxData.name.field"
          :dense="$q.screen.xs || $q.screen.sm"
          :change-value.sync="faxData.name.changeValue"
          :errors="errorsData"
        />

        <SearchSelect
          v-model.number="faxData.transporter_id.value"
          :label="faxData.transporter_id.label"
          :dense="$q.screen.xs || $q.screen.sm"
          :field="faxData.transporter_id.field"
          :func-load-data="faxData.transporter_id.funcLoadData"
          :change-value.sync="faxData.transporter_id.changeValue"
          :options="transporters"
          :errors="errorsData"
        />

        <BaseSelect
          v-model.number="faxData.transport_id.value"
          :label="faxData.transport_id.label"
          :dense="$q.screen.xs || $q.screen.sm"
          :field="faxData.transport_id.field"
          :change-value.sync="faxData.transport_id.changeValue"
          :options="transport"
          :errors="errorsData"
        />

        <BaseSelect
          v-model.number="faxData.status.value"
          :label="faxData.status.label"
          :dense="$q.screen.xs || $q.screen.sm"
          :field="faxData.status.field"
          :options="faxData.status.options"
          :change-value.sync="faxData.status.changeValue"
          :errors="errorsData"
        />

        <BaseInput
          v-model.trim="faxData.departure_date.value"
          :label="faxData.departure_date.label"
          :errors="errorsData"
          :field="faxData.departure_date.field"
          :mask="faxData.departure_date.mask"
          :change-value.sync="faxData.departure_date.changeValue"
          dense
        >
          <template v-slot:append>
            <Date
              :value.sync="faxData.departure_date.value"
              :change-value.sync="faxData.departure_date.changeValue"
            />
          </template>
        </BaseInput>

        <BaseInput
          v-model.trim="faxData.arrival_date.value"
          :label="faxData.arrival_date.label"
          :errors="errorsData"
          :field="faxData.arrival_date.field"
          :mask="faxData.arrival_date.mask"
          :change-value.sync="faxData.arrival_date.changeValue"
          dense
        >
          <template v-slot:append>
            <Date
              :value.sync="faxData.arrival_date.value"
              :change-value.sync="faxData.arrival_date.changeValue"
            />
          </template>
        </BaseInput>

        <BaseInput
          v-model.trim="faxData.notation.value"
          :label="faxData.notation.label"
          :type="faxData.notation.type"
          :field="faxData.notation.field"
          :dense="$q.screen.xs || $q.screen.sm"
          :change-value.sync="faxData.notation.changeValue"
          :errors="errorsData"
        />
      </CardSection>

      <Separator />
      <CardActions>
        <OutlineBtn
          label="Закрыть"
          color="negative"
          @clickOutlineBtn="close(faxData)"
        />

        <OutlineBtn
          label="Сохранить"
          color="positive"
          @clickOutlineBtn="checkErrors(faxData, saveFax)"
        />

      </CardActions>
    </Card>
  </Dialog>
</template>

<script>
    import { getUrl } from 'src/tools/url';
    import showNotif from 'src/mixins/showNotif';
    import CheckErrorsMixin from 'src/mixins/CheckErrors';
    import getFromSettings from 'src/tools/settings';
    import {
        setDefaultData,
        getTransports,
        getTransporters,
        setFormatedDate,
        setChangeValue,
    } from 'src/utils/FrequentlyCalledFunctions';
    import {
        reverseDate,
        addTime,
    } from 'src/utils/formatDate';
    import { formatISO } from 'date-fns';

    export default {
        name: 'DialogAddFax',
        components: {
            Dialog: () => import('src/components/Dialogs/Dialog.vue'),
            OutlineBtn: () => import('src/components/Buttons/OutlineBtn.vue'),
            BaseInput: () => import('src/components/Elements/BaseInput.vue'),
            SearchSelect: () => import('src/components/Elements/SearchSelect.vue'),
            Card: () => import('src/components/Elements/Card/Card.vue'),
            CardActions: () => import('src/components/Elements/Card/CardActions.vue'),
            CardSection: () => import('src/components/Elements/Card/CardSection.vue'),
            Separator: () => import('src/components/Separator.vue'),
            IconBtn: () => import('src/components/Buttons/IconBtn.vue'),
            BaseSelect: () => import('src/components/Elements/BaseSelect.vue'),
            Date: () => import('src/components/Date.vue'),
        },
        mixins: [showNotif, CheckErrorsMixin],
        props: {
            showDialog: {
                type: Boolean,
                default: false,
            },
            entryData: {
                type: Object,
                default: () => ({}),
            },
        },
        data() {
            return {
                value: '',
                show: false,
                faxData: {
                    name: {
                        type: 'text',
                        label: 'Название',
                        field: 'name',
                        autofocus: true,
                        rules: [
                            {
                                name: 'isLength',
                                error: 'Минимальное количество символов 2.',
                                options: {
                                    min: 2,
                                    max: 100,
                                },
                            },
                        ],
                        require: true,
                        requireError: 'Поле обьзательное для заполнения.',
                        changeValue: false,
                        default: 'Факс ',
                        value: 'Факс ',
                    },
                    status: {
                        type: 'select',
                        label: 'Статус',
                        field: 'status',
                        options: getFromSettings('transportStatusOptions'),
                        require: true,
                        requireError: 'Поле обьзательное для заполнения.',
                        changeValue: false,
                        default: 0,
                        value: 0,
                    },
                    transporter_id: {
                        type: 'number',
                        label: 'Перевожчик',
                        field: 'transporter_id',
                        require: true,
                        requireError: 'Поле обьзательное для заполнения.',
                        funcLoadData: getTransporters,
                        changeValue: false,
                        default: null,
                        value: null,
                    },
                    transport_id: {
                        type: 'number',
                        label: 'Транспорт',
                        field: 'transport_id',
                        require: true,
                        requireError: 'Поле обьзательное для заполнения.',
                        changeValue: false,
                        default: null,
                        value: null,
                    },
                    departure_date: {
                        type: 'date',
                        label: 'Дата отправления',
                        field: 'departure_date',
                        require: false,
                        requireError: 'Поле обьзательное для заполнения.',
                        changeValue: false,
                        mask: '##-##-#### ##:##:##',
                        default: '',
                        value: '',
                    },
                    arrival_date: {
                        type: 'date',
                        label: 'Дата прибытия',
                        field: 'arrival_date',
                        require: false,
                        requireError: 'Поле обьзательное для заполнения.',
                        changeValue: false,
                        mask: '##-##-#### ##:##:##',
                        default: '',
                        value: '',
                    },
                    notation: {
                        type: 'text',
                        label: 'Примечания',
                        field: 'notation',
                        rules: [
                            {
                                name: 'isLength',
                                error: 'Максимальное количество символов 255.',
                                options: {
                                    min: undefined,
                                    max: 255,
                                },
                            },
                        ],
                        changeValue: false,
                        default: '',
                        value: '',
                    },
                },
            };
        },
        computed: {
            transporters() {
                return this.$store.getters['transporter/getTransporters'];
            },
            transport() {
                return this.$store.getters['transport/getTransports'];
            },
        },
        watch: {
            showDialog(val) {
                if (val) {
                    this.$q.loading.show();
                    Promise.all([getTransports(this.$store), getTransporters(this.$store)])
                      .then(() => {
                          this.show = val;
                          this.$q.loading.hide();
                      });
                }
            },
            show(val) {
                this.$emit('update:showDialog', val);
            },
            entryData(val) {
                if (!_.isEmpty(val)) {
                    devlog.log('vALIN_ADD', val);
                    _.forEach(this.faxData, (item, index) => {
                        devlog.log('ITEM', item);
                        if (_.get(this.entryData, `row[${index}]`)) {
                            _.set(item, 'value', _.get(this.entryData, `row[${index}]`));
                        }
                    });
                }
            },
            'faxData.status.value': function view(val) {
                devlog.log('STAT_VAL', val);
                switch (val) {
                    case 1:
                        this.faxData.departure_date.value = '';
                        this.faxData.arrival_date.value = '';
                        // this.faxData.departure_date.changeValue = true;
                        // this.faxData.arrival_date.changeValue = true;
                        this.faxData.departure_date.require = false;
                        this.faxData.arrival_date.require = false;
                        break;
                    case 2:
                        this.faxData.departure_date.require = true;
                        this.faxData.arrival_date.require = false;
                        this.faxData.arrival_date.value = '';
                        // this.faxData.arrival_date.changeValue = true;
                        break;
                    case 3:
                        this.faxData.arrival_date.require = true;
                        this.faxData.departure_date.require = true;
                        break;
                    case 4:
                        this.faxData.arrival_date.require = false;
                        this.faxData.departure_date.require = true;
                        this.faxData.arrival_date.value = '';
                        // this.faxData.arrival_date.changeValue = true;
                        break;
                    default:
                        this.faxData.departure_date.require = false;
                        this.faxData.departure_date.require = false;
                }
            },
        },
        methods: {
            saveFax(faxData) {
                devlog.log('STRA');
                const sendData = _.reduce(faxData, (result, { value, changeValue }, index) => {
                    if (changeValue) {
                        if (index === 'name') {
                            result[index] = _.upperFirst(value);
                        } else if (index === 'departure_date' && value) {
                            if (value) {
                                const date = reverseDate(value);
                                result[index] = formatISO(addTime(date));
                            } else {
                                result[index] = null;
                            }
                        } else if (index === 'arrival_date' && value) {
                            if (value) {
                                const date = reverseDate(value);
                                result[index] = formatISO(addTime(date));
                            } else {
                                result[index] = null;
                            }
                        } else {
                            result[index] = value;
                        }
                    }
                    return result;
                }, {});
                devlog.log('STRA_2', sendData);
                if (!_.isEmpty(sendData)) {
                    this.$q.loading.show();
                    if (this.entryData.row) {
                        sendData.id = _.get(this.entryData, 'row.id');
                        this.$axios.post(getUrl('updateFaxes'), sendData)
                          .then(({ data: { fax } }) => {
                              this.$q.loading.hide();
                              this.$store.dispatch('faxes/updateFax', setFormatedDate(fax, ['departure_date', 'arrival_date']));
                              devlog.log('Fx0', fax);
                              setChangeValue(this.faxData);
                              this.close(this.faxData);
                              this.showNotif('success', 'Факс успешно обновлен.', 'center');
                          })
                          .catch(({ response: { data: { errors } } }) => {
                              this.errorsData.errors = errors;
                              this.$q.loading.hide();
                          });
                    } else {
                        this.$axios.post(getUrl('addFax'), sendData)
                          .then(({ data: { fax } }) => {
                              this.$q.loading.hide();
                              this.$store.dispatch('faxes/addFax', setFormatedDate(fax, ['departure_date', 'arrival_date']));
                              this.close(this.faxData);
                              this.showNotif('success', 'Факс успешно добавлен.', 'center');
                          })
                          .catch(({ response: { data: { errors } } }) => {
                              this.errorsData.errors = errors;
                              this.$q.loading.hide();
                          });
                    }
                } else {
                    this.close(this.faxData);
                }
            },
            close(data) {
                this.show = false;
                setDefaultData(data);
                this.entryData.selected = false;
            },
        },
    };
</script>
