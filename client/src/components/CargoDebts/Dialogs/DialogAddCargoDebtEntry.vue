<template>
  <Dialog
      v-model:dialog="show"
      :persistent="true"
      title="Долг"
      data-vue-component-name="DialogAddCargoDebtEntry"
  >
    <q-card style="min-width: 320px;width: 100%;max-width: 900px;">
      <q-card-section class="row justify-between items-center bg-grey q-mb-sm">
        <span class="text-h6">{{ entryData.row ? 'Редактирование долга' : 'Новая запись долга' }}</span>
        <q-list
            dense
        >
          <q-item>
            <q-item-section v-if="entryData.row">
              <q-item-label>
                <RoundBtn
                    dense
                    icon="history"
                    tooltip="История"
                    @round-btn-click="entryData.historyFunc(entryData.row.id, entryData.cols)"
                />
              </q-item-label>
            </q-item-section>
            <q-item-section>
              <q-item-label>
                <Menu :items="['Код', 'Клиента', 'Категорию']" />
              </q-item-label>
            </q-item-section>
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
                :label="item.label"
                :autofocus="item.autofocus"
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
  </Dialog>
</template>

<script>
import CheckErrorsMixin from 'src/mixins/CheckErrors';
import showNotif from 'src/mixins/showNotif';
import {
  getClientCodes,
  getCategories,
  getFaxes,
} from 'src/utils/FrequentlyCalledFunctions';
import Dialog from 'src/components/Dialogs/Dialog.vue';
import RoundBtn from 'src/components/Buttons/RoundBtn.vue';
import SquareBtn from 'src/components/Buttons/SquareBtn.vue';
import BaseInput from 'src/components/Elements/BaseInput.vue';
import SearchSelect from 'src/components/Elements/SearchSelect.vue';
import BaseBtn from 'src/components/Buttons/BaseBtn.vue';
import Separator from 'src/components/Separator.vue';
import SelectChips from 'src/components/Elements/SelectChips.vue';
import Menu from 'src/components/Menu.vue';
import DateComponent from 'src/components/Date.vue';

export default {
  name: 'DialogAddCargoDebtEntry',
  components: {
    Dialog,
    RoundBtn,
    SquareBtn,
    BaseInput,
    SearchSelect,
    BaseBtn,
    Separator,
    SelectChips,
    Menu,
    DateComponent,
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
        place: {
          name: 'place',
          type: 'number',
          label: 'Мест',
          changeValue: false,
          default: null,
          value: null,
        },
        kg: {
          name: 'kg',
          type: 'number',
          label: 'Вес',
          changeValue: false,
          default: null,
          value: null,
        },
        for_kg: {
          name: 'for_kg',
          type: 'number',
          label: 'За кг',
          changeValue: false,
          default: null,
          value: null,
        },
        for_place: {
          name: 'for_place',
          type: 'number',
          label: 'За кг',
          changeValue: false,
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
        // sale: {
        //     name: 'sale',
        //     type: 'number',
        //     label: 'Скидка',
        //     changeValue: false,
        //     default: 0,
        //     value: 0,
        // },
        paid: {
          name: 'paid',
          type: 'select',
          label: 'Оплачен',
          changeValue: false,
          options: [{
            label: 'Нет',
            value: 0,
          }, {
            label: 'Да',
            value: 1,
          }],
          default: 0,
          value: 0,
        },
        category_id: {
          type: 'select',
          label: 'Категория',
          field: 'category_id',
          options: [],
          funcLoadData: getCategories,
          changeValue: false,
          default: null,
          value: null,
        },
        fax_id: {
          type: 'select',
          label: 'Факс',
          field: 'fax_id',
          options: [],
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
        in_cargo: {
          name: 'in_cargo',
          type: 'select',
          label: 'Доставлен',
          changeValue: false,
          options: [{
            label: 'Да',
            value: 1,
          }, {
            label: 'Нет',
            value: 0,
          }],
          default: 1,
          value: 1,
        },
      },
    };
  },
  computed: {
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
    currentClientCodeId() {
      return this.$store.getters['cargoDebts/getCurrentCodeClientId'];
    },
    categories() {
      return this.$store.getters['category/getCategories'];
    },
    faxes() {
      return _.map(this.$store.getters['faxes/getFaxes'], ({
                                                             name,
                                                             id,
                                                           }) => ({
        label: name,
        value: id,
      }));
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
          const prop = _.get(this.entryData, `row[${index}]`);
          if (index === 'created_at') {
            _.set(item, 'value', prop.slice(0, 10));
          } else if (prop) {
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
    showDialog(val) {
      if (val) {
        this.$q.loading.show();
        Promise.all([getCategories(this.$store), getFaxes(this.$store)])
            .then(() => {
              this.storehouseData.code_client_id.value = this.currentClientCodeId;
              this.storehouseData.category_id.options = this.categories;
              this.storehouseData.fax_id.options = this.faxes;
              this.show = val;
              this.$q.loading.hide();
            });
      }
    },
    show(val) {
      this.$emit('update:showDialog', val);
      if (val && this.settingsDate && _.isEmpty(this.entryData)) {
        this.storehouseData.created_at.value = this.settingsDate;
        this.storehouseData.created_at.changeValue = true;
      }
    },
  },
  methods: {
    async saveData() {
      devlog.log('saveData');
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
      const { setChangeValue } = await import('src/utils/FrequentlyCalledFunctions');
      if (!this.entryData.row && !_.isEmpty(sendData)) {
        // CREATE
        sendData.code_client_id = this.storehouseData.code_client_id.value;
        this.$q.loading.show();
        this.$axios.post(getUrl('createCargoDebtEntry'), sendData)
            .then(({ data: { answer } }) => {
              this.$store.dispatch('cargoDebts/addCargoEntry', answer);
              this.$q.loading.hide();
              this.showNotif('success', 'Запись успешно добавлена.', false);
              this.close(this.storehouseData);
            })
            .catch((errors) => {
              this.errorsData.errors = _.get(errors, 'response.data.errors');
              this.$q.loading.hide();
            });
      } else if (!_.isEmpty(sendData)) {
        // UPDATE
        if (_.get(this.entryData, 'row.arr') && _.size(_.get(this.entryData, 'row.arr')) === 1) {
          sendData.id = _.get(_.first(_.get(this.entryData, 'row.arr')), 'id');
        } else {
          sendData.id = _.get(this.entryData, 'row.id');
        }
        this.$q.loading.show();
        this.$axios.post(getUrl('updateCargoDebtEntry'), sendData)
            .then(({ data: { answer } }) => {
              devlog.log('DTA_UPDATE', answer);
              this.$store.dispatch('cargoDebts/updateCargoEntry', answer);
              setChangeValue(this.storehouseData);
              this.$q.loading.hide();
              this.showNotif('success', 'Запись успешно обновлена.', false);
              this.close(this.storehouseData);
            })
            .catch((errors) => {
              this.errorsData.errors = _.get(errors, 'response.data.errors');
              this.$q.loading.hide();
            });
      } else {
        this.close(this.storehouseData);
      }
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
