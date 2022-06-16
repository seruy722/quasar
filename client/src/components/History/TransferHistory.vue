<template>
  <RoundBtn
      icon="history"
      tooltip="История"
      @round-btn-click="show = true"
  />
  <DialogComponent
      :dialog="show"
      :maximized="true"
      transition-show="slide-up"
      transition-hide="slide-down"
  >
    <q-card
        style="max-width: 600px;"
        data-vue-component-name="TransferHistory"
    >
      <q-card-section class="row justify-between bg-grey q-mb-sm">
        <span class="text-h6">История изменения данных</span>
        <div>
          <CloseBtn
              dense
              icon="clear"
              tooltip="Закрыть"
              @close-btn-click="close"
          />
        </div>
      </q-card-section>
      <q-card-section class="q-pt-none">
        <q-linear-progress
            v-show="loading"
            indeterminate
        />
        <q-timeline
            color="secondary"
        >
          <TimelineEntry
              v-for="(transfer, index) in transferHistoryData.historyData"
              :key="index"
              :subtitle="transfer.created_at"
              :icon="action[transfer.action]"
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
  </DialogComponent>
</template>

<script>
import getFromSettings from 'src/tools/settings';
import { phoneNumberFilter, statusColor } from 'src/utils';
import TimelineEntry from 'src/components/Timeline/TimelineEntry.vue';
import List from 'src/components/Elements/List/List.vue';
import ItemSection from 'src/components/Elements/List/ItemSection.vue';
import ItemLabel from 'src/components/Elements/List/ItemLabel.vue';
import ListItem from 'src/components/Elements/List/ListItem.vue';
import Badge from 'src/components/Elements/Badge.vue';
import Separator from 'src/components/Separator.vue';
import DialogComponent from 'src/components/Dialogs/DialogComponent.vue';
import CloseBtn from 'src/components/Buttons/CloseBtn.vue';
import { getUrl } from 'src/tools/url';
import {
  prepareHistoryData,
  setFormatedDate,
  setMethodLabel,
  setStatusLabel,
} from 'src/utils/FrequentlyCalledFunctions';
import { ref, defineComponent, watch } from 'vue';
import { axiosInstance } from 'boot/axios';
import RoundBtn from 'src/components/Buttons/RoundBtn.vue';

export default defineComponent({
  name: 'TransferHistory',
  components: {
    TimelineEntry,
    List,
    ItemSection,
    ItemLabel,
    ListItem,
    Badge,
    Separator,
    DialogComponent,
    CloseBtn,
    RoundBtn,
  },
  props: {
    transferId: {
      type: Number,
      default: 0,
    },
    cols: {
      type: Array,
      default: () => [],
    },
  },
  setup(props) {
    const action = getFromSettings('historyActionForIcon');
    const loading = ref(false);
    const show = ref(false);
    const transferHistoryData = ref({
      cols: {},
      transferHistory: [],
    });

    const close = (() => {
      transferHistoryData.value = {
        cols: {},
        transferHistory: [],
      };
      show.value = false;
    });

    const setAdditionalData = ((data) => setMethodLabel(setStatusLabel(setFormatedDate(data, ['created_at', 'issued_by']))));

    watch(show, (val) => {
      if (val) {
        (function getTransfersHistory(transferId, cols) {
          loading.value = true;
          axiosInstance.get(`${getUrl('transfersHistory')}/${transferId}`)
              .then(({ data: { transferHistory } }) => {
                if (!_.isEmpty(transferHistory)) {
                  const historyData = prepareHistoryData(cols, transferHistory);
                  historyData.historyData = setAdditionalData(historyData.historyData);
                  transferHistoryData.value = historyData;
                }
                loading.value = false;
              })
              .catch(() => {
                loading.value = false;
                devlog.error('Ошибка при получении данных истории.');
              });
        }(props.transferId, props.cols));
      }
    });
    return {
      action,
      loading,
      transferHistoryData,
      phoneNumberFilter,
      statusColor,
      close,
      show,
    };
  },
});
</script>
