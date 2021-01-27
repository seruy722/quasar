<template>
  <q-page
    data-vue-component-name="TransfersClient"
  >
    <PullRefresh @refresh="refresh">
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
<!--          <IconBtn-->
<!--            color="positive"-->
<!--            tooltip="Excel"-->
<!--            icon="explicit"-->
<!--            class="q-ml-sm"-->
<!--            @icon-btn-click="exportTransfers"-->
<!--          />-->
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
                    {{ props.row.receiver_name }}
                  </q-item-label>
                </q-item-section>

                <q-item-section>
                  <q-item-label>
                    {{ props.row.sum }}
                  </q-item-label>
                </q-item-section>

                <q-item-section side>
                  <q-item-label>
                    {{ props.row.created_at.slice(0, 5) }}
                  </q-item-label>
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
                      {{ col.value | phoneNumberFilter }}
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
              </q-list>
            </q-expansion-item>
          </div>
        </template>

        <template #inner-body="{props}">
          <q-tr
            :props="props"
            class="text-bold cursor-pointer"
            @click.stop="viewEditDialog(props, $event)"
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
              <q-badge :color="statusColor(props.row.status)">
                {{ props.row.status_label }}
              </q-badge>
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
      <CountTransfersDataClient :enter-data="allTransfers" />
    </PullRefresh>
    <PageSticky :offset="[18, 200]">
      <Fab color="accent">
        <FabAction
          color="positive"
          @fab-action-click="viewEditDialog"
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
      <q-card style="min-width: 320px;width: 100%;max-width: 500px;">
        <q-card-section class="row justify-between bg-grey q-mb-sm">
          <span class="text-h6">{{ dialogTitle }}</span>
          <div>
            <IconBtn
              dense
              icon="clear"
              tooltip="Закрыть"
              @icon-btn-click="cancel(transferData)"
            />
          </div>
        </q-card-section>
        <q-card-section>
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
              <template #append>
                <Date
                  :value.sync="item.value"
                  :change-value.sync="item.changeValue"
                />
              </template>
            </BaseInput>
          </div>
        </q-card-section>

        <Separator />
        <q-card-actions>
          <BaseBtn
            label="Отмена"
            color="negative"
            @click-base-btn="cancel(transferData)"
          />
          <BaseBtn
            label="Сохранить"
            color="positive"
            @click-base-btn="checkErrors(transferData, updateData)"
          />
        </q-card-actions>
      </q-card>
    </Dialog>
  </q-page>
</template>

<script>
import { getUrl } from 'src/tools/url';
import getFromSettings from 'src/tools/settings';
import {
  reverseDate,
  addTime,
} from 'src/utils/formatDate';
import CheckErrorsMixin from 'src/mixins/CheckErrors';
import showNotif from 'src/mixins/showNotif';
import ExportDataMixin from 'src/mixins/ExportData';
import { callFunction } from 'src/utils/index';
import TransferMixin from 'src/mixins/Transfer';
import {
  setMethodLabel,
  setStatusLabel,
  setFormatedDate,
  setChangeValue,
  setDefaultData,
} from 'src/utils/FrequentlyCalledFunctions';
import { formatISO } from 'date-fns';

