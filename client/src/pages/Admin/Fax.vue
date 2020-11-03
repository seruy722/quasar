<template>
  <q-page
    data-vue-component-name="Fax"
  >
    <Table
      :table-properties="faxTableProperties"
      :table-data="faxTableData"
      :table-reactive-properties="faxTableReactiveProperties"
      :title="currentFaxItem.name"
    >
      <template #top-buttons>
        <IconBtn
          v-show="addToSaveArray.length"
          color="positive"
          icon="save"
          tooltip="Сохранить"
          @icon-btn-click="saveDataInCombineTable(addToSaveArray)"
        />

        <q-checkbox
          v-model="combineTableData"
          label="Обьеденено"
          dense
        />

        <IconBtn
          color="positive"
          icon="explicit"
          tooltip="excel"
          @icon-btn-click="exportFaxData(faxTableReactiveProperties.selected)"
        />

        <IconBtn
          color="positive"
          icon="directions_bus"
          tooltip="Одесса-Харьков"
          @icon-btn-click="exportFaxDataOdessaKharkov"
        />

        <IconBtn
          color="positive"
          icon="directions_railway"
          tooltip="Одесса"
          @icon-btn-click="exportFaxDataOdessa"
        />

        <IconBtn
          v-show="!combineTableData"
          icon="sync_alt"
          tooltip="Трансфер данных"
          @icon-btn-click="openDialogTransferFromStorehouse"
        />

        <IconBtn
          icon="data_usage"
          color="orange"
          tooltip="Обновить цены"
          @icon-btn-click="updatePricesInFax(currentFaxItem.id)"
        />
        <IconBtn
          v-show="faxTableReactiveProperties.selected.length"
          color="negative"
          icon="delete"
          :tooltip="$t('delete')"
          @icon-btn-click="destroyEntry(faxTableReactiveProperties.selected)"
        />
        <IconBtn
          dense
          icon="history"
          tooltip="История"
          @icon-btn-click="getFaxTransfersDataHistory(currentFaxItem)"
        />
        <IconBtn
          v-show="faxTableReactiveProperties.selected.length"
          dense
          icon="vertical_align_center"
          color="accent"
          tooltip="Доставлено"
          @icon-btn-click="dialogSelectDeliveredPlace = true"
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
            header-class="bg-secondary text-white"
            style="border-radius: 30px;border: 1px solid #26A69A;"
            expand-icon-class="text-white"
          >
            <template #header>
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
                  <q-item-label
                    v-else-if="col.field === 'notation'"
                    :lines="4"
                  >
                    {{ col.value }}
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
                    @click-base-btn="getStorehouseDataHistory(props.row.id, props.cols)"
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
          :class="{table__tr_bold_text: props.row.brand, 'cursor-pointer': !combineTableData}"
          @click.stop="viewEditDialog(props, $event)"
        >
          <q-td auto-width>
            <q-checkbox
              v-model="props.selected"
              dense
            />
          </q-td>
          <q-td
            key="code_place"
            :props="props"
          >
            {{ props.row.code_place }}
          </q-td>

          <q-td
            key="code_client_name"
            class="cursor-pointer"
            :props="props"
          >
            {{ props.row.code_client_id | optionsFilter(clientCodes) }}
            <PopupEdit
              v-if="combineTableData"
              :value.sync="props.row.code_client_id"
              type="number"
              :title="props.row.code_client_name"
              @add-to-save="addToAddSaveArray(props.row, 'code_client_id')"
            >
              <SearchSelect
                v-model="props.row.code_client_id"
                label="Клиент"
                :dense="$q.screen.xs || $q.screen.sm"
                :options="clientCodes"
              />
            </PopupEdit>
          </q-td>

          <q-td
            key="place"
            :props="props"
          >
            {{ props.row.place }}
          </q-td>

          <q-td
            key="kg"
            :props="props"
          >
            {{ props.row.kg | numberFormatFilter }}
          </q-td>

          <q-td
            key="for_kg"
            class="text-bold text-red cursor-pointer"
            :props="props"
          >
            {{ props.row.for_kg | numberFormatFilter }}
            <PopupEdit
              v-if="combineTableData"
              :value.sync="props.row.for_kg"
              type="number"
              :title="props.row.code_client_name"
              @add-to-save="addToAddSaveArray(props.row, 'for_kg')"
            >
              <q-input
                v-model.number="props.row.for_kg"
                type="number"
                autofocus
                dense
              />
              <q-checkbox
                v-model="props.row.replacePrice"
                label="Заменить"
                dense
              />
            </PopupEdit>
          </q-td>

          <q-td
            key="for_place"
            class="text-bold text-red cursor-pointer"
            :props="props"
          >
            {{ props.row.for_place | numberFormatFilter }}
            <PopupEdit
              v-if="combineTableData"
              :value.sync="props.row.for_place"
              type="number"
              :title="props.row.code_client_name"
              @add-to-save="addToAddSaveArray(props.row, 'for_place')"
            />
          </q-td>

          <q-td
            key="category_name"
            class="cursor-pointer"
            :props="props"
          >
            {{ props.row.category_id | optionsFilter(categories) }}
            <PopupEdit
              v-if="combineTableData"
              :value.sync="props.row.category_id"
              type="number"
              :title="props.row.code_client_name"
              @add-to-save="addToAddSaveArray(props.row, 'category_id')"
            >
              <SearchSelect
                v-model="props.row.category_id"
                label="Категория"
                :dense="$q.screen.xs || $q.screen.sm"
                :options="categories"
              />
            </PopupEdit>
          </q-td>

          <q-td
            key="in_cargo"
            :props="props"
          >
            <q-badge :color="props.row.in_cargo ? 'positive' : 'negative'">{{ props.row.in_cargo ? 'Да' : 'Нет' }}
            </q-badge>
          </q-td>

          <q-td
            key="shop"
            :props="props"
          >
            {{ props.row.shop }}
          </q-td>

          <q-td
            key="delivery_method_name"
            class="cursor-pointer"
            :props="props"
          >
            {{ props.row.delivery_method_id | optionsFilter(deliveryMethodsList) }}
            <PopupEdit
              v-if="combineTableData"
              :value.sync="props.row.delivery_method_id"
              type="number"
              :title="props.row.code_client_name"
              @add-to-save="addToAddSaveArray(props.row, 'delivery_method_id')"
            >
              <SearchSelect
                v-model="props.row.delivery_method_id"
                label="Способ доставки"
                :dense="$q.screen.xs || $q.screen.sm"
                :options="deliveryMethodsList"
              />
            </PopupEdit>
          </q-td>

          <q-td
            key="department"
            :props="props"
            class="cursor-pointer"
          >
            {{ props.row.department }}
            <PopupEdit
              v-if="combineTableData"
              :value.sync="props.row.department"
              :title="props.row.code_client_name"
              @add-to-save="addToAddSaveArray(props.row, 'department')"
            />
          </q-td>

          <q-td
            key="notation"
            :props="props"
          >
            {{ props.row.notation }}
          </q-td>

          <q-td
            key="things"
            :props="props"
          >
            {{ props.row.things | thingsFilter }}
          </q-td>
        </q-tr>
      </template>
    </Table>

    <CountCategories
      :list="faxTableData"
      :fax-id="currentFaxItem.id"
      style="max-width: 600px;margin:0 auto;"
    />
    <DialogFaxData
      :show-dialog.sync="showFaxDataDialog"
      :entry-data.sync="localFaxEditData"
    />
    <Dialog
      :dialog="dialogHistory"
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
            @icon-btn-click="dialogHistory = false"
          />
        </q-bar>

        <q-card-section class="q-pt-none">
          <StorehouseDataHistory :storehouse-history-data="storehouseHistoryData" />
        </q-card-section>
      </q-card>
    </Dialog>
    <Dialog
      :dialog="dialogTransferFromStorehouse"
      :persistent="true"
      :maximized="true"
    >
      <q-card>
        <q-bar>
          <q-space />
          <IconBtn
            v-if="isTransfer"
            flat
            dense
            icon="save"
            tooltip="Сохранить"
            color="positive"
            @icon-btn-click="saveTransfersData(faxSideData, storehouseSideData)"
          />
          <IconBtn
            flat
            dense
            icon="close"
            tooltip="Закрыть"
            @icon-btn-click="closeDialogTransferFromStorehouse"
          />
        </q-bar>
        <q-card-section>
          <q-splitter
            v-model="splitterModel"
          >
            <template #before>
              <div class="q-pa-md">
                <div class="text-h6 q-mb-md"> Факс</div>
                <!--                <Search v-model="search" />-->
                <CountCategories :list="faxSideData" style="margin-bottom: 20px;" />
                <q-list bordered separator>
                  <q-slide-item
                    v-for="(item, index) in faxSideData"
                    :key="index"
                    @left="onLeft(item)"
                    @action="onAction"
                  >
                    <template #left>
                      <div>На склад</div>
                    </template>

                    <q-item>
                      <q-item-section>{{ item.code_client_name }}</q-item-section>
                      <q-item-section>{{ item.place }}</q-item-section>
                      <q-item-section>{{ item.kg }}</q-item-section>
                      <q-item-section>{{ item.category_name }}</q-item-section>
                    </q-item>
                  </q-slide-item>
                </q-list>
              </div>
            </template>

            <template #separator>
              <q-avatar color="primary" text-color="white" size="40px" icon="drag_indicator" />
            </template>

            <template #after>
              <div class="q-pa-md">
                <div class="text-h6 q-mb-md"> Склад</div>
                <!--                <Search v-model="searchStorehouseData" />-->
                <CountCategories :list="storehouseSideData" style="margin-bottom: 20px;" />
                <q-list bordered separator>
                  <q-slide-item
                    v-for="(item, index) in storehouseSideData"
                    :key="index"
                    @right="onRight(item)"
                    @action="onAction"
                  >
                    <template #right>
                      <div>В факс</div>
                    </template>

                    <q-item>
                      <q-item-section>{{ item.code_client_name }}</q-item-section>
                      <q-item-section>{{ item.place }}</q-item-section>
                      <q-item-section>{{ item.kg }}</q-item-section>
                      <q-item-section>{{ item.category_name }}</q-item-section>
                    </q-item>
                  </q-slide-item>
                </q-list>
              </div>
            </template>
          </q-splitter>
        </q-card-section>
      </q-card>
    </Dialog>
    <Dialog
      :dialog="dialogHistory"
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
            @icon-btn-click="dialogHistory = false"
          />
        </q-bar>

        <q-card-section class="q-pt-none">
          <FaxTransferDataHistory
            :history-data="historyData"
          ></FaxTransferDataHistory>
        </q-card-section>
      </q-card>
    </Dialog>
    <q-dialog v-model="dialogSelectDeliveredPlace" persistent transition-show="scale" transition-hide="scale">
      <q-card style="width: 300px">
        <q-card-section>
          <div class="text-h6 text-teal">
            Доставлено
          </div>
        </q-card-section>

        <q-card-section class="q-pt-none">
          <BaseSelect
            v-model="deliveredPlace"
            outlined
            :options="[{label: 'Да', value: 1,}, {label: 'Нет', value: 0,}]"
          />
        </q-card-section>

        <q-card-actions align="right" class="bg-white text-teal">
          <q-btn
            v-close-popup
            label="Отмена"
            color="negative"
          />
          <q-btn
            v-close-popup
            color="positive"
            label="OK"
            @click="setDeliveredPlace(faxTableReactiveProperties.selected, deliveredPlace)"
          />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import { getUrl } from 'src/tools/url';
