<template>
  <q-page
      data-vue-component-name="AdminFaxesComponent"
  >
    <PullRefresh @refresh="refresh">
      <Table
          :table-properties="faxesTableProperties"
          :table-data="faxes"
          :table-reactive-properties="faxesTableReactiveProperties"
          :loading="loading"
          title="Факсы"
      >
        <template #top-buttons>
          <div class="row q-gutter-sm">
            <RoundBtn
                v-show="faxesTableReactiveProperties.selected.length"
                color="negative"
                icon="delete"
                tooltip="Удалить"
                @round-btn-click="deleteFaxItems(faxesTableReactiveProperties.selected)"
            />

            <UpdateBtn
                :func="refresh"
            />
            <RoundBtn
                v-show="faxesTableReactiveProperties.selected.length > 1"
                color="orange"
                icon="gamepad"
                tooltip="Обьеденить"
                @round-btn-click="combineFaxes(faxesTableReactiveProperties.selected)"
            />
            <RoundBtn
                icon="flight_land"
                color="accent"
                tooltip="Не доставленные места"
                @round-btn-click="showDialogNotDeliveredCargo = true"
            />
            <Menu :items="['Факс', 'Перевожчика']" />
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
                :header-class="`${props.row.uploaded_to_cargo ? 'bg-green' : 'bg-red'} text-white`"
                :style="`border-radius: 30px;border: 1px solid ${props.row.uploaded_to_cargo ? 'green' : 'red'};`"
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
                  <q-item-label :lines="3">
                    {{ props.row.name }}
                  </q-item-label>
                </q-item-section>
              </template>

              <List
                  separator
                  dense
                  @click-list="viewEditDialog(props)"
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
                        v-if="col.field === 'status'"
                    >
                      {{ statusFilter(col.value) }}
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
                    <div class="row justify-around">
                      <BaseBtn
                          label="История"
                          color="info"
                          style="max-width: 100px;"
                          @click-base-btn="getStorehouseDataHistory(props.row.id, props.cols)"
                      />
                      <BaseBtn
                          label="Перейти"
                          color="info"
                          style="max-width: 100px;"
                          @click-base-btn="goToFaxData(props.row)"
                      />
                    </div>
                  </q-item-section>
                </q-item>
              </List>
            </q-expansion-item>
          </div>
        </template>
        <template #inner-body="{props}">
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
                class="fax_name"
                :props="props"
                @click.stop="goToFaxData(props.row)"
            >
              {{ props.row.name }}
            </q-td>

            <q-td
                key="status"
                :props="props"
            >
              <q-badge>
                {{ statusFilter(props.row.status) }}
              </q-badge>
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
                  dense
                  :name="!props.row.uploaded_to_cargo ? 'vertical_align_bottom':'vertical_align_top'"
                  @click.stop="uploadToCargo(props.row)"
              />
            </q-td>
          </q-tr>
        </template>
      </Table>
    </PullRefresh>
    <q-list
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
        <template #header>
          <q-item-section>
            <q-item-label>{{ status }}</q-item-label>
          </q-item-section>
          <q-item-section>
            <q-item-label>
              <q-badge>{{ numberFormat(count) }}</q-badge>
            </q-item-label>
          </q-item-section>
        </template>

        <q-list
            separator
            bordered
            dense
        >
          <q-item
              v-for="(elem, i) in data"
              :key="i"
          >
            <q-item-section>
              <q-item-label>{{ `${i + 1}. ${elem.name}` }}</q-item-label>
            </q-item-section>
            <q-item-section side>
              <q-item-label>{{ elem.transport_name }}</q-item-label>
            </q-item-section>
          </q-item>
        </q-list>
      </q-expansion-item>
    </q-list>
    <DialogAddFax
        v-model:show-dialog="showFaxDialog"
        v-model:entry-data="localFaxesEditData"
    />
    <DialogComponent
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
          <FaxesHistory :fax-history-data="faxHistoryData" />
        </q-card-section>
      </q-card>
    </DialogComponent>
    <DialogChooseDate
        v-model:show-dialog="showDialogChooseDate"
        v-model:date="dialogChooseDateData"
        @set-date="setDateFromDialog(dialogChooseDateData)"
    />
    <DialogNotDeliveredCargo v-model:show="showDialogNotDeliveredCargo" />
  </q-page>
