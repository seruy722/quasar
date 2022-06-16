<template>
  <DialogComponent
      v-model:dialog="show"
      :persistent="true"
      @keyup.enter="checkErrors(transferData, updateData)"
  >
    <q-card
        data-vue-component-name="DialogAddTransfer"
        style="min-width: 320px;width: 100%;max-width: 500px;"
    >
      <q-card-section class="row justify-between bg-grey q-mb-sm">
        <span class="text-h6">{{ dialogTitle }}</span>
        <div class="q-gutter-sm">
          <TransferHistory
              v-if="localProps.row"
              :transfer-id="localProps.row.id"
              :cols="localProps.cols"
          />

          <RoundBtn
              icon="save"
              tooltip="Сохранить"
              color="positive"
              @round-btn-click="checkErrors(transferData, updateData)"
          />

          <RoundBtn
              icon="clear"
              tooltip="Закрыть"
              color="negative"
              @round-btn-click="confirm(cancel, transferData)"
          />
        </div>
      </q-card-section>
      <q-card-section
          style="max-height: 70vh"
          class="scroll"
      >
        <div
            v-for="(item, index) in transferData"
            :key="index"
        >
          <BaseInput
              v-if="item.type === 'text'"
              v-model.trim="item.value"
              v-model:change-value="item.changeValue"
              :label="item.label"
              :type="item.type"
              :mask="item.mask"
              :unmasked-value="item.unmaskedValue"
              dense
              :field="item.field"
              :errors="errorsData"
          />

          <BaseInput
              v-else-if="item.type === 'number'"
              v-model.number="item.value"
              v-model:change-value="item.changeValue"
              :label="item.label"
              :type="item.type"
              :mask="item.mask"
              :unmasked-value="item.unmaskedValue"
              dense
              :field="item.field"
              :errors="errorsData"
          />

          <SearchSelect
              v-else-if="item.type === 'searchSelect'"
              v-model="item.value"
              v-model:change-value="item.changeValue"
              dense
              :options="item.options"
              :label="item.label"
              :field="item.field"
              :func-load-data="item.funcLoadData"
              :errors="errorsData"
              @change="changeValueFunc"
          />

          <BaseSelect
              v-else-if="item.type === 'select'"
              v-model="item.value"
              v-model:change-value="item.changeValue"
              :label="item.label"
              :dense="true"
              :options="item.options"
              :field="item.field"
              :errors="errorsData"
          />

          <BaseInput
              v-else-if="item.type === 'date'"
              v-model="item.value"
              v-model:change-value="item.changeValue"
              :label="item.label"
              :errors="errorsData"
              :field="item.field"
              :readonly="item.readonly"
              :mask="item.mask"
              dense
          >
            <template #append>
              <Date
                  v-model:value="item.value"
                  v-model:change-value="item.changeValue"
              />
            </template>
          </BaseInput>
        </div>
      </q-card-section>

      <Separator />
      <q-card-actions>
        <BaseBtn
            label="Отмена"
            color="negative"
            @click-base-btn="confirm(cancel, transferData)"
        />
        <BaseBtn
            label="Сохранить"
            color="positive"
            @click-base-btn="checkErrors(transferData, updateData)"
        />
      </q-card-actions>
    </q-card>
  </DialogComponent>
  <DialogAddCode
      v-model:show-dialog="showCodeDialog"
      v-model:new-code-data="newCodeData"
  />
</template>

<script>
import { getUrl } from 'src/tools/url';
import CheckErrorsFunc from 'src/utils/CheckErrors.js';
import showNotif from 'src/utils/showNotif.js';
import { setChangeValue } from 'src/utils/FrequentlyCalledFunctions';
import DialogComponent from 'src/components/Dialogs/DialogComponent.vue';
import BaseBtn from 'src/components/Buttons/BaseBtn.vue';
import BaseInput from 'src/components/Elements/BaseInput.vue';
import Separator from 'src/components/Separator.vue';
import RoundBtn from 'src/components/Buttons/RoundBtn.vue';
import SearchSelect from 'src/components/Elements/SearchSelect.vue';
import BaseSelect from 'src/components/Elements/BaseSelect.vue';
import { addTime, reverseDate } from 'src/utils/formatDate';
import { formatISO } from 'date-fns';
import {
  defineAsyncComponent,
  defineComponent,
  ref,
  reactive,
  computed,
  watch,
} from 'vue';
import { axiosInstance } from 'boot/axios';
import { useQuasar } from 'quasar';
import { useStore } from "vuex";

