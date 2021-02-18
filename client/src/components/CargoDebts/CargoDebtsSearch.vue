<template>
  <div data-vue-component-name="CargoDebtsSearch">
    <div class="row items-center">
      <div style="margin: 0 40px;text-align: center;">
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
      <q-select
        v-model="type"
        label="Тип"
        :options="optionsType"
      />
      <q-btn
        icon="settings"
        class="q-ml-lg"
        :color="dialogChooseDateData ? 'positive' : 'black'"
        @click="showDialogChooseDate = true"
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
          <div v-show="statisticsSelectData.value === 2">
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
          <div v-show="statisticsSelectData.value === 3">
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
    <Dialog
      :dialog="showDialogChooseDate"
      :persistent="true"
      transition-show="flip-up"
      transition-hide="flip-down"
    >
      <q-card style="width: 350px;max-width: 600px;">
        <q-card-section class="row items-center q-pb-none">
          <div style="font-size: 20px;">
            Выбор даты
          </div>
          <q-space />
          <q-btn
            icon="close"
            flat
            round
            dense
            color="primary"
            @click="showDialogChooseDate = false"
          />
          <q-separator />
        </q-card-section>
        <q-card-section>
          <q-list
            bordered
            separator
          >
            <q-item>
              <q-item-section avatar>
                <Date
                  :value.sync="dialogChooseDateData"
                />
              </q-item-section>
              <q-item-section>
                {{ dialogChooseDateData || 'Пусто' }}
              </q-item-section>
              <q-item-section avatar>
                <q-icon
                  v-show="dialogChooseDateData"
                  name="clear"
                  color="negative"
                  class="cursor-pointer"
                  @click="dialogChooseDateData = null"
                />
              </q-item-section>
            </q-item>
          </q-list>
        </q-card-section>
      </q-card>
    </Dialog>
  </div>
</template>

<script>
import { format } from 'date-fns';