</template>

<script>
import { getUrl } from 'src/tools/url';
import { setFormatedDate, prepareHistoryData, getFaxes } from 'src/utils/FrequentlyCalledFunctions';
import { callFunction, statusFilter, numberFormat } from 'src/utils';
import showNotif from 'src/mixins/showNotif';
import { addTime } from 'src/utils/formatDate';
import Table from 'src/components/Elements/Table/Table.vue';
import IconBtn from 'src/components/Buttons/IconBtn.vue';
import RoundBtn from 'src/components/Buttons/RoundBtn.vue';
import BaseBtn from 'src/components/Buttons/BaseBtn.vue';
import DialogComponent from 'src/components/Dialogs/DialogComponent.vue';
import List from 'src/components/Elements/List/List.vue';
import DialogAddFax from 'src/components/Dialogs/DialogAddFax.vue';
import Menu from 'src/components/Menu.vue';
import FaxesHistory from 'src/components/History/FaxesHistory.vue';
import PullRefresh from 'src/components/PullRefresh.vue';
import UpdateBtn from 'src/components/Buttons/UpdateBtn.vue';
import DialogChooseDate from 'src/components/Dialogs/DialogChooseDate.vue';
import DialogNotDeliveredCargo from 'src/components/CargoDebts/Dialogs/DialogNotDeliveredCargo.vue';

