<template>
  <q-page
    data-vue-component-name="Transfers"
  >
    <PullRefresh @refresh="refresh">
      <!--    <TransfersListSkeleton v-if="viewSkeleton" />-->
      <Table
        :table-properties="transferTableProperties"
        :table-data="allTransfers"
        :table-reactive-properties="transferTableReactiveProperties"
        title="Переводы"
      >
        <template v-slot:top-buttons>
          <IconBtn
            color="primary"
            icon="sync"
            tooltip="Обновить"
            @iconBtnClick="refresh"
          />
          <IconBtn
            color="positive"
            tooltip="Excel"
            icon="explicit"
            class="q-ml-sm"
            @iconBtnClick="exportTransfers"
          />
        </template>
        <!--ОТОБРАЖЕНИЕ КОНТЕНТА НА МАЛЕНЬКИХ ЭКРАНАХ-->
        <template v-slot:inner-item="{props}">
          <div
            class="q-pa-xs col-xs-12 col-sm-6 col-md-4 col-lg-3 grid-style-transition"
            :style="props.selected ? 'transform: scale(0.95);' : ''"
          >
            <q-expansion-item
              expand-separator
              class="shadow-1 overflow-hidden"
              style="border-radius: 30px;border: 1px solid;"
              :class="`border_${statusColor(props.row.status_label)}`"
              :header-class="`bg-${statusColor(props.row.status_label)} text-white`"
              expand-icon-class="text-white"
            >
              <template v-slot:header>
                <ItemSection avatar>
                  <CheckBox
                    v-model="props.selected"
                  />
                </ItemSection>

                <ItemSection>
                  <ItemLabel :lines="2">
                    {{ props.row.client_name }}
                  </ItemLabel>
                </ItemSection>
              </template>

              <List
                separator
                dense
                @clickList="viewEditDialog(props)"
              >
                <ListItem
                  v-for="col in props.cols.filter(col => col.name !== 'desc')"
                  :key="col.name"
                >
                  <ItemSection>
                    <ItemLabel>{{ `${col.label}:` }}</ItemLabel>
                  </ItemSection>
                  <ItemSection side>
                    <ItemLabel v-if="col.field === 'status_label'">
                      <Badge :color="statusColor(col.value)">
                        {{ col.value }}
                      </Badge>
                    </ItemLabel>
                    <ItemLabel v-else-if="col.field === 'receiver_phone'">
                      {{ col.value | phoneNumberFilter }}
                    </ItemLabel>
                    <ItemLabel v-else-if="col.field === 'receiver_name'">
                      {{ col.value }}
                    </ItemLabel>
                    <ItemLabel
                      v-else-if="col.field === 'notation'"
                      :lines="3"
                    >
                      {{ col.value }}
                    </ItemLabel>
                    <ItemLabel v-else>
                      {{ col.value }}
                    </ItemLabel>
                  </ItemSection>
                </ListItem>
                <ListItem>
                  <ItemSection>
                    <BaseBtn
                      label="История"
                      color="info"
                      style="max-width: 100px;margin: 0 auto;"
                      @clickBaseBtn="getTransfersHistory(props.row.id, props.cols)"
                    />
                  </ItemSection>
                </ListItem>
              </List>
            </q-expansion-item>
          </div>
        </template>

        <template v-slot:inner-body="{props}">
          <q-tr
            :props="props"
            class="text-bold cursor-pointer"
            @click.stop="viewEditDialog(props, $event)"
          >
            <q-td
              auto-width
              class="select_checkbox"
            >
              <CheckBox v-model="props.selected" />
            </q-td>

            <q-td
              key="client_name"
              :props="props"
            >
              {{ props.row.client_name }}
            </q-td>

            <q-td
              key="receiver_name"
              :props="props"
            >
              {{ props.row.receiver_name }}
            </q-td>

            <q-td
              key="receiver_phone"
              :props="props"
            >
              {{ props.row.receiver_phone | phoneNumberFilter }}
            </q-td>

            <q-td
              key="sum"
              :props="props"
            >
              {{ props.row.sum | numberFormatFilter }}
            </q-td>

            <q-td
              key="method_label"
              :props="props"
            >
              {{ props.row.method_label }}
            </q-td>

            <q-td
              key="status_label"
              :props="props"
            >
              <Badge :color="statusColor(props.row.status)">
                {{ props.row.status_label }}
              </Badge>
            </q-td>

            <q-td
              key="user_name"
              :props="props"
            >
              {{ props.row.user_name }}
            </q-td>

            <q-td
              key="created_at"
              :props="props"
            >
              {{ props.row.created_at }}
            </q-td>

            <q-td
              key="issued_by"
              :props="props"
            >
              {{ props.row.issued_by }}
            </q-td>

            <q-td
              key="notation"
              :props="props"
            >
              {{ props.row.notation }}
            </q-td>
          </q-tr>
        </template>
      </Table>
      <List
        separator
        bordered
        dense
        style="max-width: 450px;margin: 20px auto;font-weight: bold;"
      >
        <ListItem>
          <ItemSection>
            <ItemLabel>Статус</ItemLabel>
          </ItemSection>
          <ItemSection>
            <ItemLabel>Количество</ItemLabel>
          </ItemSection>
          <ItemSection>
            <ItemLabel> Сумма</ItemLabel>
          </ItemSection>
        </ListItem>

        <ListItem>
          <ItemSection>
            <ItemLabel>
              <Badge :color="statusColor(3)">
                Выдано
              </Badge>
            </ItemLabel>
          </ItemSection>
          <ItemSection>
            <ItemLabel>{{ countTransfers.issued }}</ItemLabel>
          </ItemSection>
          <ItemSection>
            <ItemLabel>{{ countTransfers.issuedSum }}</ItemLabel>
          </ItemSection>
        </ListItem>

        <ListItem>
          <ItemSection>
            <ItemLabel>
              <Badge :color="statusColor(2)">
                Не выдан
              </Badge>
            </ItemLabel>
          </ItemSection>
          <ItemSection>
            <ItemLabel>{{ countTransfers.notIssued }}</ItemLabel>
          </ItemSection>
          <ItemSection>
            <ItemLabel>{{ countTransfers.notIssuedSum }}</ItemLabel>
          </ItemSection>
        </ListItem>

        <ListItem>
          <ItemSection>
            <ItemLabel>
              <Badge :color="statusColor(1)">
                Вопрос
              </Badge>
            </ItemLabel>
          </ItemSection>
          <ItemSection>
            <ItemLabel>{{ countTransfers.question }}</ItemLabel>
          </ItemSection>
          <ItemSection>
            <ItemLabel>{{ countTransfers.questionSum }}</ItemLabel>
          </ItemSection>
        </ListItem>

        <ListItem>
          <ItemSection>
            <ItemLabel>
              <Badge :color="statusColor(4)">
                Отменен
              </Badge>
            </ItemLabel>
          </ItemSection>
          <ItemSection>
            <ItemLabel>{{ countTransfers.cancel }}</ItemLabel>
          </ItemSection>
          <ItemSection>
            <ItemLabel>{{ countTransfers.cancelSum }}</ItemLabel>
          </ItemSection>
        </ListItem>

        <ListItem>
          <ItemSection>
            <ItemLabel>
              <Badge :color="statusColor(5)">
                Возврат
              </Badge>
            </ItemLabel>
          </ItemSection>
          <ItemSection>
            <ItemLabel>{{ countTransfers.returned }}</ItemLabel>
          </ItemSection>
          <ItemSection>
            <ItemLabel>{{ countTransfers.returnedSum }}</ItemLabel>
          </ItemSection>
        </ListItem>

        <ListItem>
          <ItemSection>
            <ItemLabel>
              Всего
            </ItemLabel>
          </ItemSection>
          <ItemSection>
            <ItemLabel>{{ countTransfers.all }}</ItemLabel>
          </ItemSection>
          <ItemSection>
            <ItemLabel>{{ countTransfers.allSum }}</ItemLabel>
          </ItemSection>
        </ListItem>

        <ListItem>
          <ItemSection>
            <ItemLabel>Выбранных</ItemLabel>
          </ItemSection>
          <ItemSection>
            <ItemLabel>
              <Badge color="info">
                {{ countCheckedTransfers }}
              </Badge>
            </ItemLabel>
          </ItemSection>
          <ItemSection>
            <ItemLabel>
              <Badge color="info">
                {{ countSumCheckedTransfers }}
              </Badge>
            </ItemLabel>
          </ItemSection>
        </ListItem>
      </List>
      <PageSticky :offset="[18, 200]">
        <Fab color="accent">
          <FabAction
            color="positive"
            @fabActionClick="viewEditDialog"
          />
          <FabAction
            icon="person"
            @fabActionClick="showCodeDialog = true"
          />
          <PageScroller :offset="[4, 100]">
            <FabAction icon="keyboard_arrow_up" />
          </PageScroller>
        </Fab>
      </PageSticky>
      <Dialog
        :dialog="dialog"
        :persistent="true"
      >
        <Card style="min-width: 320px;width: 100%;max-width: 500px;">
          <CardSection class="row justify-between bg-grey q-mb-sm">
            <span class="text-h6">{{ dialogTitle }}</span>
            <div>
              <IconBtn
                v-if="localProps.row"
                dense
                icon="history"
                tooltip="История"
                @iconBtnClick="getTransfersHistory(localProps.row.id, localProps.cols)"
              />

              <IconBtn
                dense
                icon="clear"
                tooltip="Закрыть"
                @iconBtnClick="cancel(transferData)"
              />
            </div>
          </CardSection>
          <CardSection>
            <div
              v-for="(item, index) in transferData"
              :key="index"
            >
              <BaseInput
                v-if="item.type === 'text'"
                v-model.trim="item.value"
                :label="item.label"
                :type="item.type"
                :mask="item.mask"
                :unmasked-value="item.unmaskedValue"
                :change-value.sync="item.changeValue"
                dense
                :field="item.field"
                :errors="errorsData"
              />

              <BaseInput
                v-else-if="item.type === 'number'"
                v-model.number="item.value"
                :label="item.label"
                :type="item.type"
                :mask="item.mask"
                :unmasked-value="item.unmaskedValue"
                :change-value.sync="item.changeValue"
                dense
                :field="item.field"
                :errors="errorsData"
              />

              <SearchSelect
                v-else-if="item.type === 'searchSelect'"
                v-model="item.value"
                dense
                :options="item.options"
                :label="item.label"
                :field="item.field"
                :func-load-data="item.funcLoadData"
                :change-value.sync="item.changeValue"
                :errors="errorsData"
              />

              <BaseSelect
                v-else-if="item.type === 'select'"
                v-model="item.value"
                :label="item.label"
                :dense="true"
                :options="item.options"
                :field="item.field"
                :change-value.sync="item.changeValue"
                :errors="errorsData"
              />

              <BaseInput
                v-else-if="item.type === 'date'"
                v-model="item.value"
                :label="item.label"
                :errors="errorsData"
                :field="item.field"
                :readonly="item.readonly"
                :mask="item.mask"
                :change-value.sync="item.changeValue"
                dense
              >
                <template v-slot:append>
                  <Date
                    :value.sync="item.value"
                    :change-value.sync="item.changeValue"
                  />
                </template>
              </BaseInput>
            </div>
          </CardSection>

          <Separator />
          <CardActions>
            <BaseBtn
              label="Отмена"
              color="negative"
              @clickBaseBtn="cancel(transferData)"
            />
            <BaseBtn
              label="Сохранить"
              color="positive"
              @clickBaseBtn="checkErrors(transferData, updateData)"
            />
          </CardActions>
        </Card>
      </Dialog>
      <DialogAddCode :show-dialog.sync="showCodeDialog" />

      <Dialog
        :dialog="dialogHistory"
        :persistent="true"
        :maximized="true"
        transition-show="slide-up"
        transition-hide="slide-down"
      >
        <Card style="max-width: 600px;">
          <q-bar>
            <q-space />
            <IconBtn
              flat
              dense
              icon="close"
              tooltip="Закрыть"
              @iconBtnClick="dialogHistory = false"
            />
          </q-bar>

          <CardSection class="q-pt-none">
            <TransferHistory :transfer-history-data="transferHistoryData" />
          </CardSection>
        </Card>
      </Dialog>
    </PullRefresh>
  </q-page>
