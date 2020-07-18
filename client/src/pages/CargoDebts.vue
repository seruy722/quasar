<template>
  <q-page
    data-vue-component-name="CargoDebts"
    class="q-pa-md"
  >
    <PullRefresh @refresh="refresh">
      <SearchSelect
        v-model="currentCodeClientId"
        label="Клиент"
        :dense="$q.screen.xs || $q.screen.sm"
        :options="clientCodes"
        style="max-width: 320px;"
      />
      <div class="q-gutter-y-md">
        <q-card>
          <q-tabs
            v-model="tab"
            dense
            class="text-grey"
            active-color="primary"
            indicator-color="primary"
            align="justify"
          >
            <q-tab name="cargo" label="Карго">
              <q-badge color="red">{{ cargo.length }}</q-badge>
            </q-tab>
            <q-tab name="debts" label="Долги">
              <q-badge color="red">{{ debts.length }}</q-badge>
            </q-tab>
          </q-tabs>

          <q-separator />

          <q-tab-panels
            v-model="tab"
            animated
            swipeable
          >
            <q-tab-panel name="cargo">
              <Table
                :table-properties="faxTableProperties"
                :table-data="cargo"
                :table-reactive-properties="faxTableReactiveProperties"
                title="Сводная"
              >
                <template v-slot:top-buttons>
                  <MenuCargo
                    v-show="currentCodeClientId"
                  />
                  <UpdateBtn
                    v-show="currentCodeClientId"
                    @updateBtnClick="refresh"
                  />
                  <ExportBtn
                    @exportBtnClick="exportFaxData"
                  />
                  <IconBtn
                    v-show="faxTableReactiveProperties.selected.length"
                    color="negative"
                    icon="delete"
                    :tooltip="$t('delete')"
                    @iconBtnClick="destroyEntry(faxTableReactiveProperties.selected)"
                  />

                  <!--        <IconBtn-->
                  <!--          icon="data_usage"-->
                  <!--          color="orange"-->
                  <!--          tooltip="Обновить цены"-->
                  <!--          @iconBtnClick="updatePricesInFax(currentFaxItem.id)"-->
                  <!--        />-->
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
                      header-class="bg-secondary text-white"
                      style="border-radius: 30px;border: 1px solid #26A69A;"
                      expand-icon-class="text-white"
                    >
                      <template v-slot:header>
                        <q-item-section avatar>
                          <q-checkbox
                            v-model="props.selected"
                            dense
                          />
                        </q-item-section>

                        <q-item-section>
                          <q-item-label :lines="2">
                            {{ props.row.code_client_name }}
                          </q-item-label>
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
                              @clickBaseBtn="getStorehouseDataHistory(props.row.id, props.cols)"
                            />
                          </q-item-section>
                        </q-item>
                      </q-list>
                    </q-expansion-item>
                  </div>
                </template>

                <template v-slot:inner-body="{props}">
                  <q-tr
                    :props="props"
                    class="cursor-pointer"
                    :class="{table__tr_bold_text: props.row.brand, table__tr_red_bg: !props.row.type, table__tr_green_bg: props.row.type}"
                    @click.stop="viewEditDialog(props, $event)"
                  >
                    <q-td auto-width class="select_checkbox">
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
                      <q-badge :color="props.row.paid ? 'positive' : 'negative'">{{ props.row.paid ? 'Да':
                        props.row.type ? null : 'Нет' }}
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
            </q-tab-panel>

            <q-tab-panel name="debts">
              <Table
                :table-properties="faxDebtsTableProperties"
                :table-data="debts"
                :table-reactive-properties="faxTableDebtsReactiveProperties"
                title="Сводная"
              >
                <template v-slot:top-buttons>
                  <MenuCargo
                    v-show="currentCodeClientId"
                  />
                  <UpdateBtn
                    v-show="currentCodeClientId"
                    @updateBtnClick="refresh"
                  />
                  <ExportBtn
                    @exportBtnClick="exportFaxData"
                  />
                  <IconBtn
                    v-show="faxTableReactiveProperties.selected.length"
                    color="negative"
                    icon="delete"
                    :tooltip="$t('delete')"
                    @iconBtnClick="destroyEntry(faxTableReactiveProperties.selected)"
                  />

                  <!--        <IconBtn-->
                  <!--          icon="data_usage"-->
                  <!--          color="orange"-->
                  <!--          tooltip="Обновить цены"-->
                  <!--          @iconBtnClick="updatePricesInFax(currentFaxItem.id)"-->
                  <!--        />-->
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
                      header-class="bg-secondary text-white"
                      style="border-radius: 30px;border: 1px solid #26A69A;"
                      expand-icon-class="text-white"
                    >
                      <template v-slot:header>
                        <q-item-section avatar>
                          <q-checkbox
                            v-model="props.selected"
                            dense
                          />
                        </q-item-section>

                        <q-item-section>
                          <q-item-label :lines="2">
                            {{ props.row.code_client_name }}
                          </q-item-label>
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
                            <q-item-label>
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
                              @clickBaseBtn="getStorehouseDataHistory(props.row.id, props.cols)"
                            />
                          </q-item-section>
                        </q-item>
                      </q-list>
                    </q-expansion-item>
                  </div>
                </template>

                <template v-slot:inner-body="{props}">
                  <q-tr
                    :props="props"
                    class="cursor-pointer"
                    :class="{table__tr_bold_text: props.row.brand, table__tr_red_bg: !props.row.type, table__tr_green_bg: props.row.type}"
                    @click.stop="viewEditDialog(props, $event)"
                  >
                    <q-td auto-width class="select_checkbox">
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
                      key="commission"
                      :props="props"
                    >
                      {{ props.row.commission }}
                    </q-td>
                    <q-td
                      key="paid"
                      :props="props"
                    >
                      <q-badge :color="props.row.paid ? 'positive' : 'negative'">{{ props.row.paid ? 'Да':
                        props.row.type ? null : 'Нет' }}
                      </q-badge>
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
            </q-tab-panel>
          </q-tab-panels>
        </q-card>
      </div>
    </PullRefresh>
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
    <DialogAddCargoDebtEntry
      :entry-data.sync="dialogAddCargoDebtEntryData"
      :show-dialog.sync="showDialogAddCargoDebtEntry"
    />
  </q-page>
