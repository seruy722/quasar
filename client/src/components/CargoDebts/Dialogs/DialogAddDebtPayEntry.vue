<template>
  <DialogComponent
      v-model:dialog="show"
      :persistent="true"
      title="Оплата"
      data-vue-component-name="DialogAddDebtPayEntry"
  >
    <q-card style="min-width: 320px;width: 100%;max-width: 900px;">
      <q-card-section class="row justify-between items-center bg-grey q-mb-sm">
        <span class="text-h6">Оплата записи</span>
        <q-list
            dense
        >
          <q-item>
            <!--            <q-item-section v-if="entryData.row">-->
            <!--              <q-item-label>-->
            <!--                <IconBtn-->
            <!--                  dense-->
            <!--                  icon="history"-->
            <!--                  tooltip="История"-->
            <!--                  @icon-btn-click="entryData.historyFunc(entryData.row.id, entryData.cols)"-->
            <!--                />-->
            <!--              </q-item-label>-->
            <!--            </q-item-section>-->
            <q-item-section>
              <q-item-label>
                <SquareBtn
                    icon="clear"
                    tooltip="Закрыть"
                    color="negative"
                    @square-btn-click="close(storehouseData)"
                />
              </q-item-label>
            </q-item-section>
          </q-item>
        </q-list>
      </q-card-section>
      <q-card-section>
        <div class="fit row wrap justify-start items-start content-start">
          <div
              v-for="(item, index) in storehouseData"
              :key="index"
              class="col-xs-12 col-sm-4 col-md-4 col-lg-4 q-pt-md q-px-sm bg-white"
          >
            <BaseInput
                v-if="item.type === 'text'"
                v-model.trim="item.value"
                v-model:change-value="item.changeValue"
                :label="item.label"
                :type="item.type"
                :mask="item.mask"
                :dense="$q.screen.xs || $q.screen.sm"
                :field="index"
                :readonly="item.readonly"
                :disable="item.disable"
                :errors="errorsData"
            />

            <BaseInput
                v-else-if="item.type === 'number'"
                v-model.number="item.value"
                v-model:change-value="item.changeValue"
                :autofocus="item.autofocus"
                :label="item.label"
                :type="item.type"
                :mask="item.mask"
                :dense="$q.screen.xs || $q.screen.sm"
                :field="index"
                :disable="item.disable"
                :errors="errorsData"
            />

            <SelectChips
                v-else-if="item.type === 'select-chips'"
                v-model="item.value"
                v-model:change-value="item.changeValue"
                :label="item.label"
                :field="index"
                :dense="$q.screen.xs || $q.screen.sm"
                :options="item.options"
                :func-load-data="item.funcLoadData"
                :errors="errorsData"
            />
            <BaseInput
                v-else-if="item.type === 'date'"
                v-model.trim="item.value"
                v-model:change-value="item.changeValue"
                :label="item.label"
                :field="index"
                :mask="item.mask"
                :dense="$q.screen.xs || $q.screen.sm"
                :errors="errorsData"
            >
              <template #append>
                <DateComponent
                    v-model:value="item.value"
                    v-model:change-value="item.changeValue"
                />
              </template>
            </BaseInput>

            <SearchSelect
                v-else
                v-model="item.value"
                v-model:change-value="item.changeValue"
                :label="item.label"
                :field="index"
                :dense="$q.screen.xs || $q.screen.sm"
                :options="item.options"
                :func-load-data="item.funcLoadData"
                :errors="errorsData"
            />
          </div>
        </div>
      </q-card-section>
      <Separator />
      <q-card-actions align="right">
        <BaseBtn
            :label="$t('save')"
            color="positive"
            :size="size"
            @click-base-btn="checkErrors(storehouseData, saveData)"
        />

        <BaseBtn
            :label="$t('clear')"
            color="warning"
            :size="size"
            @click-base-btn="clear(storehouseData)"
        />

        <BaseBtn
            :label="$t('close')"
            color="negative"
            :size="size"
            @click-base-btn="close(storehouseData)"
        />
      </q-card-actions>
    </q-card>
  </DialogComponent>
</template>

<script>
import CheckErrorsMixin from 'src/mixins/CheckErrors';
import showNotif from 'src/mixins/showNotif';
import { getClientCodes } from 'src/utils/FrequentlyCalledFunctions';
import DialogComponent from 'src/components/Dialogs/DialogComponent.vue';
import IconBtn from 'src/components/Buttons/IconBtn.vue';
import BaseInput from 'src/components/Elements/BaseInput.vue';
import SearchSelect from 'src/components/Elements/SearchSelect.vue';
import BaseBtn from 'src/components/Buttons/BaseBtn.vue';
import Separator from 'src/components/Separator.vue';
import SelectChips from 'src/components/Elements/SelectChips.vue';
import DateComponent from 'src/components/Date.vue';
import SquareBtn from 'src/components/Buttons/SquareBtn.vue';

