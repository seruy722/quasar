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
        <template #top-buttons>
          <UpdateBtn
            @update-btn-click="refresh"
          />
          <IconBtn
            color="positive"
            tooltip="Excel"
            icon="explicit"
            class="q-ml-sm"
            @icon-btn-click="exportTransfers(allTransfers, transferTableReactiveProperties.selected)"
          />
          <IconBtn
            color="orange"
            tooltip="Статистика"
            icon="trending_up"
            class="q-ml-sm"
            @icon-btn-click="dialogStatistics = true"
          />
          <IconBtn
            v-show="transferTableReactiveProperties.selected.length"
            color="teal"
            tooltip="Добавить в долги"
            icon="move_to_inbox"
            class="q-ml-sm"
            @icon-btn-click="addToDebtsTable(transferTableReactiveProperties.selected)"
          />
        </template>
        <!--ОТОБРАЖЕНИЕ КОНТЕНТА НА МАЛЕНЬКИХ ЭКРАНАХ-->
        <template #inner-item="{props}">
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
              <template #header>
                <q-item-section>
                  <q-item-label :lines="2">
                    {{ props.row.client_name }}
                  </q-item-label>
                </q-item-section>

                <q-item-section>
                  <q-item-label>
                    {{ props.row.sum }}
                  </q-item-label>
                </q-item-section>

                <q-item-section>
                  <q-item-label>
                    {{ props.row.created_at.slice(0, 5) }}
                  </q-item-label>
                </q-item-section>

                <q-item-section
                  avatar
                  side
                >
                  <q-icon
                    v-if="props.row.paid"
                    name="money"
                    size="md"
                    color="white"
                  />
                </q-item-section>
              </template>

              <q-list
                separator
                dense
                @click="viewEditDialog(props)"
              >
                <q-item
                  v-for="col in props.cols.filter(col => col.name !== 'desc')"
                  :key="col.name"
                >
                  <q-item-section>
                    <q-item-label>{{ `${col.label}:` }}</q-item-label>
                  </q-item-section>
                  <q-item-section side>
                    <q-item-label v-if="col.field === 'status_label'">
                      <q-badge :color="statusColor(col.value)">
                        {{ col.value }}
                      </q-badge>
                    </q-item-label>
                    <q-item-label v-else-if="col.field === 'receiver_phone'">
                      {{ phoneNumberFilter(col.value) }}
                    </q-item-label>
                    <q-item-label v-else-if="col.field === 'receiver_name'">
                      {{ col.value }}
                    </q-item-label>
                    <q-item-label
                      v-else-if="col.field === 'notation'"
                      :lines="3"
                    >
                      {{ col.value }}
                    </q-item-label>
                    <q-item-label
                      v-else-if="col.field === 'paid'"
                      :lines="3"
                    >
                      {{ col.value ? 'Да' : 'Нет' }}
                    </q-item-label>
                    <q-item-label v-else>
                      {{ col.value }}
                    </q-item-label>
                  </q-item-section>
                </q-item>
                <q-item>
                  <q-item-section>
                    <BaseBtn
                      label="История"
                      color="info"
                      style="max-width: 100px;margin: 0 auto;"
                      @click-base-btn="getTransfersHistory(props.row.id, props.cols)"
                    />
                  </q-item-section>
                </q-item>
              </q-list>
            </q-expansion-item>
          </div>
        </template>

        <template #inner-body="{props}">
          <q-tr
            :props="props"
            class="text-bold cursor-pointer"
            @click="viewEditDialog(props, $event)"
          >
            <q-td
              auto-width
              class="select_checkbox"
            >
              <q-checkbox
                v-model="props.selected"
                dense
              />
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
              {{ phoneNumberFilter(props.row.receiver_phone) }}
            </q-td>

            <q-td
              key="sum"
              :props="props"
            >
              {{ numberFormat(props.row.sum) }}
            </q-td>

            <q-td
              key="paid"
              :props="props"
            >
              <q-badge :color="props.row.paid ? 'positive': 'negative'">
                {{ props.row.paid ? 'Да' : 'Нет' }}
              </q-badge>
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
              <q-badge :color="statusColor(props.row.status)">
                {{ props.row.status_label }}
              </q-badge>
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
      <CountTransfersData :enter-data="allTransfers" />
    </PullRefresh>
    <PageSticky :offset="[18, 200]">
      <Fab color="accent">
        <FabAction
          color="positive"
          @fab-action-click="viewEditDialog"
        />
        <FabAction
          icon="person"
          @fab-action-click="showCodeDialog = true"
        />
        <PageScroller :offset="[4, 100]">
          <FabAction icon="keyboard_arrow_up" />
        </PageScroller>
      </Fab>
    </PageSticky>
    <DialogAddTransfer
      v-model:show-dialog="dialog"
      v-model:local-props="localProps"
      :transfer-data="transferData"
    />
    <DialogAddCode v-model:show-dialog="showCodeDialog" />
    <Dialog
      :dialog="dialogHistory"
      :persistent="true"
      :maximized="true"
      transition-show="slide-up"
      transition-hide="slide-down"
    >
      <q-card style="max-width: 600px;">
        <q-bar>
          <q-space />
          <IconBtn
            flat
            dense
            icon="close"
            tooltip="Закрыть"
            @icon-btn-click="dialogHistory = false"
          />
        </q-bar>

        <q-card-section class="q-pt-none">
          <TransferHistory :transfer-history-data="transferHistoryData" />
        </q-card-section>
      </q-card>
    </Dialog>
    <Dialog
      :dialog="dialogStatistics"
      :persistent="true"
      :maximized="true"
      transition-show="slide-up"
      transition-hide="slide-down"
    >
      <q-card style="max-width: 600px;">
        <q-bar>
          <q-space />
          <IconBtn
            flat
            dense
            icon="close"
            tooltip="Закрыть"
            @icon-btn-click="dialogStatistics = false"
          />
        </q-bar>

        <q-card-section class="q-pt-none">
          <TransfersStatistics :enter-data="allTransfers" />
        </q-card-section>
      </q-card>
    </Dialog>
    <DialogChooseDate
      :show-dialog="showDialogChooseDate"
      :date="dialogChooseDateData"
    />
  </q-page>
