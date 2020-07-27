<template>
  <div
    data-vue-component-name="ExportMenuGeneralCargo"
  >
    <ExportBtn
      color="positive"
      tooltip="Excel"
      icon="explicit"
      @exportBtnClick="show = true"
    />

    <Dialog
      :dialog.sync="show"
      :persistent="true"
      title="Оплата"
    >
      <q-card style="min-width: 320px;width: 100%;max-width: 900px;">
        <q-card-section class="row justify-between items-center bg-grey q-mb-sm">
          <span class="text-h6">{{ title }}</span>
          <IconBtn
            dense
            icon="clear"
            tooltip="Закрыть"
            color="negative"
            @iconBtnClick="show = false"
          />
        </q-card-section>
        <q-card-section>
          <div
            class="row items-center"
            style="margin: 0 40px;text-align: center;"
          >
            <q-select
              v-model="type"
              :options="typeOptions"
              style="margin-right: 40px;"
            />
            <q-btn
              color="primary"
              :label="selectData.label"
            >
              <q-menu
                auto-close
                transition-show="scale"
                transition-hide="scale"
              >
                <q-list
                  separator
                  style="min-width: 100px"
                >
                  <q-item
                    v-for="(item, index) in list"
                    :key="index"
                    clickable
                    @click="setSelectData(item)"
                  >
                    <q-item-section>{{ item.label }}</q-item-section>
                  </q-item>
                </q-list>
              </q-menu>
            </q-btn>
            <div style="margin-left: 40px;">
              {{ viewPeriodDate }}
            </div>
            <q-btn
              label="сводка по клиентам"
              style="margin: 10px auto;"
              color="orange"
              @click="exportGeneralDataByClients(model)"
            />
            <q-btn
              v-show="model === 'debts'"
              label="сводка по комиссии"
              style="margin: 10px auto;"
              color="orange"
              @click="exportGeneralDataByClients('commission')"
            />
          </div>
          <Dialog
            :dialog="choosePeriodDialog"
            :persistent="true"
            transition-show="flip-up"
            transition-hide="flip-down"
          >
            <q-card style="max-width: 600px;">
              <q-card-section class="q-pt-none">
                <div v-show="selectData.value === 2">
                  <div class="row items-center">
                    <span>От:</span>
                    <DateWithInputForCargo
                      :value.sync="period.from"
                    />
                  </div>
                  <div class="row items-center">
                    <span>До:</span>
                    <DateWithInputForCargo
                      :value.sync="period.to"
                    />
                  </div>
                </div>
                <div v-show="selectData.value === 3">
                  <DateWithInputForCargo
                    :value.sync="period.day"
                  />
                </div>
              </q-card-section>
              <q-card-actions align="right">
                <OutlineBtn
                  label="Отмена"
                  dense
                  color="negative"
                  @clickOutlineBtn="setDate(null)"
                />
                <OutlineBtn
                  dense
                  color="positive"
                  @clickOutlineBtn="setDate(true)"
                />
              </q-card-actions>
            </q-card>
          </Dialog>
        </q-card-section>
        <Separator />
        <q-card-actions align="right">
          <BaseBtn
            label="Выгрузить"
            color="positive"
            icon="save"
            @clickBaseBtn="exportFaxData(selectData,type, period)"
          />
        </q-card-actions>
      </q-card>
    </Dialog>
  </div>
</template>

<script>
    import { format } from 'date-fns';
    import ExportDataMixin from 'src/mixins/ExportData';

    export default {
        name: 'ExportMenuGeneralCargo',
        components: {
            ExportBtn: () => import('src/components/Buttons/ExportBtn.vue'),
            IconBtn: () => import('src/components/Buttons/IconBtn.vue'),
            BaseBtn: () => import('src/components/Buttons/BaseBtn.vue'),
            OutlineBtn: () => import('src/components/Buttons/OutlineBtn.vue'),
            Dialog: () => import('src/components/Dialogs/Dialog.vue'),
            Separator: () => import('src/components/Separator.vue'),
            DateWithInputForCargo: () => import('src/components/DateWithInputForCargo.vue'),
        },
        mixins: [ExportDataMixin],
        props: {
            title: {
                type: String,
                default: '',
            },
            urlName: {
                type: String,
                default: '',
            },
            model: {
                type: String,
                default: '',
            },
            urlNameClients: {
                type: String,
                default: '',
            },
        },
        data() {
            return {
                show: false,
                showDialogAddCargoPaymentEntry: false,
                selectData: {
                    label: 'Все даты',
                    value: -1,
                },
                list: [
                    {
                        label: 'Все даты',
                        value: -1,
                    },
                    {
                        label: 'Сегодня',
                        value: 1,
                    },
                    {
                        label: 'Выбрать период',
                        value: 2,
                    },
                    {
                        label: 'Выбрать день',
                        value: 3,
                    },
                ],
                viewPeriodDate: '',
                choosePeriodDialog: false,
                period: {
                    to: null,
                    from: null,
                    day: null,
                    today: null,
                },
                type: {
                    label: 'Общее',
                    value: -1,
                },
                typeOptions: [
                    {
                        label: 'Общее',
                        value: -1,
                    },
                    {
                        label: 'Оплата',
                        value: 1,
                    },
                    {
                        label: 'Долги',
                        value: 0,
                    },
                ],
            };
        },
        methods: {
            onClick(value) {
                this[value] = true;
            },
            setDate(val) {
                this.choosePeriodDialog = false;
                if (!val && this.selectData.value === 2) {
                    this.period.to = null;
                    this.period.from = null;
                } else if (val && this.selectData.value === 2) {
                    const to = this.period.to ? format(new Date(this.period.to), 'dd-MM-yyyy') : '';
                    const from = this.period.from ? format(new Date(this.period.from), 'dd-MM-yyyy') : '';
                    this.viewPeriodDate = `С ${from} по ${to}`;
                } else if (!val && this.selectData.value === 3) {
                    this.period.day = null;
                } else if (val && this.selectData.value === 3) {
                    this.viewPeriodDate = format(new Date(this.period.day), 'dd-MM-yyyy');
                }
            },
            setSelectData(val) {
                this.selectData = val;
                if (val.value === 1) {
                    this.viewPeriodDate = format(new Date(), 'dd-MM-yyyy');
                    this.period.today = format(new Date(), 'yyyy-MM-dd');
                } else if (val.value === 2) {
                    this.choosePeriodDialog = true;
                } else if (val.value === 3) {
                    this.choosePeriodDialog = true;
                } else {
                    this.selectData = val;
                    this.viewPeriodDate = '';
                }
            },
            async exportFaxData(select, type, period) {
                const sendData = {
                    type: type.value,
                    day: null,
                    period: {
                        to: null,
                        from: null,
                    },
                };
                if (select.value === 1) {
                    sendData.day = period.today;
                } else if (select.value === 2) {
                    sendData.period = period;
                } else if (select.value === 3) {
                    sendData.day = period.day;
                }
                devlog.log(sendData);
                const { getUrl } = await import('src/tools/url');
                this.exportDataToExcel(getUrl(this.urlName), {
                    data: sendData,
                }, 'Cargo.xlsx');
                this.show = false;
            },
            async exportGeneralDataByClients(model) {
                const { getUrl } = await import('src/tools/url');
                this.exportDataToExcel(getUrl(this.urlNameClients), { model }, 'Cargo.xlsx');
            },
        },
    };
</script>
