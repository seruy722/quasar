<template>
  <q-page
    data-vue-component-name="Storehouse"
  >
    <PullRefresh @refresh="refresh">
      <Table
        :table-properties="storehouseTableProperties"
        :table-data="storehouseData"
        :table-reactive-properties="storehouseTableReactiveProperties"
        title="Склад"
      >
        <template #top-buttons>
          <DialogAddEntryOnStorehouse
            v-model:show-dialog="showAddEntryOnStorehouseDialog"
            v-model:entry-data="localStorehouseEditData"
          />
          <Menu />

          <UpdateBtn
            @update-btn-click="refresh"
          />

          <IconBtn
            v-show="storehouseTableReactiveProperties.selected.length"
            color="negative"
            icon="delete"
            :tooltip="$t('delete')"
            @icon-btn-click="destroyEntry(storehouseTableReactiveProperties.selected)"
          />

          <MoveToFaxBtn
            v-show="storehouseTableReactiveProperties.selected.length"
            @move-to-fax-click="moveToFax"
          />

          <IconBtn
            color="positive"
            icon="explicit"
            :tooltip="$t('excel')"
            @icon-btn-click="exportStorehouseData"
          />
          <IconBtn
            color="primary"
            icon="info"
            tooltip="Инфо по складу"
            @icon-btn-click="dialogStorehouseInfo = true"
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
                <q-item-section>
                  <q-item-label>
                    {{ props.row.kg }}
                  </q-item-label>
                </q-item-section>
                <q-item-section>
                  <q-item-label>
                    {{ props.row.category_name }}
                  </q-item-label>
                </q-item-section>
              </template>

              <q-list
                separator
                dense
                @click="viewUpdateDialog(props)"
              >
                <q-item
                  v-for="col in props.cols.filter(col => col.name !== 'desc')"
                  :key="col.name"
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
            :class="{table__tr_bold_text: props.row.brand }"
            class="cursor-pointer"
            @click.stop="viewUpdateDialog(props, $event)"
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
              key="place"
              :props="props"
            >
              {{ props.row.place }}
            </q-td>

            <q-td
              key="kg"
              :props="props"
            >
              {{ numberFormat(props.row.kg) }}
            </q-td>

            <q-td
              key="cube"
              :props="props"
            >
              {{ props.row.cube }}
            </q-td>

            <q-td
              key="category_name"
              :props="props"
            >
              {{ props.row.category_name }}
            </q-td>

            <q-td
              key="shop"
              :props="props"
            >
              {{ props.row.shop }}
            </q-td>

            <q-td
              key="created_at"
              :props="props"
            >
              {{ props.row.created_at }}
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
              {{ thingsFilter(props.row.things) }}
            </q-td>
          </q-tr>
        </template>
      </Table>

      <CountCategories
        :list="storehouseData"
        style="max-width: 500px;margin:0 auto;"
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
    </PullRefresh>
    <DialogMoveToFax
      v-model:show="showMoveToFaxDialog"
      v-model:values="storehouseTableReactiveProperties.selected"
    />
    <Dialog
      :dialog="dialogStorehouseInfo"
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
            color="negative"
            @icon-btn-click="dialogStorehouseInfo = false"
          />
        </q-bar>

        <q-card-section class="q-pt-none">
          <StorehouseInfo />
        </q-card-section>
      </q-card>
    </Dialog>
  </q-page>
</template>

<script>
import { getUrl } from 'src/tools/url';
import showNotif from 'src/mixins/showNotif';
import ExportDataMixin from 'src/mixins/ExportData';
import { callFunction, thingsFilter, numberFormat } from 'src/utils';
import {
  getClientCodes,
  getCategories,
  getShopsList,
  getStorehouseTableData,
} from 'src/utils/FrequentlyCalledFunctions';
import StorehouseDataMixin from 'src/mixins/StorehouseData';
import Table from 'src/components/Elements/Table/Table.vue';
import Dialog from 'src/components/Dialogs/Dialog.vue';
import DialogAddEntryOnStorehouse from 'src/components/Dialogs/DialogAddEntryOnStorehouse.vue';
import IconBtn from 'src/components/Buttons/IconBtn.vue';
import Menu from 'src/components/Menu.vue';
import BaseBtn from 'src/components/Buttons/BaseBtn.vue';
import StorehouseDataHistory from 'src/components/History/StorehouseDataHistory.vue';
import PullRefresh from 'src/components/PullRefresh.vue';
import CountCategories from 'src/components/CountCategories.vue';
import DialogMoveToFax from 'src/components/Dialogs/DialogMoveToFax.vue';
import MoveToFaxBtn from 'src/components/Buttons/MoveToFaxBtn.vue';
import UpdateBtn from 'src/components/Buttons/UpdateBtn.vue';
import StorehouseInfo from 'src/components/Storehouse/StorehouseInfo.vue';

