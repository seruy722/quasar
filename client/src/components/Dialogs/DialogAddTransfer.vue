<template>
  <div>
    <Dialog
      v-model:dialog="show"
      :persistent="true"
    >
      <q-card style="min-width: 320px;width: 100%;max-width: 500px;">
        <q-card-section class="row justify-between bg-grey q-mb-sm">
          <span class="text-h6">{{ dialogTitle }}</span>
          <div>
            <IconBtn
              v-if="localProps.row"
              dense
              icon="history"
              tooltip="История"
              @icon-btn-click="getTransfersHistory(localProps.row.id, localProps.cols)"
            />

            <IconBtn
              dense
              icon="save"
              tooltip="Сохранить"
              color="positive"
              @icon-btn-click="checkErrors(transferData, updateData)"
            />

            <IconBtn
              dense
              icon="clear"
              tooltip="Закрыть"
              color="negative"
              @icon-btn-click="confirm(cancel, transferData)"
            />
          </div>
        </q-card-section>
        <q-card-section>
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
    </Dialog>
    <DialogAddCode
      v-model:show-dialog="showCodeDialog"
      v-model:new-code-data="newCodeData"
    />
  </div>
</template>

<script>
import { getUrl } from 'src/tools/url';
import CheckErrorsMixin from 'src/mixins/CheckErrors';
import showNotif from 'src/mixins/showNotif';
import {
  prepareHistoryData,
  setChangeValue,
} from 'src/utils/FrequentlyCalledFunctions';
import Dialog from 'src/components/Dialogs/Dialog.vue';
import BaseBtn from 'src/components/Buttons/BaseBtn.vue';
import BaseInput from 'src/components/Elements/BaseInput.vue';
import Separator from 'src/components/Separator.vue';
import IconBtn from 'src/components/Buttons/IconBtn.vue';
import SearchSelect from 'src/components/Elements/SearchSelect.vue';
import BaseSelect from 'src/components/Elements/BaseSelect.vue';
import { addTime, reverseDate } from 'src/utils/formatDate';
import { formatISO } from 'date-fns';
import { defineAsyncComponent } from 'vue';

export default {
  name: 'DialogAddTransfer',
  components: {
    Dialog,
    BaseBtn,
    BaseInput,
    Separator,
    IconBtn,
    SearchSelect,
    BaseSelect,
    Date: defineAsyncComponent(() => import('components/Date.vue')),
    DialogAddCode: defineAsyncComponent(() => import('components/Dialogs/DialogAddCode.vue')),
  },
  mixins: [showNotif, CheckErrorsMixin],
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
  },
  emits: ['update:showDialog', 'update:localProps'],
  data() {
    return {
      showCodeDialog: false,
      newCodeData: {},
    };
  },
  computed: {
    dialogTitle() {
      return _.get(this.localProps, 'row.client_name') || 'Новый перевод';
    },
    show: {
      get: function get() {
        return this.showDialog;
      },
      set: function set(val) {
        this.$emit('update:showDialog', val);
      },
    },
  },
  watch: {
    newCodeData(val) {
      devlog.log('$refs', this.$refs);
      if (!_.isEmpty(val)) {
        _.set(this.transferData, 'client_id.value', val.value);
        _.set(this.transferData, 'client_id.changeValue', true);
      }
    },
  },
  methods: {
    cancel(data) {
      devlog.log('this.show', this.show);
      this.show = false;
      setChangeValue(data);
      this.$emit('update:localProps', {});
    },
    confirm(func, transferData) {
      this.$q.dialog({
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
        });
    },
    updateData(data) {
      const sendData = _.cloneDeep(data);
      devlog.log('DATA_N0', sendData);
      if (!sendData.client_id.value) {
        devlog.log('NEW_');
        this.showCodeDialog = true;
      } else if (_.isEmpty(this.localProps)) {
        // ДОБАВЛЕНИЕ ЗАПИСИ
        this.$q.loading.show();
        const values = _.mapValues(sendData, 'value');
        values.receiver_name = _.startCase(_.toLower(values.receiver_name));
        if (_.trim(values.issued_by)) {
          const date = reverseDate(values.issued_by);
          values.issued_by = formatISO(addTime(date));
        }
        this.$axios.post(getUrl('storeTransfers'), values)
          .then(({ data: { transfer } }) => {
            devlog.log('DDFR', transfer);
            devlog.log('this.$store', this.$store.dispatch);
            this.$store.dispatch('transfers/addTransfer', transfer);
            this.cancel(this.transferData);
            this.$q.loading.hide();
            this.show = false;
            // this.showNotif('success', `Запись клиента - ${_.get(transfer, 'client_name')} успешно добавлена.`, 'center');
          })
          .catch((errors) => {
            this.$q.loading.hide();
            this.errorsData.errors = _.get(errors, 'response.data.errors');
          });
      } else if (_.has(this.localProps, 'row.id')) {
        // ОБНОВЛЕНИЕ ЗАПИСИ
        if (_.some(sendData, 'changeValue')) {
          this.$q.loading.show();
          devlog.log('sendData_', sendData);
          const dataToSend = _.reduce(sendData, (result, {
            value,
            changeValue,
          }, index) => {
            if (changeValue && index === 'issued_by') {
              devlog.log('ISSUUE', value);
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
          _.assign(dataToSend, { id: _.get(this.localProps, 'row.id') });
          devlog.log('dataToSend', dataToSend);
          this.$axios.post(getUrl('updateTransfers'), dataToSend)
            .then(({ data: { transfer } }) => {
              devlog.log('DDFR', transfer);
              this.$store.dispatch('transfers/updateTransfer', transfer);
              this.cancel(this.transferData);
              this.$q.loading.hide();
              this.show = false;
              // this.showNotif('success', `Запись клиента - ${_.get(transfer, 'client_name')} успешно обновлена.`, 'center');
            })
            .catch((errors) => {
              this.$q.loading.hide();
              this.errorsData.errors = _.get(errors, 'response.data.errors');
            });
        } else {
          devlog.log('item_DDD_FFF', data);
        }
      }
    },
    async getTransfersHistory(transferID, cols) {
      this.$q.loading.show();
      await this.$axios.get(`${getUrl('transfersHistory')}/${transferID}`)
        .then(({ data: { transferHistory } }) => {
          if (!_.isEmpty(transferHistory)) {
            this.$q.loading.hide();
            this.dialogHistory = true;
            const historyData = prepareHistoryData(cols, transferHistory);
            historyData.historyData = this.setAdditionalData(historyData.historyData);
            this.transferHistoryData = historyData;
          } else {
            this.$q.loading.hide();
            this.showNotif('info', 'По этому переводу нет истории.', 'center');
          }
        })
        .catch(() => {
          devlog.error('Ошибка при получении данных истории.');
        });
    },
  },
};
</script>
