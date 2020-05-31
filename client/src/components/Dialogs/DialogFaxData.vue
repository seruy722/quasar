<template>
  <Dialog
    :dialog.sync="show"
    :persistent="true"
    title="Запись"
    data-vue-component-name="DialogFaxData"
  >
    <Card style="min-width: 320px;width: 100%;max-width: 900px;">
      <CardSection class="row justify-between bg-grey q-mb-sm">
        <span class="text-h6">{{ entryData.row ? 'Редактирование' : 'Новая запись' }}</span>
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
            @iconBtnClick="close(storehouseData)"
          />
        </div>
      </CardSection>
      <CardSection>
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
              v-model.trim="item.value"
              :label="item.label"
              :field="index"
              :dense="$q.screen.xs || $q.screen.sm"
              :options="item.options"
              :disable="item.disable"
              :change-value.sync="item.changeValue"
              :func-load-data="item.funcLoadData"
              :errors="errorsData"
            />

            <SearchSelect
              v-else-if="item.type === 'select'"
              v-model.number="item.value"
              :label="item.label"
              :field="index"
              :dense="$q.screen.xs || $q.screen.sm"
              :options="item.options"
              :disable="item.disable"
              :change-value.sync="item.changeValue"
              :func-load-data="item.funcLoadData"
              :errors="errorsData"
            />
          </div>

          <q-checkbox
            v-show="showReplacePrice"
            v-model="storehouseData.replacePrice.value"
            label="Заменить цену"
            dense
          />
        </div>
        <div v-show="entryData.combineTableData === false">
          <CheckBox
            v-model="withoutThings"
            label="Без описи вложения"
          />
          <IconBtn
            v-if="things && !withoutThings"
            color="positive"
            icon="edit"
            :dense="$q.screen.xs || $q.screen.sm"
            tooltip="Редактировать"
            class="q-ml-md"
            @iconBtnClick="showThingsDialog = true"
          />
          <IconBtn
            v-else-if="!withoutThings"
            icon="add"
            :dense="$q.screen.xs || $q.screen.sm"
            tooltip="Добавить"
            class="q-ml-md"
            @iconBtnClick="showThingsDialog = true"
          />


          <div v-show="!withoutThings">
            <List
              dense
              bordered
              padding
              separator
              class="q-px-sm"
            >
              <ItemLabel>
                <div class="row items-center">
                  <div>
                    Опись вложения
                  </div>
                  <DialogAddThings
                    :things.sync="things"
                    :show-dialog.sync="showThingsDialog"
                    :change-things.sync="changeThings"
                  />
                </div>
              </ItemLabel>
              <ListItem class="text-bold">
                <ItemSection>Название</ItemSection>
                <ItemSection>Количество</ItemSection>
              </ListItem>

              <ListItem
                v-for="({title, count}, index) in thingsList"
                :key="index"
                v-ripple
                clickable
              >
                <ItemSection>{{ title }}</ItemSection>
                <ItemSection>{{ count }}</ItemSection>
              </ListItem>
            </List>
          </div>
        </div>
      </CardSection>
      <Separator />
      <CardActions>
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
      </CardActions>
    </Card>
  </Dialog>
</template>