export default {
  name: 'AdminFaxesComponent',
  components: {
    Table,
    List,
    BaseBtn,
    DialogAddFax,
    IconBtn,
    Menu,
    FaxesHistory,
    DialogComponent,
    PullRefresh,
    UpdateBtn,
    DialogChooseDate,
    DialogNotDeliveredCargo,
    RoundBtn,
  },
  mixins: [showNotif],
  data() {
    return {
      loading: false,
      showDialogNotDeliveredCargo: false,
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
            label: 'Факс',
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
            label: 'Перевожчик',
            field: 'transporter_name',
            align: 'center',
            sortable: true,
          },
          {
            name: 'transport_name',
            label: 'Транспорт',
            field: 'transport_name',
            align: 'center',
            sortable: true,
          },
          {
            name: 'departure_date',
            label: 'Дата отправки',
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
      showDialogChooseDate: false,
      dialogChooseDateData: null,
      dialogChooseDataForSend: {},
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
    this.loading = true;
    Promise.all([getFaxes(this.$store)])
        .finally(() => {
          this.loading = false;
        });
  },
  methods: {
    numberFormat,
    statusFilter,
    setDateFromDialog(date) {
      devlog.log('setDateFromDialog', date);
      if (date) {
        this.dialogChooseDataForSend.date = addTime(date)
            .toISOString();
        this.uploadToCargoFinish(this.dialogChooseDataForSend);
      }
    },
    goToFaxData(item) {
      this.$store.dispatch('faxes/setCurrentFaxItem', _.find(this.$store.getters['faxes/getFaxes'], { id: item.id }));
      this.$router.push({
        name: 'admin-fax',
        params: { id: item.id },
      });
    },
    deleteFaxItems(selectedFaxes) {
      // devlog.log('ID', this.faxesTableProperties.selected);
      const selectedIds = _.map(selectedFaxes, 'id');
      const faxNames = _.map(selectedFaxes, 'name');
      this.showNotif('warning', `Удалить факс - ${faxNames.join(', ')}?`, 'center', [
        {
          label: 'Отмена',
          color: 'white',
          handler: () => {
            this.faxesTableReactiveProperties.selected = [];
          },
        },
        {
          label: 'Удалить',
          color: 'white',
          handler: () => {
            this.$q.loading.show();
            this.$axios.post(getUrl('deleteFax'), { ids: selectedIds })
                .then(({ data }) => {
                  if (data.status) {
                    this.faxesTableReactiveProperties.selected = [];
                    this.$store.dispatch('faxes/deleteFaxes', selectedIds);
                    this.$q.loading.hide();
                    this.showNotif('success', 'Данные успешно удалены.', 'center');
                  }
                })
                .catch((errors) => {
                  this.$q.loading.hide();
                  devlog.log(errors);
                });
          },
        },
      ]);
    },
    viewEditDialog(val, event) {
      if (!_.includes(_.get(event, 'target.classList'), 'select_checkbox') && !_.includes(_.get(event, 'target.classList'), 'upload_to_cargo') && !_.includes(_.get(event, 'target.classList'), 'fax_name')) {
        this.showFaxDialog = true;
        this.localFaxesEditData = val;
        this.localFaxesEditData.historyFunc = this.getFaxesHistory;
        this.faxesTableReactiveProperties.selected = [];

        setTimeout(() => {
          val.selected = !val.selected;
        }, 100);
      }
    },
    uploadToCargoFinish({
                          id,
                          value,
                          date = null,
                        }) {
      this.$q.loading.show();
      this.$axios.post(getUrl('uploadFaxToCargo'), {
        id,
        value,
        date,
      })
          .then(({ data: { fax } }) => {
            this.$q.loading.hide();
            this.$store.dispatch('faxes/updateFax', setFormatedDate(fax, ['departure_date', 'arrival_date']));
            this.showDialogChooseDate = false;
            this.showNotif('success', fax.uploaded_to_cargo ? 'Данные успешно загружены' : 'Данные успешно выгружены', 'center');
          })
          .catch(() => {
            this.$q.loading.hide();
            devlog.error('Ошибка загрузки факса в карго');
          });
    },
    uploadToCargo({
                    id,
                    uploaded_to_cargo: value,
                  }) {
      devlog.log('value', value);
      if (!value) {
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
              this.showDialogChooseDate = true;
              this.dialogChooseDataForSend = {
                id,
                value,
              };
            },
          },
        ]);
      } else {
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
              devlog.log('uploadToCargoFinish', value);
              this.uploadToCargoFinish({
                id,
                value,
              });
              devlog.log('VVV', value ? 'Выгрузить' : 'Загрузить');
            },
          },
        ]);
      }
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
      if (!done) {
        this.$q.loading.show();
      }
      this.$store.dispatch('faxes/fetchFaxes')
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
    combineFaxes(selectedFaxes) {
      const faxNames = _.map(selectedFaxes, 'name');
      const faxIds = _.uniq(_.map(selectedFaxes, 'transporter_id'));
      devlog.log('faxIds', faxIds);
      if (!_.isEmpty(selectedFaxes) && _.size(faxIds) === 1) {
        this.showNotif('warning', `Обьеденить факсы - ${faxNames.join(', ')}?`, 'center', [
          {
            label: 'Отмена',
            color: 'white',
            handler: () => {
            },
          },
          {
            label: 'Обьеденить',
            color: 'white',
            handler: () => {
              this.$q.loading.show();
              this.$axios.post(getUrl('combineFaxes'), selectedFaxes)
                  .then(({ data: { fax } }) => {
                    devlog.log('COM_DATA', fax);
                    this.$store.dispatch('faxes/addFax', setFormatedDate(fax, ['departure_date', 'arrival_date']));
                    this.faxesTableReactiveProperties.selected = [];
                    this.$q.loading.hide();
                    this.showNotif('success', 'Факсы успешно обьеденены.', 'center');
                  })
                  .catch((errors) => {
                    devlog.error('Ошибка запроса combineFaxes', errors);
                    this.showNotif('error', 'Произошла ошибка при обьеденении факсов. Обновите страницу пожалуйста.', 'center');
                    this.faxesTableReactiveProperties.selected = [];
                    this.$q.loading.hide();
                  });
            },
          },
        ]);
      } else {
        this.showNotif('warning', 'Нельзя соеденять факсы разных перевожчиков!', 'center');
      }
    },
  },
};
</script>
