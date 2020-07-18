<template>
  <Dialog
    :dialog.sync="show"
    :persistent="true"
    title="Долг"
    data-vue-component-name="DialogAddCargoDebtEntry"
  >
    <q-card style="min-width: 320px;width: 100%;max-width: 900px;">
      <q-card-section class="row justify-between items-center bg-grey q-mb-sm">
        <span class="text-h6">{{ entryData.row ? 'Редактирование долга' : 'Новая запись долга' }}</span>
        <q-list
          dense
        >
          <q-item>
            <q-item-section v-if="entryData.row">
              <q-item-label>
                <IconBtn
                  dense
                  icon="history"
                  tooltip="История"
                  @iconBtnClick="entryData.historyFunc(entryData.row.id, entryData.cols)"
                />
              </q-item-label>
            </q-item-section>
            <q-item-section>
              <q-item-label>
                <Menu :items="['Код', 'Клиента', 'Категорию']" />
              </q-item-label>
            </q-item-section>
            <q-item-section>
              <q-item-label>
                <IconBtn
                  dense
                  icon="clear"
                  tooltip="Закрыть"
                  @iconBtnClick="close(storehouseData)"
                />
              </q-item-label>
            </q-item-section>
          </q-item>
        </q-list>
      </q-card-section>
      <q-card-section>
        <div class="fit row wrap justify-start items-start content-start">
          <div
            v-for="(item, index) in storehouseData"
            :key="index"
            class="col-xs-12 col-sm-4 col-md-4 col-lg-4 q-pt-md q-px-sm bg-white"
          >
            <BaseInput
              v-if="item.type === 'text'"
              v-model.trim="item.value"
              :label="item.label"
              :autofocus="item.autofocus"
              :type="item.type"
              :mask="item.mask"
              :dense="$q.screen.xs || $q.screen.sm"
              :field="index"
              :readonly="item.readonly"
              :disable="item.disable"
              :change-value.sync="item.changeValue"
              :errors="errorsData"
            />

            <BaseInput
              v-else-if="item.type === 'number'"
              v-model.number="item.value"
              :label="item.label"
              :type="item.type"
              :mask="item.mask"
              :dense="$q.screen.xs || $q.screen.sm"
              :field="index"
              :disable="item.disable"
              :change-value.sync="item.changeValue"
              :errors="errorsData"
            />

            <SelectChips
              v-else-if="item.type === 'select-chips'"
              v-model="item.value"
              :label="item.label"
              :field="index"
              :dense="$q.screen.xs || $q.screen.sm"
              :options="item.options"
              :change-value.sync="item.changeValue"
              :func-load-data="item.funcLoadData"
              :errors="errorsData"
            />
            <BaseInput
              v-else-if="item.type === 'date'"
              v-model.trim="item.value"
              :label="item.label"
              :field="index"
              :mask="item.mask"
              :change-value.sync="item.changeValue"
              :dense="$q.screen.xs || $q.screen.sm"
              :errors="errorsData"
            >
              <template v-slot:append>
                <Date
                  :value.sync="item.value"
                  :change-value.sync="item.changeValue"
                />
              </template>
            </BaseInput>

            <SearchSelect
              v-else
              v-model="item.value"
              :label="item.label"
              :field="index"
              :dense="$q.screen.xs || $q.screen.sm"
              :options="item.options"
              :change-value.sync="item.changeValue"
              :func-load-data="item.funcLoadData"
              :errors="errorsData"
            />
          </div>
        </div>
      </q-card-section>
      <Separator />
      <q-card-actions align="right">
        <BaseBtn
          :label="$t('save')"
          color="positive"
          icon="save"
          :size="size"
          @clickBaseBtn="checkErrors(storehouseData, saveData)"
        />

        <BaseBtn
          :label="$t('clear')"
          color="negative"
          icon="clear"
          :size="size"
          @clickBaseBtn="clear(storehouseData)"
        />

        <BaseBtn
          :label="$t('close')"
          color="negative"
          icon="cancel"
          :size="size"
          @clickBaseBtn="close(storehouseData)"
        />
      </q-card-actions>
    </q-card>
  </Dialog>
</template>