</template>

<script>
    import showNotif from 'src/mixins/showNotif';

    export default {
        name: 'CargoDebts',
        components: {
            SearchSelect: () => import('src/components/Elements/SearchSelect.vue'),
            Table: () => import('src/components/Elements/Table/Table.vue'),
            IconBtn: () => import('src/components/Buttons/IconBtn.vue'),
            BaseBtn: () => import('src/components/Buttons/BaseBtn.vue'),
            DialogViewCargoData: () => import('src/components/CargoDebts/Dialogs/DialogViewCargoData.vue'),
            CountCargoCategories: () => import('src/components/CountCargoCategories.vue'),
            UpdateBtn: () => import('src/components/Buttons/UpdateBtn.vue'),
            ExportBtn: () => import('src/components/Buttons/ExportBtn.vue'),
            PullRefresh: () => import('src/components/PullRefresh.vue'),
            DialogAddCargoPaymentEntry: () => import('src/components/CargoDebts/Dialogs/DialogAddCargoPaymentEntry.vue'),
            DialogAddCargoDebtEntry: () => import('src/components/CargoDebts/Dialogs/DialogAddCargoDebtEntry.vue'),
            MenuCargo: () => import('src/components/CargoDebts/MenuCargo.vue'),
        },
        mixins: [showNotif],
        data() {
            return {
                tab: 'cargo',
                faxTableProperties: {
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
                faxTableReactiveProperties: {
                    selected: [],
                    visibleColumns: ['code_client_name', 'paid', 'created_at', 'type', 'sum', 'place', 'kg', 'for_kg', 'for_place', 'notation', 'category_name', 'fax_name', 'sale'],
                    title: '',
                },
                entryData: [],
                showDialogViewCargoData: false,
                showDialogAddCargoPaymentEntry: false,
                dialogAddCargoPaymentEntryData: {},
                dialogAddCargoDebtEntryData: {},
                showDialogAddCargoDebtEntry: false,
                faxDebtsTableProperties: {
                    columns: [
                        {
                            name: 'created_at',
                            label: 'Дата',
                            align: 'center',
                            field: 'created_at',
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
                            name: 'sum',
                            label: 'Сумма',
                            field: 'sum',
                            align: 'center',
                            sortable: true,
                        },
                        {
                            name: 'commission',
                            label: 'Комиссия',
                            field: 'commission',
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
                            name: 'notation',
                            label: this.$t('notation'),
                            field: 'notation',
                            align: 'center',
                            sortable: true,
                        },
                    ],
                },
                faxTableDebtsReactiveProperties: {
                    selected: [],
                    visibleColumns: ['code_client_name', 'paid', 'created_at', 'type', 'sum', 'notation', 'commission'],
                    title: '',
                },
            };
        },
        computed: {
            clientCodes() {
                return this.$store.getters['codes/getCodes'];
            },
            cargo() {
                return this.$store.getters['cargoDebts/getCargo'];
            },
            debts() {
                return this.$store.getters['cargoDebts/getDebts'];
            },
            currentCodeClientId: {
                get: function get() {
                    return this.$store.getters['cargoDebts/getCurrentCodeClientId'];
                },
                set: function set(val) {
                    this.$store.dispatch('cargoDebts/setCurrentCodeClientId', val);
                },
            },
        },
        watch: {
            currentCodeClientId(id) {
                if (id) {
                    this.getClientData(id);
                }
            },
        },
        async created() {
            const { getClientCodes } = await import('src/utils/FrequentlyCalledFunctions');
            getClientCodes(this.$store);
            const { getUrl } = await import('src/tools/url');
            await this.$axios.get(getUrl('generalCargoData'));
        },
        methods: {
            getClientData(clientId) {
                this.$store.dispatch('cargoDebts/getCargoDebts', clientId);
            },
            viewEditDialog(data, event) {
                devlog.log('data', data, event);
                if (data.row.type) {
                    devlog.log('EMPTY');
                    this.dialogAddCargoPaymentEntryData = data;
                    this.showDialogAddCargoPaymentEntry = true;
                } else if (!data.row.type && !_.includes(event.target.classList, 'faxName')) {
                    this.entryData = _.get(data, 'row.arr');
                    this.showDialogViewCargoData = true;
                }
            },
            async refresh(done) {
                if (!done) {
                    this.$q.loading.show();
                }
                const { callFunction } = await import('src/utils/index');
                this.$store.dispatch('cargoDebts/getCargoDebts', this.currentCodeClientId)
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
            exportFaxData() {
                devlog.log('EXPORT');
            },
            destroyEntry(data) {
                const ids = [];
                _.forEach(data, ({ arr, id }) => {
                    if (!_.isEmpty(arr)) {
                        ids.push(..._.map(arr, 'id'));
                    } else {
                        ids.push(id);
                    }
                });
                if (!_.isEmpty(ids)) {
                    this.showNotif('warning', _.size(ids) > 1 ? 'Удалить записи?' : 'Удалить запись?', 'center', [
                        {
                            label: 'Отмена',
                            color: 'white',
                            handler: () => {
                                this.faxTableReactiveProperties.selected = [];
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
                                      this.faxTableReactiveProperties.selected = [];
                                      this.$q.loading.hide();
                                      this.showNotif('success', _.size(ids) > 1 ? 'Записи успешно удалены.' : 'Запись успешно удалена.', 'center');
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
        },
    };
</script>
