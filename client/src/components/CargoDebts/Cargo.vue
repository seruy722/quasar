<template>
  <div
      data-vue-component-name="CargoComponent"
  >
    <Table
        :table-properties="cargoTableProperties"
        :table-data="cargo"
        :table-reactive-properties="cargoTableReactiveProperties"
        :title="titleTable"
        :loading="loading"
    >
      <template #top-buttons>
        <div class="row q-gutter-sm">
          <MenuCargo
              v-show="currentCodeClientId"
          />
          <UpdateBtn
              v-show="currentCodeClientId"
              :func="refresh"
          />
          <RoundBtn
              v-show="cargoTableReactiveProperties.selected.length"
              color="negative"
              icon="delete"
              tooltip="Удалить"
              @round-btn-click="destroyEntry(cargoTableReactiveProperties.selected)"
          />

          <RoundBtn
              v-show="cargoTableReactiveProperties.selected.length === 1"
              icon="attach_money"
              tooltip="Оплатить"
              @round-btn-click="pay(cargoTableReactiveProperties.selected[0])"
          />
          <RoundBtn
              v-show="currentCodeClientId"
              icon="payments"
              color="orange"
              tooltip="Оплатить все"
              @round-btn-click="paymentsAll(currentCodeClientId)"
          />
          <q-btn-dropdown
              split
              color="positive"
              icon="explicit"
              dense
          >
            <q-list separator>
              <q-item
                  v-close-popup
                  clickable
                  @click="exportFaxData(cargoTableReactiveProperties.selected)"
              >
                <q-item-section>
                  <q-item-label>Выгрузить</q-item-label>
                </q-item-section>
              </q-item>

              <q-item
                  v-close-popup
                  clickable
                  @click="exportDetailFaxData(cargoTableReactiveProperties.selected)"
              >
                <q-item-section>
                  <q-item-label>Выгрузить детально</q-item-label>
                </q-item-section>
              </q-item>
            </q-list>
          </q-btn-dropdown>
          <RoundBtn
              v-show="currentCodeClientId"
              icon="payment"
              color="brown"
              tooltip="Рассчитать клиента"
              @round-btn-click="calculateClient(cargoTableReactiveProperties.selected, cargo)"
          />
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
              :header-class="`${props.row.type ? 'bg-green' : 'bg-red'} text-white`"
              :style="`border-radius: 30px;border: 1px solid ${props.row.type ? 'lightgreen' : 'lightcoral'};`"
              expand-icon-class="text-white"
          >
            <template #header>
              <q-item-section avatar>
                <q-checkbox
                    v-model="props.selected"
                    dense
                />
              </q-item-section>
              <q-item-section avatar>
                <q-item-label>
                  {{ numberFormat(props.row.sum) }}
                </q-item-label>
              </q-item-section>

              <q-item-section>
                <q-item-label :lines="2">
                  {{
                    props.row.kg ? `${props.row.place}м/${props.row.kg}кг ${props.row.fax_name || ''}` : props.row.type
                        ?
                        props.row.sum : props.row.notation
                  }}
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
            >
              <q-item
                  v-for="col in props.cols.filter(col => col.name !== 'desc')"
                  :key="col.name"
                  @click="viewEditDialog(props)"
              >
                <q-item-section>
                  <q-item-label>{{ `${col.label}:` }}</q-item-label>
                </q-item-section>
                <q-item-section side>
                  <q-item-label
                      v-if="col.field === 'things'"
                      :lines="10"
                  >
                    {{ thingsFilter(col.value) }}
                  </q-item-label>
                  <q-item-label
                      v-else-if="col.field === 'kg'"
                  >
                    {{ numberFormat(col.value) }}
                  </q-item-label>
                  <q-item-label
                      v-else-if="col.field === 'sum'"
                  >
                    {{ numberFormat(col.value) }}
                  </q-item-label>
                  <q-item-label
                      v-else-if="col.field === 'paid'"
                  >
                    <q-badge :color="props.row.paid ? 'positive' : 'negative'">
                      {{
                        props.row.paid ? 'Да' :
                            props.row.type ? null : 'Нет'
                      }}
                    </q-badge>
                  </q-item-label>
                  <q-item-label
                      v-else-if="col.field === 'type'"
                  >
                    {{ col.value ? 'Оплата' : 'Долг' }}
                  </q-item-label>
                  <q-item-label
                      v-else-if="col.field === 'notation'"
                      :lines="3"
                  >
                    {{ col.value }}
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
            class="cursor-pointer"
            :class="{table__tr_bold_text: props.row.brand, table__tr_red_bg: !props.row.type, table__tr_green_bg: props.row.type}"
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
              key="created_at"
              :props="props"
          >
            {{ props.row.created_at }}
          </q-td>

          <q-td
              key="code_place"
              :props="props"
          >
            {{ props.row.code_place }}
          </q-td>

          <q-td
              key="code_client_name"
              :props="props"
          >
            {{ props.row.code_client_name }}
          </q-td>

          <q-td
              key="type"
              :props="props"
          >
            {{ props.row.type ? 'Оплата' : 'Долг' }}
          </q-td>

          <q-td
              key="place"
              :props="props"
          >
            {{ props.row.type ? null : props.row.place }}
          </q-td>

          <q-td
              key="kg"
              :props="props"
          >
            {{ props.row.type ? null : props.row.kg }}
          </q-td>

          <q-td
              key="for_kg"
              class="text-bold cursor-pointer"
              :props="props"
          >
            {{ props.row.type ? null : props.row.for_kg }}
          </q-td>

          <q-td
              key="for_place"
              class="text-bold cursor-pointer"
              :props="props"
          >
            {{ props.row.type ? null : props.row.for_place }}
          </q-td>

          <q-td
              key="sum"
              :props="props"
          >
            {{ numberFormat(props.row.sum) }}
          </q-td>
          <q-td
              key="sale"
              :props="props"
          >
            {{ props.row.type ? props.row.sale : null }}
          </q-td>
          <q-td
              key="paid"
              :props="props"
          >
            <q-badge :color="props.row.paid ? 'positive' : 'negative'">
              {{
                props.row.paid ? 'Да' :
                    props.row.type ? null : 'Нет'
              }}
            </q-badge>
          </q-td>

          <q-td
              key="category_name"
              :props="props"
          >
            {{ props.row.category_name }}
          </q-td>
          <q-td
              key="fax_name"
              :props="props"
              class="faxName"
          >
            <router-link :to="{ name: 'fax', params: { id: props.row.fax_id }}">
              {{ props.row.fax_name }}
            </router-link>
          </q-td>

          <q-td
              key="get_pay_user_name"
              :props="props"
          >
            {{ props.row.get_pay_user_name }}
          </q-td>

          <q-td
              key="notation"
              :props="props"
          >
            {{ props.row.notation }}
          </q-td>

          <q-td
              key="shop"
              :props="props"
          >
            {{ props.row.shop }}
          </q-td>

          <q-td
              key="in_cargo"
              :props="props"
          >
            <q-badge :color="props.row.in_cargo ? 'positive' : 'negative'">
              {{
                props.row.in_cargo ? 'Да' :
                    props.row.type ? null : 'Нет'
              }}
            </q-badge>
          </q-td>

          <q-td
              key="things"
              :props="props"
          >
            {{ thingsFilter(props.row.things) }}
          </q-td>

          <q-td
              key="delivery_method_name"
              :props="props"
          >
            {{ props.row.delivery_method_name }}
          </q-td>

          <q-td
              key="department"
              :props="props"
          >
            {{ props.row.department }}
          </q-td>
        </q-tr>
      </template>
    </Table>
    <GeneralClientCargoData
        :list="cargo"
        title="Баланс"
        style="max-width: 500px;margin:20px auto;"
    />
    <CountCargoCategories
        :list="cargo"
        style="max-width: 500px;margin:0 auto;"
    />
    <DialogViewCargoData
        v-model:values="entryData"
        v-model:show="showDialogViewCargoData"
    />
    <DialogAddCargoPaymentEntry
        v-model:entry-data="dialogAddCargoPaymentEntryData"
        v-model:show-dialog="showDialogAddCargoPaymentEntry"
    />
    <DialogAddCargoPayEntry
        v-model:entry-data="dialogAddCargoPayEntryData"
        v-model:show-dialog="showDialogAddCargoPayEntry"
    />
    <DialogAddCargoDebtEntry
        v-model:entry-data="dialogAddCargoDebtEntryData"
        v-model:show-dialog="showDialogAddCargoDebtEntry"
    />
    <Dialog
        :dialog="dialogCalculateClient"
        :persistent="true"
        :maximized="true"
    >
      <q-card style="max-width: 600px;">
        <q-bar>
          <q-space />
          <IconBtn
              flat
              dense
              icon="close"
              tooltip="Закрыть"
              @icon-btn-click="dialogCalculateClient = false"
          />
        </q-bar>

        <q-card-section class="q-pt-none">
          <CalculateClient :calculate-data="calculateData" />
        </q-card-section>
      </q-card>
    </Dialog>
  </div>