export default {
  name: 'Storehouse',
  components: {
    Table,
    Dialog,
    DialogAddEntryOnStorehouse,
    IconBtn,
    Menu,
    BaseBtn,
    StorehouseDataHistory,
    PullRefresh,
    CountCategories,
    DialogMoveToFax,
    MoveToFaxBtn,
    UpdateBtn,
    StorehouseInfo,
  },
  mixins: [showNotif, ExportDataMixin, StorehouseDataMixin],
  data() {
    return {
      dialogStorehouseInfo: false,
      showMoveToFaxDialog: false,
      localStorehouseEditData: {},
      showAddEntryOnStorehouseDialog: false,
      storehouseTableProperties: {
        title: 'Склад',
        viewBody: true,
        viewTop: true,
        hideBottom: false,
        columns: [
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
            field: 'code_client_name',
            align: 'center',
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
            name: 'cube',
            label: 'Куб',
            field: 'cube',
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
            name: 'shop',
            label: 'Магазин',
            field: 'shop',
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
            name: 'notation',
            label: 'Примечания',
            field: 'notation',
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
        ],
      },
      storehouseTableReactiveProperties: {
        selected: [],
        visibleColumns: ['code_client_name', 'place', 'kg', 'category_name', 'code_place', 'notation', 'shop', 'things', 'created_at', 'cube'],
      },
    };
  },
  computed: {
    storehouseData() {
      return this.$store.getters['storehouse/getStorehouseData'];
    },
    storehouseCategoriesData() {
      return this.$store.getters['storehouse/getStorehouseCategoriesData'];
    },
  },
  mounted() {
    this.$q.loading.show();
    Promise.all([getStorehouseTableData(this.$store)])
      .then(() => {
        this.$q.loading.hide();
      });
  },
  methods: {
    numberFormat,
    thingsFilter,
    moveToFax() {
      this.showMoveToFaxDialog = true;
    },
    viewUpdateDialog(val, event) {
      if (!_.includes(_.get(event, 'target.classList'), 'select_checkbox')) {
        this.localStorehouseEditData = val;
        this.localStorehouseEditData.historyFunc = this.getStorehouseDataHistory;
        this.storehouseTableReactiveProperties.selected = [];

        setTimeout(() => {
          val.selected = !val.selected;
        }, 100);

        this.$q.loading.show();
        Promise.all([getClientCodes(this.$store), getShopsList(this.$store), getCategories(this.$store)])
          .then(() => {
            this.showAddEntryOnStorehouseDialog = true;
            this.$q.loading.hide();
          })
          .catch(() => {
            this.$q.loading.hide();
            devlog.warn('Ошибка при получении данных. Edit storehouseData');
          });
      }
    },
    destroyEntry(data) {
      if (!_.isEmpty(data)) {
        const ids = _.map(data, 'id');
        this.showNotif('warning', _.size(ids) > 1 ? 'Удалить записи?' : 'Удалить запись?', 'center', [
          {
            label: 'Отмена',
            color: 'white',
            handler: () => {
              this.storehouseTableReactiveProperties.selected = [];
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
                  this.$store.dispatch('storehouse/destroyStorehouseData', ids);
                  // this.$store.dispatch('storehouse/setStorehouseCategoriesData', setCategoriesStoreHouseData(this.storehouseData));
                  this.storehouseTableReactiveProperties.selected = [];
                  this.$q.loading.hide();
                  this.showNotif('success', _.size(ids) > 1 ? 'Записи успешно удалены.' : 'Запись успешно удалена.', false);
                })
                .catch(() => {
                  devlog.error('Ошибка запроса - destroyStorehouseData');
                });
            },
          },
        ]);
      }
    },
    exportStorehouseData() {
      if (!_.isEmpty(this.storehouseData)) {
        this.exportDataToExcel(getUrl('exportStorehouseData'), {
          ids: _.map(this.storehouseTableReactiveProperties.selected, 'id'),
        }, 'На складе.xlsx');
      }
    },
    async refresh(done) {
      if (!done) {
        this.$q.loading.show();
      }
      this.$store.dispatch('storehouse/fetchStorehouseTableData')
        .then(() => {
          callFunction(done);
          this.$q.loading.hide();
          this.showNotif('success', 'Данные успешно обновлены.', false);
        })
        .catch(() => {
          this.$q.loading.hide();
          callFunction(done);
        });
    },
  },
};
</script>
