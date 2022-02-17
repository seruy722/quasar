<template>
  <q-dialog
      v-model="dialog"
      persistent
      :maximized="maximizedToggle"
      transition-show="slide-up"
      transition-hide="slide-down"
      data-vue-component-name="DialogSendSms"
  >
    <q-card>
      <q-bar>
        <q-space />

        <q-btn
            dense
            flat
            icon="minimize"
            :disable="!maximizedToggle"
            @click="maximizedToggle = false"
        >
          <q-tooltip
              v-if="maximizedToggle"
              content-class="bg-white text-primary"
          >
            Minimize
          </q-tooltip>
        </q-btn>
        <q-btn
            dense
            flat
            icon="crop_square"
            :disable="maximizedToggle"
            @click="maximizedToggle = true"
        >
          <q-tooltip
              v-if="!maximizedToggle"
              content-class="bg-white text-primary"
          >
            Maximize
          </q-tooltip>
        </q-btn>
        <q-btn
            v-close-popup
            dense
            flat
            icon="close"
        >
          <q-tooltip content-class="bg-white text-primary">
            Close
          </q-tooltip>
        </q-btn>
      </q-bar>

      <div>
        Тариф: {{ rate }} | Баланс :
        <q-badge :color="balance > 0 ? 'positive' : 'negative'">
          {{ balance }}
        </q-badge>
        |
        Остаток сообщений: {{ Math.round(balance / rate) }}
      </div>

      <q-card-section>
        <Table
            :table-properties="faxTableProperties"
            :table-data="faxTableData"
            :table-reactive-properties="faxTableReactiveProperties"
            :title="fax.name"
        >
          <!--          <template #top-buttons>-->
          <!--            <IconBtn-->
          <!--              color="positive"-->
          <!--              icon="explicit"-->
          <!--              tooltip="excel"-->
          <!--              @icon-btn-click="exportFaxData(faxTableReactiveProperties.selected)"-->
          <!--            />-->
          <!--          </template>-->

          <!--ОТОБРАЖЕНИЕ КОНТЕНТА НА МАЛЕНЬКИХ ЭКРАНАХ-->
          <!--          <template #inner-item="{props}">-->
          <!--            <div-->
          <!--              class="q-pa-xs col-xs-12 col-sm-6 col-md-4 col-lg-3 grid-style-transition"-->
          <!--              :style="props.selected ? 'transform: scale(0.95);' : ''"-->
          <!--            >-->
          <!--              <q-expansion-item-->
          <!--                expand-separator-->
          <!--                class="shadow-1 overflow-hidden"-->
          <!--                header-class="bg-secondary text-white"-->
          <!--                style="border-radius: 30px;border: 1px solid #26A69A;"-->
          <!--                expand-icon-class="text-white"-->
          <!--              >-->
          <!--                <template #header>-->
          <!--                  <q-item-section avatar>-->
          <!--                    <q-checkbox-->
          <!--                      v-model="props.selected"-->
          <!--                      dense-->
          <!--                    />-->
          <!--                  </q-item-section>-->

          <!--                  <q-item-section>-->
          <!--                    <q-item-label :lines="2">-->
          <!--                      {{ props.row.code_client_name }}-->
          <!--                    </q-item-label>-->
          <!--                  </q-item-section>-->
          <!--                </template>-->

          <!--                <q-list-->
          <!--                  separator-->
          <!--                  dense-->
          <!--                >-->
          <!--                  <q-item-->
          <!--                    v-for="col in props.cols.filter(col => col.name !== 'desc')"-->
          <!--                    :key="col.name"-->
          <!--                  >-->
          <!--                    <q-item-section>-->
          <!--                      <q-item-label>{{ `${col.label}:` }}</q-item-label>-->
          <!--                    </q-item-section>-->
          <!--                    <q-item-section side>-->
          <!--                      <q-item-label-->
          <!--                        v-if="col.field === 'things'"-->
          <!--                        :lines="10"-->
          <!--                      >-->
          <!--                        {{ thingsFilter(col.value) }}-->
          <!--                      </q-item-label>-->
          <!--                      <q-item-label-->
          <!--                        v-else-if="col.field === 'kg'"-->
          <!--                      >-->
          <!--                        {{ numberFormat(col.value) }}-->
          <!--                      </q-item-label>-->
          <!--                      <q-item-label-->
          <!--                        v-else-if="col.field === 'notation'"-->
          <!--                        :lines="4"-->
          <!--                      >-->
          <!--                        {{ col.value }}-->
          <!--                      </q-item-label>-->
          <!--                      <q-item-label v-else>-->
          <!--                        {{ col.value }}-->
          <!--                      </q-item-label>-->
          <!--                    </q-item-section>-->
          <!--                  </q-item>-->
          <!--                  <q-item>-->
          <!--                    <q-item-section>-->
          <!--                      <BaseBtn-->
          <!--                        label="История"-->
          <!--                        color="info"-->
          <!--                        style="max-width: 100px;margin: 0 auto;"-->
          <!--                      />-->
          <!--                    </q-item-section>-->
          <!--                  </q-item>-->
          <!--                </q-list>-->
          <!--              </q-expansion-item>-->
          <!--            </div>-->
          <!--          </template>-->

          <template #inner-body="{props}">
            <q-tr
                :props="props"
                :class="{table__tr_bold_text: props.row.brand}"
            >
              <q-td auto-width>
                <q-checkbox
                    v-model="props.selected"
                    dense
                />
              </q-td>

              <q-td
                  key="code_client_name"
                  :props="props"
              >
                {{ props.row.code_client_name }}
              </q-td>

              <q-td
                  key="place"
                  :props="props"
              >
                {{ props.row.place }}
              </q-td>

              <q-td
                  key="kg"
                  :props="props"
              >
                {{ numberFormat(props.row.kg) }}
              </q-td>

              <q-td
                  key="for_kg"
                  class="text-bold text-red"
                  :props="props"
              >
                {{ numberFormat(props.row.for_kg) }}
              </q-td>

              <q-td
                  key="for_place"
                  class="text-bold text-red"
                  :props="props"
              >
                {{ numberFormat(props.row.for_place) }}
              </q-td>

              <q-td
                  key="sum"
                  class="text-bold"
                  :props="props"
              >
                {{ props.row.sum }}
              </q-td>

              <q-td
                  key="category_name"
                  :props="props"
              >
                {{ props.row.category_name }}
              </q-td>

              <q-td
                  key="text"
                  :props="props"
              >
                <q-input
                    v-model="props.row.text"
                    :maxlength="maxLength"
                    counter
                    dense
                />
              </q-td>

              <q-td
                  key="status"
                  :props="props"
              >
                <q-badge
                    v-show="props.row.status"
                    :color="props.row.status === 'OK' ? 'positive' : 'negative'"
                >
                  {{ props.row.status }}
                </q-badge>
              </q-td>

              <q-td
                  key="phones"
                  :props="props"
              >
                <q-input
                    v-if="!Array.isArray(props.row.selectedPhones)"
                    v-model="props.row.selectedPhones"
                    type="tel"
                    lazy-rules
                    mask="+38 (###)-###-##-##"
                    filled
                    label="Номер телефона"
                    :rules="[ val => val && val.length > 0 || 'Введите номер телефона', val => val && val.length === 19 || 'Некоректный номер телефона']"
                />
                <q-option-group
                    v-model="props.row.selectedPhones"
                    :options="props.row.phones"
                    color="green"
                    type="checkbox"
                />
              </q-td>
            </q-tr>
          </template>
        </Table>
      </q-card-section>
      <q-card-section v-if="faxTableData.length">
        <q-btn-group outline spread>
          <q-btn
              v-for="(item,index) in options"
              :key="index"
              outline
              :color="item.color"
              :label="item.label"
              @click="addText(item.value)"
          />
        </q-btn-group>
        <q-input
            v-model="text"
            filled
            counter
            label="Сообщение"
            type="textarea"
            :maxlength="maxLength"
            :hint="`Максимальное количество символов ${maxLength}`"
        />
      </q-card-section>

      <q-card-actions
          v-if="faxTableData.length"
          align="right"
      >
        <q-btn
            :disable="!text"
            label="Просмотр"
            color="primary"
            @click="viewSms(text, faxTableData, sendSmsDialogData, options)"
        />

        <q-btn
            :disable="!text"
            label="Отправить"
            color="positive"
            @click="confirmDialog('Отправки сообщений','Отправить сообщение клиентам?')"
        />
      </q-card-actions>
      <q-card-section>
        <q-btn
            label="Отчет"
            color="primary"
            @click="getArchiveSms(fax.id)"
        />
      </q-card-section>
      <q-card-section>
        <q-list bordered separator class="rounded-borders">
          <q-item>
            <q-item-section>
              <q-item-label>Дата отправки</q-item-label>
            </q-item-section>
            <q-item-section>
              <q-item-label>Количество смс</q-item-label>
            </q-item-section>
            <q-item-section>
              <q-item-label>Отправитель</q-item-label>
            </q-item-section>
          </q-item>
          <q-expansion-item
              v-for="(item, index) in archive"
              :key="index"
          >
            <template #header>
              <q-item-section>
                {{ fullDate(item[0].created_at) }}
              </q-item-section>

              <q-item-section>
                {{ item.length }}
              </q-item-section>
              <q-item-section>
                {{ item[0].user_name }}
              </q-item-section>
            </template>

            <q-card>
              <q-card-section>
                <q-list bordered separator>
                  <q-item>
                    <q-item-section>
                      <q-item-label>Клиент</q-item-label>
                    </q-item-section>
                    <q-item-section>
                      <q-item-label>Телефон</q-item-label>
                    </q-item-section>
                    <q-item-section>
                      <q-item-label>СМС</q-item-label>
                    </q-item-section>
                    <q-item-section>
                      <q-item-label>Статус</q-item-label>
                    </q-item-section>
                  </q-item>
                  <q-item
                      v-for="(elem, i) in item"
                      :key="i"
                  >
                    <q-item-section>
                      <q-item-label>{{ elem.result.req.clientData.code_client_name }}</q-item-label>
                    </q-item-section>
                    <q-item-section>
                      <q-item-label>{{ elem.result.req.recipients[0] }}</q-item-label>
                    </q-item-section>
                    <q-item-section>
                      <q-item-label>{{ elem.result.req.sms.text }}</q-item-label>
                    </q-item-section>
                    <q-item-section>
                      <q-item-label>
                        {{ elem.result.res.response_code === 801 ? 'Отправлено' : 'Не отправлено' }}
                      </q-item-label>
                    </q-item-section>
                  </q-item>
                </q-list>
              </q-card-section>
            </q-card>
          </q-expansion-item>
        </q-list>
      </q-card-section>
    </q-card>
  </q-dialog>