</template>

<script>
import ExportDataMixin from 'src/mixins/ExportData';
import showNotif from 'src/mixins/showNotif';
import { numberFormat, thingsFilter } from 'src/utils';
import Table from 'src/components/Elements/Table/Table.vue';
import CountCargoCategories from 'src/components/CargoDebts/CountCargoCategories.vue';
import GeneralClientCargoData from 'src/components/CargoDebts/GeneralClientCargoData.vue';
import UpdateBtn from 'src/components/Buttons/UpdateBtn.vue';
import DialogViewCargoData from 'src/components/CargoDebts/Dialogs/DialogViewCargoData.vue';
import MenuCargo from 'src/components/CargoDebts/MenuCargo.vue';
import DialogAddCargoPaymentEntry from 'src/components/CargoDebts/Dialogs/DialogAddCargoPaymentEntry.vue';
import DialogAddCargoDebtEntry from 'src/components/CargoDebts/Dialogs/DialogAddCargoDebtEntry.vue';
import DialogAddCargoPayEntry from 'src/components/CargoDebts/Dialogs/DialogAddCargoPayEntry.vue';
import IconBtn from 'src/components/Buttons/IconBtn.vue';
import RoundBtn from 'src/components/Buttons/RoundBtn.vue';
import CalculateClient from 'src/components/CargoDebts/CalculateClient.vue';
import Dialog from 'src/components/Dialogs/Dialog.vue';

