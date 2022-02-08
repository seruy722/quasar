<template>
  <div data-vue-component-name="TransfersStatistics">
    <div class="column items-center">
      <q-btn
        color="primary"
        :label="statisticsSelectData.label"
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
              v-for="(item, index) in optionsStatistics"
              :key="index"
              clickable
              @click="setStatistics(item)"
            >
              <q-item-section>{{ item.label }}</q-item-section>
            </q-item>
          </q-list>
        </q-menu>
      </q-btn>
      <div>{{ viewPeriodDate }}</div>
    </div>
    <CountTransfersData :enter-data="statisticsData" />

    <!--Топ 10 клиентов-->
<!--    <q-list-->
<!--      separator-->
<!--      bordered-->
<!--      dense-->
<!--      style="max-width: 450px;margin: 20px auto;font-weight: bold;"-->
<!--    >-->
<!--      <q-item>-->
<!--        <q-item-section class="statistics_title">-->
<!--          Топ 10 клиентов-->
<!--        </q-item-section>-->
<!--        <q-item-section>-->
<!--          <q-item-label>-->
<!--            <q-btn-->
<!--              push-->
<!--              color="primary"-->
<!--              label="Показать"-->
<!--              @click="setClientStatistics"-->
<!--            />-->
<!--          </q-item-label>-->
<!--        </q-item-section>-->
<!--      </q-item>-->
<!--      <q-item>-->
<!--        <q-item-section>Клиент</q-item-section>-->
<!--        <q-item-section>Количество</q-item-section>-->
<!--        <q-item-section>Сумма</q-item-section>-->
<!--      </q-item>-->
<!--      <q-item-->
<!--        v-for="(user, index) in topTransfersClients"-->
<!--        :key="index"-->
<!--      >-->
<!--        <q-item-section>-->
<!--          <q-item-label> {{ user.name }}</q-item-label>-->
<!--        </q-item-section>-->
<!--        <q-item-section>-->
<!--          <q-item-label>{{ numberFormat(user.count) }}</q-item-label>-->
<!--        </q-item-section>-->
<!--        <q-item-section>-->
<!--          <q-item-label>{{ numberFormat(user.sum) }}</q-item-label>-->
<!--        </q-item-section>-->
<!--      </q-item>-->
<!--    </q-list>-->

    <Dialog
      :dialog="choosePeriodDialog"
      :persistent="true"
      transition-show="flip-up"
      transition-hide="flip-down"
    >
      <q-card style="max-width: 600px;">
        <q-bar />
        <q-card-section class="q-pt-none">
          <div v-show="statisticsSelectData.value === 2">
            <div class="row items-center">
              <span>От:</span>
              <DateWithInput
                v-model:value="period.from"
              />
            </div>
            <div class="row items-center">
              <span>До:</span>
              <DateWithInput
                v-model:value="period.to"
              />
            </div>
          </div>
          <div v-show="statisticsSelectData.value === 3">
            <DateWithInput
              v-model:value="period.day"
            />
          </div>
        </q-card-section>
        <q-card-actions align="right">
          <OutlineBtn
            label="Отмена"
            dense
            color="negative"
            @click-outline-btn="choosePeriodDialog = false"
          />
          <OutlineBtn
            dense
            color="positive"
            @click-outline-btn="setPeriod(period)"
          />
        </q-card-actions>
      </q-card>
    </Dialog>
  </div>
</template>

<script>
import { format } from 'date-fns';
import { numberFormat } from 'src/utils';
import Dialog from 'src/components/Dialogs/Dialog.vue';
import CountTransfersData from 'src/components/Transfers/CountTransfersData.vue';
import OutlineBtn from 'src/components/Buttons/OutlineBtn.vue';
import DateWithInput from 'src/components/DateWithInput.vue';

