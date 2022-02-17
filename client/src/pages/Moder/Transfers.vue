<template>
  <q-page
      data-vue-component-name="TransfersPage"
  >
    <NewTable
        :columns="columns"
        :rows="allTransfers"
    >
      <template #top-buttons>
        <div class="row q-gutter-sm">
          <UpdateBtn
              :func="refresh"
          />
          <DialogStatistics />
        </div>
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
              @before-show="props.row.show = true"
              @hide="props.row.show = false"
          >
            <template #header>
              <q-item-section>
                <q-item-label :lines="2">
                  {{ props.row.client_name }}
                </q-item-label>
              </q-item-section>

              <q-item-section>
                <q-item-label>
                  {{ numberFormat(props.row.sum) }}
                </q-item-label>
              </q-item-section>

              <q-item-section>
                <q-item-label>
                  {{ props.row.created_at.slice(0, 10) }}
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
                v-if="props.row.show"
                separator
                dense
                @click="viewEditDialog(props)"
            >
              <q-item
                  v-for="col in columns"
                  :key="col.name"
              >
                <q-item-section>
                  <q-item-label>{{ `${col.label}:` }}</q-item-label>
                </q-item-section>
                <q-item-section side>
                  <q-item-label v-if="col.field === 'status'">
                    <q-badge :color="statusColor(props.row.status_label)">
                      {{ props.row.status_label }}
                    </q-badge>
                  </q-item-label>
                  <q-item-label v-else-if="col.field === 'receiver_phone'">
                    {{ phoneNumberFilter(props.row[col.field]) }}
                  </q-item-label>
                  <q-item-label v-else-if="col.field === 'method'">
                    {{ setMethodLabel(props.row[col.field]) }}
                  </q-item-label>
                  <q-item-label v-else-if="col.field === 'receiver_name'">
                    {{ props.row[col.field] }}
                  </q-item-label>
                  <q-item-label v-else-if="col.field === 'sum'">
                    {{ numberFormat(props.row[col.field]) }}
                  </q-item-label>
                  <q-item-label
                      v-else-if="col.field === 'paid'"
                      :lines="3"
                  >
                    {{ props.row[col.field] ? 'Да' : 'Нет' }}
                  </q-item-label>
                  <q-item-label v-else>
                    {{ props.row[col.field] }}
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
              key="method"
              :props="props"
          >
            {{ setMethodLabel(props.row.method) }}
          </q-td>

          <q-td
              key="status"
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
            {{ fullDate(props.row.created_at) }}
          </q-td>

          <q-td
              key="issued_by"
              :props="props"
          >
            {{ fullDate(props.row.issued_by) }}
          </q-td>

          <q-td
              key="notation"
              :props="props"
          >
            {{ props.row.notation }}
          </q-td>
        </q-tr>
      </template>
    </NewTable>
    <!--    </PullRefresh>-->
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
        v-model:selected="transferTableReactiveProperties.selected"
        :transfer-data="transferData"
    />
    <DialogAddCode v-model:show-dialog="showCodeDialog" />
    <TransferHistory
        ref="transferHistoryComponent"
        v-model:show="dialogHistory"
    />
  </q-page>
</template>

<script>
// import { getUrl } from 'src/tools/url';
import getFromSettings from 'src/tools/settings';
import {
  formatToDotDate,
  fullDate,
} from 'src/utils/formatDate';
import CheckErrorsMixin from 'src/mixins/CheckErrors';
import showNotif from 'src/mixins/showNotif';
import ExportDataMixin from 'src/mixins/ExportData';
import { numberFormat, phoneNumberFilter } from 'src/utils';
import TransferMixin from 'src/mixins/Transfer';
import {
  setDefaultData,
  getClientCodes,
  setMethodLabel,
} from 'src/utils/FrequentlyCalledFunctions';
import { defineAsyncComponent } from 'vue';
// import TableVirtualScrolling from 'src/components/Elements/Table/TableVirtualScrolling.vue';
import NewTable from 'src/components/Elements/Table/NewTable.vue';
import UpdateBtn from 'src/components/Buttons/UpdateBtn.vue';
// import CountTransfersData from 'src/components/Transfers/CountTransfersData.vue';
// import PullRefresh from 'src/components/PullRefresh.vue';
import PageSticky from 'src/components/PageSticky.vue';
import Fab from 'src/components/Elements/Fab.vue';
import FabAction from 'src/components/Elements/FabAction.vue';
import PageScroller from 'src/components/PageScroller.vue';
import DialogStatistics from 'src/components/Elements/Table/DialogStatistics.vue';
import BaseBtn from 'src/components/Buttons/BaseBtn.vue';
import Pusher from 'pusher-js';

