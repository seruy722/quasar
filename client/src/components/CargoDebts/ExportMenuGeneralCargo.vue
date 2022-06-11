<template>
  <div
      data-vue-component-name="ExportMenuGeneralCargo"
  >
    <RoundBtn
        color="positive"
        tooltip="Excel"
        icon="explicit"
        @round-btn-click="show = true"
    />

    <DialogComponent
        v-model:dialog="show"
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
              @icon-btn-click="show = false"
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
            <div class="q-gutter-md row items-start">
              <SearchSelect
                  v-model="city"
                  label="Сводка по городу"
                  clearable
                  :dense="$q.screen.xs || $q.screen.sm"
                  :func-load-data="getCities"
                  :options="cities"
              />
              <q-btn
                  v-if="city"
                  label="Выгрузить"
                  color="positive"
                  @click="exportGeneralDataByClientsCity(model, city)"
              />
            </div>
          </div>
          <DialogComponent
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
                        v-model:value="period.from"
                    />
                  </div>
                  <div class="row items-center">
                    <span>До:</span>
                    <DateWithInputForCargo
                        v-model:value="period.to"
                    />
                  </div>
                </div>
                <div v-show="selectData.value === 3">
                  <DateWithInputForCargo
                      v-model:value="period.day"
                  />
                </div>
              </q-card-section>
              <q-card-actions align="right">
                <OutlineBtn
                    label="Отмена"
                    dense
                    color="negative"
                    @click-outline-btn="setDate(null)"
                />
                <OutlineBtn
                    dense
                    color="positive"
                    @click-outline-btn="setDate(true)"
                />
              </q-card-actions>
            </q-card>
          </DialogComponent>
        </q-card-section>
        <Separator />
        <q-card-actions align="right">
          <BaseBtn
              label="Сводка по клиентам"
              color="orange"
              @click-base-btn="exportGeneralDataByClients(model)"
          />
          <BaseBtn
              v-show="model === 'debts'"
              label="сводка по комиссии"
              color="orange"
              @click-base-btn="exportGeneralDataByClients('commission')"
          />
          <BaseBtn
              label="Общее"
              color="positive"
              @click-base-btn="exportFaxData(selectData,type, period, city)"
          />
        </q-card-actions>
      </q-card>
    </DialogComponent>
  </div>
</template>

<script>
import { format } from 'date-fns';
import ExportDataMixin from 'src/mixins/ExportData';
import RoundBtn from 'src/components/Buttons/RoundBtn.vue';
import IconBtn from 'src/components/Buttons/IconBtn.vue';
import BaseBtn from 'src/components/Buttons/BaseBtn.vue';
import OutlineBtn from 'src/components/Buttons/OutlineBtn.vue';
import DialogComponent from 'src/components/Dialogs/DialogComponent.vue';
import Separator from 'src/components/Separator.vue';
import DateWithInputForCargo from 'src/components/DateWithInputForCargo.vue';
import SearchSelect from 'src/components/Elements/SearchSelect.vue';
import { getCities } from 'src/utils/FrequentlyCalledFunctions';
import { getUrl } from 'src/tools/url';

export default {
  name: 'ExportMenuGeneralCargo',
  components: {
    RoundBtn,
    IconBtn,
    BaseBtn,
    OutlineBtn,
    DialogComponent,
    Separator,
    DateWithInputForCargo,
    SearchSelect,
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
      city: null,
      options: null,
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
  computed: {
    cities() {
      return this.$store.getters['cities/getCities'];
    },
  },
  watch: {
    city(val) {
      devlog.log('VAL_CITY', val);
    },
  },
  methods: {
    getCities,
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
    async exportFaxData(select, type, period, city) {
      devlog.log('EXPORT', city);
      const { getTimeZone } = await import('src/utils/formatDate');
      const sendData = {
        type: type.value,
        day: null,
        period: {
          to: null,
          from: null,
        },
        timeZone: getTimeZone(),
      };
      const { addTime } = await import('src/utils/formatDate');
      if (select.value === 1) {
        sendData.day = addTime(period.today)
            .toISOString();
      } else if (select.value === 2) {
        sendData.period = period;
        if (period.to) {
          sendData.period.to = addTime(period.to)
              .toISOString();
        }
        if (period.from) {
          sendData.period.from = addTime(period.from)
              .toISOString();
        }
      } else if (select.value === 3) {
        sendData.day = addTime(period.day)
            .toISOString();
      }
      devlog.log(sendData);
      const findCity = _.find(this.cities, { value: city });
      this.exportDataToExcel(getUrl(this.urlName), {
        data: sendData,
        cityId: _.get(findCity, 'value'),
      }, `${this.model}_${findCity ? _.get(findCity, 'label') : 'все'}.xlsx`);
      this.show = false;
    },
    async exportGeneralDataByClients(model) {
      this.exportDataToExcel(getUrl(this.urlNameClients), {
        model,
      }, `${model}.xlsx`);
    },
    async exportGeneralDataByClientsCity(model, cityId) {
      const findCity = _.find(this.cities, { value: cityId });
      this.exportDataToExcel(getUrl('exportClientsGeneralDataOdessa'), {
        model,
        cityId,
        cityName: _.get(findCity, 'label'),
      }, `${model}_${findCity ? _.get(findCity, 'label') : 'все'}.xlsx`);
    },
  },
};
</script>
