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
        <template v-slot:top-buttons>
          <DialogAddEntryOnStorehouse
            :show-dialog.sync="showAddEntryOnStorehouseDialog"
            :entry-data.sync="localStorehouseEditData"
          />
          <Menu />

          <IconBtn
            color="primary"
            icon="sync"
            tooltip="Обновить"
            @iconBtnClick="refresh"
          />

          <IconBtn
            v-show="storehouseTableReactiveProperties.selected.length"
            color="negative"
            icon="delete"
            :tooltip="$t('delete')"
            @iconBtnClick="destroyEntry(storehouseTableReactiveProperties.selected)"
          />

          <IconBtn
            v-show="storehouseTableReactiveProperties.selected.length"
            color="info"
            icon="move_to_inbox"
            tooltip="Переместить"
          />

          <IconBtn
            color="positive"
            icon="explicit"
            :tooltip="$t('excel')"
            @iconBtnClick="exportStorehouseData"
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
              header-class="bg-secondary text-white"
              style="border-radius: 30px;border: 1px solid #26A69A;"
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
                    {{ props.row.code_client_name }}
                  </ItemLabel>
                </ItemSection>
              </template>

              <List
                separator
                dense
                @clickList="viewUpdateDialog(props)"
              >
                <ListItem
                  v-for="col in props.cols.filter(col => col.name !== 'desc')"
                  :key="col.name"
                >
                  <ItemSection>
                    <ItemLabel>{{ `${col.label}:` }}</ItemLabel>
                  </ItemSection>
                  <ItemSection side>
                    <ItemLabel
                      v-if="col.field === 'things'"
                      :lines="10"
                    >
                      {{ col.value | thingsFilter }}
                    </ItemLabel>
                    <ItemLabel
                      v-else-if="col.field === 'kg'"
                    >
                      {{ col.value | numberFormatFilter }}
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
                      @clickBaseBtn="getStorehouseDataHistory(props.row.id, props.cols)"
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
            :class="{table__tr_bold_text: props.row.brand }"
            class="cursor-pointer"
            @click.stop="viewUpdateDialog(props, $event)"
          >
            <q-td
              auto-width
              class="select_checkbox"
            >
              <CheckBox v-model="props.selected" />
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
              {{ props.row.kg | numberFormatFilter }}
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
              {{ props.row.things | thingsFilter }}
            </q-td>
          </q-tr>
        </template>
      </Table>

      <div class="text-center text-bold text-uppercase q-mt-lg">Категории</div>
      <List
        separator
        bordered
        dense
        style="max-width: 450px;margin: 20px auto;"
      >
        <ListItem class="text-bold">
          <ItemSection>
            <ItemLabel>Категория</ItemLabel>
          </ItemSection>
          <ItemSection>
            <ItemLabel>Мест</ItemLabel>
          </ItemSection>
          <ItemSection>
            <ItemLabel>Вес</ItemLabel>
          </ItemSection>
        </ListItem>

        <ListItem
          v-for="({name, kg, place}, index) in storehouseCategoriesData"
          :key="index"
        >
          <ItemSection>
            <ItemLabel>{{ name }}</ItemLabel>
          </ItemSection>
          <ItemSection>
            <ItemLabel v-if="name">{{ place | numberFormatFilter }}</ItemLabel>
            <ItemLabel v-else>
              <Badge>{{ place | numberFormatFilter }}</Badge>
            </ItemLabel>
          </ItemSection>
          <ItemSection>
            <ItemLabel v-if="name">{{ kg | numberFormatFilter }}</ItemLabel>
            <ItemLabel v-else>
              <Badge>{{ kg | numberFormatFilter }}</Badge>
            </ItemLabel>
          </ItemSection>
        </ListItem>
      </List>
      <Dialog
        :dialog="dialogHistory"
        :persistent="true"
        :maximized="true"
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
            <StorehouseDataHistory :storehouse-history-data="storehouseHistoryData" />
          </CardSection>
        </Card>
      </Dialog>
    </PullRefresh>
  </q-page>
</template>