</template>

<script>
    import { getUrl } from 'src/tools/url';
    import getFromSettings from 'src/tools/settings';
    import {
        formatToDotDate,
        isoDate,
        toDate,
        formatToMysql,
        reverseDate,
        addTime,
    } from 'src/utils/formatDate';
    import CheckErrorsMixin from 'src/mixins/CheckErrors';
    import showNotif from 'src/mixins/showNotif';
    import ExportDataMixin from 'src/mixins/ExportData';
    import { callFunction, countSumCollection, numberFormat } from 'src/utils/index';
    import { sortCollection } from 'src/utils/sort';
    import TransferMixin from 'src/mixins/Transfer';
    import {
        setMethodLabel,
        setStatusLabel,
        setFormatedDate,
        prepareHistoryData,
        setChangeValue,
        setDefaultData,
        getClientCodes,
    } from 'src/utils/FrequentlyCalledFunctions';
    import {
        max,
        formatISO,
    } from 'date-fns';

    export default {
        name: 'Transfers',
        components: {
            Table: () => import('src/components/Elements/Table/Table.vue'),
            CheckBox: () => import('src/components/Elements/CheckBox.vue'),
            Dialog: () => import('src/components/Dialogs/Dialog.vue'),
            Card: () => import('src/components/Elements/Card/Card.vue'),
            CardActions: () => import('src/components/Elements/Card/CardActions.vue'),
            CardSection: () => import('src/components/Elements/Card/CardSection.vue'),
            Date: () => import('src/components/Date.vue'),
            List: () => import('src/components/Elements/List/List.vue'),
            ItemSection: () => import('src/components/Elements/List/ItemSection.vue'),
            ItemLabel: () => import('src/components/Elements/List/ItemLabel.vue'),
            ListItem: () => import('src/components/Elements/List/ListItem.vue'),
            BaseInput: () => import('src/components/Elements/BaseInput.vue'),
            IconBtn: () => import('src/components/Buttons/IconBtn.vue'),
            BaseBtn: () => import('src/components/Buttons/BaseBtn.vue'),
            Separator: () => import('src/components/Separator.vue'),
            SearchSelect: () => import('src/components/Elements/SearchSelect.vue'),
            BaseSelect: () => import('src/components/Elements/BaseSelect.vue'),
            DialogAddCode: () => import('src/components/Dialogs/DialogAddCode.vue'),
            PageSticky: () => import('src/components/PageSticky.vue'),
            Fab: () => import('src/components/Elements/Fab.vue'),
            FabAction: () => import('src/components/Elements/FabAction.vue'),
            PageScroller: () => import('src/components/PageScroller.vue'),
            Badge: () => import('src/components/Elements/Badge.vue'),
            PullRefresh: () => import('src/components/PullRefresh.vue'),
            // Skeleton: () => import('src/components/Elements/Skeleton.vue'),
            // TransfersListSkeleton: () => import('src/components/Skeletons/Transfers/TransfersListSkeleton.vue'),
            TransferHistory: () => import('src/components/History/TransferHistory.vue'),
        },
        mixins: [CheckErrorsMixin, showNotif, ExportDataMixin, TransferMixin],
        data() {
            return {
                dialogHistory: false,
                transferHistoryData: {
                    cols: {},
                    transferHistory: [],
                },
                localProps: {},
                showCodeDialog: false,
                dialog: false,
                transferData: {
                    client_id: {
                        type: 'searchSelect',
                        label: 'Клиент',
                        field: 'client_id',
                        require: false,
                        requireError: 'Выберите клиента.',
                        options: [],
                        changeValue: false,
                        funcLoadData: getClientCodes,
                        default: null,
                        value: null,
                    },
                    receiver_name: {
                        type: 'text',
                        label: 'Получатель',
                        field: 'receiver_name',
                        rules: [
                            {
                                name: 'isLength',
                                error: 'Минимальное количество символов 2.',
                                options: {
                                    min: 2,
                                    max: 255,
                                },
                            },
                        ],
                        require: true,
                        requireError: 'Поле обьязательное для заполнения.',
                        changeValue: false,
                        default: '',
                        value: '',
                    },
                    receiver_phone: {
                        type: 'text',
                        label: 'Телефон получателя',
                        field: 'receiver_phone',
                        require: true,
                        requireError: 'Поле обьязательное для заполнения.',
                        rules: [
                            {
                                name: 'isLength',
                                error: 'Минимальное количество символов 12.',
                                options: {
                                    min: 12,
                                    max: 12,
                                },
                            },
                        ],
                        unmaskedValue: true,
                        mask: '+## (###) ###-##-##',
                        changeValue: false,
                        default: '',
                        value: '',
                    },
                    sum: {
                        type: 'number',
                        label: 'Сумма',
                        field: 'sum',
                        require: true,
                        requireError: 'Поле обьязательное для заполнения.',
                        changeValue: false,
                        default: 0,
                        value: 0,
                    },
                    method: {
                        type: 'select',
                        label: 'Метод',
                        field: 'method',
                        require: true,
                        requireError: 'Поле обьязательное для заполнения.',
                        options: getFromSettings('transferMethod'),
                        changeValue: false,
                        default: 0,
                        value: 0,
                    },
                    status: {
                        type: 'select',
                        label: 'Статус',
                        field: 'status',
                        options: getFromSettings('transferStatus'),
                        require: true,
                        requireError: 'Поле обьязательное для заполнения.',
                        changeValue: false,
                        default: 2,
                        value: 0,
                    },
                    issued_by: {
                        type: 'date',
                        field: 'issued_by',
                        label: 'Выдано',
                        mask: '##-##-####',
                        require: true,
                        requireError: 'Поле обьязательное для заполнения.',
                        changeValue: false,
                        default: null,
                        value: formatToDotDate(new Date().toISOString()),
                    },
                    notation: {
                        type: 'text',
                        label: 'Примечания',
                        field: 'notation',
                        changeValue: false,
                        default: '',
                        value: '',
                    },
                },
                transferTableProperties: {
                    columns: [
                        {
                            name: 'client_name',
                            label: 'Клиент',
                            align: 'center',
                            field: 'client_name',
                            sortable: true,
                            // sort: (a, b) => parseInt(a, 10) - parseInt(b, 10),
                        },
                        {
                            name: 'receiver_name',
                            label: 'Получатель',
                            field: 'receiver_name',
                            align: 'center',
                            sortable: true,
                        },
                        {
                            name: 'receiver_phone',
                            label: 'Телефон получателя',
                            field: 'receiver_phone',
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
                            name: 'method_label',
                            label: 'Метод',
                            field: 'method_label',
                            align: 'center',
                            sortable: true,
                        },
                        {
                            name: 'status_label',
                            label: 'Статус',
                            field: 'status_label',
                            align: 'center',
                            sortable: true,
                        },
                        {
                            name: 'user_name',
                            label: 'Пользователь',
                            field: 'user_name',
                            align: 'center',
                            sortable: true,
                        },
                        {
                            name: 'created_at',
                            label: 'Добавлено',
                            field: 'created_at',
                            align: 'center',
                            sortable: true,
                        },
                        {
                            name: 'issued_by',
                            field: 'issued_by',
                            label: 'Выдано',
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
                },
                transferTableReactiveProperties: {
                    selected: [],
                    visibleColumns: ['client_name', 'receiver_name', 'receiver_phone', 'sum', 'method_label', 'user_name', 'notation', 'status_label', 'created_at', 'issued_by'],
                },
            };
        },
        computed: {
            allTransfers() {
                return this.$store.getters['transfers/getTransfers'];
            },
            dialogTitle() {
                return _.get(this.localProps, 'row.client_name') || 'Новый перевод';
            },
            clientCodes() {
                return this.$store.getters['codes/getCodes'];
            },
            countTransfers() {
                const { allTransfers } = this;
                return {
                    all: numberFormat(_.size(allTransfers)),
                    allSum: numberFormat(countSumCollection(allTransfers, 'sum')),
                    notIssued: numberFormat(_.size(_.filter(allTransfers, { status: 2 }))),
                    notIssuedSum: numberFormat(countSumCollection(_.filter(allTransfers, { status: 2 }), 'sum')),
                    issued: numberFormat(_.size(_.filter(allTransfers, { status: 3 }))),
                    issuedSum: numberFormat(countSumCollection(_.filter(allTransfers, { status: 3 }), 'sum')),
                    question: numberFormat(_.size(_.filter(allTransfers, { status: 1 }))),
                    questionSum: numberFormat(countSumCollection(_.filter(allTransfers, { status: 1 }), 'sum')),
                    cancel: numberFormat(_.size(_.filter(allTransfers, { status: 4 }))),
                    cancelSum: numberFormat(countSumCollection(_.filter(allTransfers, { status: 4 }), 'sum')),
                    returned: numberFormat(_.size(_.filter(allTransfers, { status: 5 }))),
                    returnedSum: numberFormat(countSumCollection(_.filter(allTransfers, { status: 5 }), 'sum')),
                };
            },
            countCheckedTransfers() {
                return numberFormat(_.size(this.transferTableReactiveProperties.selected));
            },
            countSumCheckedTransfers() {
                return numberFormat(countSumCollection(this.transferTableReactiveProperties.selected, 'sum'));
            },
        },
        watch: {
            clientCodes: {
                handler: function setCodes(val) {
                    devlog.log('CLIENT_CODES');
                    _.set(this.transferData, 'client_id.options', val);
                },
                immediate: true,
            },
            'transferData.status.value': {
                handler: function status(val) {
                    if (val > 2) {
                        _.set(this.transferData, 'issued_by.require', true);
                    } else {
                        _.set(this.transferData, 'issued_by.require', false);
                        _.set(this.transferData, 'issued_by.value', null);
                    }
                },
                immediate: true,
            },
        },
        mounted() {
            this.getTransfers();
        },
        methods: {
            updateData(data) {
                const sendData = _.cloneDeep(data);
                devlog.log('DATA_N0', sendData);
                if (!sendData.client_id.value) {
                    devlog.log('NEW_');
                    this.showCodeDialog = true;
                } else if (_.isEmpty(this.localProps)) {
                    // ДОБАВЛЕНИЕ ЗАПИСИ
                    this.$q.loading.show();
                    const values = _.mapValues(sendData, 'value');
                    values.receiver_name = _.startCase(_.toLower(values.receiver_name));
                    if (_.trim(values.issued_by)) {
                        const date = reverseDate(values.issued_by);
                        values.issued_by = formatISO(addTime(date));
                    }
                    this.$axios.post(getUrl('storeTransfers'), values)
                      .then(({ data: { transfer } }) => {
                          devlog.log('DDFR', transfer);
                          this.$store.dispatch('transfers/addTransfer', this.setAdditionalData([_.first(transfer)]));
                          this.openCloseDialog(false);
                          setChangeValue(this.transferData);
                          this.$q.loading.hide();
                          this.showNotif('success', `Запись клиента - ${_.get(transfer, '[0].client_name')} успешно добавлена.`, 'center');
                      })
                      .catch((errors) => {
                          this.$q.loading.hide();
                          this.errorsData.errors = _.get(errors, 'response.data.errors');
                      });
                } else if (_.has(this.localProps, 'row.id')) {
                    // ОБНОВЛЕНИЕ ЗАПИСИ
                    if (_.some(sendData, 'changeValue')) {
                        this.$q.loading.show();
                        devlog.log('sendData_', sendData);
                        const dataToSend = _.reduce(sendData, (result, { value, changeValue }, index) => {
                            if (changeValue && index === 'issued_by') {
                                devlog.log('ISSUUE', value);
                                if (value) {
                                    const date = reverseDate(value);
                                    result[index] = formatISO(addTime(date));
                                } else {
                                    result[index] = value;
                                }
                            } else if (changeValue && index === 'receiver_name' && value) {
                                result[index] = _.startCase(_.toLower(value));
                            } else if (changeValue) {
                                result[index] = value;
                            }
                            return result;
                        }, {});
                        _.assign(dataToSend, { id: _.get(this.localProps, 'row.id') });
                        devlog.log('dataToSend', dataToSend);
                        this.$axios.post(getUrl('updateTransfers'), dataToSend)
                          .then(({ data: { transfer } }) => {
                              devlog.log('DDFR', transfer);
                              this.$store.dispatch('transfers/updateTransfer', this.setAdditionalData([_.first(transfer)]));
                              this.openCloseDialog(false);
                              this.localProps.selected = false;
                              this.$q.loading.hide();
                              setChangeValue(this.transferData);
                              this.showNotif('success', `Запись клиента - ${_.get(transfer, '[0].client_name')} успешно обновлена.`, 'center');
                          })
                          .catch((errors) => {
                              this.$q.loading.hide();
                              this.errorsData.errors = _.get(errors, 'response.data.errors');
                          });
                    } else {
                        this.openCloseDialog(false);
                    }
                    devlog.log('item_DDD_FFF', data);
                }
            },
            viewEditDialog(val, event) {
                devlog.log('EVENT', event);
                devlog.log('EVENT_get', _.get(event, 'target.classList'));
                devlog.log('val', val);
                if (!_.includes(_.get(event, 'target.classList'), 'select_checkbox')) {
                    if (val) {
                        this.localProps = val;
                        this.transferTableReactiveProperties.selected = [];
                        setTimeout(() => {
                            val.selected = !val.selected;
                        }, 100);
                        devlog.log('SEL', val);
                        this.$q.loading.show();
                        Promise.all([getClientCodes(this.$store)])
                          .then(() => {
                              this.openCloseDialog(true);
                              this.$q.loading.hide();
                          });

                        _.forEach(this.transferData, (item) => {
                            if (_.has(val.row, item.field)) {
                                if (_.get(val, `row[${item.field}]`)) {
                                    _.set(item, 'value', _.get(val, `row[${item.field}]`));
                                } else {
                                    _.set(item, 'value', item.default);
                                }
                            }
                        });
                    } else {
                        this.openCloseDialog(true);
                        this.localProps = {};
                        setDefaultData(this.transferData);
                    }
                }
            },
            async getTransfers() {
                if (_.isEmpty(this.allTransfers)) {
                    this.$q.loading.show();
                    await this.$axios.get(getUrl('transfers'))
                      .then(({ data: { transfers } }) => {
                          this.$store.dispatch('transfers/setTransfers', this.setAdditionalData(transfers));
                          this.$q.loading.hide();
                      })
                      .catch(() => {
                          this.$q.loading.hide();
                      });
                }
            },
            setAdditionalData(data) {
                return setMethodLabel(setStatusLabel(setFormatedDate(data, ['created_at', 'issued_by'])));
            },
            openCloseDialog(val) {
                this.dialog = val;
            },
            cancel(data) {
                this.openCloseDialog(false);
                this.localProps.selected = false;
                setChangeValue(data);
            },
            async refresh(done) {
                const { allTransfers } = this;
                await this.$axios.post(getUrl('getNewTransfers'), {
                    created_at: isoDate(max(_.map(allTransfers, (item) => new Date(toDate(item.created_at))))),
                    updated_at: formatToMysql(max(_.map(allTransfers, (item) => new Date(item.updated_at)))),
                })
                  .then(({ data: { transfers } }) => {
                      devlog.log('DTA', transfers);
                      if (!_.isEmpty(transfers)) {
                          const createdItems = [];
                          _.forEach(transfers, (item) => {
                              const find = _.some(allTransfers, ['id', item.id]);
                              if (find) {
                                  this.$store.dispatch('transfers/updateTransfer', this.setAdditionalData([item]));
                              } else {
                                  createdItems.push(item);
                              }
                          });

                          if (!_.isEmpty(createdItems)) {
                              _.forEach(sortCollection(createdItems, 'id'), (elem) => {
                                  this.$store.dispatch('transfers/addTransfer', this.setAdditionalData([elem]));
                              });
                          }

                          this.showNotif('success', 'Данные успешно обновлены.', 'center');
                      } else {
                          this.showNotif('info', 'Данные актуальны.', 'center');
                      }
                      callFunction(done);
                  })
                  .catch(() => {
                      callFunction(done);
                  });
            },
            exportTransfers() {
                if (!_.isEmpty(this.allTransfers)) {
                    this.exportDataToExcel(getUrl('exportTransfers'), {
                        ids: _.map(this.transferTableReactiveProperties.selected, 'id'),
                    }, 'Переводы.xlsx');
                }
            },
            async getTransfersHistory(transferID, cols) {
                this.$q.loading.show();
                await this.$axios.get(`${getUrl('transfersHistory')}/${transferID}`)
                  .then(({ data: { transferHistory } }) => {
                      if (!_.isEmpty(transferHistory)) {
                          this.$q.loading.hide();
                          this.dialogHistory = true;
                          const historyData = prepareHistoryData(cols, transferHistory);
                          historyData.historyData = this.setAdditionalData(historyData.historyData);
                          this.transferHistoryData = historyData;
                      } else {
                          this.$q.loading.hide();
                          this.showNotif('info', 'По этому переводу нет истории.', 'center');
                      }
                  })
                  .catch(() => {
                      devlog.error('Ошибка при получении данных истории.');
                  });
            },
        },
    };
</script>

<style lang="stylus">
  .border_red {
    border-color $red !important
  }

  .border_positive {
    border-color $positive !important
  }

  .border_warning {
    border-color $warning !important
  }

  .border_grey {
    border-color $grey !important
  }

  .border_info {
    border-color $info !important
  }

</style>