export default defineComponent({
  name: 'DialogAddTransfer',
  components: {
    DialogComponent,
    BaseBtn,
    BaseInput,
    Separator,
    RoundBtn,
    SearchSelect,
    BaseSelect,
    Date: defineAsyncComponent(() => import('src/components/Date.vue')),
    DialogAddCode: defineAsyncComponent(() => import('src/components/Dialogs/DialogAddCode.vue')),
    TransferHistory: defineAsyncComponent(() => import('src/components/History/TransferHistory.vue')),
  },
  props: {
    showDialog: {
      type: Boolean,
      default: false,
    },
    localProps: {
      type: Object,
      default: () => ({}),
    },
    transferData: {
      type: Object,
      default: () => ({}),
    },
    selected: {
      type: Array,
      default: () => [],
    },
  },
  emits: ['update:showDialog', 'update:localProps', 'update:selected'],
  setup(props, { emit }) {
    const $q = useQuasar();
    const transferHistoryComponent = ref(null);
    const showCodeDialog = ref(false);
    const newCodeData = reactive({});
    const { errorsData, checkErrors } = CheckErrorsFunc();

    const dialogTitle = computed(() => _.get(props.localProps, 'row.client_name') || 'Новый перевод');
    const show = computed({
      get: function get() {
        return props.showDialog;
      },
      set: function set(val) {
        emit('update:showDialog', val);
      },
    });

    watch(newCodeData, (val) => {
      if (!_.isEmpty(val)) {
        _.set(props.transferData, 'client_id.value', val.value);
        _.set(props.transferData, 'client_id.changeValue', true);
      }
    });

    function changeValueFunc(id) {
      if (_.isEmpty(props.localProps)) {
        axiosInstance.get(`${getUrl('getTransferCodeCommission')}/${id}`)
            .then(({ data: { transfer } }) => {
              if (transfer) {
                _.set(props.transferData, 'transfer_commission.value', transfer.transfer_commission || 1);
              }
            });
      }
    }

    function cancel(data) {
      show.value = false;
      setChangeValue(data);
      emit('update:localProps', {});
    }

    function confirm(func, transferData) {
      $q.dialog({
        title: 'Предупреждение!',
        message: 'Закрыть окно?',
        persistent: true,
        ok: {
          push: true,
          label: 'Закрыть',
        },
        cancel: {
          push: true,
          color: 'negative',
        },
      })
          .onOk(() => {
            func(transferData);
          })
          .onCancel(() => {
            func(transferData);
          });
    }

    function updateData(data) {
      const sendData = _.cloneDeep(data);
      if (!sendData.client_id.value) {
        showCodeDialog.value = true;
      } else if (_.isEmpty(props.localProps)) {
        // ДОБАВЛЕНИЕ ЗАПИСИ
        $q.loading.show();
        const values = _.mapValues(sendData, 'value');
        values.receiver_name = _.startCase(_.toLower(values.receiver_name));
        if (_.trim(values.issued_by)) {
          const date = reverseDate(values.issued_by);
          values.issued_by = formatISO(addTime(date));
        }
        axiosInstance.post(getUrl('storeTransfers'), values)
            .then(({ data: { transfer } }) => {
              cancel(props.transferData);
              $q.loading.hide();
              show.value = false;
              showNotif('success', `Запись клиента - ${_.get(transfer, 'client_name')} успешно добавлена.`);
            })
            .catch((errors) => {
              $q.loading.hide();
              errorsData.errors = _.get(errors, 'response.data.errors');
            });
      } else if (_.has(props.localProps, 'row.id')) {
        // ОБНОВЛЕНИЕ ЗАПИСИ
        if (_.some(sendData, 'changeValue')) {
          $q.loading.show();
          const dataToSend = _.reduce(sendData, (result, {
            value,
            changeValue,
          }, index) => {
            if (changeValue && index === 'issued_by') {
              if (value) {
                const date = reverseDate(value);
                result[index] = formatISO(addTime(date));
              } else {
                result[index] = value;
              }
            } else if (changeValue && index === 'receiver_name' && value) {
              result[index] = _.startCase(_.toLower(value));
            } else if (changeValue) {
              result[index] = value;
            }
            return result;
          }, {});
          _.assign(dataToSend, { id: _.get(props.localProps, 'row.id') });
          axiosInstance.post(getUrl('updateTransfers'), dataToSend)
              .then(({
                       data: {
                         transfer: {
                           client_name: clientName,
                         },
                       },
                     }) => {
                cancel(props.transferData);
                $q.loading.hide();
                show.value = false;
                emit('update:selected', []);
                showNotif('success', `Запись клиента - ${clientName} успешно обновлена.`);
              })
              .catch((errors) => {
                $q.loading.hide();
                errorsData.errors = _.get(errors, 'response.data.errors');
              });
        } else {
          show.value = false;
        }
      }
    }

    return {
      showCodeDialog,
      newCodeData,
      errorsData,
      transferHistoryComponent,
      checkErrors,
      dialogTitle,
      show,
      changeValueFunc,
      cancel,
      confirm,
      updateData,
    };
  },
});
</script>