export default {
  name: 'CargoDebtsSearch',
  components: {
    DateWithInputForCargo: () => import('components/DateWithInputForCargo.vue'),
    OutlineBtn: () => import('components/Buttons/OutlineBtn.vue'),
    Dialog: () => import('components/Dialogs/Dialog.vue'),
    Date: () => import('components/Date.vue'),
  },
  props: {
    enterData: {
      type: Array,
      default: () => [],
    },
    settings: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      showDialogChooseDate: null,
      type: {
        label: 'Все',
        value: -1,
      },
      optionsType: [
        {
          label: 'Все',
          value: -1,
        },
        {
          label: 'Оплата',
          value: 1,
        }, {
          label: 'Долг',
          value: 0,
        },
      ],
      choosePeriodDialog: false,
      viewPeriodDate: '',
      period: {
        from: '',
        to: '',
        day: '',
      },
      statisticsSelectData: {
        label: 'Все даты',
        value: -1,
      },
      optionsStatistics: [
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
      statisticsData: [],
    };
  },
  computed: {
    cargoForSearch() {
      return this.$store.getters['cargoDebts/getCargoForSearch'];
    },
    debtsForSearch() {
      return this.$store.getters['cargoDebts/getDebtsForSearch'];
    },
    dialogChooseDateData: {
      get: function get() {
        return this.$store.getters['settings/getDateForSaveData'];
      },
      set: function set(val) {
        return this.$store.dispatch('settings/setDateForSaveData', val);
      },
    },
  },
  watch: {
    type() {
      this.setStatisticsData(this.period);
    },
    dialogChooseDateData(val) {
      _.set(this, 'settings.date', val);
    },
  },
  beforeDestroy() {
    this.$store.dispatch('cargoDebts/setCargo', this.cargoForSearch);
    this.$store.dispatch('cargoDebts/setDebts', this.debtsForSearch);
  },
  methods: {
    setStatistics(val) {
      devlog.log('ITEM', val);
      if (val.value === 1) {
        [, this.statisticsSelectData] = this.optionsStatistics;
        this.setStatisticsData(new Date());
      } else if (val.value === 2) {
        this.choosePeriodDialog = true;
        [, , this.statisticsSelectData] = this.optionsStatistics;
      } else if (val.value === 3) {
        this.choosePeriodDialog = true;
        [, , , this.statisticsSelectData] = this.optionsStatistics;
      } else {
        [this.statisticsSelectData] = this.optionsStatistics;
        this.setStatisticsData(null);
      }
    },
    setPeriod(period) {
      this.setStatisticsData(period);
      this.choosePeriodDialog = false;
    },
    async setStatisticsData(date) {
      const { cargoForSearch, debtsForSearch } = this;
      const { getDateWithoutTime } = await import('src/utils/formatDate');
      devlog.log('DATE', date);
      const type = this.type.value;
      if (this.statisticsSelectData.value === -1) {
        this.viewPeriodDate = '';
        if (type === -1) {
          await this.$store.dispatch('cargoDebts/setCargo', cargoForSearch);
          await this.$store.dispatch('cargoDebts/setDebts', debtsForSearch);
        } else {
          await this.$store.dispatch('cargoDebts/setCargo', _.filter(cargoForSearch, { type }));
          await this.$store.dispatch('cargoDebts/setDebts', _.filter(debtsForSearch, { type }));
        }
      } else if (_.isObject(date) && this.statisticsSelectData.value === 3) {
        this.viewPeriodDate = date.day;
        if (type === -1) {
          await this.$store.dispatch('cargoDebts/setCargo', _.filter(cargoForSearch, (item) => getDateWithoutTime(date.day) === getDateWithoutTime(item.created_at)));
          await this.$store.dispatch('cargoDebts/setDebts', _.filter(debtsForSearch, (item) => getDateWithoutTime(date.day) === getDateWithoutTime(item.created_at)));
        } else {
          await this.$store.dispatch('cargoDebts/setCargo', _.filter(cargoForSearch, (item) => getDateWithoutTime(date.day) === getDateWithoutTime(item.created_at) && item.type === type));
          await this.$store.dispatch('cargoDebts/setDebts', _.filter(debtsForSearch, (item) => getDateWithoutTime(date.day) === getDateWithoutTime(item.created_at) && item.type === type));
        }
      } else if (_.isObject(date) && this.statisticsSelectData.value === 2) {
        const { from, to } = date;
        this.viewPeriodDate = `${from} - ${to}`;
        if (type === -1) {
          await this.$store.dispatch('cargoDebts/setCargo', _.filter(cargoForSearch, (item) => getDateWithoutTime(item.created_at) >= getDateWithoutTime(from) && getDateWithoutTime(item.created_at) <= getDateWithoutTime(to)));
          await this.$store.dispatch('cargoDebts/setDebts', _.filter(debtsForSearch, (item) => getDateWithoutTime(item.created_at) >= getDateWithoutTime(from) && getDateWithoutTime(item.created_at) <= getDateWithoutTime(to)));
        } else {
          await this.$store.dispatch('cargoDebts/setCargo', _.filter(cargoForSearch, (item) => getDateWithoutTime(item.created_at) >= getDateWithoutTime(from) && getDateWithoutTime(item.created_at) <= getDateWithoutTime(to) && item.type === type));
          await this.$store.dispatch('cargoDebts/setDebts', _.filter(debtsForSearch, (item) => getDateWithoutTime(item.created_at) >= getDateWithoutTime(from) && getDateWithoutTime(item.created_at) <= getDateWithoutTime(to) && item.type === type));
        }
      } else {
        this.viewPeriodDate = format(new Date(), 'dd-MM-yyyy');
        const today = format(new Date(), 'yyyy-MM-dd');
        if (type === -1) {
          await this.$store.dispatch('cargoDebts/setCargo', _.filter(cargoForSearch, (item) => getDateWithoutTime(today) === getDateWithoutTime(item.created_at)));
          await this.$store.dispatch('cargoDebts/setDebts', _.filter(debtsForSearch, (item) => getDateWithoutTime(today) === getDateWithoutTime(item.created_at)));
        } else {
          await this.$store.dispatch('cargoDebts/setCargo', _.filter(cargoForSearch, (item) => getDateWithoutTime(today) === getDateWithoutTime(item.created_at) && item.type === type));
          await this.$store.dispatch('cargoDebts/setDebts', _.filter(debtsForSearch, (item) => getDateWithoutTime(today) === getDateWithoutTime(item.created_at) && item.type === type));
        }
      }
    },
  },
};
</script>