<script>
    import CheckErrorsMixin from 'src/mixins/CheckErrors';
    // import { getUrl } from 'src/tools/url';
    import showNotif from 'src/mixins/showNotif';
    import {
        getClientCodes,
        getCategories,
        getFaxes,
        // getShopsList,
        // setFormatedDate,
        // setChangeValue,
    } from 'src/utils/FrequentlyCalledFunctions';

    export default {
        name: 'DialogAddCargoDebtEntry',
        components: {
            Dialog: () => import('src/components/Dialogs/Dialog.vue'),
            IconBtn: () => import('src/components/Buttons/IconBtn.vue'),
            BaseInput: () => import('src/components/Elements/BaseInput.vue'),
            SearchSelect: () => import('src/components/Elements/SearchSelect.vue'),
            BaseBtn: () => import('src/components/Buttons/BaseBtn.vue'),
            Separator: () => import('src/components/Separator.vue'),
            SelectChips: () => import('src/components/Elements/SelectChips.vue'),
            Menu: () => import('src/components/Menu.vue'),
            Date: () => import('src/components/Date.vue'),
        },
        mixins: [CheckErrorsMixin, showNotif],
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
                show: false,
                storehouseData: {
                    created_at: {
                        name: 'created_at',
                        type: 'date',
                        label: 'Дата',
                        require: true,
                        requireError: 'Поле обьзательное для заполнения.',
                        changeValue: false,
                        default: new Date().toISOString()
                          .slice(0, 10)
                          .split('-')
                          .reverse()
                          .join('-'),
                        value: new Date().toISOString()
                          .slice(0, 10)
                          .split('-')
                          .reverse()
                          .join('-'),
                    },
                    code_client_id: {
                        name: 'code_client_id',
                        type: 'select',
                        label: this.$t('client'),
                        options: [],
                        require: true,
                        requireError: 'Выберите значение.',
                        changeValue: false,
                        funcLoadData: getClientCodes,
                        default: null,
                        value: null,
                    },
                    place: {
                        name: 'place',
                        type: 'number',
                        label: 'Мест',
                        changeValue: false,
                        default: 0,
                        value: 0,
                    },
                    kg: {
                        name: 'kg',
                        type: 'number',
                        label: 'Вес',
                        changeValue: false,
                        default: 0,
                        value: 0,
                    },
                    for_kg: {
                        name: 'for_kg',
                        type: 'number',
                        label: 'За кг',
                        changeValue: false,
                        default: 0,
                        value: 0,
                    },
                    for_place: {
                        name: 'for_place',
                        type: 'number',
                        label: 'За место',
                        changeValue: false,
                        default: 0,
                        value: 0,
                    },
                    sum: {
                        name: 'sum',
                        type: 'number',
                        label: 'Сумма',
                        changeValue: false,
                        default: 0,
                        value: 0,
                    },
                    // sale: {
                    //     name: 'sale',
                    //     type: 'number',
                    //     label: 'Скидка',
                    //     changeValue: false,
                    //     default: 0,
                    //     value: 0,
                    // },
                    paid: {
                        name: 'paid',
                        type: 'select',
                        label: 'Оплачен',
                        changeValue: false,
                        options: [{
                            label: 'Нет',
                            value: 0,
                        }, {
                            label: 'Да',
                            value: 1,
                        }],
                        default: 0,
                        value: 0,
                    },
                    category_id: {
                        type: 'select',
                        label: 'Категория',
                        field: 'category_id',
                        options: [],
                        funcLoadData: getCategories,
                        changeValue: false,
                        default: null,
                        value: null,
                    },
                    fax_id: {
                        type: 'select',
                        label: 'Факс',
                        field: 'fax_id',
                        options: [],
                        changeValue: false,
                        default: null,
                        value: null,
                    },
                    notation: {
                        name: 'notation',
                        type: 'text',
                        label: this.$t('notation'),
                        changeValue: false,
                        default: '',
                        value: '',
                    },
                },
            };
        },
        computed: {
            clientCodes() {
                return this.$store.getters['codes/getCodes'];
            },
            size() {
                const {
                    sm,
                    xs,
                    md,
                    lg,
                } = this.$q.screen;

                let size = '';
                if (sm) {
                    size = 'sm';
                } else if (xs) {
                    size = 'xs';
                } else if (md) {
                    size = 'md';
                } else if (lg) {
                    size = 'lg';
                }
                return size;
            },
            currentClientCodeId() {
                return this.$store.getters['cargoDebts/getCurrentCodeClientId'];
            },
            categories() {
                return this.$store.getters['category/getCategories'];
            },
            faxes() {
                return _.map(this.$store.getters['faxes/getFaxes'], ({ name, id }) => ({
                    label: name,
                    value: id,
                }));
            },
        },
        watch: {
            entryData(val) {
                if (!_.isEmpty(val)) {
                    devlog.log('vALIN_ADD', val);
                    _.forEach(this.storehouseData, (item, index) => {
                        const prop = _.get(this.entryData, `row[${index}]`);
                        if (index === 'created_at') {
                            _.set(item, 'value', prop.slice(0, 10));
                        } else if (prop) {
                            _.set(item, 'value', prop);
                        }
                    });
                }
            },
            clientCodes: {
                handler: function set(val) {
                    this.storehouseData.code_client_id.options = val;
                },
                immediate: true,
            },
            showDialog(val) {
                if (val) {
                    this.$q.loading.show();
                    Promise.all([getCategories(this.$store), getFaxes(this.$store)])
                      .then(() => {
                          this.storehouseData.code_client_id.value = this.currentClientCodeId;
                          this.storehouseData.category_id.options = this.categories;
                          this.storehouseData.fax_id.options = this.faxes;
                          this.show = val;
                          this.$q.loading.hide();
                      });
                }
            },
            show(val) {
                this.$emit('update:showDialog', val);
            },
        },
        methods: {
            async saveData() {
                devlog.log('saveData');
                const sendData = _.reduce(this.storehouseData, (result, { changeValue, value }, index) => {
                    if (changeValue) {
                        result[index] = value;
                    }
                    return result;
                }, {});
                if (_.has(sendData, 'created_at')) {
                    const { reverseDate, addTime } = await import('src/utils/formatDate');
                    sendData.created_at = addTime(reverseDate(sendData.created_at))
                      .toISOString();
                }
                const { getUrl } = await import('src/tools/url');
                const { setChangeValue } = await import('src/utils/FrequentlyCalledFunctions');
                if (!this.entryData.row && !_.isEmpty(sendData)) {
                    // CREATE
                    sendData.code_client_id = this.storehouseData.code_client_id.value;
                    this.$q.loading.show();
                    this.$axios.post(getUrl('createCargoDebtEntry'), sendData)
                      .then(({ data: { answer } }) => {
                          this.$store.dispatch('cargoDebts/addCargoEntry', answer);
                          this.$q.loading.hide();
                          this.showNotif('success', 'Запись успешно добавлена.', 'center');
                          this.close(this.storehouseData);
                      })
                      .catch((errors) => {
                          this.errorsData.errors = _.get(errors, 'response.data.errors');
                          this.$q.loading.hide();
                      });
                } else if (!_.isEmpty(sendData)) {
                    // UPDATE
                    if (_.get(this.entryData, 'row.arr') && _.size(_.get(this.entryData, 'row.arr')) === 1) {
                        sendData.id = _.get(_.first(_.get(this.entryData, 'row.arr')), 'id');
                    } else {
                        sendData.id = _.get(this.entryData, 'row.id');
                    }
                    this.$q.loading.show();
                    this.$axios.post(getUrl('updateCargoDebtEntry'), sendData)
                      .then(({ data: { answer } }) => {
                          devlog.log('DTA_UPDATE', answer);
                          this.$store.dispatch('cargoDebts/updateCargoEntry', answer);
                          setChangeValue(this.storehouseData);
                          this.$q.loading.hide();
                          this.showNotif('success', 'Запись успешно обновлена.', 'center');
                          this.close(this.storehouseData);
                      })
                      .catch((errors) => {
                          this.errorsData.errors = _.get(errors, 'response.data.errors');
                          this.$q.loading.hide();
                      });
                } else {
                    this.close(this.storehouseData);
                }
                devlog.log('DATA_TO_SAVE', sendData);
            },
            clear(data) {
                _.forEach(data, (item) => {
                    item.value = item.default;
                });
            },
            close(data) {
                this.clear(data);
                this.show = false;
                if (!_.isEmpty(this.entryData)) {
                    this.entryData.selected = false;
                    this.$emit('update:entryData', {});
                }
            },
        },
    };
</script>
