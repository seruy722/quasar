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
{{ parseInt(history, 10) === 2 ? 'Мужской': 'Женский' }}
</ItemLabel>
              <ItemLabel v-else-if="i === 'phone'">
{{ history | phoneNumberFilter }}
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

    export default {
        name: 'CodeHistory',
        components: {
            TimelineEntry: () => import('src/components/Timeline/TimelineEntry.vue'),
            List: () => import('src/components/Elements/List/List.vue'),
            ItemSection: () => import('src/components/Elements/List/ItemSection.vue'),
            ItemLabel: () => import('src/components/Elements/List/ItemLabel.vue'),
            ListItem: () => import('src/components/Elements/List/ListItem.vue'),
            Badge: () => import('src/components/Elements/Badge.vue'),
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