export default {
  name: 'TransfersStatistics',
  components: {
    DateWithInput,
    OutlineBtn,
    CountTransfersData,
    Dialog,
  },
  props: {
    enterData: {
      type: Array,
      default: () => [],
    },
  },
  data() {
    return {
      topTransfersClients: [],
      choosePeriodDialog: false,
      viewPeriodDate: '',
      period: {
        from: '',
        to: '',
        day: '',
      },
      statisticsSelectData: {
        label: 'Сегодня',
        value: 1,
      },
      optionsStatistics: [
        {
          label: 'Все',
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
      statisticsData: [],
    };
  },
  computed: {
    countTransfersStatisticsData() {
      return this.countedData(this.statisticsData);
    },
  },
  created() {
    this.openDialogStatistics();
  },
  methods: {
    numberFormat,
    setClientStatistics() {
      const { enterData } = this;
      const clientNamesArray = _.uniq(_.map(enterData, 'client_name'));
      const clientsDataArray = [];
      _.forEach(clientNamesArray, (clientName) => {
        const clientData = _.filter(enterData, { client_name: clientName });
        clientsDataArray.push({
          name: clientName,
          count: _.size(clientData),
          sum: _.sumBy(clientData, 'sum'),
        });
      });
      clientsDataArray.sort((a, b) => b.sum - a.sum);
      devlog.log('dataCLIRNT', clientsDataArray.slice(0, 10));
      this.topTransfersClients = clientsDataArray.slice(0, 10);
    },
    setStatistics(val) {
      this.$q.loading.show();
      const { enterData } = this;
      if (val.value === 1) {
        this.statisticsSelectData = {
          label: 'Сегодня',
          value: 1,
        };
        this.setStatisticsData(enterData, format(new Date(), 'dd-MM-yyyy'));
      } else if (val.value === 2) {
        this.choosePeriodDialog = true;
        this.statisticsSelectData = {
          label: 'Выбрать период',
          value: 2,
        };
      } else if (val.value === 3) {
        this.choosePeriodDialog = true;
        this.statisticsSelectData = {
          label: 'Выбрать день',
          value: 3,
        };
      } else {
        this.statisticsSelectData = {
          label: 'Все',
          value: -1,
        };
        this.setStatisticsData(enterData, null);
      }
      this.$q.loading.hide();
    },
    setPeriod(period) {
      const { enterData } = this;
      this.setStatisticsData(enterData, period);
      this.choosePeriodDialog = false;
    },
    openDialogStatistics() {
      if (this.statisticsSelectData.value === 1) {
        const { enterData } = this;
        const today = format(new Date(), 'dd-MM-yyyy');
        this.period.from = today;
        this.period.to = today;
        this.period.day = today;
        this.viewPeriodDate = today;
        this.setStatisticsData(enterData, today);
      }
    },
    async setStatisticsData(data, date) {
      this.$q.loading.show();
      const {
        /* eslint-disable-next-line */
        reverseDate,
      } = await import('src/utils/formatDate');
      if (this.statisticsSelectData.value === -1) {
        this.viewPeriodDate = '';
        this.statisticsData = _.cloneDeep(data);
      } else if (_.isObject(date) && this.statisticsSelectData.value === 3) {
        this.viewPeriodDate = date.day;
        const { isEqual } = await import('date-fns');
        const day = reverseDate(date.day);
        this.statisticsData = _.filter(data, (item) => isEqual(new Date(reverseDate(item.created_at.slice(0, 10))), new Date(day)));
      } else if (_.isObject(date) && this.statisticsSelectData.value === 2) {
        const from = new Date(reverseDate(date.from));
        const to = new Date(reverseDate(date.to));
        this.viewPeriodDate = `${date.from} - ${date.to}`;
        this.statisticsData = _.filter(data, (item) => (new Date(reverseDate(item.created_at.slice(0, 10))) >= from && new Date(reverseDate(item.created_at.slice(0, 10))) <= to));
      } else {
        this.viewPeriodDate = date;
        this.statisticsData = _.filter(data, (item) => _.includes(item.created_at, date));
      }
      this.$q.loading.hide();
    },
  },
};
</script>
