<template>
  <div
    data-vue-component-name="Faxes"
  >
    <Table
      :table-properties="faxesTableProperties"
      :table-data="faxes"
      :table-reactive-properties="faxesTableReactiveProperties"
      title="Факсы"
    >
      <template v-slot:top-buttons>
        <IconBtn
          v-show="faxesTableReactiveProperties.selected.length"
          color="negative"
          icon="delete"
          :tooltip="$t('delete')"
          class="q-ml-md"
          @iconBtnClick="deleteFaxItems"
        />

        <IconBtn
          color="primary"
          icon="sync"
          tooltip="Обновить"
          @iconBtnClick="refresh"
        />

        <!--        <IconBtn-->
        <!--          v-show="faxesTableReactiveProperties.selected.length"-->
        <!--          icon="swap_vert"-->
        <!--          tooltip=""-->
        <!--          class="q-ml-md"-->
        <!--          @iconBtnClick="deleteFaxItems"-->
        <!--        />-->
        <Menu :items="['Факс', 'Перевожчика']" />
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
                <ItemLabel :lines="3">
                  {{ props.row.name }}
                </ItemLabel>
              </ItemSection>
            </template>

            <List
              separator
              dense
              @clickList="viewEditDialog(props)"
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
                    v-if="col.field === 'status'"
                  >
                    {{ col.value | statusFilter }}
                  </ItemLabel>

                  <ItemLabel
                    v-else-if="col.field === 'notation'"
                    :lines="4"
                  >
                    {{ col.value }}
                  </ItemLabel>
                  <ItemLabel v-else>
                    {{ col.value }}
                  </ItemLabel>
                </ItemSection>
              </ListItem>
              <ListItem>
                <ItemSection>
                  <div class="row justify-around">
                    <BaseBtn
                      label="История"
                      color="info"
                      style="max-width: 100px;"
                      @clickBaseBtn="getStorehouseDataHistory(props.row.id, props.cols)"
                    />
                    <BaseBtn
                      label="Перейти"
                      color="info"
                      style="max-width: 100px;"
                      @clickBaseBtn="goToFaxData(props.row)"
                    />
                  </div>
                </ItemSection>
              </ListItem>
            </List>
          </q-expansion-item>
        </div>
      </template>
      <template v-slot:inner-body="{props}">
        <q-tr
          :props="props"
          :class="[props.row.uploaded_to_cargo ? 'table__tr_green_bg' : 'table__tr_red_bg']"
          class="cursor-pointer"
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
            key="name"
            :props="props"
            class="fax_name"
            @click.stop="goToFaxData(props.row)"
          >
            {{ props.row.name }}
          </q-td>

          <q-td
            key="status"
            :props="props"
          >
            <Badge>
              {{ props.row.status | statusFilter }}
            </Badge>
          </q-td>

          <q-td
            key="transporter_name"
            :props="props"
          >
            {{ props.row.transporter_name }}
          </q-td>

          <q-td
            key="transport_name"
            :props="props"
          >
            {{ props.row.transport_name }}
          </q-td>

          <q-td
            key="departure_date"
            :props="props"
          >
            {{ props.row.departure_date }}
          </q-td>

          <q-td
            key="arrival_date"
            :props="props"
          >
            {{ props.row.arrival_date }}
          </q-td>

          <q-td
            key="user_name"
            :props="props"
          >
            {{ props.row.user_name }}
          </q-td>

          <q-td
            key="notation"
            :props="props"
          >
            {{ props.row.notation }}
          </q-td>

          <q-td
            key="up_to_cargo"
            :props="props"
            class="upload_to_cargo"
            @click.stop="uploadToCargo(props.row)"
          >
            <q-icon
              v-if="!props.row.uploaded_to_cargo"
              dense
              name="vertical_align_bottom"
              @click.stop="uploadToCargo(props.row)"
            ></q-icon>
            <q-icon
              v-else
              dense
              name="vertical_align_top"
              @click.stop="uploadToCargo(props.row)"
            ></q-icon>
          </q-td>
        </q-tr>
      </template>
    </Table>

    <List
      separator
      bordered
      dense
      style="max-width: 450px;margin: 20px auto;"
    >
      <q-expansion-item
        v-for="({status, count, data}, index) in faxesStatusData"
        :key="index"
        expand-separator
        class="shadow-1 overflow-hidden"
      >
        <template v-slot:header>
          <ItemSection>
            <ItemLabel>{{ status }}</ItemLabel>
          </ItemSection>
          <ItemSection>
            <ItemLabel>{{ count | numberFormatFilter }}</ItemLabel>
          </ItemSection>
        </template>

        <List
          separator
          bordered
          dense
        >
          <ListItem
            v-for="(elem, i) in data"
            :key="i"
          >
            <ItemSection>
              <ItemLabel>{{ elem.name }}</ItemLabel>
            </ItemSection>
            <ItemSection>
              <ItemLabel>{{ elem.transport_name }}</ItemLabel>
            </ItemSection>
          </ListItem>
        </List>
      </q-expansion-item>
    </List>
    <DialogAddFax
      :show-dialog.sync="showFaxDialog"
      :entry-data.sync="localFaxesEditData"
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
          <FaxesHistory :fax-history-data="faxHistoryData" />
        </CardSection>
      </Card>
    </Dialog>
  </div>
