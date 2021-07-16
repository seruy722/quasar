<template>
  <q-timeline
    data-vue-component-name="CodeHistory"
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
      <List
        separator
        dense
      >
        <div
          v-for="(history, i) in transfer"
          :key="i"
        >
          <ListItem
            v-if="historyData.cols[i] && i !== 'created_at'"
            dense
          >
            <ItemSection>
              <ItemLabel v-if="transfer.action !== 'create' && i !== 'created_at' && i !== 'user_name'">
                <Badge color="warning">
                  {{ historyData.cols[i] }}
                </Badge>
              </ItemLabel>
              <ItemLabel v-else>
                {{ historyData.cols[i] }}
              </ItemLabel>
            </ItemSection>
            <ItemSection side>
              <ItemLabel v-if="i === 'sex'">
                {{ parseInt(history, 10) === 2 ? 'Мужской' : 'Женский' }}
              </ItemLabel>
              <ItemLabel v-else-if="i === 'phone'">
                {{ phoneNumberFilter(history) }}
              </ItemLabel>
              <ItemLabel v-else>
                {{ history }}
              </ItemLabel>
            </ItemSection>
          </ListItem>
          <Separator v-if="historyData.cols[i]" />
        </div>
      </List>
    </TimelineEntry>
  </q-timeline>
</template>

<script>
import TransferMixin from 'src/mixins/Transfer';
import getFromSettings from 'src/tools/settings';
import { phoneNumberFilter } from 'src/utils';
import TimelineEntry from 'src/components/Timeline/TimelineEntry.vue';
import List from 'src/components/Elements/List/List.vue';
import ItemSection from 'src/components/Elements/List/ItemSection.vue';
import ItemLabel from 'src/components/Elements/List/ItemLabel.vue';
import ListItem from 'src/components/Elements/List/ListItem.vue';
import Badge from 'src/components/Elements/Badge.vue';
import Separator from 'src/components/Separator.vue';

export default {
  name: 'CodeHistory',
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
    historyData: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    this.$action = getFromSettings('historyActionForIcon');
    return {};
  },
  methods: {
    phoneNumberFilter,
  },
};
</script>