</template>

<script>
import { getUrl } from 'src/tools/url';
import getFromSettings from 'src/tools/settings';
import {
  formatToDotDate,
  addTime,
} from 'src/utils/formatDate';
import CheckErrorsMixin from 'src/mixins/CheckErrors';
import showNotif from 'src/mixins/showNotif';
import ExportDataMixin from 'src/mixins/ExportData';
import { callFunction, numberFormat, phoneNumberFilter } from 'src/utils';
import TransferMixin from 'src/mixins/Transfer';
import {
  setMethodLabel,
  setStatusLabel,
  setFormatedDate,
  setDefaultData,
  getClientCodes,
} from 'src/utils/FrequentlyCalledFunctions';
import { defineAsyncComponent } from 'vue';
import Table from 'components/Elements/Table/Table.vue';
import IconBtn from 'src/components/Buttons/IconBtn.vue';
import UpdateBtn from 'src/components/Buttons/UpdateBtn.vue';
import CountTransfersData from 'src/components/Transfers/CountTransfersData.vue';
import PullRefresh from 'src/components/PullRefresh.vue';
import PageSticky from 'src/components/PageSticky.vue';
import Fab from 'src/components/Elements/Fab.vue';
import FabAction from 'src/components/Elements/FabAction.vue';
import PageScroller from 'src/components/PageScroller.vue';
import BaseBtn from 'src/components/Buttons/BaseBtn.vue';

