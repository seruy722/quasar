<template>
  <q-timeline
    data-vue-component-name="Timeline"
    color="secondary"
  >
    <TimelineEntry heading tag="h6">
      История изменения данных
    </TimelineEntry>

    <TimelineEntry
      v-for="(transfer, index) in transferHistoryData.transferHistory"
      :key="index"
      :subtitle="transfer.created_at"
      :icon="index === 0 ? 'add':'update'"
    >
      <List
        separator
        dense
      >
        <div
          v-for="(history, i) in transfer"
          :key="i"
        >
          <ListItem
            v-if="transferHistoryData.cols[i] && history"
            dense
          >
            <ItemSection>
              <ItemLabel>{{ transferHistoryData.cols[i] }}</ItemLabel>
            </ItemSection>
            <ItemSection side>
              <ItemLabel
                v-if="i === 'notation'"
                :lines="3"
              >
                {{ history }}
              </ItemLabel>
              <ItemLabel
                v-if="i === 'status_label'"
              >
                <Badge :color="statusColor(history)">
                  {{ history }}
                </Badge>
              </ItemLabel>
              <ItemLabel v-else>{{ history }}</ItemLabel>
            </ItemSection>
          </ListItem>
        </div>
      </List>
    </TimelineEntry>
  </q-timeline>
</template>

<script>
    import TransferMixin from 'src/mixins/Transfer';

    export default {
        name: 'Timeline',
        components: {
            TimelineEntry: () => import('src/components/Timeline/TimelineEntry.vue'),
            List: () => import('src/components/Elements/List/List.vue'),
            ItemSection: () => import('src/components/Elements/List/ItemSection.vue'),
            ItemLabel: () => import('src/components/Elements/List/ItemLabel.vue'),
            ListItem: () => import('src/components/Elements/List/ListItem.vue'),
            Badge: () => import('src/components/Elements/Badge.vue'),
            // Separator: () => import('src/components/Separator.vue'),
            // Badge: () => import('src/components/Elements/Badge.vue'),
        },
        mixins: [TransferMixin],
        props: {
            transferHistoryData: {
                type: Object,
                default: () => ({}),
            },
        },
    };
</script>
