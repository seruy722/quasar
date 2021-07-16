<template>
  <div
    data-vue-component-name="Debts"
  >
    <Table
      :table-properties="debtsTableProperties"
      :table-data="debts"
      :table-reactive-properties="debtsTableReactiveProperties"
      title="Сводная"
    >
      <template #top-buttons>
        <UpdateBtn
          v-show="currentCodeClientId"
          @update-btn-click="refresh"
        />
        <ExportBtn
          @export-btn-click="exportFaxData(debtsTableReactiveProperties.selected)"
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
            :header-class="`${props.row.type ? 'bg-green' : 'bg-red'} text-white`"
            :style="`border-radius: 30px;border: 1px solid ${props.row.type ? 'lightgreen' : 'lightcoral'};`"
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
                  {{ `${props.row.sum} / ${props.row.commission}` }}
                </q-item-label>
              </q-item-section>

              <q-item-section
                avatar
                side
              >
                <q-icon
                  v-if="props.row.paid"
                  name="money"
                  size="md"
                  color="white"
                />
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
                  <q-item-label v-if="col.field === 'sum'">
                    {{ numberFormat(col.value) }}
                  </q-item-label>
                  <q-item-label v-else-if="col.field === 'commission'">
                    {{ numberFormat(col.value) }}
                  </q-item-label>
                  <q-item-label v-else-if="col.field === 'paid'">
                    <q-badge :color="props.row.paid ? 'positive' : 'negative'">
                      {{
                        props.row.paid ? 'Да' :
                          props.row.type ? null : 'Нет'
                      }}
                    </q-badge>
                  </q-item-label>
                  <q-item-label v-else>
                    {{ col.value }}
                  </q-item-label>
                </q-item-section>
              </q-item>
            </q-list>
          </q-expansion-item>
        </div>
      </template>

      <template #inner-body="{props}">
        <q-tr
          :props="props"
          class="cursor-pointer"
          :class="{table__tr_bold_text: props.row.brand, table__tr_red_bg: !props.row.type, table__tr_green_bg: props.row.type}"
          @click.stop="viewDebtEditDialog(props, $event)"
        >
          <q-td
            auto-width
            class="select_checkbox"
          >
            <q-checkbox
              v-model="props.selected"
              dense
            />
          </q-td>

          <q-td
            key="created_at"
            :props="props"
          >
            {{ props.row.created_at }}
          </q-td>

          <q-td
            key="code_client_name"
            :props="props"
          >
            {{ props.row.code_client_name }}
          </q-td>

          <q-td
            key="type"
            :props="props"
          >
            {{ props.row.type ? 'Оплата' : 'Долг' }}
          </q-td>

          <q-td
            key="sum"
            :props="props"
          >
            {{ numberFormat(props.row.sum) }}
          </q-td>
          <q-td
            key="commission"
            :props="props"
          >
            {{ Math.round(props.row.commission) }}
          </q-td>
          <q-td
            key="paid"
            :props="props"
          >
            <q-badge :color="props.row.paid ? 'positive' : 'negative'">
              {{
                props.row.paid ? 'Да' :
                  props.row.type ? null : 'Нет'
              }}
            </q-badge>
          </q-td>

          <q-td
            key="notation"
            :props="props"
          >
            {{ props.row.notation }}
          </q-td>
        </q-tr>
      </template>
    </Table>
    <GeneralClientDebtsData
      :list="debts"
      title="Баланс"
      style="max-width: 500px;margin:0 auto;"
    />
  </div>
</template>

<script>
import ExportDataMixin from 'src/mixins/ExportData';
import { numberFormat } from 'src/utils';
import Table from 'src/components/Elements/Table/Table.vue';
import UpdateBtn from 'src/components/Buttons/UpdateBtn.vue';
import ExportBtn from 'src/components/Buttons/ExportBtn.vue';
import GeneralClientDebtsData from 'src/components/CargoDebts/GeneralClientDebtsData.vue';

export default {
  name: 'Debts',
  components: {
    Table,
    UpdateBtn,
    ExportBtn,
    GeneralClientDebtsData,
  },
  mixins: [ExportDataMixin],
  props: {
    refresh: {
      type: Function,
      default: () => {
      },
    },
  },
  data() {
    return {
      debtsTableProperties: {
        columns: [
          {
            name: 'created_at',
            label: 'Дата',
            align: 'center',
            field: 'created_at',
            sortable: true,
          },
          {
            name: 'code_client_name',
            label: 'Клиент',
            align: 'center',
            field: 'code_client_name',
            sortable: true,
          },
          {
            name: 'type',
            label: 'Тип',
            align: 'center',
            field: 'type',
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
            name: 'commission',
            label: 'Комиссия',
            field: 'commission',
            align: 'center',
            sortable: true,
          },
          {
            name: 'paid',
            label: 'Оплачен',
            field: 'paid',
            align: 'center',
            sortable: true,
          },
          {
            name: 'notation',
            label: 'Примечания',
            field: 'notation',
            align: 'center',
            sortable: true,
          },
        ],
      },
      debtsTableReactiveProperties: {
        selected: [],
        visibleColumns: ['code_client_name', 'paid', 'created_at', 'type', 'sum', 'notation', 'commission'],
        title: '',
      },
    };
  },
  computed: {
    debts() {
      return this.$store.getters['cargoDebts/getDebts'];
    },
    currentCodeClientId() {
      return this.$store.getters['cargoDebts/getCurrentCodeClientId'];
    },
  },
  methods: {
    numberFormat,
    async exportFaxData(selected) {
      let data = selected;
      if (_.isEmpty(data)) {
        data = this.debts;
      } else {
        const searchData = this.$store.getters['cargoDebts/getDebtsForSearch'];
        const newArr = [];
        _.forEach(data, ({ id }) => {
          newArr.push(_.find(searchData, { id }));
        });
        data = _.orderBy(newArr, (item) => new Date(item.created_at), 'desc');
      }
      if (!_.isEmpty(data)) {
        const { getUrl } = await import('src/tools/url');
        this.exportDataToExcel(getUrl('exportDebtsData'), {
          data,
        }, 'Debts.xlsx');
      }
    },
  },
};
</script>