export default {
  name: 'Transfers',
  components: {
    Table,
    BaseBtn,
    IconBtn,
    PageSticky,
    PullRefresh,
    CountTransfersData,
    UpdateBtn,
    Fab,
    FabAction,
    PageScroller,
    Dialog: defineAsyncComponent(() => import('src/components/Dialogs/Dialog.vue')),
    DialogAddCode: defineAsyncComponent(() => import('src/components/Dialogs/DialogAddCode.vue')),
    DialogAddTransfer: defineAsyncComponent(() => import('src/components/Dialogs/DialogAddTransfer.vue')),
    TransferHistory: defineAsyncComponent(() => import('src/components/History/TransferHistory.vue')),
    TransfersStatistics: defineAsyncComponent(() => import('components/Transfers/TransfersStatistics.vue')),
    DialogChooseDate: defineAsyncComponent(() => import('components/Dialogs/DialogChooseDate.vue')),
  },
  mixins: [CheckErrorsMixin, showNotif, ExportDataMixin, TransferMixin],
  data() {
    return {
      dialogStatistics: false,
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
            label: 'Сумма',
            field: 'sum',
            align: 'center',
            sortable: true,
          },
          {
            name: 'paid',
            label: 'Оплачен',
            field: 'paid',
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
            label: 'Примечания',
            field: 'notation',
            align: 'center',
            sortable: true,
          },
        ],
      },
      transferTableReactiveProperties: {
        selected: [],
        visibleColumns: ['client_name', 'receiver_name', 'receiver_phone', 'sum', 'method_label', 'user_name', 'notation', 'status_label', 'created_at', 'issued_by', 'paid'],
      },
      showDialogChooseDate: false,
      dialogChooseDateData: null,
      dialogChooseDataForSend: {},
    };
  },
  computed: {
    allTransfers() {
      return this.$store.getters['transfers/getTransfers'];
    },
    clientCodes() {
      return this.$store.getters['codes/getCodes'];
    },
    countCheckedTransfers() {
      return _.size(this.transferTableReactiveProperties.selected);
    },
    countSumCheckedTransfers() {
      return _.sumBy(this.transferTableReactiveProperties.selected, 'sum');
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
    dialogChooseDateData(val) {
      if (val) {
        this.dialogChooseDataForSend.date = addTime(val)
          .toISOString();
        this.addToDebtsTableFinish(this.dialogChooseDataForSend);
      }
    },
  },
  mounted() {
    this.getTransfers();
  },
  methods: {
    phoneNumberFilter,
    numberFormat,
    openCloseDialog(val) {
      this.dialog = val;
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
        await this.$store.dispatch('transfers/fetchTransfers')
          .finally(() => {
            this.$q.loading.hide();
          });
      } else {
        await this.$store.dispatch('transfers/fetchNewAndChangedTransfers');
      }
    },
    setAdditionalData(data) {
      return setMethodLabel(setStatusLabel(setFormatedDate(data, ['created_at', 'issued_by'])));
    },
    async refresh(done) {
      if (!done) {
        this.$q.loading.show();
      }
      this.$store.dispatch('transfers/fetchNewAndChangedTransfers')
        .then(() => {
          callFunction(done);
          this.$q.loading.hide();
          this.showNotif('success', 'Данные успешно обновлены.');
        })
        .catch(() => {
          this.$q.loading.hide();
          callFunction(done);
        });
    },
    exportTransfers(data, selectedData) {
      const ids = _.isEmpty(selectedData) ? [] : _.uniq(_.map(selectedData, 'id'));
      this.exportDataToExcel(getUrl('exportTransfers'), {
        ids,
      }, 'Переводы.xlsx');
    },
    addToDebtsTableFinish({
                            ids,
                            date = null,
                          }) {
      this.$q.loading.show();
      this.$axios.post(getUrl('addTransfersToDebts'), {
        ids,
        date,
      })
        .then(() => {
          this.transferTableReactiveProperties.selected = [];
          this.showDialogChooseDate = false;
          this.$q.loading.hide();
          this.showNotif('success', 'Данные успешно добавлены в таблицу долгов', 'center');
        })
        .catch(() => {
          this.$q.loading.hide();
          this.showNotif('error', 'Произошла ошибка при добавлении данных', 'center');
          devlog.error('Ошибка запроса - addTransfersToDebts');
        });
    },
    addToDebtsTable(data) {
      devlog.log('DATAAAA', data);
      const ids = _.map(data, 'id');
      this.showNotif('warning', _.size(ids) > 1 ? 'Добавить записи в талицу долгов?' : 'Добавить запись в талицу долгов?', 'center', [
        {
          label: 'Отмена',
          color: 'white',
          handler: () => {
            this.transferTableReactiveProperties.selected = [];
          },
        },
        {
          label: 'Добавить',
          color: 'white',
          handler: () => {
            this.showDialogChooseDate = true;
            this.dialogChooseDataForSend = {
              ids,
            };
          },
        },
      ]);
    },
  },
};
</script>
