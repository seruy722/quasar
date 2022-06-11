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
      <div class="text-red text-bold">
        {{ viewPeriodDate }}
      </div>
    </div>

    <q-linear-progress
        v-show="visible"
        indeterminate
    />

    <q-list
        v-if="!isEmptyData"
        bordered
        separator
    >
      <q-item class="bg-grey">
        <q-item-section>Статус</q-item-section>
        <q-item-section>Количество</q-item-section>
        <q-item-section>Сумма</q-item-section>
      </q-item>

      <q-item
          v-for="({count, sum}, index) in statistics"
          :key="index"
      >
        <q-item-section>
          <q-item-label>
            <q-badge :color="statusColor2(index)">
              {{ index }}
            </q-badge>
          </q-item-label>
        </q-item-section>
        <q-item-section>
          <q-item-label>
            {{ numberFormat(count) }}
          </q-item-label>
        </q-item-section>
        <q-item-section>
          <q-item-label>
            {{ numberFormat(sum) }}
          </q-item-label>
        </q-item-section>
      </q-item>

      <q-item>
        <q-item-section>Общее</q-item-section>
        <q-item-section>
          {{ summa(statistics, 'count') }}
        </q-item-section>
        <q-item-section>{{ summa(statistics, 'sum') }}</q-item-section>
      </q-item>
    </q-list>

    <div
        v-else
        class="border-2"
    >
      <q-icon
          name="warning"
          size="md"
          color="orange"
      />
      Нет данных
    </div>

    <q-list
        v-if="!isEmptyData"
        bordered
        separator
    >
      <q-item>
        <q-item-section class="text-center text-bold">
          По пользователям
        </q-item-section>
      </q-item>
      <q-item class="bg-grey">
        <q-item-section>
          <q-item-label>Пользователь</q-item-label>
        </q-item-section>
        <q-item-section>
          <q-item-label>Количество</q-item-label>
        </q-item-section>
        <q-item-section>
          <q-item-label>Сумма</q-item-label>
        </q-item-section>
      </q-item>
      <q-item
          v-for="(user, index) in usersStatistics"
          :key="index"
      >
        <q-item-section>
          <q-item-label>{{ user.name }}</q-item-label>
        </q-item-section>
        <q-item-section>
          <q-item-label>{{ numberFormat(user.count) }}</q-item-label>
        </q-item-section>
        <q-item-section>
          <q-item-label>{{ numberFormat(user.sum) }}</q-item-label>
        </q-item-section>
      </q-item>
    </q-list>

    <DialogComponent
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
    </DialogComponent>
  </div>
</template>

<script>
import { format } from 'date-fns';
import { numberFormat, statusColor2 } from 'src/utils';
import DialogComponent from 'src/components/Dialogs/DialogComponent.vue';
import OutlineBtn from 'src/components/Buttons/OutlineBtn.vue';
import DateWithInput from 'src/components/DateWithInput.vue';
import { getUrl } from 'src/tools/url';

export default {
  name: 'TransfersStatistics',
  components: {
    DateWithInput,
    OutlineBtn,
    DialogComponent,
  },
  data() {
    return {
      visible: false,
      statistics: {},
      usersStatistics: [],
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
    };
  },
  computed: {
    isEmptyData() {
      return _.isEmpty(this.statistics);
    },
  },
  created() {
    this.openDialogStatistics();
  },
  methods: {
    summa(obj, field) {
      if (!_.isArray(obj)) {
        return numberFormat(_.reduce(obj, (sum, item) => sum + item[field], 0));
      }
      return null;
    },
    numberFormat,
    statusColor2,
    setStatistics(val) {
      this.$q.loading.show();
      if (val.value === 1) {
        this.statisticsSelectData = {
          label: 'Сегодня',
          value: 1,
        };
        this.setStatisticsData([], format(new Date(), 'dd-MM-yyyy'));
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
        this.setStatisticsData([], null);
      }
      this.$q.loading.hide();
    },
    setPeriod(period) {
      this.setStatisticsData(null, period);
      this.choosePeriodDialog = false;
    },
    openDialogStatistics() {
      if (this.statisticsSelectData.value === 1) {
        const today = format(new Date(), 'dd-MM-yyyy');
        this.period.from = today;
        this.period.to = today;
        this.period.day = today;
        this.viewPeriodDate = today;
        this.setStatisticsData([], today);
      }
    },
    getStatistics(selectValue, values) {
      return this.$axios.post(getUrl('transfersStatistics'), { selectValue, values });
    },
    getUsersDataForTransfers(statistics) {
      const obj = {};
      _.forEach(statistics, (elem) => {
        const usersIds = _.uniq(_.map(elem.countUserTransfers, 'user_id'));
        _.forEach(usersIds, (id) => {
          const userData = _.filter(elem.countUserTransfers, (item) => item.user_id === id);
          if (!obj[id]) {
            obj[id] = {
              name: _.get(_.first(userData), 'user_name'),
              sum: _.sumBy(userData, 'sum'),
              count: _.size(userData),
            };
          } else {
            obj[id]['sum'] += _.sumBy(userData, 'sum');
            obj[id]['count'] += _.size(userData);
          }
        });
      });
      const arr = _.filter(obj, (item) => item);
      return _.orderBy(arr, 'sum', 'desc');
    },
    async setStatisticsData(data, date) {
      this.visible = true;
      devlog.log('visible', this.visible);
      const {
        /* eslint-disable-next-line */
        reverseDate,
      } = await import('src/utils/formatDate');
      if (this.statisticsSelectData.value === -1) {
        this.viewPeriodDate = '';
        this.getStatistics(-1, null)
            .then(({ data: { statistics } }) => {
              this.statistics = statistics;
              this.usersStatistics = this.getUsersDataForTransfers(statistics);
            });
      } else if (_.isObject(date) && this.statisticsSelectData.value === 3) {
        this.viewPeriodDate = date.day;
        const day = reverseDate(date.day);
        this.getStatistics(3, { day })
            .then(({ data: { statistics } }) => {
              this.statistics = statistics;
              this.usersStatistics = this.getUsersDataForTransfers(statistics);
            });
      } else if (_.isObject(date) && this.statisticsSelectData.value === 2) {
        const from = new Date(reverseDate(date.from));
        const to = new Date(reverseDate(date.to));
        this.viewPeriodDate = `${date.from} - ${date.to}`;
        this.getStatistics(2, { from, to })
            .then(({ data: { statistics } }) => {
              this.statistics = statistics;
              this.usersStatistics = this.getUsersDataForTransfers(statistics);
            });
      } else {
        this.viewPeriodDate = date;
        this.getStatistics(0, { day: date })
            .then(({ data: { statistics } }) => {
              this.statistics = statistics;
              this.usersStatistics = this.getUsersDataForTransfers(statistics);
            });
      }
      this.visible = false;
    },
  },
};
</script>