export default {
  name: 'TransfersClient',
  components: {
    Table: () => import('components/Elements/Table/Table.vue'),
    Dialog: () => import('components/Dialogs/Dialog.vue'),
    Date: () => import('components/Date.vue'),
    BaseInput: () => import('components/Elements/BaseInput.vue'),
    IconBtn: () => import('components/Buttons/IconBtn.vue'),
    BaseBtn: () => import('components/Buttons/BaseBtn.vue'),
    Separator: () => import('components/Separator.vue'),
    SearchSelect: () => import('components/Elements/SearchSelect.vue'),
    BaseSelect: () => import('components/Elements/BaseSelect.vue'),
    PageSticky: () => import('components/PageSticky.vue'),
    Fab: () => import('components/Elements/Fab.vue'),
    FabAction: () => import('components/Elements/FabAction.vue'),
    PageScroller: () => import('components/PageScroller.vue'),
    PullRefresh: () => import('components/PullRefresh.vue'),
    CountTransfersDataClient: () => import('components/Transfers/CountTransfersDataClient.vue'),
    UpdateBtn: () => import('components/Buttons/UpdateBtn.vue'),
  },
  mixins: [CheckErrorsMixin, showNotif, ExportDataMixin, TransferMixin],
  data() {
    return {
      localProps: {},
      dialog: false,
      transferData: {
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
          options: getFromSettings('transferStatusClient'),
          require: true,
          requireError: 'Поле обьязательное для заполнения.',
          changeValue: false,
          default: 6,
          value: 0,
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
        visibleColumns: ['receiver_name', 'receiver_phone', 'sum', 'method_label', 'notation', 'status_label', 'created_at', 'issued_by'],
      },
    };
  },
  computed: {
    allTransfers() {
      return this.$store.getters['transfers/getTransfersClient'];
    },
    dialogTitle() {
      return _.get(this.localProps, 'row.client_name') || 'Новый перевод';
    },
    countCheckedTransfers() {
      return _.size(this.transferTableReactiveProperties.selected);
    },
    countSumCheckedTransfers() {
      return _.sumBy(this.transferTableReactiveProperties.selected, 'sum');
    },
  },
  mounted() {
    this.getTransfers();
  },
  methods: {
    updateData(data) {
      const sendData = _.cloneDeep(data);
      devlog.log('DATA_N0', sendData);
      if (_.isEmpty(this.localProps)) {
        // ДОБАВЛЕНИЕ ЗАПИСИ
        this.$q.loading.show();
        const values = _.mapValues(sendData, 'value');
        values.receiver_name = _.startCase(_.toLower(values.receiver_name));
        if (_.trim(values.issued_by)) {
          const date = reverseDate(values.issued_by);
          values.issued_by = formatISO(addTime(date));
        }
        this.$axios.post(getUrl('storeTransfersClient'), values)
          .then(({ data: { transfer } }) => {
            devlog.log('DDFR', transfer);
            this.$store.dispatch('transfers/addTransferClient', this.setAdditionalData([_.first(transfer)]));
            this.openCloseDialog(false);
            setChangeValue(this.transferData);
            this.$q.loading.hide();
            this.showNotif('success', `Перевод на сумму - ${_.get(transfer, '[0].sum')} успешно добавлен.`, 'center');
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
          const dataToSend = _.reduce(sendData, (result, {
            value,
            changeValue,
          }, index) => {
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
              this.$store.dispatch('transfers/updateTransferClient', this.setAdditionalData([_.first(transfer)]));
              this.openCloseDialog(false);
              this.localProps.selected = false;
              this.$q.loading.hide();
              setChangeValue(this.transferData);
              this.showNotif('success', 'Запись успешно обновлена.', 'center');
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
      if (!_.includes(_.get(event, 'target.classList'), 'select_checkbox')) {
        const status = _.get(val, 'row.status');
        if (val && (status === 6 || status === 2)) {
          this.localProps = val;
          this.transferTableReactiveProperties.selected = [];
          setTimeout(() => {
            val.selected = !val.selected;
          }, 100);

          _.forEach(this.transferData, (item) => {
            if (_.has(val.row, item.field)) {
              if (_.get(val, `row[${item.field}]`)) {
                _.set(item, 'value', _.get(val, `row[${item.field}]`));
              } else {
                _.set(item, 'value', item.default);
              }
            }
          });
          this.openCloseDialog(true);
        } else if (val) {
          this.openCloseDialog(false);
          this.localProps = {};
          setDefaultData(this.transferData);
          this.showNotif('info', 'Запись закрыта для редактирования', 'center');
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
        await this.$store.dispatch('transfers/fetchTransfersClient')
          .then(() => {
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
      if (!done) {
        this.$q.loading.show();
      }
      this.$store.dispatch('transfers/fetchTransfersClient')
        .then(() => {
          callFunction(done);
          this.$q.loading.hide();
          this.showNotif('success', 'Данные успешно обновлены.', 'center');
        })
        .catch(() => {
          this.$q.loading.hide();
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

.statistics_title
  background-color lightgrey
  text-align center
</style>
