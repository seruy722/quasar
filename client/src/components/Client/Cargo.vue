<template>
  <div
    data-vue-component-name="Cargo"
  >
    <Table
      :table-properties="cargoTableProperties"
      :table-data="cargo"
      :table-reactive-properties="cargoTableReactiveProperties"
      :title="titleTable"
    >
      <template #top-buttons>
        <UpdateBtn
          @update-btn-click="refresh"
        />
        <ExportBtn
          v-show="false"
          @export-btn-click="exportFaxData(cargoTableReactiveProperties.selected)"
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
                <q-item-label>
                  {{ props.row.created_at.slice(0, 10) }}
                </q-item-label>
              </q-item-section>

              <q-item-section>
                <q-item-label :lines="2">
                  {{
                    props.row.kg ? `${props.row.place}м/${props.row.kg}кг` : props.row.type
                      ?
                      props.row.sum : props.row.notation
                  }}
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
                  <q-item-label
                    v-if="col.field === 'things'"
                    :lines="10"
                  >
                    {{ thingsFilter(col.value) }}
                  </q-item-label>
                  <q-item-label
                    v-else-if="col.field === 'kg'"
                  >
                    {{ numberFormat(col.value) }}
                  </q-item-label>
                  <q-item-label
                    v-else-if="col.field === 'sum'"
                  >
                    {{ numberFormat(col.value) }}
                  </q-item-label>
                  <q-item-label
                    v-else-if="col.field === 'paid'"
                  >
                    <q-badge :color="props.row.paid ? 'positive' : 'negative'">
                      {{
                        props.row.paid ? 'Да' :
                          props.row.type ? null : 'Нет'
                      }}
                    </q-badge>
                  </q-item-label>
                  <q-item-label
                    v-else-if="col.field === 'type'"
                  >
                    {{ col.value ? 'Оплата' : 'Долг' }}
                  </q-item-label>
                  <q-item-label
                    v-else-if="col.field === 'notation'"
                    :lines="3"
                  >
                    {{ col.value }}
                  </q-item-label>
                  <q-item-label
                    v-else-if="col.field === 'in_cargo'"
                  >
                    <q-badge :color="props.row.in_cargo ? 'positive' : 'negative'">
                      {{ props.row.in_cargo ? 'Да' : 'Нет' }}
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
          :class="{table__tr_bold_text: props.row.brand, table__tr_red_bg: !props.row.type, table__tr_green_bg: props.row.type}"
        >
          <q-td
            auto-width
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
            key="code_place"
            :props="props"
          >
            {{ props.row.code_place }}
          </q-td>

          <q-td
            key="type"
            :props="props"
          >
            {{ props.row.type ? 'Оплата' : 'Долг' }}
          </q-td>

          <q-td
            key="place"
            :props="props"
          >
            {{ props.row.type ? null : props.row.place }}
          </q-td>

          <q-td
            key="kg"
            :props="props"
          >
            {{ props.row.type ? null : props.row.kg }}
          </q-td>

          <q-td
            key="sum"
            :props="props"
          >
            {{ numberFormat(props.row.sum) }}
          </q-td>
          <q-td
            key="sale"
            :props="props"
          >
            {{ props.row.type ? props.row.sale : null }}
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
            key="category_name"
            :props="props"
          >
            {{ props.row.category_name }}
          </q-td>

          <q-td
            key="in_cargo"
            :props="props"
          >
            <q-badge :color="props.row.in_cargo ? 'positive' : 'negative'">
              {{
                props.row.in_cargo ? 'Да' :
                  props.row.type ? null : 'Нет'
              }}
            </q-badge>
          </q-td>

          <q-td
            key="shop"
            :props="props"
          >
            {{ props.row.shop }}
          </q-td>

          <q-td
            key="things"
            :props="props"
          >
            {{ thingsFilter(props.row.things) }}
          </q-td>
        </q-tr>
      </template>
    </Table>
    <CountCargoCategories
      :list="cargo"
      style="max-width: 500px;margin:0 auto;"
    />
  </div>
</template>

<script>
import ExportDataMixin from 'src/mixins/ExportData';
import { numberFormat, thingsFilter } from 'src/utils';
import Table from 'src/components/Elements/Table/Table.vue';
import CountCargoCategories from 'src/components/CargoDebts/CountCargoCategories.vue';
import ExportBtn from 'src/components/Buttons/ExportBtn.vue';
import UpdateBtn from 'src/components/Buttons/UpdateBtn.vue';

export default {
  name: 'Cargo',
  components: {
    Table,
    CountCargoCategories,
    ExportBtn,
    UpdateBtn,
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
      cargoTableProperties: {
        columns: [
          {
            name: 'created_at',
            label: 'Дата',
            align: 'center',
            field: 'created_at',
            sortable: true,
          },
          {
            name: 'code_place',
            label: 'Код',
            align: 'center',
            field: 'code_place',
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
          // {
          //   name: 'for_kg',
          //   label: 'За кг',
          //   field: 'for_kg',
          //   align: 'center',
          //   sortable: true,
          // },
          // {
          //   name: 'for_place',
          //   label: 'За место',
          //   field: 'for_place',
          //   align: 'center',
          //   sortable: true,
          // },
          {
            name: 'sum',
            label: 'Сумма',
            field: 'sum',
            align: 'center',
            sortable: true,
          },
          {
            name: 'sale',
            label: 'Скидка',
            field: 'sale',
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
            name: 'category_name',
            label: 'Категория',
            field: 'category_name',
            align: 'center',
            sortable: true,
          },
          // {
          //   name: 'notation',
          //   label: 'Примечания',
          //   field: 'notation',
          //   align: 'center',
          //   sortable: true,
          // },
          {
            name: 'in_cargo',
            label: 'Доставлен',
            field: 'in_cargo',
            align: 'center',
            sortable: true,
          },
          {
            name: 'shop',
            label: 'Магазин',
            field: 'shop',
            align: 'center',
            sortable: true,
          },
          {
            name: 'things',
            label: 'Опись',
            field: 'things',
            align: 'center',
            sortable: true,
          },
          // {
          //   name: 'delivery_method_name',
          //   label: 'Способ доставки',
          //   field: 'delivery_method_name',
          //   align: 'center',
          //   sortable: true,
          // },
          // {
          //   name: 'department',
          //   label: 'Отделение',
          //   field: 'department',
          //   align: 'center',
          //   sortable: true,
          // },
        ],
      },
      cargoTableReactiveProperties: {
        selected: [],
        visibleColumns: ['paid', 'created_at', 'type', 'sum', 'in_cargo', 'place', 'kg', 'category_name', 'sale', 'things', 'shop'],
        title: '',
      },
    };
  },
  computed: {
    cargo() {
      return this.$store.getters['cargoDebts/getCargo'];
    },
    titleTable() {
      return `Сумма: ${_.sumBy(this.cargo, 'sum')}, Скидки: ${_.sumBy(this.cargo, 'sale')}`;
    },
  },
  methods: {
    numberFormat,
    thingsFilter,
    async exportFaxData(selected) {
      const data = !_.isEmpty(selected) ? selected : this.cargo;
      if (!_.isEmpty(data)) {
        const clientName = `${_.get(_.first(data), 'code_client_name')} код карго`;
        const { getUrl } = await import('src/tools/url');
        this.exportDataToExcel(getUrl('exportCargoData'), {
          data,
        }, `${clientName}.xlsx`);
      }
    },
  },
};
</script>
