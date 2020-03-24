<template>
  <Dialog
    :dialog.sync="show"
    title="Клиент"
    :persistent="true"
    data-vue-component-name="DialogAddClient"
  >
    <Card style="min-width: 320px;width: 100%;max-width: 500px;">
      <CardSection class="row justify-between bg-grey q-mb-sm">
        <span class="text-h6">{{ dialogTitle }}</span>
        <div>
          <IconBtn
            v-show="entryData.id"
            dense
            icon="delete"
            color="negative"
            tooltip="Удалить"
            @iconBtnClick="destroyCustomer(entryData)"
          />
          <IconBtn
            dense
            icon="clear"
            tooltip="Закрыть"
            @iconBtnClick="close(customerData)"
          />
        </div>
      </CardSection>

      <CardSection>
        <div
          v-for="(item, index) in customerData"
          :key="index"
        >
          <SearchSelect
            v-if="item.type==='searchSelect'"
            v-model.number="item.value"
            :label="item.label"
            :dense="$q.screen.xs || $q.screen.sm"
            :field="item.field"
            :options="item.options"
            :func-load-data="item.funcLoadData"
            :change-value.sync="item.changeValue"
            :errors="errorsData"
          />

          <BaseSelect
            v-else-if="item.type==='select'"
            v-model.number="item.value"
            :label="item.label"
            :dense="$q.screen.xs || $q.screen.sm"
            :field="item.field"
            :options="item.options"
            :change-value.sync="item.changeValue"
            :errors="errorsData"
          />

          <BaseInput
            v-else
            v-model="item.value"
            :label="item.label"
            :dense="$q.screen.xs || $q.screen.sm"
            :field="item.field"
            :mask="item.mask"
            :unmasked-value="item.unmaskedValue"
            :change-value.sync="item.changeValue"
            :autofocus="item.autofocus"
            :errors="errorsData"
          />
        </div>
      </CardSection>

      <Separator />
      <CardActions>
        <BaseBtn
          label="Отмена"
          color="negative"
          :dense="$q.screen.xs || $q.screen.sm"
          @clickBaseBtn="close(customerData)"
        />

        <BaseBtn
          label="Сохранить"
          color="positive"
          :dense="$q.screen.xs || $q.screen.sm"
          @clickBaseBtn="checkErrors(customerData, saveData)"
        />
      </CardActions>
    </Card>
  </Dialog>
</template>

