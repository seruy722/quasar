<template>
  <q-timeline
    data-vue-component-name="StorehouseDataHistory"
    color="secondary"
  >
    <TimelineEntry
      heading
      tag="h6"
    >
      История изменения данных
    </TimelineEntry>

    <TimelineEntry
      v-for="(transfer, index) in storehouseHistoryData.historyData"
      :key="index"
      :subtitle="transfer.created_at"
      :icon="$action[transfer.action]"
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
            v-if="storehouseHistoryData.cols[i] && i !== 'created_at'"
            dense
          >
            <ItemSection>
              <ItemLabel v-if="transfer.action !== 'create' && i !== 'created_at' && i !== 'user_name'">
                <Badge color="warning">
                  {{ storehouseHistoryData.cols[i] }}
                </Badge>
              </ItemLabel>
              <ItemLabel v-else>
                {{ storehouseHistoryData.cols[i] }}
              </ItemLabel>
            </ItemSection>
            <ItemSection side>
              <ItemLabel
                v-if="i === 'notation'"
                :lines="3"
              >
                {{ history }}
              </ItemLabel>
              <ItemLabel
                v-else-if="i === 'status_label'"
              >
                <Badge :color="statusColor(history)">
                  {{ history }}
                </Badge>
              </ItemLabel>
              <ItemLabel
                v-else-if="i === 'things'"
                :lines="5"
              >
                {{ thingsFilter(history) }}
              </ItemLabel>
              <ItemLabel v-else>
                {{ history }}
              </ItemLabel>
            </ItemSection>
          </ListItem>
          <Separator v-if="storehouseHistoryData.cols[i]" />
        </div>
      </List>
    </TimelineEntry>
  </q-timeline>
</template>

<script>
import TransferMixin from 'src/mixins/Transfer';
import getFromSettings from 'src/tools/settings';
import { thingsFilter } from 'src/utils';
import TimelineEntry from 'src/components/Timeline/TimelineEntry.vue';
import List from 'src/components/Elements/List/List.vue';
import ItemSection from 'src/components/Elements/List/ItemSection.vue';
import ItemLabel from 'src/components/Elements/List/ItemLabel.vue';
import ListItem from 'src/components/Elements/List/ListItem.vue';
import Badge from 'src/components/Elements/Badge.vue';
import Separator from 'src/components/Separator.vue';

export default {
  name: 'StorehouseDataHistory',
  components: {
    TimelineEntry,
    List,
    ItemSection,
    ItemLabel,
    ListItem,
    Badge,
    Separator,
  },
  mixins: [TransferMixin],
  props: {
    storehouseHistoryData: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    this.$action = getFromSettings('historyActionForIcon');
    return {};
  },
  methods: {
    thingsFilter,
  },
};
</script>
