<template>
  <q-list
    separator
    bordered
    dense
    style="max-width: 450px;margin: 20px auto;font-weight: bold;"
    data-vue-component-name="CountTransfersData"
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

    <q-item v-if="countTransfersStatisticsData.pending">
      <q-item-section>
        <q-item-label>
          <q-badge :color="statusColor(6)">
            Обработка
          </q-badge>
        </q-item-label>
      </q-item-section>
      <q-item-section>
        <q-item-label>{{ numberFormat(countTransfersStatisticsData.pending) }}</q-item-label>
      </q-item-section>
      <q-item-section>
        <q-item-label>{{ numberFormat(countTransfersStatisticsData.pendingSum) }}</q-item-label>
      </q-item-section>
    </q-item>

    <q-item v-if="countTransfersStatisticsData.notIssued">
      <q-item-section>
        <q-item-label>
          <q-badge :color="statusColor(2)">
            Не выдан
          </q-badge>
        </q-item-label>
      </q-item-section>
      <q-item-section>
        <q-item-label>{{ numberFormat(countTransfersStatisticsData.notIssued) }}</q-item-label>
      </q-item-section>
      <q-item-section>
        <q-item-label>{{ numberFormat(countTransfersStatisticsData.notIssuedSum) }}</q-item-label>
      </q-item-section>
    </q-item>

    <q-item v-if="countTransfersStatisticsData.issued">
      <q-item-section>
        <q-item-label>
          <q-badge :color="statusColor(3)">
            Выдано
          </q-badge>
        </q-item-label>
      </q-item-section>
      <q-item-section>
        <q-item-label>{{ numberFormat(countTransfersStatisticsData.issued) }}</q-item-label>
      </q-item-section>
      <q-item-section>
        <q-item-label>{{ numberFormat(countTransfersStatisticsData.issuedSum) }}</q-item-label>
      </q-item-section>
    </q-item>

    <q-item v-if="countTransfersStatisticsData.question">
      <q-item-section>
        <q-item-label>
          <q-badge :color="statusColor(1)">
            Вопрос
          </q-badge>
        </q-item-label>
      </q-item-section>
      <q-item-section>
        <q-item-label>{{ numberFormat(countTransfersStatisticsData.question) }}</q-item-label>
      </q-item-section>
      <q-item-section>
        <q-item-label>{{ numberFormat(countTransfersStatisticsData.questionSum) }}</q-item-label>
      </q-item-section>
    </q-item>

    <q-item v-if="countTransfersStatisticsData.cancel">
      <q-item-section>
        <q-item-label>
          <q-badge :color="statusColor(4)">
            Отменен
          </q-badge>
        </q-item-label>
      </q-item-section>
      <q-item-section>
        <q-item-label>{{ numberFormat(countTransfersStatisticsData.cancel) }}</q-item-label>
      </q-item-section>
      <q-item-section>
        <q-item-label>{{ numberFormat(countTransfersStatisticsData.cancelSum) }}</q-item-label>
      </q-item-section>
    </q-item>

    <q-item v-if="countTransfersStatisticsData.returned">
      <q-item-section>
        <q-item-label>
          <q-badge :color="statusColor(5)">
            Возврат
          </q-badge>
        </q-item-label>
      </q-item-section>
      <q-item-section>
        <q-item-label>{{ numberFormat(countTransfersStatisticsData.returned) }}</q-item-label>
      </q-item-section>
      <q-item-section>
        <q-item-label>{{ numberFormat(countTransfersStatisticsData.returnedSum) }}</q-item-label>
      </q-item-section>
    </q-item>

    <q-item v-if="countTransfersStatisticsData.returnedClient">
      <q-item-section>
        <q-item-label>
          <q-badge :color="statusColor(7)">
            Отменен клиентом
          </q-badge>
        </q-item-label>
      </q-item-section>
      <q-item-section>
        <q-item-label>{{ numberFormat(countTransfersStatisticsData.returnedClient) }}</q-item-label>
      </q-item-section>
      <q-item-section>
        <q-item-label>{{ numberFormat(countTransfersStatisticsData.returnedSumClient) }}</q-item-label>
      </q-item-section>
    </q-item>

    <q-item>
      <q-item-section>
        <q-item-label>
          Всего
        </q-item-label>
      </q-item-section>
      <q-item-section>
        <q-item-label>{{ numberFormat(countTransfersStatisticsData.all) }}</q-item-label>
      </q-item-section>
      <q-item-section>
        <q-item-label>{{ numberFormat(countTransfersStatisticsData.allSum) }}</q-item-label>
      </q-item-section>
    </q-item>

<!--    <q-item>-->
<!--      <q-item-section class="statistics_title">-->
<!--        По пользователям-->
<!--      </q-item-section>-->
<!--    </q-item>-->
<!--    <q-item-->
<!--      v-for="(user, id) in countTransfersStatisticsData.usersData"-->
<!--      :key="id"-->
<!--    >-->
<!--      <q-item-section>-->
<!--        <q-item-label>{{ user.name }}</q-item-label>-->
<!--      </q-item-section>-->
<!--      <q-item-section>-->
<!--        <q-item-label>{{ numberFormat(user.all) }}</q-item-label>-->
<!--      </q-item-section>-->
<!--      <q-item-section>-->
<!--        <q-item-label>{{ numberFormat(user.allSum) }}</q-item-label>-->
<!--      </q-item-section>-->
<!--    </q-item>-->
  </q-list>
</template>

<script>
import TransferMixin from 'src/mixins/Transfer';
import { numberFormat } from 'src/utils';

export default {
  name: 'CountTransfersData',
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
    numberFormat,
    countedData(data) {
      const userObj = {};
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
        usersData: [],
        returnedClient: 0,
        returnedSumClient: 0,
        pending: 0,
        pendingSum: 0,
      };
      _.forEach(data, (item) => {
        const userData = userObj[item.user_name];
        if (userData) {
          userData.all += 1;
          userData.allSum += item.sum;
        } else {
          userObj[item.user_name] = {
            name: item.user_name,
            all: 1,
            allSum: item.sum,
          };
        }
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
      const usersAr = _.values(userObj);
      usersAr.sort((a, b) => b.allSum - a.allSum);
      obj.usersData = usersAr;
      devlog.log('usersAr', usersAr);
      devlog.log('OBJ', obj);
      return obj;
    },
  },
};
</script>
