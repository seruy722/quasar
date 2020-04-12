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
      <template v-slot:top-buttons>
        <IconBtn
          v-show="addToSaveArray.length"
          color="positive"
          icon="save"
          tooltip="Сохранить"
          @iconBtnClick="saveDataInCombineTable(addToSaveArray)"
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
          @iconBtnClick="exportFaxData"
        />

        <IconBtn
          v-show="!combineTableData"
          icon="sync_alt"
          tooltip="Трансфер данных"
          @iconBtnClick="openDialogTransferFromStorehouse"
        />

        <IconBtn
          icon="data_usage"
          color="orange"
          tooltip="Обновить цены"
          @iconBtnClick="updatePricesInFax(currentFaxItem.id)"
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
                <q-checkbox
                  v-model="props.selected"
                  dense
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
            >
              <ListItem
                v-for="col in props.cols.filter(col => col.name !== 'desc')"
                :key="col.name"
                @clickList="viewEditDialog(props)"
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
              @addToSave="addToAddSaveArray(props.row, 'code_client_id')"
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
              @addToSave="addToAddSaveArray(props.row, 'for_kg')"
            >
              <q-input
                v-model.number="props.row.for_kg"
                type="number"
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
              @addToSave="addToAddSaveArray(props.row, 'for_place')"
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
              @addToSave="addToAddSaveArray(props.row, 'category_id')"
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
            key="shop"
            :props="props"
          >
            {{ props.row.shop }}
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
    <Dialog
      :dialog="dialogTransferFromStorehouse"
      :persistent="true"
      :maximized="true"
    >
      <Card>
        <q-bar>
          <q-space />
          <IconBtn
            flat
            dense
            icon="save"
            tooltip="Сохранить"
            color="positive"
            @iconBtnClick="saveTransfersData(faxSideData, storehouseSideData)"
          />
          <IconBtn
            flat
            dense
            icon="close"
            tooltip="Закрыть"
            @iconBtnClick="dialogTransferFromStorehouse = false"
          />
        </q-bar>
        <CardSection>
          <q-splitter
            v-model="splitterModel"
          >
            <template v-slot:before>
              <div class="q-pa-md">
                <div class="text-h6 q-mb-md"> Факс</div>
                <!--                <Search v-model="search" />-->
                <CountCategories :list="faxSideData" />
                <q-list bordered separator>

                  <q-slide-item
                    v-for="(item, index) in faxSideData"
                    :key="index"
                    @left="onLeft(item)"
                    @action="onAction"
                  >
                    <template v-slot:left>
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

            <template v-slot:separator>
              <q-avatar color="primary" text-color="white" size="40px" icon="drag_indicator" />
            </template>

            <template v-slot:after>
              <div class="q-pa-md">
                <div class="text-h6 q-mb-md"> Склад</div>
                <!--                <Search v-model="searchStorehouseData" />-->
                <CountCategories :list="storehouseSideData" />
                <q-list bordered separator>
                  <q-slide-item
                    v-for="(item, index) in storehouseSideData"
                    :key="index"
                    @right="onRight(item)"
                    @action="onAction"
                  >
                    <template v-slot:right>
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
        </CardSection>
      </Card>
    </Dialog>
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
        getStorehouseTableData,
        getFaxes,
        // setFormatedDate,
        // prepareHistoryData,
    } from 'src/utils/FrequentlyCalledFunctions';
    import StorehouseDataMixin from 'src/mixins/StorehouseData';

    export default {
        name: 'Fax',
        components: {
            Table: () => import('src/components/Elements/Table/Table.vue'),
            // Icon: () => import('src/components/Buttons/Icons/Icon.vue'),
            IconBtn: () => import('src/components/Buttons/IconBtn.vue'),
            List: () => import('src/components/Elements/List/List.vue'),
            ItemSection: () => import('src/components/Elements/List/ItemSection.vue'),
            ItemLabel: () => import('src/components/Elements/List/ItemLabel.vue'),
            ListItem: () => import('src/components/Elements/List/ListItem.vue'),
            // Badge: () => import('src/components/Elements/Badge.vue'),
            BaseBtn: () => import('src/components/Buttons/BaseBtn.vue'),
            DialogFaxData: () => import('src/components/Dialogs/DialogFaxData.vue'),
            StorehouseDataHistory: () => import('src/components/History/StorehouseDataHistory.vue'),
            Card: () => import('src/components/Elements/Card/Card.vue'),
            CardSection: () => import('src/components/Elements/Card/CardSection.vue'),
            Dialog: () => import('src/components/Dialogs/Dialog.vue'),
            CountCategories: () => import('src/components/CountCategories.vue'),
            PopupEdit: () => import('src/components/PopupEdit.vue'),
            SearchSelect: () => import('src/components/Elements/SearchSelect.vue'),
            // Search: () => import('src/components/Search.vue'),
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
                            name: 'shop',
                            label: this.$t('shop'),
                            field: 'shop',
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
                    visibleColumns: ['code_client_name', 'place', 'kg', 'category_name'],
                    title: '',
                },
                visibleColumns: ['code_client_name', 'place', 'kg', 'for_kg', 'for_place', 'category_name'],
                fullVisibleColumns: ['code_place', 'code_client_name', 'for_kg', 'for_place', 'place', 'kg', 'category_name', 'things', 'notation', 'shop'],
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
            // storehouseData() {
            //     return this.$store.getters['storehouse/getStorehouseData'];
            // },
            // fax() {
            //     return _.find(this.$store.getters['faxes/getFaxes'], { id: this.$route.params.id });
            // },
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
            search(val) {
                if (_.trim(val)) {
                    this.faxSideData = this.searchInList(val, 'faxSideData');
                } else {
                    this.faxSideData = _.filter(_.uniqBy([...this.faxSideData, ...this.faxTableData], 'id'), ({ id }) => !_.includes(this.faxSideDataTransferIds, id));
                }
            },
            searchStorehouseData(val) {
                if (_.trim(val)) {
                    this.storehouseSideData = this.searchInList(val, 'storehouseSideData');
                } else {
                    this.storehouseSideData = _.filter(_.uniqBy([...this.storehouseData, ...this.storehouseSideData], 'id'), ({ id }) => !_.includes(this.storehouseSideDataTransferIds, id));
                }
            },
        },
        mounted() {
            this.$q.loading.show();
            Promise.all([this.getFax(this.$route.params.id), this.getFaxData(this.$route.params.id), getCategories(this.$store), getClientCodes(this.$store)])
              .then(() => {
                  this.$q.loading.hide();
              });
            // this.getFaxData(this.$route.params.id);
            // getCategories(this.$store);
            // getClientCodes(this.$store);
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
            exportFaxData() {
                this.exportDataToExcel(getUrl('exportFaxAdminData'), {
                    id: 1,
                }, `${this.currentFaxItem.name}.xlsx`);
            },
            searchInList(val, arrayName) {
                const needle = val.toLowerCase();
                devlog.log('this[arrayName]', this[arrayName]);
                return _.filter(this[arrayName], (v) => _.includes(_.toLower(v.category_name), needle)
                  || _.includes(_.toLower(v.code_client_name), needle));
                // return this[arrayName].filter((v) => v.category_name.toLowerCase()
                //   .indexOf(needle) > -1 || v.code_client_name.toLowerCase()
                //   .indexOf(needle) > -1);
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
            // exportFaxData({ id, name, transporter: { id: transporterID } }) {
            //     this.exportDataToExcel(getUrl('exportFaxData'), {
            //         faxID: id,
            //         transporterID,
            //     }, `${name}.xlsx`);
            // },
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
                this.$q.loading.show();
                Promise.all([getStorehouseTableData(this.$store), getFaxes(this.$store)])
                  .then(() => {
                      if (_.isEmpty(this.faxSideData)) {
                          this.faxSideData = sortArrayCollection(this.faxTableData, 'code_client_name');
                      }
                      if (_.isEmpty(this.storehouseSideData)) {
                          setTimeout(() => {
                              this.storehouseSideData = sortArrayCollection(_.cloneDeep(this.storehouseData), 'code_client_name');
                          }, 100);
                      }
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
                this.$q.notify('Left action triggered. Resetting in 1 second.');
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
                this.$q.notify('Right action triggered. Resetting in 1 second.');
            },
            saveTransfersData(faxData, storehouseData) {
                this.$axios.post(getUrl('transfersStoreFax'), {
                    id: this.$route.params.id,
                    faxIds: _.map(faxData, 'id'),
                    storehouseIds: _.map(storehouseData, 'id'),
                })
                  .then(({ data }) => {
                      devlog.log('DATA', data);
                      this.getFaxData(this.$route.params.id);
                      this.dialogTransferFromStorehouse = false;
                      getStorehouseTableData(this.$store);
                      this.faxSideData = [];
                      this.storehouseSideData = [];
                  })
                  .catch(() => {
                      devlog.error('Произошла ошибка в запросе - saveTransfersData');
                  });
            },
            updatePricesInFax(faxId) {
                this.$q.loading.show();
                devlog.log('faxId', faxId);
                this.$axios.get(`${getUrl('updatePricesInFax')}/${faxId}`)
                  .then(({ data: { faxData } }) => {
                      this.$store.dispatch('faxes/setFaxData', faxData);
                      this.$q.loading.hide();
                      this.showNotif('success', 'Цены успешно обновлены.', 'center');
                  })
                  .catch(() => {
                      this.$q.loading.hide();
                      devlog.error('Ошибка получения данных факса');
                  });
            },
        },
    };
</script>
