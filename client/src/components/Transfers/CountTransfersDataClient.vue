<template>
  <q-list
    separator
    bordered
    dense
    style="max-width: 450px;margin: 20px auto;font-weight: bold;"
    data-vue-component-name="CountTransfersDataClient"
  >
    <q-item>
      <q-item-section class="statistics_title">
        По переводам
      </q-item-section>
    </q-item>
    <q-item>
      <q-item-section>
        <q-item-label>Статус</q-item-label>
      </q-item-section>
      <q-item-section>
        <q-item-label>Количество</q-item-label>
      </q-item-section>
      <q-item-section>
        <q-item-label> Сумма</q-item-label>
      </q-item-section>
    </q-item>

    <q-item>
      <q-item-section>
        <q-item-label>
          <q-badge :color="statusColor(6)">
            Обработка
          </q-badge>
        </q-item-label>
      </q-item-section>
      <q-item-section>
        <q-item-label>{{ countTransfersStatisticsData.pending | numberFormatFilter }}</q-item-label>
      </q-item-section>
      <q-item-section>
        <q-item-label>{{ countTransfersStatisticsData.pendingSum | numberFormatFilter }}</q-item-label>
      </q-item-section>
    </q-item>

    <q-item>
      <q-item-section>
        <q-item-label>
          <q-badge :color="statusColor(2)">
            Не выдан
          </q-badge>
        </q-item-label>
      </q-item-section>
      <q-item-section>
        <q-item-label>{{ countTransfersStatisticsData.notIssued | numberFormatFilter }}</q-item-label>
      </q-item-section>
      <q-item-section>
        <q-item-label>{{ countTransfersStatisticsData.notIssuedSum | numberFormatFilter }}</q-item-label>
      </q-item-section>
    </q-item>

    <q-item>
      <q-item-section>
        <q-item-label>
          <q-badge :color="statusColor(3)">
            Выдано
          </q-badge>
        </q-item-label>
      </q-item-section>
      <q-item-section>
        <q-item-label>{{ countTransfersStatisticsData.issued | numberFormatFilter }}</q-item-label>
      </q-item-section>
      <q-item-section>
        <q-item-label>{{ countTransfersStatisticsData.issuedSum | numberFormatFilter }}</q-item-label>
      </q-item-section>
    </q-item>

    <q-item>
      <q-item-section>
        <q-item-label>
          <q-badge :color="statusColor(1)">
            Вопрос
          </q-badge>
        </q-item-label>
      </q-item-section>
      <q-item-section>
        <q-item-label>{{ countTransfersStatisticsData.question | numberFormatFilter }}</q-item-label>
      </q-item-section>
      <q-item-section>
        <q-item-label>{{ countTransfersStatisticsData.questionSum | numberFormatFilter }}</q-item-label>
      </q-item-section>
    </q-item>

    <q-item>
      <q-item-section>
        <q-item-label>
          <q-badge :color="statusColor(4)">
            Отменен
          </q-badge>
        </q-item-label>
      </q-item-section>
      <q-item-section>
        <q-item-label>{{ countTransfersStatisticsData.cancel | numberFormatFilter }}</q-item-label>
      </q-item-section>
      <q-item-section>
        <q-item-label>{{ countTransfersStatisticsData.cancelSum | numberFormatFilter }}</q-item-label>
      </q-item-section>
    </q-item>

    <q-item>
      <q-item-section>
        <q-item-label>
          <q-badge :color="statusColor(5)">
            Возврат
          </q-badge>
        </q-item-label>
      </q-item-section>
      <q-item-section>
        <q-item-label>{{ countTransfersStatisticsData.returned | numberFormatFilter }}</q-item-label>
      </q-item-section>
      <q-item-section>
        <q-item-label>{{ countTransfersStatisticsData.returnedSum | numberFormatFilter }}</q-item-label>
      </q-item-section>
    </q-item>

    <q-item>
      <q-item-section>
        <q-item-label>
          <q-badge :color="statusColor(7)">
            Отменен клиентом
          </q-badge>
        </q-item-label>
      </q-item-section>
      <q-item-section>
        <q-item-label>{{ countTransfersStatisticsData.returnedClient | numberFormatFilter }}</q-item-label>
      </q-item-section>
      <q-item-section>
        <q-item-label>{{ countTransfersStatisticsData.returnedSumClient | numberFormatFilter }}</q-item-label>
      </q-item-section>
    </q-item>

    <q-item>
      <q-item-section>
        <q-item-label>
          Всего
        </q-item-label>
      </q-item-section>
      <q-item-section>
        <q-item-label>{{ countTransfersStatisticsData.all | numberFormatFilter }}</q-item-label>
      </q-item-section>
      <q-item-section>
        <q-item-label>{{ countTransfersStatisticsData.allSum | numberFormatFilter }}</q-item-label>
      </q-item-section>
    </q-item>
  </q-list>
</template>

<script>
import TransferMixin from 'src/mixins/Transfer';

export default {
  name: 'CountTransfersDataClient',
  mixins: [TransferMixin],
  props: {
    enterData: {
      type: Array,
      default: () => [],
    },
  },
  computed: {
    countTransfersStatisticsData() {
      return this.countedData(this.enterData);
    },
  },
  methods: {
    countedData(data) {
      const obj = {
        all: data.length,
        allSum: 0,
        notIssued: 0,
        notIssuedSum: 0,
        issued: 0,
        issuedSum: 0,
        question: 0,
        questionSum: 0,
        cancel: 0,
        cancelSum: 0,
        returned: 0,
        returnedSum: 0,
        returnedClient: 0,
        returnedSumClient: 0,
        pending: 0,
        pendingSum: 0,
      };
      _.forEach(data, (item) => {
        obj.allSum += item.sum;
        if (item.status === 1) {
          obj.question += 1;
          obj.questionSum += item.sum;
        } else if (item.status === 2) {
          obj.notIssued += 1;
          obj.notIssuedSum += item.sum;
        } else if (item.status === 3) {
          obj.issued += 1;
          obj.issuedSum += item.sum;
        } else if (item.status === 4) {
          obj.cancel += 1;
          obj.cancelSum += item.sum;
        } else if (item.status === 5) {
          obj.returned += 1;
          obj.returnedSum += item.sum;
        } else if (item.status === 7) {
          obj.returnedClient += 1;
          obj.returnedSumClient += item.sum;
        } else if (item.status === 6) {
          obj.pending += 1;
          obj.pendingSum += item.sum;
        }
      });
      return obj;
    },
  },
};
</script>
