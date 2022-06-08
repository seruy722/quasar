<template>
  <Dialog
      :dialog="showDialog"
      :maximized="true"
      transition-show="slide-up"
      transition-hide="slide-down"
  >
    <q-card style="max-width: 600px;">
      <q-card-section class="row justify-between bg-grey q-mb-sm">
        <span class="text-h6">История изменения данных</span>
        <div>
          <IconBtn
              dense
              icon="clear"
              tooltip="Закрыть"
              color="negative"
              @icon-btn-click="close"
          />
        </div>
      </q-card-section>
      <q-card-section class="q-pt-none">
        <q-linear-progress
            v-show="loading"
            indeterminate
        />
        <q-timeline
            data-vue-component-name="TransferHistory"
            color="secondary"
        >

          <TimelineEntry
              v-for="(transfer, index) in transferHistoryData.historyData"
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
                    v-if="transferHistoryData.cols[i] && i !== 'created_at'"
                    dense
                >
                  <ItemSection>
                    <ItemLabel v-if="transfer.action !== 'create' && i !== 'created_at' && i !== 'user_name'">
                      <Badge color="warning">
                        {{ transferHistoryData.cols[i] }}
                      </Badge>
                    </ItemLabel>
                    <ItemLabel v-else>
                      {{ transferHistoryData.cols[i] }}
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
                        v-else-if="i === 'status'"
                    >
                      <Badge :color="statusColor(transfer['status_label'])">
                        {{ transfer['status_label'] }}
                      </Badge>
                    </ItemLabel>
                    <ItemLabel
                        v-else-if="i === 'method'"
                    >
                      {{ transfer['method_label'] }}
                    </ItemLabel>
                    <ItemLabel
                        v-else-if="i === 'receiver_phone'"
                    >
                      {{ phoneNumberFilter(history) }}
                    </ItemLabel>
                    <ItemLabel v-else>
                      {{ history }}
                    </ItemLabel>
                  </ItemSection>
                </ListItem>
                <Separator v-if="transferHistoryData.cols[i]" />
              </div>
            </List>
          </TimelineEntry>
        </q-timeline>
      </q-card-section>
    </q-card>
  </Dialog>
</template>

<script>
import TransferMixin from 'src/mixins/Transfer';
import getFromSettings from 'src/tools/settings';
import { phoneNumberFilter, statusColor } from 'src/utils';
import TimelineEntry from 'src/components/Timeline/TimelineEntry.vue';
import List from 'src/components/Elements/List/List.vue';
import ItemSection from 'src/components/Elements/List/ItemSection.vue';
import ItemLabel from 'src/components/Elements/List/ItemLabel.vue';
import ListItem from 'src/components/Elements/List/ListItem.vue';
import Badge from 'src/components/Elements/Badge.vue';
import Separator from 'src/components/Separator.vue';
import Dialog from 'src/components/Dialogs/Dialog.vue';
import IconBtn from 'src/components/Buttons/IconBtn.vue';
import { getUrl } from 'src/tools/url';
import {
  prepareHistoryData,
  setFormatedDate,
  setMethodLabel,
  setStatusLabel,
} from 'src/utils/FrequentlyCalledFunctions';

export default {
  name: 'TransferHistory',
  components: {
    TimelineEntry,
    List,
    ItemSection,
    ItemLabel,
    ListItem,
    Badge,
    Separator,
    Dialog,
    IconBtn,
  },
  mixins: [TransferMixin],
  props: {
    show: {
      type: Boolean,
      default: false,
    },
  },
  emits: ['update:show'],
  data() {
    this.$action = getFromSettings('historyActionForIcon');
    return {
      loading: false,
      transferHistoryData: {
        cols: {},
        transferHistory: [],
      },
    };
  },
  computed: {
    showDialog: {
      get: function get() {
        return this.show;
      },
      set: function set(val) {
        this.$emit('update:show', val);
      },
    },
  },
  methods: {
    setAdditionalData(data) {
      return setMethodLabel(setStatusLabel(setFormatedDate(data, ['created_at', 'issued_by'])));
    },
    phoneNumberFilter,
    statusColor,
    getTransfersHistory(transferID, cols) {
      devlog.log('cols', cols);

      this.loading = true;
      this.$axios.get(`${getUrl('transfersHistory')}/${transferID}`)
          .then(({ data: { transferHistory } }) => {
            if (!_.isEmpty(transferHistory)) {
              devlog.log('transferHistory', transferHistory);
              const historyData = prepareHistoryData(cols, transferHistory);
              historyData.historyData = this.setAdditionalData(historyData.historyData);
              this.transferHistoryData = historyData;
            }
            this.loading = false;
          })
          .catch(() => {
            this.loading = false;
            devlog.error('Ошибка при получении данных истории.');
          });
    },
    close() {
      this.transferHistoryData = {
        cols: {},
        transferHistory: [],
      };
      this.showDialog = false;
    },
  },
};
</script>
