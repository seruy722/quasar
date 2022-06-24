<template>
  <DialogComponent
      v-model:dialog="show"
      title="Клиент"
      :persistent="true"
  >
    <Card
        data-vue-component-name="DialogAddClient"
        style="min-width: 320px;width: 100%;max-width: 500px;"
    >
      <CardSection class="row justify-between bg-grey q-mb-sm">
        <span class="text-h6">{{ dialogTitle }}</span>
        <div class="q-gutter-sm">
          <RoundBtn
              icon="delete"
              tooltip="Удалить"
              color="negative"
              @round-btn-click="destroyCustomer(entryData)"
          />

          <RoundBtn
              icon="clear"
              tooltip="Закрыть"
              color="negative"
              @round-btn-click="close(customerData)"
          />
        </div>
      </CardSection>

      <CardSection
          style="max-height: 70vh"
          class="scroll"
      >
        <div
            v-for="(item, index) in customerData"
            :key="index"
        >
          <SearchSelect
              v-if="item.type==='searchSelect'"
              v-model.number="item.value"
              v-model:change-value="item.changeValue"
              :label="item.label"
              :dense="$q.screen.xs || $q.screen.sm"
              :field="item.field"
              :options="item.options"
              :func-load-data="item.funcLoadData"
              :errors="errorsData"
          />

          <BaseSelect
              v-else-if="item.type==='select'"
              v-model.number="item.value"
              v-model:change-value="item.changeValue"
              :label="item.label"
              :dense="$q.screen.xs || $q.screen.sm"
              :field="item.field"
              :options="item.options"
              :errors="errorsData"
          />

          <BaseInput
              v-else
              v-model="item.value"
              v-model:change-value="item.changeValue"
              :label="item.label"
              :dense="$q.screen.xs || $q.screen.sm"
              :field="item.field"
              :mask="item.mask"
              :unmasked-value="item.unmaskedValue"
              :autofocus="item.autofocus"
              :errors="errorsData"
          >
            <template
                v-if="item.type==='text' && item.field==='telegram_user_id' && item.value"
                #append
            >
              <IconBtn
                  :disable="showSendTelegramTestMessageBtn"
                  dense
                  icon="send"
                  color="primary"
                  @icon-btn-click="sendTelegramTestMessage(item.value)"
              />
            </template>
          </BaseInput>

        </div>
      </CardSection>

      <Separator />
      <CardActions>
        <BaseBtn
            label="Отмена"
            color="negative"
            :dense="$q.screen.xs || $q.screen.sm"
            @click-base-btn="close(customerData)"
        />

        <BaseBtn
            label="Сохранить"
            color="positive"
            :dense="$q.screen.xs || $q.screen.sm"
            @click-base-btn="checkErrors(customerData, saveData)"
        />
      </CardActions>
    </Card>
  </DialogComponent>
</template>

<script>
import { getUrl } from 'src/tools/url';
import CheckErrorsMixin from 'src/mixins/CheckErrors';
import OnKeyUp from 'src/mixins/OnKeyUp';
import showNotif from 'src/mixins/showNotif';
import getFromSettings from 'src/tools/settings';
import {
  getClientCodes,
  setDefaultData,
  getCities,
  setChangeValue,
} from 'src/utils/FrequentlyCalledFunctions';
import BaseSelect from 'src/components/Elements/BaseSelect.vue';
import SearchSelect from 'src/components/Elements/SearchSelect.vue';
import DialogComponent from 'src/components/Dialogs/DialogComponent.vue';
import BaseBtn from 'src/components/Buttons/BaseBtn.vue';
import BaseInput from 'src/components/Elements/BaseInput.vue';
import Separator from 'src/components/Separator.vue';
import Card from 'src/components/Elements/Card/Card.vue';
import CardActions from 'src/components/Elements/Card/CardActions.vue';
import CardSection from 'src/components/Elements/Card/CardSection.vue';
import RoundBtn from 'src/components/Buttons/RoundBtn.vue';
import IconBtn from 'src/components/Buttons/IconBtn.vue';