<script>
    import CheckErrorsMixin from 'src/mixins/CheckErrors';
    import { getUrl } from 'src/tools/url';
    import showNotif from 'src/mixins/showNotif';
    import {
        getClientCodes,
        getCategories,
        getShopsList,
        setFormatedDate,
        setChangeValue,
    } from 'src/utils/FrequentlyCalledFunctions';

    export default {
        name: 'DialogFaxData',
        components: {
            Dialog: () => import('src/components/Dialogs/Dialog.vue'),
            DialogAddThings: () => import('src/components/Dialogs/DialogAddThings.vue'),
            IconBtn: () => import('src/components/Buttons/IconBtn.vue'),
            BaseInput: () => import('src/components/Elements/BaseInput.vue'),
            SearchSelect: () => import('src/components/Elements/SearchSelect.vue'),
            List: () => import('src/components/Elements/List/List.vue'),
            ItemSection: () => import('src/components/Elements/List/ItemSection.vue'),
            ItemLabel: () => import('src/components/Elements/List/ItemLabel.vue'),
            ListItem: () => import('src/components/Elements/List/ListItem.vue'),
            Card: () => import('src/components/Elements/Card/Card.vue'),
            CardActions: () => import('src/components/Elements/Card/CardActions.vue'),
            CardSection: () => import('src/components/Elements/Card/CardSection.vue'),
            BaseBtn: () => import('src/components/Buttons/BaseBtn.vue'),
            Separator: () => import('src/components/Separator.vue'),
            SelectChips: () => import('src/components/Elements/SelectChips.vue'),
            CheckBox: () => import('src/components/Elements/CheckBox.vue'),
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
                showReplacePrice: true,
                show: false,
                withoutThings: false,
                showThingsDialog: false,
                changeThings: false,
                things: null,
                storehouseData: {
                    code_place: {
                        name: 'code_place',
                        type: 'text',
                        label: this.$t('code'),
                        mask: '###/###/###',
                        rules: [
                            {
                                name: 'isLength',
                                error: 'Минимальное количество символов 11.',
                                options: {
                                    min: 11,
                                    max: 11,
                                },
                            },
                        ],
                        require: true,
                        requireError: 'Поле обьзательное для заполнения.',
                        autofocus: true,
                        disable: false,
                        changeValue: false,
                        default: '',
                        value: '',
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
                    kg: {
                        name: 'kg',
                        type: 'number',
                        label: this.$t('kg'),
                        require: true,
                        requireError: 'Поле обьзательное для заполнения.',
                        changeValue: false,
                        default: 0,
                        value: 0,
                    },
                    for_kg: {
                        name: 'for_kg',
                        type: 'number',
                        label: this.$t('for_kg'),
                        require: false,
                        requireError: 'Поле обьзательное для заполнения.',
                        changeValue: false,
                        default: 0,
                        value: 0,
                    },
                    for_place: {
                        name: 'for_place',
                        type: 'number',
                        label: this.$t('for_place'),
                        require: false,
                        requireError: 'Поле обьзательное для заполнения.',
                        changeValue: false,
                        default: 0,
                        value: 0,
                    },
                    category_id: {
                        name: 'category_id',
                        type: 'select',
                        label: this.$t('category'),
                        options: [],
                        require: true,
                        requireError: 'Выберите значение.',
                        changeValue: false,
                        funcLoadData: getCategories,
                        default: null,
                        value: null,
                    },
                    shop: {
                        name: 'shop',
                        type: 'select-chips',
                        label: this.$t('shop'),
                        options: [],
                        changeValue: false,
                        funcLoadData: getShopsList,
                        default: null,
                        value: null,
                    },
                    delivery_method_id: {
                        name: 'delivery_method_id',
                        type: 'select',
                        label: 'Способ доставки',
                        options: [],
                        changeValue: false,
                        default: 0,
                        value: 0,
                    },
                    notation: {
                        name: 'notation',
                        type: 'text',
                        label: this.$t('notation'),
                        changeValue: false,
                        default: '',
                        value: '',
                    },
                    replacePrice: {
                        name: 'replacePrice',
                        type: 'checkbox',
                        default: false,
                        value: false,
                    },
                },
            };
        },
        computed: {
            thingsList() {
                if (this.things) {
                    return JSON.parse(this.things);
                }
                return this.things;
            },
            categories() {
                return this.$store.getters['category/getCategories'];
            },
            clientCodes() {
                return this.$store.getters['codes/getCodes'];
            },
            shopList() {
                return this.$store.getters['shopsList/getShopsList'];
            },
            deliveryMethodsList() {
                return this.$store.getters['deliveryMethods/getDeliveryMethodsList'];
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
        },
        watch: {
            entryData(val) {
                if (!_.isEmpty(val)) {
                    devlog.log('vALIN_ADD', val);
                    _.forEach(this.storehouseData, (item, index) => {
                        if (_.get(val, `row[${index}]`)) {
                            _.set(item, 'value', _.get(val, `row[${index}]`));
                        }
                    });
                    this.things = _.get(val, 'row.things');
                    devlog.log('COMBINE', val);
                    // this.storehouseData.code_place.disable = true;
                    if (val.combineTableData) {
                        this.storehouseData.kg.disable = true;
                        this.storehouseData.shop.disable = true;
                        this.storehouseData.notation.disable = true;
                    } else {
                        this.storehouseData.kg.disable = false;
                        this.storehouseData.shop.disable = false;
                        this.storehouseData.notation.disable = false;
                    }
                    if (!_.get(val, 'row.for_kg')) {
                        delete this.storehouseData.for_kg;
                        delete this.storehouseData.for_place;
                        this.showReplacePrice = false;
                    }
                }
            },
            categories: {
                handler: function set(val) {
                    this.storehouseData.category_id.options = val;
                },
                immediate: true,
            },
            clientCodes: {
                handler: function set(val) {
                    this.storehouseData.code_client_id.options = val;
                },
                immediate: true,
            },
            shopList: {
                handler: function set(val) {
                    this.storehouseData.shop.options = val;
                },
                immediate: true,
            },
            deliveryMethodsList: {
                handler: function set(val) {
                    this.storehouseData.delivery_method_id.options = val;
                },
                immediate: true,
            },
            showDialog(val) {
                this.show = val;
            },
            show(val) {
                this.$emit('update:showDialog', val);
            },
        },
        methods: {
            saveData() {
                devlog.log('saveData');
                const sendData = _.reduce(this.storehouseData, (result, { changeValue, value }, index) => {
                    if (changeValue) {
                        result[index] = value;
                    } else if (index === 'replacePrice' && value) {
                        result[index] = value;
                    }
                    return result;
                }, {});

                if (this.changeThings) {
                    sendData.things = this.things;
                }
                if (_.has(sendData, 'shop')) {
                    sendData.shop = _.startCase(sendData.shop);
                }
                devlog.log('DATA_TO_SAVE', sendData);
                if (this.withoutThings || this.changeThings || this.entryData.row) {
                    if (!this.entryData.row) {
                        this.$q.loading.show();
                        this.$axios.post(getUrl('addStorehouseData'), sendData)
                          .then(({ data: { storehouseData } }) => {
                              this.$store.dispatch('storehouse/addToStorehouseData', setFormatedDate(storehouseData, ['created_at']));
                              // this.setChangeValue(this.storehouseData);
                              this.$q.loading.hide();
                              this.showNotif('success', 'Запись успешно добавлена.', 'center');
                          })
                          .catch((errors) => {
                              this.errorsData.errors = _.get(errors, 'response.data.errors');
                              this.$q.loading.hide();
                          });
                    } else {
                        devlog.log('UPDATE');
                        if (!_.isEmpty(sendData)) {
                            sendData.id = _.get(this.entryData, 'row.id');
                            this.$q.loading.show();
                            this.$axios.post(getUrl('updateStorehouseData'), sendData)
                              .then(({ data: { storehouseData } }) => {
                                  devlog.log('DTA_UPDATE', storehouseData);
                                  const formatedData = setFormatedDate(storehouseData, ['created_at']);
                                  this.$store.dispatch('storehouse/updateStorehouseData', formatedData);
                                  this.$store.dispatch('faxes/updateFaxData', formatedData)
                                    .catch((e) => {
                                        devlog.error('EROR', e);
                                    });
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
                    }
                } else {
                    this.showThingsDialog = true;
                }
            },
            clear(data) {
                _.forEach(data, (item) => {
                    item.value = item.default;
                });
                this.changeThings = false;
                this.withoutThings = false;
                this.things = null;
            },
            remove(index) {
                const things = JSON.parse(this.things);
                things.splice(index, 1);
                this.things = JSON.stringify(things);
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
