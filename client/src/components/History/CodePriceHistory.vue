<template>
  <q-timeline
    data-vue-component-name="CodePriceHistory"
    color="secondary"
  >
    <TimelineEntry
heading
tag="h6"
>
      История изменения данных
    </TimelineEntry>

    <TimelineEntry
      v-for="(transfer, index) in historyData.historyData"
      :key="index"
      :subtitle="transfer.created_at"
      :icon="$action[transfer.action]"
    >
      <q-list
        separator
        dense
      >
        <div
          v-for="(history, i) in transfer"
          :key="i"
        >
          <q-item
            v-if="historyData.cols[i] && i !== 'created_at'"
            dense
          >
            <q-item-section>
              <q-item-label v-if="transfer.action !== 'create' && i !== 'created_at' && i !== 'user_name'">
                <q-badge color="warning">
                  {{ historyData.cols[i] }}
                </q-badge>
              </q-item-label>
              <q-item-label v-else>
{{ historyData.cols[i] }}
</q-item-label>
            </q-item-section>
            <q-item-section side>
              <q-item-label :lines="3">
{{ history }}
</q-item-label>
            </q-item-section>
          </q-item>
          <Separator v-if="historyData.cols[i]" />
        </div>
      </q-list>
    </TimelineEntry>
  </q-timeline>
</template>

<script>
    import TransferMixin from 'src/mixins/Transfer';
    import getFromSettings from 'src/tools/settings';

    export default {
        name: 'CodePriceHistory',
        components: {
            TimelineEntry: () => import('src/components/Timeline/TimelineEntry.vue'),
            Separator: () => import('src/components/Separator.vue'),
        },
        mixins: [TransferMixin],
        props: {
            historyData: {
                type: Object,
                default: () => ({}),
            },
        },
        data() {
            this.$action = getFromSettings('historyActionForIcon');
            return {};
        },
    };
</script>