export default {
  name: 'DialogAddClient',
  components: {
    BaseSelect,
    SearchSelect,
    DialogComponent,
    BaseBtn,
    BaseInput,
    Separator,
    Card,
    CardActions,
    CardSection,
    IconBtn,
    RoundBtn,
  },
  mixins: [OnKeyUp, showNotif, CheckErrorsMixin],
  props: {
    showDialog: {
      type: Boolean,
      default: false,
    },
    codeId: {
      type: Number,
      default: 0,
    },
    entryData: {
      type: Object,
      default: () => ({}),
    },
  },
  emits: ['update:codeId', 'update:entryData', 'update:showDialog'],
  data() {
    return {
      showSendTelegramTestMessageBtn: false,
      customers: [],
      show: false,
      customerData: {
        code_id: {
          type: 'searchSelect',
          label: 'Код',
          field: 'code_id',
          options: [],
          require: true,
          requireError: 'Поле обьзательное для заполнения.',
          funcLoadData: getClientCodes,
          changeValue: false,
          default: null,
          value: null,
        },
        name: {
          type: 'text',
          label: 'Имя',
          field: 'name',
          autofocus: true,
          rules: [
            {
              name: 'isLength',
              error: 'Минимальное количество символов 2.',
              options: {
                min: 2,
                max: 100,
              },
            },
          ],
          require: true,
          requireError: 'Поле обьзательное для заполнения.',
          changeValue: false,
          default: '',
          value: '',
        },
        phone: {
          type: 'text',
          label: 'Телефон',
          field: 'phone',
          mask: '+## (###) ###-##-##',
          unmaskedValue: true,
          rules: [
            {
              name: 'isLength',
              error: 'Минимальное количество символов 12.',
              options: {
                min: 12,
                max: 100,
              },
            },
          ],
          require: true,
          requireError: 'Поле обьзательное для заполнения.',
          changeValue: false,
          default: '',
          value: '',
        },
        city_id: {
          type: 'searchSelect',
          label: 'Город',
          field: 'city_id',
          options: [],
          require: false,
          requireError: 'Поле обьзательное для заполнения.',
          funcLoadData: getCities,
          changeValue: false,
          default: null,
          value: null,
        },
        sex: {
          type: 'select',
          label: 'Пол',
          field: 'sex',
          options: getFromSettings('sex'),
          require: false,
          requireError: 'Поле обьзательное для заполнения.',
          changeValue: false,
          default: 0,
          value: 0,
        },
        telegram_user_id: {
          type: 'text',
          label: 'Телеграм ID',
          field: 'telegram_user_id',
          require: false,
          requireError: 'Поле обьзательное для заполнения.',
          rules: [
            {
              name: 'isLength',
              error: 'Максимальное количество символов 50.',
              options: {
                min: 0,
                max: 50,
              },
            },
          ],
          changeValue: false,
          default: '',
          value: '',
        },
      },
    };
  },
  computed: {
    clientCodes() {
      return this.$store.getters['codes/getCodes'];
    },
    cities() {
      return this.$store.getters['cities/getCities'];
    },
    dialogTitle() {
      return _.isEmpty(this.entryData) ? 'Новый клиет' : 'Редактирование';
    },
  },
  watch: {
    entryData(val) {
      devlog.log('entryData', val);
      if (!_.isEmpty(val)) {
        _.forEach(this.customerData, (item, index) => {
          if (_.get(val, index)) {
            if (index === 'code_id' || index === 'city_id' || index === 'sex') {
              _.set(item, 'value', _.toNumber(_.get(val, index)));
            } else {
              _.set(item, 'value', _.get(val, index));
            }
          }
        });
      }
    },
    showDialog(val) {
      this.$q.loading.show();
      Promise.all([getClientCodes(this.$store), getCities(this.$store)])
          .then(() => {
            this.showAddEntryOnStorehouseDialog = true;
            this.show = val;
            this.$q.loading.hide();
          })
          .catch(() => {
            this.$q.loading.hide();
            devlog.warn('Ошибка при получении данных. Edit storehouseData');
          });
    },
    show(val) {
      this.$emit('update:showDialog', val);
    },
    clientCodes: {
      handler: function set(val) {
        _.set(this.customerData, 'code_id.options', val);
      },
      immediate: true,
    },
    cities: {
      handler: function set(val) {
        _.set(this.customerData, 'city_id.options', _.clone(val));
        this.customerData.city_id.options.unshift({
          label: 'Не выбрано',
          value: null,
        });
      },
      immediate: true,
    },
    codeId: {
      handler: function set(val) {
        devlog.log('CODE_ID', val);
        if (val) {
          _.set(this.customerData, 'code_id.value', val);
        }
      },
      immediate: true,
    },
  },
  methods: {
    saveData(values) {
      const createData = _.reduce(values, (result, { value }, index) => {
        if (index === 'name') {
          result[index] = _.startCase(value);
        } else {
          result[index] = value;
        }
        return result;
      }, {});
      if (_.isEmpty(this.entryData)) {
        // ДОБАВЛЕНИЕ ЗАПИСИ
        this.$q.loading.show();
        this.$axios.post(getUrl('storeCustomers'), createData)
            .then(({ data: { customer } }) => {
              this.$q.loading.hide();
              this.$store.dispatch('codes/addCustomerToCodeWithCustomers', customer);
              this.showNotif('success', 'Клиент успешно добавлен.', 'center');
            })
            .catch((errors) => {
              this.errorsData.errors = _.get(errors, 'response.data.errors');
              this.$q.loading.hide();
            });
      } else {
        // ОБНОВЛЕНИЕ ЗАПИСИ
        const updateData = _.reduce(values, (result, {
          value,
          changeValue,
        }, index) => {
          if (changeValue) {
            if (index === 'name') {
              result[index] = _.startCase(value);
            } else {
              result[index] = value;
            }
          }
          return result;
        }, {});
        if (!_.isEmpty(updateData)) {
          updateData.id = this.entryData.id;
          devlog.log('VALUIR', values);

          this.$q.loading.show();
          this.$axios.post(getUrl('updateCustomer'), updateData)
              .then(({ data: { customer } }) => {
                this.$q.loading.hide();
                this.$store.dispatch('codes/updateCustomer', customer);
                if (_.toNumber(this.entryData.code_id) !== _.toNumber(customer.code_id)) {
                  this.$store.dispatch('codes/deleteCustomerFromStore', {
                    id: this.entryData.id,
                    code_id: this.entryData.code_id,
                  });
                }
                setChangeValue(values);
                this.close(this.customerData);
                this.showNotif('success', 'Данные клиента успешно обновлены.', 'center');
              })
              .catch((errors) => {
                this.errorsData.errors = _.get(errors, 'response.data.errors');
                this.$q.loading.hide();
              });
        } else {
          this.showNotif('info', 'Нет измененных данных', 'center');
        }
      }
    },
    destroyCustomer({
                      id,
                      name,
                    }) {
      this.showNotif('warning', `Удалить клиента - ${name} ?`, 'center', [
        {
          label: 'Отмена',
          color: 'white',
          handler: () => {
          },
        },
        {
          label: 'Удалить',
          color: 'white',
          handler: async () => {
            await this.$store.dispatch('codes/deleteCustomer', {
              id,
              code_id: this.entryData.code_id,
            })
                .then(() => {
                  this.close(this.customerData);
                  this.showNotif('success', 'Запись успешно удалена.', 'center');
                })
                .catch(() => {
                  devlog.error('Ошибка запроса - destroyCustomerEntry');
                });
          },
        },
      ]);
    },
    close(data) {
      this.show = false;
      setDefaultData(data);
      setChangeValue(data);
      this.$emit('update:entryData', {});
      this.$emit('update:codeId', 0);
    },
    sendTelegramTestMessage(chatId) {
      if (_.toNumber(chatId)) {
        this.$axios.post(getUrl('sendTelegramTestMessage'), { message: 'Тестовое сообщение!', chatId });
        this.showSendTelegramTestMessageBtn = true;
        setTimeout(() => {
          this.showSendTelegramTestMessageBtn = false;
        }, 30 * 1000);
      }
    },
  },
};
</script>