export default {
  name: 'DialogAddDebtPayEntry',
  components: {
    DialogComponent,
    IconBtn,
    BaseInput,
    SearchSelect,
    BaseBtn,
    Separator,
    SelectChips,
    DateComponent,
    SquareBtn,
  },
  mixins: [CheckErrorsMixin, showNotif],
  props: {
    showDialog: {
      type: Boolean,
      default: false,
    },
    entryData: {
      type: Object,
      default: () => ({}),
    },
  },
  emits: ['update:entryData', 'update:showDialog'],
  data() {
    return {
      show: false,
      storehouseData: {
        created_at: {
          name: 'created_at',
          type: 'date',
          label: 'Дата',
          require: true,
          requireError: 'Поле обьзательное для заполнения.',
          changeValue: false,
          default: new Date().toISOString()
              .slice(0, 10)
              .split('-')
              .reverse()
              .join('-'),
          value: new Date().toISOString()
              .slice(0, 10)
              .split('-')
              .reverse()
              .join('-'),
        },
        code_client_id: {
          name: 'code_client_id',
          type: 'select',
          label: 'Клиент',
          options: [],
          require: true,
          requireError: 'Выберите значение.',
          changeValue: false,
          funcLoadData: getClientCodes,
          default: null,
          value: null,
        },
        sum: {
          name: 'sum',
          type: 'number',
          label: 'Сумма',
          changeValue: false,
          autofocus: true,
          default: null,
          value: null,
        },
        commission: {
          name: 'commission',
          type: 'number',
          label: 'Комиссия',
          changeValue: false,
          default: null,
          value: null,
        },
        notation: {
          name: 'notation',
          type: 'text',
          label: 'Примечания',
          changeValue: false,
          default: '',
          value: '',
        },
        get_pay_user_id: {
          name: 'get_pay_user_id',
          type: 'select',
          label: 'Пользователь',
          options: [],
          changeValue: false,
          funcLoadData: getClientCodes,
          default: 0,
          value: 0,
        },
      },
    };
  },
  computed: {
    usersList() {
      return this.$store.getters['auth/getUsersList'];
    },
    clientCodes() {
      return this.$store.getters['codes/getCodes'];
    },
    size() {
      const {
        sm,
        xs,
        md,
        lg,
      } = this.$q.screen;

      let size = '';
      if (sm) {
        size = 'sm';
      } else if (xs) {
        size = 'xs';
      } else if (md) {
        size = 'md';
      } else if (lg) {
        size = 'lg';
      }
      return size;
    },
    settingsDate() {
      return this.$store.getters['settings/getDateForSaveData'];
    },
  },
  watch: {
    entryData(val) {
      if (!_.isEmpty(val)) {
        devlog.log('vALIN_ADD', val);
        _.forEach(this.storehouseData, (item, index) => {
          const prop = _.get(this.entryData, index);
          if (index === 'sum' || index === 'commission') {
            _.set(item, 'value', prop * -1);
          } else if (index !== 'created_at' && index !== 'notation') {
            _.set(item, 'value', prop);
          }
        });
      }
    },
    clientCodes: {
      handler: function set(val) {
        this.storehouseData.code_client_id.options = val;
      },
      immediate: true,
    },
    usersList: {
      handler: function set(val) {
        this.storehouseData.get_pay_user_id.options = val;
      },
      immediate: true,
    },
    showDialog(val) {
      this.storehouseData.code_client_id.changeValue = true;
      this.storehouseData.sum.changeValue = true;
      this.storehouseData.commission.changeValue = true;
      if (_.isEmpty(this.usersList)) {
        this.$store.dispatch('auth/fetchUsersList');
      }
      this.show = val;
    },
    show(val) {
      this.$emit('update:showDialog', val);
      if (val && this.settingsDate) {
        this.storehouseData.created_at.value = this.settingsDate;
        this.storehouseData.created_at.changeValue = true;
      }
    },
  },
  methods: {
    async saveData() {
      const sendData = _.reduce(this.storehouseData, (result, {
        changeValue,
        value,
      }, index) => {
        if (changeValue) {
          result[index] = value;
        }
        return result;
      }, {});
      if (_.has(sendData, 'created_at')) {
        const {
          reverseDate,
          addTime,
        } = await import('src/utils/formatDate');
        sendData.created_at = addTime(reverseDate(sendData.created_at))
            .toISOString();
      }
      const { getUrl } = await import('src/tools/url');
      sendData.entry_id = this.entryData.id;
      this.$q.loading.show();
      this.$axios.post(getUrl('debtPayEntry'), sendData)
          .then(({ data: { answer } }) => {
            devlog.log(answer);
            _.forEach(answer, (item) => {
              this.$store.dispatch('cargoDebts/updateOrAddDebtEntry', item);
            });
            this.$q.loading.hide();
            this.showNotif('success', 'Запись успешно добавлена.', false);
            this.close(this.storehouseData);
          })
          .catch((errors) => {
            this.errorsData.errors = _.get(errors, 'response.data.errors');
            this.$q.loading.hide();
          });
      devlog.log('DATA_TO_SAVE', sendData);
    },
    clear(data) {
      _.forEach(data, (item) => {
        item.value = item.default;
      });
    },
    close(data) {
      this.clear(data);
      this.show = false;
      if (!_.isEmpty(this.entryData)) {
        _.set(this.entryData, 'selected', false);
        this.$emit('update:entryData', {});
      }
    },
  },
};
</script>