export default {
  name: 'CargoComponent',
  components: {
    Table,
    CountCargoCategories,
    GeneralClientCargoData,
    UpdateBtn,
    DialogViewCargoData,
    MenuCargo,
    DialogAddCargoPaymentEntry,
    DialogAddCargoDebtEntry,
    DialogAddCargoPayEntry,
    IconBtn,
    CalculateClient,
    Dialog,
    RoundBtn,
  },
  mixins: [ExportDataMixin, showNotif],
  props: {
    refresh: {
      type: Function,
      default: () => {
      },
    },
    loading: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      calculateData: [],
      dialogCalculateClient: false,
      entryData: [],
      showDialogViewCargoData: false,
      cargoTableProperties: {
        columns: [
          {
            name: 'created_at',
            label: 'Дата',
            align: 'center',
            field: 'created_at',
            sortable: true,
          },
          {
            name: 'code_place',
            label: 'Код',
            align: 'center',
            field: 'code_place',
            sortable: true,
          },
          {
            name: 'code_client_name',
            label: 'Клиент',
            align: 'center',
            field: 'code_client_name',
            sortable: true,
          },
          {
            name: 'type',
            label: 'Тип',
            align: 'center',
            field: 'type',
            sortable: true,
          },
          {
            name: 'place',
            label: 'Мест',
            field: 'place',
            align: 'center',
            sortable: true,
          },
          {
            name: 'kg',
            label: 'Вес',
            field: 'kg',
            align: 'center',
            sortable: true,
          },
          {
            name: 'for_kg',
            label: 'За кг',
            field: 'for_kg',
            align: 'center',
            sortable: true,
          },
          {
            name: 'for_place',
            label: 'За место',
            field: 'for_place',
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
            name: 'sale',
            label: 'Скидка',
            field: 'sale',
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
            name: 'category_name',
            label: 'Категория',
            field: 'category_name',
            align: 'center',
            sortable: true,
          },
          {
            name: 'fax_name',
            label: 'Факс',
            field: 'fax_name',
            align: 'center',
            sortable: true,
          },
          {
            name: 'get_pay_user_name',
            label: 'Пользователь',
            field: 'get_pay_user_name',
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
          {
            name: 'shop',
            label: 'Магазин',
            field: 'shop',
            align: 'center',
            sortable: true,
          },
          {
            name: 'in_cargo',
            label: 'Доставлен',
            field: 'in_cargo',
            align: 'center',
            sortable: true,
          },
          {
            name: 'things',
            label: 'Опись',
            field: 'things',
            align: 'center',
            sortable: true,
          },
          {
            name: 'delivery_method_name',
            label: 'Способ доставки',
            field: 'delivery_method_name',
            align: 'center',
            sortable: true,
          },
          {
            name: 'department',
            label: 'Отделение',
            field: 'department',
            align: 'center',
            sortable: true,
          },

        ],
      },
      cargoTableReactiveProperties: {
        selected: [],
        visibleColumns: ['code_client_name', 'paid', 'created_at', 'type', 'sum', 'in_cargo', 'place', 'kg', 'for_kg', 'for_place', 'notation', 'category_name', 'fax_name', 'sale', 'get_pay_user_name'],
        title: '',
      },
      showDialogAddCargoPaymentEntry: false,
      dialogAddCargoPaymentEntryData: {},
      dialogAddCargoDebtEntryData: {},
      showDialogAddCargoDebtEntry: false,
      dialogAddCargoPayEntryData: {},
      showDialogAddCargoPayEntry: false,
    };
  },
  computed: {
    cargo() {
      return this.$store.getters['cargoDebts/getCargo'];
    },
    currentCodeClientId() {
      return this.$store.getters['cargoDebts/getCurrentCodeClientId'];
    },
    titleTable() {
      return `Сумма: ${_.sumBy(this.cargo, 'sum')}, Скидки: ${_.sumBy(this.cargo, 'sale')}`;
    },
  },
  methods: {
    numberFormat,
    thingsFilter,
    async viewEditDialog(data, event) {
      if (!_.includes(_.get(event, 'target.classList'), 'select_checkbox')) {
        devlog.log('data', data, event);
        if (data.row.type) {
          devlog.log('EMPTY');
          this.dialogAddCargoPaymentEntryData = data;
          this.showDialogAddCargoPaymentEntry = true;
        } else if (!data.row.type && !_.includes(event.target.classList, 'faxName') && _.has(data, 'row.arr')) {
          const { setFormatedDate } = await import('src/utils/FrequentlyCalledFunctions');
          this.entryData = setFormatedDate(_.cloneDeep(_.get(data, 'row.arr')), ['created_at']);
          this.showDialogViewCargoData = true;
        } else {
          this.entryData = [_.get(data, 'row')];
          this.showDialogViewCargoData = true;
        }
      }
    },
    destroyEntry(data) {
      const ids = this.getIds(data);
      if (!_.isEmpty(ids)) {
        this.showNotif('warning', _.size(ids) > 1 ? 'Удалить записи?' : 'Удалить запись?', 'center', [
          {
            label: 'Отмена',
            color: 'white',
            handler: () => {
              this.cargoTableReactiveProperties.selected = [];
            },
          },
          {
            label: 'Удалить',
            color: 'white',
            handler: async () => {
              const { getUrl } = await import('src/tools/url');
              this.$q.loading.show();
              this.$axios.post(getUrl('deleteCargoEntry'), { ids })
                  .then(() => {
                    this.$store.dispatch('cargoDebts/deleteCargoEntry', ids);
                    this.cargoTableReactiveProperties.selected = [];
                    this.$q.loading.hide();
                    this.showNotif('success', _.size(ids) > 1 ? 'Записи успешно удалены.' : 'Запись успешно удалена.', false);
                  })
                  .catch(() => {
                    this.$q.loading.hide();
                    devlog.error('Ошибка запроса - destroyEntry');
                  });
            },
          },
        ]);
      }
    },
    async exportFaxData(selected) {
      const data = !_.isEmpty(selected) ? selected : this.cargo;
      if (!_.isEmpty(data)) {
        const clientName = `${_.get(_.first(data), 'code_client_name')} код карго`;
        const { getUrl } = await import('src/tools/url');
        this.exportDataToExcel(getUrl('exportCargoData'), {
          data,
        }, `${clientName}.xlsx`);
      }
    },
    async exportDetailFaxData(selected) {
      const data = !_.isEmpty(selected) ? selected : this.cargo;
      if (!_.isEmpty(data)) {
        const clientName = `${_.get(_.first(data), 'code_client_name')} код карго`;
        const { getUrl } = await import('src/tools/url');
        const ids = [];
        _.forEach(data, (item) => {
          if (item.arr) {
            ids.push(..._.map(item.arr, 'id'));
          } else {
            ids.push(item.id);
          }
        });
        this.exportDataToExcel(getUrl('exportDetailCargoData'), {
          ids,
        }, `${clientName}.xlsx`);
      }
    },
    getIds(data) {
      const ids = [];
      _.forEach(data, ({
                         arr,
                         id,
                       }) => {
        if (!_.isEmpty(arr)) {
          ids.push(..._.map(arr, 'id'));
        } else {
          ids.push(id);
        }
      });
      return ids;
    },
    pay(data) {
      if (!data.type && !data.paid) {
        this.dialogAddCargoPayEntryData = data;
        this.showDialogAddCargoPayEntry = true;
      }
      this.cargoTableReactiveProperties.selected = [];
    },
    async paymentsAll(id) {
      if (id) {
        this.showNotif('warning', 'Перевести все данные клиента в статус оплаты?', 'center', [
          {
            label: 'Отмена',
            color: 'white',
            handler: () => {
            },
          },
          {
            label: 'OK',
            color: 'white',
            handler: async () => {
              const { getUrl } = await import('src/tools/url');
              this.$q.loading.show();
              this.$axios.get(`${getUrl('cargoPaymentsAll')}/${id}`)
                  .then(({ data: { cargo } }) => {
                    this.$store.dispatch('cargoDebts/updateCargoEntry', cargo);
                    this.$q.loading.hide();
                    this.showNotif('success', 'Данные по карго все переведены в статус оплаты.', false);
                  })
                  .catch(() => {
                    this.$q.loading.hide();
                  });
            },
          },
        ]);
      }
    },
    calculateClient(selected, data) {
      if (_.isEmpty(selected) && _.isEmpty(data)) {
        this.showNotif('warning', 'В таблице нет записей', 'center');
      } else if (!_.isEmpty(selected)) {
        devlog.log(selected);
        this.calculateData = _.cloneDeep(selected);
      } else if (!_.isEmpty(data)) {
        devlog.log(data);
        this.calculateData = _.cloneDeep(data);
      } else {
        this.dialogCalculateClient = this.calculateData.length;
      }
    },
    onItemClick() {
      // console.log('Clicked on an Item')
    },
  },
};
</script>