</template>

<script>
    import { getUrl } from 'src/tools/url';
    import getFromSettings from 'src/tools/settings';
    import { setFormatedDate, prepareHistoryData, getFaxes } from 'src/utils/FrequentlyCalledFunctions';
    import { sortCollection } from 'src/utils/sort';
    import { isoDate, toDate, formatToMysql } from 'src/utils/formatDate';
    import { max } from 'date-fns';
    import { callFunction } from 'src/utils/index';
    import showNotif from 'src/mixins/showNotif';

    export default {
        name: 'Faxes',
        components: {
            Table: () => import('src/components/Elements/Table/Table.vue'),
            List: () => import('src/components/Elements/List/List.vue'),
            ListItem: () => import('src/components/Elements/List/ListItem.vue'),
            ItemSection: () => import('src/components/Elements/List/ItemSection.vue'),
            ItemLabel: () => import('src/components/Elements/List/ItemLabel.vue'),
            CheckBox: () => import('src/components/Elements/CheckBox.vue'),
            BaseBtn: () => import('src/components/Buttons/BaseBtn.vue'),
            DialogAddFax: () => import('src/components/Dialogs/DialogAddFax.vue'),
            IconBtn: () => import('src/components/Buttons/IconBtn.vue'),
            Badge: () => import('src/components/Elements/Badge.vue'),
            Menu: () => import('src/components/Menu.vue'),
            FaxesHistory: () => import('src/components/History/FaxesHistory.vue'),
            Card: () => import('src/components/Elements/Card/Card.vue'),
            CardSection: () => import('src/components/Elements/Card/CardSection.vue'),
            Dialog: () => import('src/components/Dialogs/Dialog.vue'),
        },
        filters: {
            statusFilter(value) {
                const options = getFromSettings('transportStatusOptions');
                return _.get(_.find(options, { value }), 'label');
            },
        },
        mixins: [showNotif],
        data() {
            return {
                dialogHistory: false,
                showFaxDialog: false,
                faxHistoryData: {
                    cols: {},
                    historyData: [],
                },
                localFaxesEditData: {},
                faxesTableProperties: {
                    title: 'Факсы',
                    viewBody: true,
                    viewTop: true,
                    hideBottom: false,
                    columns: [
                        {
                            name: 'name',
                            label: this.$t('fax'),
                            align: 'center',
                            field: 'name',
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
                            name: 'transporter_name',
                            label: this.$t('transporter'),
                            field: 'transporter_name',
                            align: 'center',
                            sortable: true,
                        },
                        {
                            name: 'transport_name',
                            label: this.$t('transport'),
                            field: 'transport_name',
                            align: 'center',
                            sortable: true,
                        },
                        {
                            name: 'departure_date',
                            label: this.$t('departureDate'),
                            align: 'center',
                            field: 'departure_date',
                            sortable: true,
                        },
                        {
                            name: 'arrival_date',
                            label: 'Дата прибытия',
                            align: 'center',
                            field: 'arrival_date',
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
                            name: 'notation',
                            label: 'Примечания',
                            field: 'notation',
                            align: 'center',
                            sortable: true,
                        },
                        {
                            name: 'up_to_cargo',
                            label: 'В карго',
                            field: 'up_to_cargo',
                            align: 'center',
                        },
                    ],
                },
                faxesTableReactiveProperties: {
                    selected: [],
                    visibleColumns: ['name', 'departure_date', 'up_to_cargo', 'notation', 'arrival_date', 'status', 'transport_name', 'transporter_name', 'user_name'],
                },
            };
        },
        computed: {
            faxes() {
                return this.$store.getters['faxes/getFaxes'];
            },
            transporters() {
                return this.$store.getters['transporter/getTransporters'];
            },
            transport() {
                return this.$store.getters['transport/getTransports'];
            },
            faxesStatusData() {
                const { faxes } = this;
                return [
                    {
                        status: 'Погрузка',
                        count: _.size(_.filter(faxes, { status: 1 })),
                        data: _.filter(faxes, { status: 1 }),
                    },
                    {
                        status: 'Ушло',
                        count: _.size(_.filter(faxes, { status: 2 })),
                        data: _.filter(faxes, { status: 2 }),
                    },
                    {
                        status: 'Задерживается',
                        count: _.size(_.filter(faxes, { status: 4 })),
                        data: _.filter(faxes, { status: 4 }),
                    },
                ];
            },
        },
        mounted() {
            this.$q.loading.show();
            Promise.all([getFaxes(this.$store)])
              .then(() => {
                  this.$q.loading.hide();
              })
              .catch(() => {
                  this.$q.loading.hide();
              });
        },
        methods: {
            goToFaxData(item) {
                this.$store.dispatch('faxes/setCurrentFaxItem', _.find(this.$store.getters['faxes/getFaxes'], { id: item.id }));
                devlog.log('FAX_S', _.find(this.$store.getters['faxes/getFaxes'], { id: item.id }));
                this.$router.push({
                    name: 'fax',
                    params: { id: item.id },
                });
            },
            // async getFaxesList() {
            //     if (_.isEmpty(this.faxes)) {
            //         this.$q.loading.show();
            //         await this.$axios.get(getUrl('faxes'))
            //           .then(({ data: { faxesList } }) => {
            //               devlog.log('DSW', faxesList);
            //               this.$store.dispatch('faxes/setFaxes', setFormatedDate(faxesList, ['departure_date', 'arrival_date']));
            //               this.$q.loading.hide();
            //           })
            //           .catch((errors) => {
            //               this.$q.loading.hide();
            //               devlog.log('errors', errors);
            //           });
            //     }
            // },
            deleteFaxItems() {
                this.$q.loading.show();
                // devlog.log('ID', this.faxesTableProperties.selected);
                const selectedIds = _.map(this.faxesTableProperties.selected, 'id');
                this.$axios.post(getUrl('deleteFax'), { ids: selectedIds })
                  .then(({ data }) => {
                      if (data.status) {
                          this.$q.loading.hide();
                          this.faxesTableReactiveProperties.selected = [];
                          this.$store.dispatch('faxes/deleteFaxes', selectedIds);
                          this.showNotif('success', 'Данные успешно удалены.', 'center');
                      }
                  })
                  .catch((errors) => {
                      this.$q.loading.hide();
                      devlog.log(errors);
                  });
            },
            viewEditDialog(val, event) {
                if (!_.includes(_.get(event, 'target.classList'), 'select_checkbox') && !_.includes(_.get(event, 'target.classList'), 'upload_to_cargo')) {
                    this.showFaxDialog = true;
                    this.localFaxesEditData = val;
                    this.localFaxesEditData.historyFunc = this.getFaxesHistory;
                    this.faxesTableReactiveProperties.selected = [];

                    setTimeout(() => {
                        val.selected = !val.selected;
                    }, 100);
                }
            },
            async uploadToCargo({ id, uploaded_to_cargo: value }) {
                this.showNotif('warning', value ? 'Выгрузить факс из карго?' : 'Загрузить факс в карго?', 'center', [
                    {
                        label: 'Отмена',
                        color: 'white',
                        handler: () => {
                        },
                    },
                    {
                        label: value ? 'Выгрузить' : 'Загрузить',
                        color: 'white',
                        handler: () => {
                            this.$q.loading.show();
                            this.$axios.post(getUrl('uploadFaxToCargo'), {
                                id,
                                value,
                            })
                              .then(({ data: { fax } }) => {
                                  this.$q.loading.hide();
                                  devlog.log('data', fax);
                                  this.$store.dispatch('faxes/updateFax', setFormatedDate(fax, ['departure_date', 'arrival_date']));
                              })
                              .catch(() => {
                                  this.$q.loading.hide();
                                  devlog.error('Ошибка загрузки факса в карго');
                              });
                        },
                    },
                ]);
            },
            async getFaxesHistory(id, cols) {
                this.$q.loading.show();
                await this.$axios.get(`${getUrl('faxHistory')}/${id}`)
                  .then(({ data: { faxHistory } }) => {
                      if (!_.isEmpty(faxHistory)) {
                          this.$q.loading.hide();
                          this.dialogHistory = true;
                          this.faxHistoryData = prepareHistoryData(cols, faxHistory);
                          this.faxHistoryData.cols.uploaded_to_cargo = 'Загрузка';
                          this.faxHistoryData.historyData = setFormatedDate(this.faxHistoryData.historyData, ['created_at', 'arrival_date', 'departure_date']);
                          devlog.log('storehouseDataHistory', faxHistory);
                      } else {
                          this.$q.loading.hide();
                          this.showNotif('info', 'По этой записи нет истории.', 'center');
                      }
                  })
                  .catch(() => {
                      this.$q.loading.hide();
                      devlog.error('Ошибка при получении данных истории.');
                  });
            },
            async refresh(done) {
                const { faxes } = this;
                await this.$axios.post(getUrl('getNewFax'), {
                    created_at: isoDate(max(_.map(faxes, (item) => new Date(toDate(item.created_at))))),
                    updated_at: formatToMysql(max(_.map(faxes, (item) => new Date(item.updated_at)))),
                })
                  .then(({ data: { newData } }) => {
                      devlog.log('DTA', newData);
                      if (!_.isEmpty(newData)) {
                          const createdItems = [];
                          _.forEach(newData, (item) => {
                              const find = _.some(faxes, ['id', item.id]);
                              if (find) {
                                  this.$store.dispatch('faxes/updateFax', setFormatedDate(item, ['departure_date', 'arrival_date']));
                              } else {
                                  createdItems.push(item);
                              }
                          });

                          if (!_.isEmpty(createdItems)) {
                              _.forEach(sortCollection(createdItems, 'id'), (elem) => {
                                  this.$store.dispatch('faxes/addFax', setFormatedDate(elem, ['departure_date', 'arrival_date']));
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
