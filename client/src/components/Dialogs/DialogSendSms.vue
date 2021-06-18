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

        <q-btn dense flat icon="minimize" @click="maximizedToggle = false" :disable="!maximizedToggle">
          <q-tooltip v-if="maximizedToggle" content-class="bg-white text-primary">Minimize</q-tooltip>
        </q-btn>
        <q-btn dense flat icon="crop_square" @click="maximizedToggle = true" :disable="maximizedToggle">
          <q-tooltip v-if="!maximizedToggle" content-class="bg-white text-primary">Maximize</q-tooltip>
        </q-btn>
        <q-btn dense flat icon="close" v-close-popup>
          <q-tooltip content-class="bg-white text-primary">Close</q-tooltip>
        </q-btn>
      </q-bar>

      <div>
        Тариф: {{ rate }} | Баланс : {{ balance }}
      </div>

      <q-card-section>
        <Table
          :table-properties="faxTableProperties"
          :table-data="faxTableData"
          :table-reactive-properties="faxTableReactiveProperties"
          :title="fax.name"
        >
          <template #top-buttons>
            <IconBtn
              color="positive"
              icon="explicit"
              tooltip="excel"
              @icon-btn-click="exportFaxData(faxTableReactiveProperties.selected)"
            />
          </template>

          <!--ОТОБРАЖЕНИЕ КОНТЕНТА НА МАЛЕНЬКИХ ЭКРАНАХ-->
          <template #inner-item="{props}">
            <div
              class="q-pa-xs col-xs-12 col-sm-6 col-md-4 col-lg-3 grid-style-transition"
              :style="props.selected ? 'transform: scale(0.95);' : ''"
            >
              <q-expansion-item
                expand-separator
                class="shadow-1 overflow-hidden"
                header-class="bg-secondary text-white"
                style="border-radius: 30px;border: 1px solid #26A69A;"
                expand-icon-class="text-white"
              >
                <template #header>
                  <q-item-section avatar>
                    <q-checkbox
                      v-model="props.selected"
                      dense
                    />
                  </q-item-section>

                  <q-item-section>
                    <q-item-label :lines="2">
                      {{ props.row.code_client_name }}
                    </q-item-label>
                  </q-item-section>
                </template>

                <q-list
                  separator
                  dense
                >
                  <q-item
                    v-for="col in props.cols.filter(col => col.name !== 'desc')"
                    :key="col.name"
                  >
                    <q-item-section>
                      <q-item-label>{{ `${col.label}:` }}</q-item-label>
                    </q-item-section>
                    <q-item-section side>
                      <q-item-label
                        v-if="col.field === 'things'"
                        :lines="10"
                      >
                        {{ col.value | thingsFilter }}
                      </q-item-label>
                      <q-item-label
                        v-else-if="col.field === 'kg'"
                      >
                        {{ col.value | numberFormatFilter }}
                      </q-item-label>
                      <q-item-label
                        v-else-if="col.field === 'notation'"
                        :lines="4"
                      >
                        {{ col.value }}
                      </q-item-label>
                      <q-item-label v-else>
                        {{ col.value }}
                      </q-item-label>
                    </q-item-section>
                  </q-item>
                  <q-item>
                    <q-item-section>
                      <BaseBtn
                        label="История"
                        color="info"
                        style="max-width: 100px;margin: 0 auto;"
                      />
                    </q-item-section>
                  </q-item>
                </q-list>
              </q-expansion-item>
            </div>
          </template>

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
                <!--                <PopupEdit-->
                <!--                  v-if="combineTableData"-->
                <!--                  :value.sync="props.row.code_client_id"-->
                <!--                  type="number"-->
                <!--                  :title="props.row.code_client_name"-->
                <!--                  @add-to-save="addToAddSaveArray(props.row, 'code_client_id')"-->
                <!--                >-->
                <!--                  <SearchSelect-->
                <!--                    v-model="props.row.code_client_id"-->
                <!--                    label="Клиент"-->
                <!--                    :dense="$q.screen.xs || $q.screen.sm"-->
                <!--                    :options="clientCodes"-->
                <!--                  />-->
                <!--                </PopupEdit>-->
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
                {{ props.row.kg | numberFormatFilter }}
              </q-td>

              <q-td
                key="for_kg"
                class="text-bold text-red"
                :props="props"
              >
                {{ props.row.for_kg | numberFormatFilter }}
                <!--                <PopupEdit-->
                <!--                  v-if="combineTableData"-->
                <!--                  :value.sync="props.row.for_kg"-->
                <!--                  type="number"-->
                <!--                  :title="props.row.code_client_name"-->
                <!--                  @add-to-save="addToAddSaveArray(props.row, 'for_kg')"-->
                <!--                >-->
                <!--                  <q-input-->
                <!--                    v-model.number="props.row.for_kg"-->
                <!--                    type="number"-->
                <!--                    autofocus-->
                <!--                    dense-->
                <!--                  />-->
                <!--                  <q-checkbox-->
                <!--                    v-model="props.row.replacePrice"-->
                <!--                    label="Заменить"-->
                <!--                    dense-->
                <!--                  />-->
                <!--                </PopupEdit>-->
              </q-td>

              <q-td
                key="for_place"
                class="text-bold text-red"
                :props="props"
              >
                {{ props.row.for_place | numberFormatFilter }}
                <!--                <PopupEdit-->
                <!--                  v-if="combineTableData"-->
                <!--                  :value.sync="props.row.for_place"-->
                <!--                  type="number"-->
                <!--                  :title="props.row.code_client_name"-->
                <!--                  @add-to-save="addToAddSaveArray(props.row, 'for_place')"-->
                <!--                />-->
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
                <!--                <PopupEdit-->
                <!--                  v-if="combineTableData"-->
                <!--                  :value.sync="props.row.category_id"-->
                <!--                  type="number"-->
                <!--                  :title="props.row.code_client_name"-->
                <!--                  @add-to-save="addToAddSaveArray(props.row, 'category_id')"-->
                <!--                >-->
                <!--                  <SearchSelect-->
                <!--                    v-model="props.row.category_id"-->
                <!--                    label="Категория"-->
                <!--                    :dense="$q.screen.xs || $q.screen.sm"-->
                <!--                    :options="categories"-->
                <!--                  />-->
                <!--                </PopupEdit>-->
              </q-td>

              <q-td
                key="text"
                :props="props"
              >
                {{ props.row.text }}
              </q-td>

              <q-td
                key="phones"
                :props="props"
              >
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
      <q-card-section>
        <q-btn-group outline>
          <q-btn
            v-for="(item,index) in options"
            :key="index"
            outline
            :color="item.color"
            :label="item.label"
            @click="text = `${text}${item.value}`"
          />
        </q-btn-group>
        <q-input
          v-model="text"
          filled
          counter
          label="Сообщение"
          type="textarea"
          :rules="[val => val.length < 71 || 'Превышено максимальное количество символов']"
        />
      </q-card-section>

      <q-card-actions align="right">
        <q-btn
          label="Просмотр"
          color="primary"
          @click="viewSms(text, faxTableData, sendSmsDialogData, options)"
        />

        <q-btn
          label="Отправить"
          color="secondary"
          @click="sendSms(faxTableData)"
        />
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>