<script>
    import { getUrl } from 'src/tools/url';
    import CheckErrorsMixin from 'src/mixins/CheckErrors';
    import OnKeyUp from 'src/mixins/OnKeyUp';
    import showNotif from 'src/mixins/showNotif';
    import getFromSettings from 'src/tools/settings';
    import {
        getClientCodes,
        setDefaultData,
        getCities,
        setChangeValue,
    } from 'src/utils/FrequentlyCalledFunctions';

    export default {
        name: 'DialogAddClient',
        components: {
            BaseSelect: () => import('src/components/Elements/BaseSelect.vue'),
            Dialog: () => import('src/components/Dialogs/Dialog.vue'),
            // OutlineBtn: () => import('src/components/Buttons/OutlineBtn.vue'),
            BaseInput: () => import('src/components/Elements/BaseInput.vue'),
            SearchSelect: () => import('src/components/Elements/SearchSelect.vue'),
            // SelectChips: () => import('src/components/Elements/SelectChips.vue'),
            Separator: () => import('src/components/Separator.vue'),
            Card: () => import('src/components/Elements/Card/Card.vue'),
            CardActions: () => import('src/components/Elements/Card/CardActions.vue'),
            CardSection: () => import('src/components/Elements/Card/CardSection.vue'),
            IconBtn: () => import('src/components/Buttons/IconBtn.vue'),
            BaseBtn: () => import('src/components/Buttons/BaseBtn.vue'),
        },
        mixins: [OnKeyUp, showNotif, CheckErrorsMixin],
        props: {
            showDialog: {
                type: Boolean,
                default: false,
            },
            codeId: {
                type: Number,
                default: 0,
            },
            entryData: {
                type: Object,
                default: () => ({}),
            },
        },
        data() {
            return {
                customers: [],
                show: false,
                customerData: {
                    code_id: {
                        type: 'searchSelect',
                        label: 'Код',
                        field: 'code_id',
                        options: [],
                        require: true,
                        requireError: 'Поле обьзательное для заполнения.',
                        funcLoadData: getClientCodes,
                        changeValue: false,
                        default: null,
                        value: null,
                    },
                    name: {
                        type: 'text',
                        label: 'Имя',
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
                        default: '',
                        value: '',
                    },
                    phone: {
                        type: 'text',
                        label: 'Телефон',
                        field: 'phone',
                        mask: '+## (###) ###-##-##',
                        unmaskedValue: true,
                        rules: [
                            {
                                name: 'isLength',
                                error: 'Минимальное количество символов 12.',
                                options: {
                                    min: 12,
                                    max: 100,
                                },
                            },
                        ],
                        require: true,
                        requireError: 'Поле обьзательное для заполнения.',
                        changeValue: false,
                        default: '',
                        value: '',
                    },
                    city_id: {
                        type: 'searchSelect',
                        label: 'Город',
                        field: 'city_id',
                        options: [],
                        require: false,
                        requireError: 'Поле обьзательное для заполнения.',
                        funcLoadData: getCities,
                        changeValue: false,
                        default: null,
                        value: null,
                    },
                    sex: {
                        type: 'select',
                        label: 'Пол',
                        field: 'sex',
                        options: getFromSettings('sex'),
                        require: true,
                        requireError: 'Поле обьзательное для заполнения.',
                        changeValue: false,
                        default: 0,
                        value: 0,
                    },
                },
            };
        },
        computed: {
            clientCodes() {
                return this.$store.getters['codes/getCodes'];
            },
            cities() {
                return this.$store.getters['cities/getCities'];
            },
            dialogTitle() {
                return _.isEmpty(this.entryData) ? 'Новый клиет' : 'Редактирование';
            },
        },
        watch: {
            entryData(val) {
                devlog.log('entryData', val);
                if (!_.isEmpty(val)) {
                    _.forEach(this.customerData, (item, index) => {
                        if (_.get(val, index)) {
                            _.set(item, 'value', _.get(val, index));
                        }
                    });
                }
            },
            showDialog(val) {
                this.$q.loading.show();
                Promise.all([getClientCodes(this.$store), getCities(this.$store)])
                  .then(() => {
                      this.showAddEntryOnStorehouseDialog = true;
                      this.show = val;
                      this.$q.loading.hide();
                  })
                  .catch(() => {
                      this.$q.loading.hide();
                      devlog.warn('Ошибка при получении данных. Edit storehouseData');
                  });
            },
            show(val) {
                this.$emit('update:showDialog', val);
            },
            clientCodes: {
                handler: function set(val) {
                    _.set(this.customerData, 'code_id.options', val);
                },
                immediate: true,
            },
            cities: {
                handler: function set(val) {
                    _.set(this.customerData, 'city_id.options', val);
                },
                immediate: true,
            },
            codeId: {
                handler: function set(val) {
                    devlog.log('CODE_ID', val);
                    if (val) {
                        _.set(this.customerData, 'code_id.value', val);
                    }
                },
                immediate: true,
            },
        },
        methods: {
            saveData(values) {
                const createData = _.reduce(values, (result, { value }, index) => {
                    if (index === 'name') {
                        result[index] = _.startCase(value);
                    } else {
                        result[index] = value;
                    }
                    return result;
                }, {});
                if (_.isEmpty(this.entryData)) {
                    // ДОБАВЛЕНИЕ ЗАПИСИ
                    this.$q.loading.show();
                    this.$axios.post(getUrl('storeCustomers'), createData)
                      .then(({ data: { customer } }) => {
                          this.$q.loading.hide();
                          this.$store.dispatch('codes/addCustomerToCodeWithCustomers', customer);
                          this.showNotif('success', 'Клиент успешно добавлен.', 'center');
                      })
                      .catch((errors) => {
                          this.errorsData.errors = _.get(errors, 'response.data.errors');
                          this.$q.loading.hide();
                      });
                } else {
                    // ОБНОВЛЕНИЕ ЗАПИСИ
                    const updateData = _.reduce(values, (result, { value, changeValue }, index) => {
                        if (changeValue) {
                            if (index === 'name') {
                                result[index] = _.startCase(value);
                            } else {
                                result[index] = value;
                            }
                        }
                        return result;
                    }, {});
                    if (!_.isEmpty(updateData)) {
                        updateData.id = this.entryData.id;
                        devlog.log('VALUIR', values);

                        this.$q.loading.show();
                        this.$axios.post(getUrl('updateCustomer'), updateData)
                          .then(({ data: { customer } }) => {
                              this.$q.loading.hide();
                              this.$store.dispatch('codes/addCustomerToCodeWithCustomers', customer);
                              setChangeValue(values);
                              this.showNotif('success', 'Данные клиента успешно обновлены.', 'center');
                          })
                          .catch((errors) => {
                              this.errorsData.errors = _.get(errors, 'response.data.errors');
                              this.$q.loading.hide();
                          });
                    } else {
                        this.showNotif('info', 'Нет измененных данных', 'center');
                    }
                }
            },
            destroyCustomer({ id, name }) {
                this.showNotif('warning', `Удалить клиента - ${name} ?`, 'center', [
                    {
                        label: 'Отмена',
                        color: 'white',
                        handler: () => {
                        },
                    },
                    {
                        label: 'Удалить',
                        color: 'white',
                        handler: () => {
                            this.$axios.get(`${getUrl('destroyCustomerEntry')}/${id}`)
                              .then(() => {
                                  this.$store.dispatch('codes/deleteCustomer', {
                                      id,
                                      code_id: this.entryData.code_id,
                                  });
                                  this.close(this.customerData);
                                  this.showNotif('success', 'Запись успешно удалена.', 'center');
                              })
                              .catch(() => {
                                  devlog.error('Ошибка запроса - destroyCustomerEntry');
                              });
                        },
                    },
                ]);
            },
            close(data) {
                this.show = false;
                setDefaultData(data);
                setChangeValue(data);
                this.$emit('update:entryData', {});
            },
        },
    };
</script>