<script>
    import { getUrl } from 'src/tools/url';
    import showNotif from 'src/mixins/showNotif';
    import ExportDataMixin from 'src/mixins/ExportData';
    import { sortCollection } from 'src/utils/sort';
    import { isoDate, toDate, formatToMysql } from 'src/utils/formatDate';
    import { callFunction } from 'src/utils/index';
    import {
        getClientCodes,
        getCategories,
        getShopsList,
        setCategoriesStoreHouseData,
        setFormatedDate,
        getStorehouseTableData,
    } from 'src/utils/FrequentlyCalledFunctions';
    import { max } from 'date-fns';
    import StorehouseDataMixin from 'src/mixins/StorehouseData';

    export default {
        name: 'Storehouse',
        components: {
            Table: () => import('src/components/Elements/Table/Table.vue'),
            Dialog: () => import('src/components/Dialogs/Dialog.vue'),
            DialogAddEntryOnStorehouse: () => import('src/components/Dialogs/DialogAddEntryOnStorehouse.vue'),
            IconBtn: () => import('src/components/Buttons/IconBtn.vue'),
            Menu: () => import('src/components/Menu.vue'),
            ItemSection: () => import('src/components/Elements/List/ItemSection.vue'),
            ItemLabel: () => import('src/components/Elements/List/ItemLabel.vue'),
            ListItem: () => import('src/components/Elements/List/ListItem.vue'),
            BaseBtn: () => import('src/components/Buttons/BaseBtn.vue'),
            List: () => import('src/components/Elements/List/List.vue'),
            CheckBox: () => import('src/components/Elements/CheckBox.vue'),
            Badge: () => import('src/components/Elements/Badge.vue'),
            StorehouseDataHistory: () => import('src/components/History/StorehouseDataHistory.vue'),
            Card: () => import('src/components/Elements/Card/Card.vue'),
            CardSection: () => import('src/components/Elements/Card/CardSection.vue'),
            PullRefresh: () => import('src/components/PullRefresh.vue'),
        },
        mixins: [showNotif, ExportDataMixin, StorehouseDataMixin],
        data() {
            return {
                localStorehouseEditData: {},
                showAddEntryOnStorehouseDialog: false,
                storehouseTableProperties: {
                    title: this.$t('storehouse'),
                    viewBody: true,
                    viewTop: true,
                    hideBottom: false,
                    columns: [
                        {
                            name: 'code_place',
                            label: this.$t('code'),
                            align: 'center',
                            field: 'code_place',
                            sortable: true,
                        },
                        {
                            name: 'code_client_name',
                            label: this.$t('client'),
                            field: 'code_client_name',
                            align: 'center',
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
                            name: 'category_name',
                            label: this.$t('category'),
                            field: 'category_name',
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
                            name: 'created_at',
                            label: 'Добавлено',
                            field: 'created_at',
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
                storehouseTableReactiveProperties: {
                    selected: [],
                    visibleColumns: ['code_client_name', 'place', 'kg', 'category_name', 'code_place', 'notation', 'shop', 'things', 'created_at'],
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
        watch: {
            storehouseData(val) {
                this.$store.dispatch('storehouse/setStorehouseCategoriesData', setCategoriesStoreHouseData(val));
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
                                this.$axios.post(getUrl('destroyStorehouseData'), { ids })
                                  .then(({ data: { status } }) => {
                                      devlog.log('status', status);
                                      this.$store.dispatch('storehouse/destroyStorehouseData', ids);
                                      this.$store.dispatch('storehouse/setStorehouseCategoriesData', setCategoriesStoreHouseData(this.storehouseData));
                                      this.storehouseTableReactiveProperties.selected = [];
                                      this.showNotif('success', _.size(ids) > 1 ? 'Записи успешно удалены.' : 'Запись успешно удалена.', 'center');
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
                const { storehouseData } = this;
                // devlog.log('formatToMysql', max(_.map(storehouseData, (item) => new Date(toDate(item.created_at)))));
                await this.$axios.post(getUrl('getNewStorehouseData'), {
                    created_at: isoDate(max(_.map(storehouseData, (item) => new Date(toDate(item.created_at))))),
                    updated_at: formatToMysql(max(_.map(storehouseData, (item) => new Date(item.updated_at)))),
                })
                  .then(({ data: { newData } }) => {
                      devlog.log('DTA', newData);
                      if (!_.isEmpty(newData)) {
                          const createdItems = [];
                          _.forEach(newData, (item) => {
                              const find = _.some(storehouseData, ['id', item.id]);
                              if (find) {
                                  this.$store.dispatch('storehouse/updateStorehouseData', setFormatedDate(item, ['created_at']));
                              } else {
                                  createdItems.push(item);
                              }
                          });

                          if (!_.isEmpty(createdItems)) {
                              _.forEach(sortCollection(createdItems, 'id'), (elem) => {
                                  this.$store.dispatch('storehouse/addToStorehouseData', setFormatedDate(elem, ['created_at']));
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
        },
    };
</script>
