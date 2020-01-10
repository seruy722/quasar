<template>
  <q-page
    data-vue-component-name="Transfers"
  >
    <PullRefresh
      @refresh="refresh"
    >
      <q-card>
        <q-tabs
          v-model="tab"
          dense
          class="text-grey"
          active-color="primary"
          indicator-color="primary"
          align="justify"
          narrow-indicator
        >
          <q-tab name="transfers" label="Все переводы" />
          <q-tab name="alarms" label="История" />
        </q-tabs>

        <q-separator />

        <q-tab-panels v-model="tab" animated>
          <q-tab-panel name="transfers">
            <Table
              :table-properties="faxTableProperties"
              :table-data="allTransfers"
              :table-reactive-properties="faxTableReactiveProperties"
              title="Переводы"
            >
              <template v-slot:top-buttons>
                <!--              <IconBtn-->
                <!--                color="positive"-->
                <!--                icon="save"-->
                <!--                tooltip="save"-->
                <!--                :disable="!arrayToUpdate.length"-->
                <!--                @iconBtnClick="updateFaxData"-->
                <!--              />-->

                <!--              <IconBtn-->
                <!--                color="positive"-->
                <!--                icon="explicit"-->
                <!--                tooltip="excel"-->
                <!--                @iconBtnClick="exportFaxData(currentFaxItem)"-->
                <!--              />-->

                <!--              <IconBtn-->
                <!--                color="primary"-->
                <!--                icon="update"-->
                <!--                tooltip="update"-->
                <!--                @iconBtnClick="updateAllPriceInFaxData(currentFaxItem.id)"-->
                <!--              />-->
                <IconBtn
                  color="positive"
                  tooltip="Excel"
                  icon="get_app"
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
                  <!--                <Card-->
                  <!--                  :class="props.selected ? 'bg-grey-2' : ''"-->
                  <!--                  @clickCard="viewEditDialog(props)"-->
                  <!--                >-->
                  <!--                  <CardSection>-->
                  <!--                    <CheckBox-->
                  <!--                      v-model="props.selected"-->
                  <!--                    />-->
                  <!--                  </CardSection>-->
                  <!--                  <Separator />-->
                  <List>
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
                          {{ props.row.client_name | truncateFilter(30) }}
                        </ItemSection>
                      </template>

                      <List
                        separator
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
                            <ItemLabel v-else-if="col.field === 'receiver_phone'">{{ col.value | phoneNumberFilter }}
                            </ItemLabel>
                            <ItemLabel v-else-if="col.field === 'receiver_name'">{{ col.value | truncateFilter(40) }}
                            </ItemLabel>
                            <ItemLabel v-else-if="col.field === 'notation'">{{ col.value | truncateFilter(40) }}
                            </ItemLabel>
                            <ItemLabel v-else>{{ col.value }}</ItemLabel>
                          </ItemSection>
                        </ListItem>
                      </List>
                    </q-expansion-item>
                  </List>
                  <!--                </Card>-->
                </div>
              </template>

              <template v-slot:inner-body="{props}">
                <TR
                  :props="props"
                  class="text-bold cursor-pointer"
                  @trClick="viewEditDialog(props)"
                >
                  <q-td>
                    <CheckBox v-model="props.selected" />
                  </q-td>

                  <TD
                    key-td="client_name"
                    :props="props"
                  >
                    {{ props.row.client_name }}
                  </TD>

                  <TD
                    key-td="receiver_name"
                    :props="props"
                  >
                    {{ props.row.receiver_name }}
                  </TD>

                  <TD
                    key-td="receiver_phone"
                    :props="props"
                  >
                    {{ props.row.receiver_phone | phoneNumberFilter }}
                  </TD>

                  <TD
                    key-td="sum"
                    :props="props"
                  >
                    {{ props.row.sum | numberFormatFilter }}
                  </TD>

                  <TD
                    key-td="method_label"
                    :props="props"
                  >
                    {{ props.row.method_label }}
                  </TD>

                  <TD
                    key-td="status_label"
                    :props="props"
                  >
                    <Badge :color="statusColor(props.row.status)">
                      {{ props.row.status_label }}
                    </Badge>
                  </TD>

                  <TD
                    key-td="user_name"
                    :props="props"
                  >
                    {{ props.row.user_name }}
                  </TD>

                  <TD
                    key-td="created_at"
                    :props="props"
                  >
                    {{ props.row.created_at }}
                  </TD>

                  <TD
                    key-td="issued_by"
                    :props="props"
                  >
                    {{ props.row.issued_by }}
                  </TD>

                  <TD
                    key-td="notation"
                    :props="props"
                  >
                    {{ props.row.notation }}
                  </TD>
                </TR>
              </template>
            </Table>
          </q-tab-panel>

          <q-tab-panel name="alarms">
            <div class="text-h6">Пусто</div>
          </q-tab-panel>
        </q-tab-panels>
      </q-card>
    </PullRefresh>
    <Dialog
      :dialog="dialog"
      :persistent="true"
    >
      <Card style="min-width: 320px;width: 100%;max-width: 500px;">
        <CardSection class="row justify-between bg-grey q-mb-sm">
          <span class="text-h6">{{ dialogTitle }}</span>
          <div>
            <IconBtn
              dense
              icon="clear"
              tooltip="Закрыть"
              @iconBtnClick="cancel"
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
                <Date :value.sync="item.value" />
              </template>
            </BaseInput>
          </div>
        </CardSection>

        <Separator />
        <CardActions>
          <BaseBtn
            label="Отмена"
            color="negative"
            @clickBaseBtn="cancel"
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
  </q-page>
