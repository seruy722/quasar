<template>
  <q-timeline
    data-vue-component-name="FaxTransferDataHistory"
    color="secondary"
  >
    <TimelineEntry
      heading
      tag="h6"
    >
      История изменения данных
    </TimelineEntry>

    <TimelineEntry
      v-for="(transfer, index) in historyData"
      :key="index"
      :subtitle="fullDate(transfer.created_at)"
      :icon="$action[transfer.action]"
    >
      <q-list
        separator
        bordered
        dense
      >
        <q-item class="text-bold">
          <q-item-section>Клиент</q-item-section>
          <q-item-section>Код</q-item-section>
          <q-item-section>Вес</q-item-section>
        </q-item>
        <q-separator />
        <div
          v-for="(history, i) in JSON.parse(transfer.history_data)"
          :key="i"
        >
          <q-separator />
          <q-item
            v-if="history.created_at"
            dense
          >
            <q-item-section>{{ history.code_client_name }}</q-item-section>
            <q-item-section>{{ history.code_place }}</q-item-section>
            <q-item-section>{{ history.kg }}</q-item-section>
          </q-item>
          <q-separator v-if="i === 0" />
        </div>
        <q-separator />
        <q-item>
          <q-item-section class="text-center text-bold">
            Информация
          </q-item-section>
        </q-item>
        <q-item v-if="movedData(JSON.parse(transfer.history_data), 'to')">
          <q-item-section>
            В факс
            <q-badge>{{ movedData(JSON.parse(transfer.history_data), 'to') }}</q-badge>
          </q-item-section>
          <q-item-section>
            Из факса
            <q-badge>{{ movedData(JSON.parse(transfer.history_data), 'from') }}</q-badge>
          </q-item-section>
        </q-item>
        <q-item>
          <q-item-section>Пользователь</q-item-section>
          <q-item-section>
            <q-badge>{{ movedData(JSON.parse(transfer.history_data), 'user') }}</q-badge>
          </q-item-section>
        </q-item>
        <q-item>
          <q-item-section>{{ sum('place', JSON.parse(transfer.history_data)) }}</q-item-section>
          <q-item-section>{{ sum('kg', JSON.parse(transfer.history_data)) }}</q-item-section>
        </q-item>
      </q-list>
    </TimelineEntry>
  </q-timeline>
</template>

<script>
import getFromSettings from 'src/tools/settings';
import { fullDate } from 'src/utils/formatDate';

export default {
  name: 'FaxTransferDataHistory',
  components: {
    TimelineEntry: () => import('src/components/Timeline/TimelineEntry.vue'),
  },
  props: {
    historyData: {
      type: Array,
      default: () => ([]),
    },
  },
  data() {
    this.$action = getFromSettings('historyActionForIcon');
    return {};
  },
  methods: {
    fullDate,
    sum(field, data) {
      let sum = 0;
      _.forEach(data, (elem) => {
        if (_.isNumber(elem[field])) {
          sum += elem[field];
        }
      });
      return sum;
    },
    movedData(data, direction) {
      let res = '';
      if (direction === 'to') {
        res = _.get(data, 'moveData.moveToFax');
      } else if (direction === 'from') {
        res = _.get(data, 'moveData.moveFromFax');
      } else {
        res = data.user_name;
      }
      return res;
    },
  },
};
</script>