export default {
  name: 'TransfersPage',
  components: {
    DialogStatistics,
    NewTable,
    // TableVirtualScrolling,
    BaseBtn,
    PageSticky,
    // PullRefresh,
    // CountTransfersData,
    UpdateBtn,
    Fab,
    FabAction,
    PageScroller,
    DialogAddCode: defineAsyncComponent(() => import('src/components/Dialogs/DialogAddCode.vue')),
    DialogAddTransfer: defineAsyncComponent(() => import('src/components/Dialogs/DialogAddTransfer.vue')),
    // DialogChooseDate: defineAsyncComponent(() => import('src/components/Dialogs/DialogChooseDate.vue')),
    TransferHistory: defineAsyncComponent(() => import('src/components/History/TransferHistory.vue')),
  },
  mixins: [CheckErrorsMixin, showNotif, ExportDataMixin, TransferMixin],
  data() {
    return {
      channel: null,
      dialogHistory: false,
      loading: false,
      dialogStatistics: false,
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
        transfer_commission: {
          type: 'number',
          label: 'Комиссия %',
          field: 'transfer_commission',
          require: false,
          requireError: 'Поле обьязательное для заполнения.',
          changeValue: false,
          default: 1,
          value: 1,
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
      columns: [
        {
          name: 'client_name',
          label: 'Клиент',
          align: 'center',
          field: 'client_name',
          sortable: true,
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
          name: 'method',
          label: 'Метод',
          field: 'method',
          align: 'center',
          sortable: true,
        },
        {
          name: 'status',
          label: 'Статус',
          field: 'status',
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
      transferTableProperties: {
        columns: [
          {
            name: 'client_name',
            label: 'Клиент',
            align: 'center',
            field: 'client_name',
            sortable: true,
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
            name: 'method',
            label: 'Метод',
            field: 'method',
            align: 'center',
            sortable: true,
          },
          {
            name: 'status',
            label: 'Статус',
            field: 'status',
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
        visibleColumns: ['client_name', 'receiver_name', 'receiver_phone', 'sum', 'method_label', 'user_name', 'notation', 'status_label', 'created_at_date', 'issued_by_date', 'paid'],
      },
      showDialogChooseDate: false,
      // dialogChooseDateData: null,
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
    const pusher = new Pusher('47b7c9db3b44606e887f', {
      cluster: 'eu',
    });

    const channel = pusher.subscribe('transfer');
    this.channel = channel.bind('Transfers', ({ transferData: { type, transfer } }) => {
      devlog.log('transferData', transfer);
      if (type === 'store') {
        this.$store.dispatch('transfers/addTransfer', transfer);
      } else {
        this.$store.dispatch('transfers/updateTransfer', transfer);
      }
    });
  },
  unmounted() {
    this.channel.unbind('Transfers');
    this.$store.dispatch('transfers/setTransfers', []);
  },
  methods: {
    setMethodLabel,
    fullDate,
    getTransfersHistory(transferID, cols) {
      this.dialogHistory = true;
      this.$refs.transferHistoryComponent.getTransfersHistory(transferID, cols);
    },
    phoneNumberFilter,
    numberFormat,
    openCloseDialog(val) {
      this.dialog = val;
    },
    viewEditDialog(val, event) {
      if (!_.includes(_.get(event, 'target.classList'), 'select_checkbox')) {
        if (val) {
          this.localProps = val;
          this.transferTableReactiveProperties.selected = [];
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
    refresh() {
      this.loading = true;
      return this.$store.dispatch('transfers/fetchTransfers')
          .finally(() => {
            this.loading = false;
          });
    },
  },
};
</script>