<script>
import { getUrl } from 'src/tools/url';

export default {
  name: 'DialogSendSms',
  components: {
    Table: () => import('components/Elements/Table/Table.vue'),
    IconBtn: () => import('components/Buttons/IconBtn.vue'),
    BaseBtn: () => import('components/Buttons/BaseBtn.vue'),
  },
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
  data() {
    return {
      rate: 0.35,
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
          color: 'green',
          text: 'вес',
        },
        {
          label: 'Сумма',
          value: '{sum}',
          field: 'sum',
          color: 'red',
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
            label: this.$t('place'),
            field: 'place',
            align: 'center',
            sortable: true,
          },
          {
            name: 'kg',
            label: this.$t('kg'),
            field: 'kg',
            align: 'center',
            sortable: true,
          },
          {
            name: 'for_kg',
            label: this.$t('forKg'),
            field: 'for_kg',
            align: 'center',
            sortable: true,
          },
          {
            name: 'for_place',
            label: this.$t('forPlace'),
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
            label: this.$t('category'),
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
            name: 'phones',
            label: 'Телефоны',
            field: 'phones',
            align: 'center',
          },
        ],
      },
      faxTableReactiveProperties: {
        selected: [],
        visibleColumns: ['code_client_name', 'place', 'kg', 'for_kg', 'for_place', 'category_name', 'sum', 'text', 'phones'],
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
      }
    },
  },
  created() {
    this.$axios.get(getUrl('getSmsBalance'))
      .then(({ data: { response_result: { balance } } }) => {
        devlog.log('balance', balance);
        this.balance = balance;
      });
  },
  methods: {
    sendSms(values) {
      // const phone = parseInt(this.phone.replace(/[^\d]/g, ''), 10);
      // const sendData = [
      //   {
      //     phones: [],
      //     text: 'Code for register: 62',
      //   },
      //   {
      //     phones: [],
      //     text: 'Code for register: 90',
      //   },
      // ];
      devlog.log('VALSD', values);
      const res = _.map(values, ({
                                   selectedPhones,
                                   text,
                                 }) => ({
        selectedPhones,
        text,
      }));
      devlog.log('VALSD_RES', res);
      this.$axios.post(getUrl('sendSms'), res);
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
        devlog.log('findPhonesData', findPhonesData);
        item.sum = this.sum(item);
        item.text = '';
        const phones = _.uniqBy(_.map(_.get(findPhonesData, 'customers') || [], ({ phone }) => ({
          label: phone,
          value: phone,
        })), 'label');
        item.phones = phones;
        item.selectedPhones = [_.get(_.first(phones), 'value')] || [];
      });
      this.faxTableData = cloneData;
    },
  },
};
</script>