</template>

<script>
    import { getUrl } from 'src/tools/url';
    import getFromSettings from 'src/tools/settings';
    import { fullDate, formatToDotDate, isoDate } from 'src/utils/formatDate';
    import CheckErrorsMixin from 'src/mixins/CheckErrors';
    import showNotif from 'src/mixins/showNotif';
    import { sortCollection } from 'src/utils/sort';
    import ExportDataMixin from 'src/mixins/ExportData';

    export default {
        name: 'Transfers',
        components: {
            Table: () => import('src/components/Elements/Table/Table.vue'),
            CheckBox: () => import('src/components/Elements/CheckBox.vue'),
            Dialog: () => import('src/components/Dialogs/Dialog.vue'),
            TR: () => import('src/components/Elements/Table/TR.vue'),
            TD: () => import('src/components/Elements/Table/TD.vue'),
            Card: () => import('src/components/Elements/Card/Card.vue'),
            CardActions: () => import('src/components/Elements/Card/CardActions.vue'),
            CardSection: () => import('src/components/Elements/Card/CardSection.vue'),
            Date: () => import('src/components/Date.vue'),
            List: () => import('src/components/Elements/List/List.vue'),
            ItemSection: () => import('src/components/Elements/List/ItemSection.vue'),
            ItemLabel: () => import('src/components/Elements/List/ItemLabel.vue'),
            ListItem: () => import('src/components/Elements/List/ListItem.vue'),
            BaseInput: () => import('src/components/Elements/BaseInput.vue'),
            // Icon: () => import('src/components/Buttons/Icons/Icon.vue'),
            IconBtn: () => import('src/components/Buttons/IconBtn.vue'),
            BaseBtn: () => import('src/components/Buttons/BaseBtn.vue'),
            Separator: () => import('src/components/Separator.vue'),
            SearchSelect: () => import('src/components/Elements/SearchSelect.vue'),
            // PopupEdit: () => import('src/components/PopupEdit.vue'),
            // SelectWithSearchInput: () => import('src/components/Elements/SelectWithSearchInput.vue'),
            BaseSelect: () => import('src/components/Elements/BaseSelect.vue'),
            DialogAddCode: () => import('src/components/Dialogs/DialogAddCode.vue'),
            PageSticky: () => import('src/components/PageSticky.vue'),
            Fab: () => import('src/components/Elements/Fab.vue'),
            FabAction: () => import('src/components/Elements/FabAction.vue'),
            PageScroller: () => import('src/components/PageScroller.vue'),
            Badge: () => import('src/components/Elements/Badge.vue'),
            PullRefresh: () => import('src/components/PullRefresh.vue'),
        },
        filters: {
            phoneNumberFilter(val) {
                if (_.isString(val)) {
                    return `+${val.slice(0, 2)} (${val.slice(2, 5)}) ${val.slice(5, 8)}-${val.slice(8, 10)}-${val.slice(10, 12)}`;
                }
                return val;
            },
            truncateFilter(str, maxSize) {
                if (_.isString(str) && _.size(str) > maxSize) {
                    return _.truncate(str, {
                        length: maxSize,
                        separator: ' ',
                    });
                }

                return str;
            },
        },
        mixins: [CheckErrorsMixin, showNotif, ExportDataMixin],
        data() {
            return {
                tab: 'transfers',
                // intervalFunc: null,
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
                        default: 0,
                        value: 0,
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
                        options: Object.freeze(getFromSettings('transferMethod')),
                        changeValue: false,
                        default: 0,
                        value: 0,
                    },
                    status: {
                        type: 'select',
                        label: 'Статус',
                        field: 'status',
                        options: Object.freeze(getFromSettings('transferStatus')),
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
                        mask: '##.##.####',
                        require: true,
                        requireError: 'Поле обьязательное для заполнения.',
                        changeValue: false,
                        default: formatToDotDate(new Date().toISOString()),
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
                faxTableData: [],
                faxTableProperties: {
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
                faxTableReactiveProperties: {
                    selected: [],
                    visibleColumns: ['client_name', 'receiver_name', 'receiver_phone', 'sum', 'method_label', 'notation', 'status_label', 'created_at', 'issued_by'],
                    title: '',
                },
            };
        },
        computed: {
            notIssuedTransfers() {
                return _.filter(this.$store.getters['transfers/getTransfers'], { status: 1 });
            },
            allTransfers() {
                return this.$store.getters['transfers/getTransfers'];
            },
            dialogTitle() {
                return _.get(this.localProps, 'row.client_name') || 'Новая запись';
            },
            clientCodes() {
                return this.$store.getters['clientCodes/getCodes'];
            },
        },
        watch: {
            clientCodes: {
                handler: function setCodes(val) {
                    devlog.log('CLIENT_CODES');
                    _.set(this.transferData, 'client_id.options', val);
                    // _.set(this.transferData, 'client.options[0].label', 'Новый');
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
        // mounted() {
        //     const socket = this.$io('http://localhost:8080');
        //     socket.on('transfer-create:App\\Events\\TransferCreate', (data) => {
        //         devlog.log('S_DATA', data);
        //     });
        // },
        // beforeDestroy() {
        //     clearInterval(this.intervalFunc);
        // },
        created() {
            this.getTransfers();
            this.getClientCodes();
            this.$nextTick(() => {
                devlog.log('$nextTick');
            });
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
                    values.issued_by = isoDate(values.issued_by);
                    this.$axios.post(getUrl('storeTransfers'), values)
                      .then(({ data: { transfer } }) => {
                          devlog.log('DDFR', transfer);
                          this.$store.dispatch('transfers/addTransfer', this.setAdditionalData([_.first(transfer)]));
                          this.openCloseDialog(false);
                          this.$q.loading.hide();
                          this.showNotif('success', `Запись клиента - ${_.get(transfer, '[0].client_name')} успешно добавлена.`, 'center');
                      });
                } else if (_.has(this.localProps, 'row.id')) {
                    // ОБНОВЛЕНИЕ ЗАПИСИ
                    if (_.some(sendData, 'changeValue')) {
                        this.$q.loading.show();
                        const dataToSend = _.reduce(sendData, (result, { value, changeValue }, index) => {
                            if (changeValue) {
                                result[index] = value;
                            }

                            if (index === 'issued_by') {
                                result[index] = isoDate(value);
                            }
                            if (index === 'receiver_name') {
                                result[index] = _.startCase(_.toLower(value));
                            }
                            return result;
                        }, {});
                        _.assign(dataToSend, { id: _.get(this.localProps, 'row.id') });
                        this.$axios.post(getUrl('updateTransfers'), dataToSend)
                          .then(({ data: { transfer } }) => {
                              devlog.log('DDFR', transfer);
                              this.$store.dispatch('transfers/updateTransfer', this.setAdditionalData([_.first(transfer)]));
                              this.openCloseDialog(false);
                              this.localProps.selected = false;
                              this.$q.loading.hide();
                              this.setChangeValue(this.transferData);
                              this.showNotif('success', `Запись клиента - ${_.get(transfer, '[0].client_name')} успешно обновлена.`, 'center');
                          });
                    } else {
                        this.openCloseDialog(false);
                    }
                    devlog.log('item_DDD_FFF', data);
                }
            },
            viewEditDialog(val) {
                this.openCloseDialog(true);
                if (val) {
                    this.localProps = val;
                    this.faxTableReactiveProperties.selected = [];
                    setTimeout(() => {
                        val.selected = !val.selected;
                    }, 100);
                    devlog.log('SEL', val);

                    _.forEach(this.transferData, (item) => {
                        if (_.has(val.row, item.field)) {
                            if (_.get(val, `row[${item.field}]`)) {
                                _.set(item, 'value', _.get(val, `row[${item.field}]`));
                            }
                        }
                    });
                } else {
                    this.localProps = {};
                    _.forEach(this.transferData, (item) => {
                        _.set(item, 'value', item.default);
                    });
                }
            },
            setFormatedDate(value) {
                _.forEach(value, (item) => {
                    _.set(item, 'created_at', fullDate(_.get(item, 'created_at')));
                    _.set(item, 'issued_by', fullDate(_.get(item, 'issued_by')));
                });
                return value;
            },
            setChangeValue(data) {
                _.forEach(data, (elem) => {
                    if (_.get(elem, 'changeValue')) {
                        _.set(elem, 'changeValue', false);
                    }
                });
            },
            setStatusLabel(value) {
                _.forEach(value, (item) => {
                    const findLabel = _.find(getFromSettings('transferStatus'), { value: item.status });
                    if (findLabel) {
                        _.set(item, 'status_label', _.get(findLabel, 'label'));
                    }
                });
                return value;
            },
            setMethodLabel(value) {
                _.forEach(value, (item) => {
                    const findLabel = _.find(getFromSettings('transferMethod'), { value: item.method });
                    if (findLabel) {
                        _.set(item, 'method_label', _.get(findLabel, 'label'));
                    }
                });
                return value;
            },
            async getTransfers() {
                this.$q.loading.show();
                await this.$axios.get(getUrl('transfers'))
                  .then(({ data: { transfers } }) => {
                      this.$store.dispatch('transfers/setTransfers', this.setAdditionalData(transfers));
                      this.$q.loading.hide();
                      // this.intervalFunc = setInterval(() => {
                      //     devlog.log('ISO', _.get(this.allTransfers, '[0].created_at'));
                      //     devlog.log('ISO_2', isoDate(_.get(this.allTransfers, '[2].created_at')));
                      //     this.$axios.post(getUrl('getNewTransfers'), { created_at: isoDate(_.get(this.allTransfers, '[0].created_at')) });
                      // }, 30000);
                  });
            },
            setAdditionalData(data) {
                return this.setMethodLabel(this.setStatusLabel(this.setFormatedDate(data)));
            },
            statusColor(value) {
                const findLabel = _.find(getFromSettings('transferStatus'), { value }) || _.find(getFromSettings('transferStatus'), { label: value });
                return _.get(findLabel, 'color');
            },
            openCloseDialog(val) {
                this.dialog = val;
            },
            cancel() {
                this.openCloseDialog(false);
                this.localProps.selected = false;
            },
            getClientCodes() {
                if (_.isEmpty(this.clientCodes)) {
                    this.$store.dispatch('clientCodes/getCodes');
                }
            },
            async refresh(done) {
                devlog.log('CL', sortCollection(this.allTransfers, 'updated_at'));
                await this.$axios.post(getUrl('getNewTransfers'), {
                    created_at: isoDate(_.get(this.allTransfers, '[0].created_at')),
                    updated_at: _.get(_.last(sortCollection(this.allTransfers, 'updated_at')), 'updated_at'),
                })
                  .then(({ data: { transfers } }) => {
                      devlog.log('DTA', transfers);
                      if (!_.isEmpty(transfers)) {
                          _.forEach(transfers, (item) => {
                              const find = _.some(this.allTransfers, ['id', item.id]);
                              if (find) {
                                  this.$store.dispatch('transfers/updateTransfer', this.setAdditionalData([item]));
                              } else {
                                  this.$store.dispatch('transfers/addTransfer', this.setAdditionalData([item]));
                              }
                          });
                          this.showNotif('success', 'Данные успешно обновлены.', 'center');
                      } else {
                          this.showNotif('info', 'Данные актуальны.', 'center');
                      }
                      done();
                  })
                  .catch(() => {
                      done();
                  });
            },
            exportTransfers() {
                this.exportDataToExcel(getUrl('exportTransfers'), {
                    ids: _.map(this.faxTableReactiveProperties.selected, 'id'),
                }, 'Переводы.xlsx');
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