</template>

<script>
import { getUrl } from 'src/tools/url';
import { fullDate } from 'src/utils/formatDate';
import { thingsFilter, numberFormat } from 'src/utils';
import Table from 'src/components/Elements/Table/Table.vue';
import showNotif from 'src/mixins/showNotif';
import { uid } from 'quasar';

export default {
  name: 'DialogSendSms',
  components: {
    Table,
  },
  mixins: [showNotif],
  props: {
    show: {
      type: Boolean,
      default: false,
    },
    values: {
      type: Array,
      default: () => ([]),
    },
    fax: {
      type: Object,
      default: () => ({}),
    },
  },
  emits: ['update:show'],
  data() {
    return {
      status: false,
      archive: {},
      maxLength: 70,
      rate: 0.48,
      balance: null,
      group: [],
      options: [
        {
          label: 'Мест',
          value: '{place}',
          field: 'place',
          text: 'мест',
        },
        {
          label: 'Вес',
          value: '{kg}',
          field: 'kg',
          // color: 'green',
          text: 'вес',
        },
        {
          label: 'Сумма',
          value: '{sum}',
          field: 'sum',
          // color: 'red',
          text: '',
        },
        {
          label: 'Категория',
          value: '{category_name}',
          field: 'category_name',
          // color: 'red',
          text: '',
        },
      ],
      sendSmsDialogData: [],
      text: '',
      maximizedToggle: true,
      faxTableData: [],
      faxTableProperties: {
        columns: [
          {
            name: 'code_client_name',
            label: 'Клиент',
            align: 'center',
            field: 'code_client_name',
            sortable: true,
          },
          {
            name: 'place',
            label: 'Мест',
            field: 'place',
            align: 'center',
            sortable: true,
          },
          {
            name: 'kg',
            label: 'Вес',
            field: 'kg',
            align: 'center',
            sortable: true,
          },
          {
            name: 'for_kg',
            label: 'За кг',
            field: 'for_kg',
            align: 'center',
            sortable: true,
          },
          {
            name: 'for_place',
            label: 'За место',
            field: 'for_place',
            align: 'center',
            sortable: true,
          },
          {
            name: 'sum',
            label: 'Сумма',
            field: 'sum',
            align: 'center',
            sortable: true,
          },
          {
            name: 'category_name',
            label: 'Категория',
            field: 'category_name',
            align: 'center',
            sortable: true,
          },
          {
            name: 'text',
            label: 'Сообщение',
            field: 'sms',
            align: 'center',
          },
          {
            name: 'status',
            label: 'Статус',
            field: 'status',
            align: 'center',
          },
          {
            name: 'phones',
            label: 'Телефоны',
            field: 'phones',
            align: 'center',
          },
        ],
      },
      faxTableReactiveProperties: {
        selected: [],
        visibleColumns: ['code_client_name', 'place', 'kg', 'for_kg', 'for_place', 'category_name', 'sum', 'text', 'status', 'phones'],
        title: '',
      },
    };
  },
  computed: {
    dialog: {
      set: function set(val) {
        this.$emit('update:show', val);
      },
      get: function get() {
        return this.show;
      },
    },
    clientCodes() {
      return this.$store.getters['codes/getCodes'];
    },
  },
  watch: {
    show(val) {
      if (val && _.isEmpty(this.sendSmsDialogData)) {
        this.getCustomersPhones(this.values, this.faxTableReactiveProperties.selected);
        this.getBalance();
      }
    },
  },
  methods: {
    fullDate,
    getArchiveSms(id) {
      this.$axios.get(`${getUrl('getArchiveSms')}/${id}`)
          .then(({ data: { archives } }) => {
            devlog.log('archives', archives);
            _.forEach(archives, (item) => {
              _.forEach(item, (elem) => {
                elem.result = JSON.parse(elem.result);
              });
            });
            this.archive = archives;
          });
    },
    addText(value) {
      const textLength = this.text.length;
      const valueLength = value.length;
      if (valueLength + textLength <= this.maxLength) {
        this.text = `${this.text}${value}`;
      }
    },
    async getBalance() {
      return await this.$axios.get(getUrl('getSmsBalance'))
          .then(({ data: { response_result: { balance } } }) => {
            devlog.log('balance', balance);
            this.balance = balance;
            return balance;
          });
    },
    thingsFilter,
    numberFormat,
    sendSms(values) {
      devlog.log('VALSD', values);
      const sendData = [];
      // const sendData = [{
      //   recipients: ['380508842290'],
      //   sms: {
      //     sender: 'Cargo007',
      //     text: 'TEXT',
      //   },
      //   clientData: { code_client_name: '2555' }
      // },
      //   {
      //     recipients: ['380977376062'],
      //     sms: {
      //       sender: 'Cargo007',
      //       text: 'TEXT2',
      //     },
      //     clientData: { code_client_name: '2555' }
      //   }];

      _.forEach(values, (item) => {
        if (!_.isEmpty(item.selectedPhones)) {
          sendData.push({
            recipients: _.isArray(item.selectedPhones) ? item.selectedPhones : _.toString(parseInt(item.selectedPhones.replace(/[^\d]/g, ''), 10)),
            clientData: item,
            sms: {
              sender: 'Cargo007',
              text: item.text,
            },
          });
        }
      });

      devlog.log('sendData', sendData);

      this.getBalance()
          .then((balance) => {
            if (balance > _.sum(_.map(sendData, (elem) => _.size(elem.recipients))) * this.rate) {
              this.$axios.post(getUrl('sendSms'), {
                sendData,
                faxId: this.fax.id,
                uid: uid(),
              })
                  .then(({ data }) => {
                    this.text = '';
                    _.forEach(this.faxTableData, (elem) => {
                      elem.text = '';
                    });
                    this.getBalance();
                    devlog.log('data.response_result', data);
                    _.forEach(data.response_result, (elem) => {
                      const find = _.find(this.faxTableData, (item) => _.includes(item.selectedPhones, _.first(elem.req.recipients)));
                      if (find) {
                        find.status = elem.res.response_status;
                      }
                      devlog.log('find', find);
                    });
                    this.showNotif('success', 'Сообщения успешно отправлены!', 'center');
                  });
            } else {
              this.showNotif('info', 'Недостаточно средств', 'center');
            }
          });
    },
    confirmDialog(title, message) {
      this.$q.dialog({
        title,
        message,
        cancel: true,
        persistent: true
      })
          .onOk(() => {
            this.viewSms(this.text, this.faxTableData, this.sendSmsDialogData, this.options);
            this.sendSms(this.faxTableReactiveProperties.selected.length ? this.faxTableReactiveProperties.selected : this.faxTableData);
          })
          .onCancel(() => {
            // console.log('>>>> Cancel')
          })
          .onDismiss(() => {
            // console.log('I am triggered on both OK and Cancel')
          });
    },
    sum(data) {
      return _.round((_.get(data, 'for_kg') * _.get(data, 'kg')) + (_.get(data, 'for_place') * _.get(data, 'place')));
    },
    getCustomersPhones(allData, selected) {
      const list = _.isEmpty(selected) ? allData : selected;
      const codeIds = _.uniq(_.map(list, 'code_client_id'));
      devlog.log('codeIds', codeIds);
      this.$axios.post(getUrl('codesIds'), { ids: codeIds })
          .then(({ data: { codesData } }) => {
            this.sendSmsDialogData = codesData;
            this.faxTableData = _.cloneDeep(this.values);
            this.setSmsObj(this.faxTableData, codesData);
          });
    },
    viewSms(text, data, phonesData, options) {
      const cloneData = _.cloneDeep(data);
      _.forEach(cloneData, (item) => {
        let cloneText = _.clone(text);
        _.forEach(options, (elem) => {
          cloneText = cloneText.replaceAll(elem.value, item[elem.field]);
        });

        _.set(item, 'text', cloneText);
      });
      this.faxTableData = cloneData;
    },
    setSmsObj(data, phonesData) {
      const cloneData = _.cloneDeep(data);
      _.forEach(cloneData, (item) => {
        const findPhonesData = _.find(phonesData, { id: item.code_client_id });
        item.sum = this.sum(item);
        item.text = '';
        const phones = _.uniqBy(_.map(_.get(findPhonesData, 'customers') || [], ({ phone }) => ({
          label: phone,
          value: phone,
        })), 'label');
        item.phones = phones;
        item.selectedPhones = !_.isEmpty(phones) ? [_.get(_.first(phones), 'value')] : '';
        item.status = '';
      });
      this.faxTableData = cloneData;
      devlog.log('this.faxTableData', this.faxTableData);
    },
  },
};
</script>