import { mapGetters } from 'vuex';
import showNotif from 'src/mixins/showNotif';
import ExportDataMixin from 'src/mixins/ExportData';
import { sortArrayCollection } from 'src/utils/sort';
import {
  getClientCodes,
  getCategories,
  getShopsList,
  setCategoriesStoreHouseData,
  combineStoreHouseData,
  getDeliveryMethodsList,
  // getStorehouseTableData,
  getFaxes,
  setFormatedDate,
  // prepareHistoryData,
} from 'src/utils/FrequentlyCalledFunctions';
import StorehouseDataMixin from 'src/mixins/StorehouseData';

export default {
  name: 'Fax',
  components: {
    Table: () => import('components/Elements/Table/Table.vue'),
    // Icon: () => import('components/Buttons/Icons/Icon.vue'),
    IconBtn: () => import('components/Buttons/IconBtn.vue'),
    // Badge: () => import('components/Elements/Badge.vue'),
    BaseBtn: () => import('components/Buttons/BaseBtn.vue'),
    DialogFaxData: () => import('components/Dialogs/DialogFaxData.vue'),
    StorehouseDataHistory: () => import('components/History/StorehouseDataHistory.vue'),
    Dialog: () => import('components/Dialogs/Dialog.vue'),
    CountCategories: () => import('components/CountCategories.vue'),
    PopupEdit: () => import('components/PopupEdit.vue'),
    SearchSelect: () => import('components/Elements/SearchSelect.vue'),
    BaseSelect: () => import('components/Elements/BaseSelect.vue'),
    FaxTransferDataHistory: () => import('components/History/FaxTransferDataHistory.vue'),
    // Search: () => import('components/Search.vue'),
  },
  filters: {
    optionsFilter(id, categories) {
      const find = _.find(categories, { value: id });
      if (find) {
        return find.label;
      }
      return id;
    },
  },
  mixins: [showNotif, ExportDataMixin, StorehouseDataMixin],
  data() {
    return {
      deliveredPlace: 1,
      dialogSelectDeliveredPlace: false,
      dialogHistory: false,
      historyData: [],
      isTransfer: false,
      addToSaveArray: [],
      search: null,
      searchStorehouseData: null,
      faxSideData: [],
      faxSideDataTransferIds: [],
      storehouseSideData: [],
      storehouseSideDataTransferIds: [],
      splitterModel: 50,
      showFaxDataDialog: false,
      dialogTransferFromStorehouse: false,
      localFaxEditData: {},
      combineTableData: true,
      faxTableData: [],
      faxTableProperties: {
        columns: [
          {
            name: 'code_place',
            label: 'Клиент',
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
            name: 'category_name',
            label: this.$t('category'),
            field: 'category_name',
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
            name: 'shop',
            label: this.$t('shop'),
            field: 'shop',
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
          {
            name: 'notation',
            label: this.$t('notation'),
            field: 'notation',
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
        ],
      },
      faxTableReactiveProperties: {
        selected: [],
        visibleColumns: ['code_client_name', 'place', 'kg', 'category_name', 'in_cargo', 'delivery_method_name', 'department'],
        title: '',
      },
      visibleColumns: ['code_client_name', 'place', 'kg', 'for_kg', 'for_place', 'in_cargo', 'category_name', 'delivery_method_name', 'department'],
      fullVisibleColumns: ['code_place', 'code_client_name', 'in_cargo', 'for_kg', 'for_place', 'place', 'kg', 'category_name', 'things', 'notation', 'shop', 'delivery_method_name', 'department'],
    };
  },
  computed: {
    ...mapGetters({
      faxData: 'faxes/getFaxData',
      faxCategoriesData: 'faxes/getFaxCategoriesData',
      storehouseData: 'storehouse/getStorehouseData',
      currentFaxItem: 'faxes/getCurrentFaxItem',
    }),
    categories() {
      return this.$store.getters['category/getCategories'];
    },
    clientCodes() {
      return this.$store.getters['codes/getCodes'];
    },
    faxName() {
      return _.get(this.currentFaxItem, 'name');
    },
    deliveryMethodsList() {
      return this.$store.getters['deliveryMethods/getDeliveryMethodsList'];
    },
  },
  watch: {
    faxData(val) {
      const { faxData } = this;
      if (this.combineTableData) {
        this.faxTableData = sortArrayCollection(combineStoreHouseData(faxData), 'code_client_name');
      } else {
        this.faxTableData = sortArrayCollection(_.cloneDeep(faxData), 'code_client_name');
      }
      this.$store.dispatch('faxes/setFaxCategoriesData', setCategoriesStoreHouseData(val));
    },
    combineTableData: {
      handler: function set(val) {
        devlog.log('combineTableData');
        const { faxData } = this;
        if (val) {
          this.faxTableReactiveProperties.visibleColumns = this.visibleColumns;
          this.faxTableData = sortArrayCollection(combineStoreHouseData(faxData), 'code_client_name');
        } else {
          this.faxTableData = sortArrayCollection(_.cloneDeep(faxData), 'code_client_name');
          this.faxTableReactiveProperties.visibleColumns = this.fullVisibleColumns;
        }
      },
      immediate: true,
    },
  },
  mounted() {
    this.$q.loading.show();
    Promise.all([this.getFax(this.$route.params.id), this.getFaxData(this.$route.params.id), getCategories(this.$store), getClientCodes(this.$store), getDeliveryMethodsList(this.$store)])
      .then(() => {
        this.$q.loading.hide();
      });
  },
  beforeDestroy() {
    clearTimeout(this.timer);
  },
  methods: {
    async getFax(id) {
      if (_.isEmpty(this.currentFaxItem)) {
        this.$axios.get(`${getUrl('fax')}/${id}`)
          .then(({ data: { fax } }) => {
            this.$store.dispatch('faxes/setCurrentFaxItem', fax);
          })
          .catch((errors) => {
            devlog.log('errors', errors);
          });
      }
    },
    saveDataInCombineTable(values) {
      this.$q.loading.show();
      this.$axios.post(getUrl('updateFaxCombineData'), values)
        .then(({ data: { updatedData } }) => {
          _.forEach(updatedData, (item) => {
            this.$store.dispatch('faxes/updateFaxData', item);
          });
          this.addToSaveArray = [];
          this.$q.loading.hide();
          this.showNotif('success', 'Запись успешно обновлена.', 'center');
        })
        .catch((errors) => {
          this.$q.loading.hide();
          devlog.log('errors', errors);
        });
    },
    addToAddSaveArray(val, key) {
      const findIndex = _.findIndex(this.addToSaveArray, { id: val.id });
      if (findIndex !== -1) {
        this.addToSaveArray[findIndex][key] = val[key];
      } else {
        const newObj = _.assign({}, {
          id: val.id,
          arr: val.arr,
          replacePrice: val.replacePrice,
        });
        newObj[key] = val[key];
        this.addToSaveArray.push(newObj);
      }
    },
    exportFaxData(data) {
      const ids = [];
      _.forEach(data, ({ arr, id }) => {
        if (!_.isEmpty(arr)) {
          ids.push(..._.map(arr, 'id'));
        } else {
          ids.push(id);
        }
      });
      this.exportDataToExcel(getUrl('exportFaxAdminData'), {
        id: this.currentFaxItem.id,
        ids,
      }, `${this.currentFaxItem.name}.xlsx`);
    },
    async getFaxData(id) {
      // this.$q.loading.show();
      await this.$axios.get(`${getUrl('faxData')}/${id}`)
        .then(({ data: { faxData } }) => {
          this.$store.dispatch('faxes/setFaxData', faxData);
          this.faxTableData = combineStoreHouseData(faxData);
          // this.$q.loading.hide();
        })
        .catch(() => {
          // this.$q.loading.hide();
          devlog.error('Ошибка получения данных факса');
        });
    },
    viewEditDialog(val) {
      if (!this.combineTableData) {
        this.$q.loading.show();
        this.localFaxEditData = val;
        this.localFaxEditData.combineTableData = this.combineTableData;
        this.localFaxEditData.historyFunc = this.getStorehouseDataHistory;
        this.faxTableReactiveProperties.selected = [];

        setTimeout(() => {
          val.selected = !val.selected;
        }, 100);

        Promise.all([getClientCodes(this.$store), getShopsList(this.$store), getCategories(this.$store)])
          .then(() => {
            this.showFaxDataDialog = true;
            this.$q.loading.hide();
          })
          .catch(() => {
            this.$q.loading.hide();
            devlog.warn('Ошибка при получении данных. Edit faxData');
          });
      }
    },
    openDialogTransferFromStorehouse() {
      this.dialogTransferFromStorehouse = true;
      this.$q.loading.show();
      Promise.all([this.$store.dispatch('storehouse/fetchStorehouseTableData'), getFaxes(this.$store)])
        .then(() => {
          devlog.log('Promise.all');
          // if (_.isEmpty(this.faxSideData)) {
          this.faxSideData = sortArrayCollection(_.cloneDeep(this.faxTableData), 'code_client_name');
          // }
          // if (_.isEmpty(this.storehouseSideData)) {
          // setTimeout(() => {
          this.storehouseSideData = sortArrayCollection(_.cloneDeep(this.storehouseData), 'code_client_name');
          // }, 100);
          // }
          this.dialogTransferFromStorehouse = true;
          this.$q.loading.hide();
        })
        .catch(() => {
          this.$q.loading.hide();
          devlog.warn('Ошибка при получении данных. openDialogTransferFromStorehouse');
        });
    },
    // Действие, когда пользователь закончил сдвиг элемента в лево, с факса в склад
    onLeft(item) {
      // this.faxSideDataTransferIds.push(item.id);
      // const indexId = _.findIndex(this.storehouseSideDataTransferIds, item.id);
      // if (indexId) {
      //     this.storehouseSideDataTransferIds.splice(indexId, 1);
      // }
      this.storehouseSideData.push(item);
      const index = _.findIndex(this.faxSideData, { id: item.id });
      this.faxSideData.splice(index, 1);
      this.$q.notify(`На склад ${item.code_client_name} код - (${item.place}м/${item.kg}кг)`);
      this.isTransfer = true;
    },
    // Действие, когда пользователь закончил сдвиг элемента в любую сторону
    onAction({ reset }) {
      reset();
    },
    // Действие, когда пользователь закончил сдвиг элемента в право
    onRight(item) {
      // this.storehouseSideDataTransferIds.push(item.id);
      // const indexId = _.findIndex(this.faxSideDataTransferIds, item.id);
      // if (indexId) {
      //     this.faxSideDataTransferIds.splice(indexId, 1);
      // }
      this.faxSideData.push(item);
      const index = _.findIndex(this.storehouseSideData, { id: item.id });
      this.storehouseSideData.splice(index, 1);
      this.$q.notify(`В факс ${item.code_client_name} код - (${item.place}м/${item.kg}кг)`);
      this.isTransfer = true;
    },
    saveTransfersData(faxData, storehouseData) {
      devlog.log(faxData, storehouseData);
      this.$q.loading.show();
      this.$axios.post(getUrl('transfersStoreFax'), {
        id: this.$route.params.id,
        faxIds: _.map(faxData, 'id'),
        storehouseIds: _.map(storehouseData, 'id'),
      })
        .then(({ data }) => {
          devlog.log('DATA', data);
          this.faxSideData = [];
          this.storehouseSideData = [];
          this.getFaxData(this.$route.params.id);
          this.$store.dispatch('storehouse/fetchStorehouseTableData');
          this.isTransfer = false;
          this.closeDialogTransferFromStorehouse();
          this.$q.loading.hide();
          this.showNotif('success', 'Данные успешно сохранены.', 'center');
        })
        .catch(() => {
          this.$q.loading.hide();
          devlog.error('Произошла ошибка в запросе - saveTransfersData');
        });
    },
    closeDialogTransferFromStorehouse() {
      if (this.isTransfer) {
        this.showNotif('warning', 'Сохранить изменения?', 'center', [
          {
            label: 'Нет',
            color: 'white',
            handler: () => {
              this.isTransfer = false;
              this.dialogTransferFromStorehouse = false;
            },
          },
          {
            label: 'Да',
            color: 'white',
            handler: () => {
              this.saveTransfersData(this.faxSideData, this.storehouseSideData);
            },
          },
        ]);
      } else {
        this.isTransfer = false;
        this.dialogTransferFromStorehouse = false;
      }
    },
    updatePricesInFax(faxId) {
      this.$q.loading.show();
      devlog.log('faxId', faxId);
      this.$axios.get(`${getUrl('updatePricesInFax')}/${faxId}`)
        .then(({ data: { faxData } }) => {
          this.$store.dispatch('faxes/setFaxData', faxData);
          this.faxTableData = combineStoreHouseData(faxData);
          this.$q.loading.hide();
          this.showNotif('success', 'Цены успешно обновлены.', 'center');
        })
        .catch(() => {
          this.$q.loading.hide();
          devlog.error('Ошибка получения данных факса');
        });
    },
    exportFaxDataOdessaKharkov() {
      const ids = [];
      _.forEach(this.faxTableReactiveProperties.selected, ({ arr }) => {
        ids.push(..._.map(arr, 'id'));
      });
      this.exportDataToExcel(getUrl('exportFaxDataOdessaKharkov'), {
        id: this.currentFaxItem.id,
        ids,
      }, `Одесса-Харьков ${this.currentFaxItem.name}.xlsx`);
    },
    exportFaxDataOdessa() {
      const ids = [];
      _.forEach(this.faxTableReactiveProperties.selected, ({ arr }) => {
        ids.push(..._.map(arr, 'id'));
      });
      this.exportDataToExcel(getUrl('exportFaxDataOdessa'), {
        id: this.currentFaxItem.id,
        ids,
      }, `Одесса ${this.currentFaxItem.name}.xlsx`);
    },
    destroyEntry(data) {
      if (!_.isEmpty(data)) {
        const ids = [];
        _.forEach(data, (item) => {
          if (item.arr) {
            ids.push(..._.map(item.arr, 'id'));
          } else {
            ids.push(item.id);
          }
        });
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
            handler: () => {
              this.$q.loading.show();
              this.$axios.post(getUrl('destroyStorehouseData'), { ids })
                .then(({ data: { status } }) => {
                  devlog.log('status', status);
                  this.$store.dispatch('faxes/deleteEntryFromFaxData', ids);
                  // this.$store.dispatch('storehouse/setStorehouseCategoriesData', setCategoriesStoreHouseData(this.storehouseData));
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
    getFaxTransfersDataHistory(fax) {
      this.$q.loading.show();
      this.$axios.post(getUrl('getFaxDataHistory'), { faxId: fax.id })
        .then(({ data: { historyData } }) => {
          this.$q.loading.hide();
          if (!_.isEmpty(historyData)) {
            this.dialogHistory = true;
            this.historyData = historyData;
          }
        });
    },
    setDeliveredPlace(data, flag) {
      this.$q.loading.show();
      const ids = [];
      _.forEach(data, (item) => {
        if (item.arr) {
          ids.push(..._.map(item.arr, 'id'));
        } else {
          ids.push(item.id);
        }
      });
      devlog.log('ids', ids);
      this.$axios.post(getUrl('setDeliveredFaxData'), {
        ids,
        flag,
      })
        .then(({ data: { storehouseData } }) => {
          const formatedData = setFormatedDate(storehouseData, ['created_at']);
          _.forEach(formatedData, (item) => {
            this.$store.dispatch('storehouse/updateStorehouseData', item);
            this.$store.dispatch('faxes/updateFaxData', item);
          });
          this.faxTableReactiveProperties.selected = [];
          this.$q.loading.hide();
          this.showNotif('success', 'Данные успешно обновлены.', 'center');
        })
        .catch(() => {
          this.faxTableReactiveProperties.selected = [];
          this.$q.loading.hide();
          this.showNotif('error', 'Произошла ошибка при обновлении данных.', 'center');
        });
    },
  },
};
</script>
