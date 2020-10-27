<template>
  <div
    data-vue-component-name="Cargo"
  >
    <Table
      :table-properties="cargoTableProperties"
      :table-data="cargo"
      :table-reactive-properties="cargoTableReactiveProperties"
      :title="titleTable"
    >
      <template #top-buttons>
        <MenuCargo
          v-show="currentCodeClientId"
        />
        <UpdateBtn
          v-show="currentCodeClientId"
          @update-btn-click="refresh"
        />
        <ExportBtn
          @export-btn-click="exportFaxData(cargoTableReactiveProperties.selected)"
        />
        <IconBtn
          v-show="cargoTableReactiveProperties.selected.length"
          color="negative"
          icon="delete"
          :tooltip="$t('delete')"
          @icon-btn-click="destroyEntry(cargoTableReactiveProperties.selected)"
        />

        <IconBtn
          v-show="cargoTableReactiveProperties.selected.length === 1"
          icon="attach_money"
          tooltip="Оплатить"
          @icon-btn-click="pay(cargoTableReactiveProperties.selected[0])"
        />
        <IconBtn
          v-show="currentCodeClientId"
          icon="payments"
          color="orange"
          tooltip="Оплатить все"
          @icon-btn-click="paymentsAll(currentCodeClientId)"
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
                  {{ props.row.sum | numberFormatFilter }}
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
                    {{ col.value | thingsFilter }}
                  </q-item-label>
                  <q-item-label
                    v-else-if="col.field === 'kg'"
                  >
                    {{ col.value | numberFormatFilter }}
                  </q-item-label>
                  <q-item-label
                    v-else-if="col.field === 'sum'"
                  >
                    {{ col.value | numberFormatFilter }}
                  </q-item-label>
                  <q-item-label
                    v-else-if="col.field === 'paid'"
                  >
                    <q-badge :color="props.row.paid ? 'positive' : 'negative'">{{
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
              <!--              <q-item>-->
              <!--                <q-item-section>-->
              <!--                  <BaseBtn-->
              <!--                    label="История"-->
              <!--                    color="info"-->
              <!--                    style="max-width: 100px;margin: 0 auto;"-->
              <!--                    @click-base-btn="getStorehouseDataHistory(props.row.id, props.cols)"-->
              <!--                  />-->
              <!--                </q-item-section>-->
              <!--              </q-item>-->
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
            {{ props.row.sum | numberFormatFilter }}
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
            <q-badge :color="props.row.paid ? 'positive' : 'negative'">{{
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
            <q-badge :color="props.row.in_cargo ? 'positive' : 'negative'">{{
                props.row.in_cargo ? 'Да' :
                  props.row.type ? null : 'Нет'
              }}
            </q-badge>
          </q-td>

          <q-td
            key="things"
            :props="props"
          >
            {{ props.row.things | thingsFilter }}
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
      :values.sync="entryData"
      :show.sync="showDialogViewCargoData"
    />
    <DialogAddCargoPaymentEntry
      :entry-data.sync="dialogAddCargoPaymentEntryData"
      :show-dialog.sync="showDialogAddCargoPaymentEntry"
    />
    <DialogAddCargoPayEntry
      :entry-data.sync="dialogAddCargoPayEntryData"
      :show-dialog.sync="showDialogAddCargoPayEntry"
    />
    <DialogAddCargoDebtEntry
      :entry-data.sync="dialogAddCargoDebtEntryData"
      :show-dialog.sync="showDialogAddCargoDebtEntry"
    />
  </div>
</template>

<script>
import ExportDataMixin from 'src/mixins/ExportData';
import showNotif from 'src/mixins/showNotif';

export default {
  name: 'Cargo',
  components: {
    Table: () => import('components/Elements/Table/Table.vue'),
    CountCargoCategories: () => import('components/CargoDebts/CountCargoCategories.vue'),
    GeneralClientCargoData: () => import('components/CargoDebts/GeneralClientCargoData.vue'),
    UpdateBtn: () => import('components/Buttons/UpdateBtn.vue'),
    DialogViewCargoData: () => import('components/CargoDebts/Dialogs/DialogViewCargoData.vue'),
    MenuCargo: () => import('components/CargoDebts/MenuCargo.vue'),
    DialogAddCargoPaymentEntry: () => import('components/CargoDebts/Dialogs/DialogAddCargoPaymentEntry.vue'),
    DialogAddCargoDebtEntry: () => import('components/CargoDebts/Dialogs/DialogAddCargoDebtEntry.vue'),
    DialogAddCargoPayEntry: () => import('components/CargoDebts/Dialogs/DialogAddCargoPayEntry.vue'),
    IconBtn: () => import('components/Buttons/IconBtn.vue'),
    ExportBtn: () => import('components/Buttons/ExportBtn.vue'),
  },
  mixins: [ExportDataMixin, showNotif],
  props: {
    refresh: {
      type: Function,
      default: () => {
      },
    },
  },
  data() {
    return {
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
            label: this.$t('place'),
            field: 'place',
            align: 'center',
            sortable: true,
          },
          {
            name: 'kg',
            label: this.$t('kg'),
            field: 'kg',
            align: 'center',
            sortable: true,
          },
          {
            name: 'for_kg',
            label: this.$t('forKg'),
            field: 'for_kg',
            align: 'center',
            sortable: true,
          },
          {
            name: 'for_place',
            label: this.$t('forPlace'),
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
            label: this.$t('category'),
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
            label: this.$t('notation'),
            field: 'notation',
            align: 'center',
            sortable: true,
          },
          {
            name: 'shop',
            label: this.$t('shop'),
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
            label: this.$t('things'),
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
      // if (_.isEmpty(data)) {
      //     data = this.cargo;
      // } else {
      //     const searchData = this.$store.getters['cargoDebts/getCargoForSearch'];
      //     const newArr = [];
      //     _.forEach(data, ({ id, arr }) => {
      //         if (!_.isEmpty(arr)) {
      //             _.forEach(arr, ({ id: ID }) => {
      //                 newArr.push(_.find(searchData, { id: ID }));
      //             });
      //         } else {
      //             newArr.push(_.find(searchData, { id }));
      //         }
      //     });
      //     data = _.orderBy(newArr, (item) => new Date(item.created_at), 'desc');
      // }
      if (!_.isEmpty(data)) {
        const clientName = `${_.get(_.first(data), 'code_client_name')} код карго`;
        const { getUrl } = await import('src/tools/url');
        this.exportDataToExcel(getUrl('exportCargoData'), {
          data,
        }, `${clientName}.xlsx`);
      }
    },
    getIds(data) {
      const ids = [];
      _.forEach(data, ({ arr, id }) => {
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
      this.secargoTableReactivePropertieslected = [];
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
  },
};
</script>
