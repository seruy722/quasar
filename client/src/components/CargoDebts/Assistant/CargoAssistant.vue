<template>
  <div
    data-vue-component-name="Cargo"
  >
    <Table
      :table-properties="cargoTableProperties"
      :table-data="cargo"
      :table-reactive-properties="cargoTableReactiveProperties"
      title="Сводная"
    >
      <template #top-buttons>
        <UpdateBtn
          v-show="currentCodeClientId"
          @update-btn-click="refresh"
        />
        <ExportBtn
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
              <q-item-section>
                <q-checkbox v-model="props.selected" />
              </q-item-section>
              <q-item-section>
                <q-item-label>
                  {{ numberFormat(props.row.sum) }}
                </q-item-label>
              </q-item-section>

              <q-item-section>
                <q-item-label :lines="2">
                  {{
                    props.row.kg ? `${props.row.place}м/${props.row.kg}кг ${props.row.fax_name}` : props.row.type ?
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
                  <q-item-label v-else>
                    {{ col.value }}
                  </q-item-label>
                </q-item-section>
              </q-item>
              <!--              <q-item>-->
              <!--                <q-item-section>-->
              <!--                  <BaseBtn-->
              <!--                    label="История"-->
              <!--                    color="info"-->
              <!--                    style="max-width: 100px;margin: 0 auto;"-->
              <!--                    @click-base-btn="getStorehouseDataHistory(props.row.id, props.cols)"-->
              <!--                  />-->
              <!--                </q-item-section>-->
              <!--              </q-item>-->
            </q-list>
          </q-expansion-item>
        </div>
      </template>

      <template #inner-body="{props}">
        <q-tr
          :props="props"
          class="cursor-pointer"
          :class="{table__tr_bold_text: props.row.brand, table__tr_red_bg: !props.row.type, table__tr_green_bg: props.row.type}"
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
            key="code_place"
            :props="props"
          >
            {{ props.row.code_place }}
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
            key="for_kg"
            class="text-bold cursor-pointer"
            :props="props"
          >
            {{ props.row.type ? null : props.row.for_kg }}
          </q-td>

          <q-td
            key="for_place"
            class="text-bold cursor-pointer"
            :props="props"
          >
            {{ props.row.type ? null : props.row.for_place }}
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
            key="fax_name"
            :props="props"
            class="faxName"
          >
            {{ props.row.fax_name }}
          </q-td>

          <q-td
            key="notation"
            :props="props"
          >
            {{ props.row.notation }}
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

          <q-td
            key="delivery_method_name"
            :props="props"
          >
            {{ props.row.delivery_method_name }}
          </q-td>

          <q-td
            key="department"
            :props="props"
          >
            {{ props.row.department }}
          </q-td>
        </q-tr>
      </template>
    </Table>
    <GeneralClientCargoData
      :list="cargo"
      title="Баланс"
      style="max-width: 500px;margin:20px auto;"
    />
  </div>
</template>

<script>
import ExportDataMixin from 'src/mixins/ExportData';
import { numberFormat, thingsFilter } from 'src/utils';
import Table from 'src/components/Elements/Table/Table.vue';
import UpdateBtn from 'src/components/Buttons/UpdateBtn.vue';
import ExportBtn from 'src/components/Buttons/ExportBtn.vue';
import GeneralClientCargoData from 'src/components/CargoDebts/GeneralClientCargoData.vue';

export default {
  name: 'Cargo',
  components: {
    Table,
    UpdateBtn,
    ExportBtn,
    GeneralClientCargoData,
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
          {
            name: 'fax_name',
            label: 'Факс',
            field: 'fax_name',
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
          {
            name: 'delivery_method_name',
            label: 'Способ доставки',
            field: 'delivery_method_name',
            align: 'center',
            sortable: true,
          },
          {
            name: 'department',
            label: 'Отделение',
            field: 'department',
            align: 'center',
            sortable: true,
          },
        ],
      },
      cargoTableReactiveProperties: {
        selected: [],
        visibleColumns: ['code_client_name', 'paid', 'created_at', 'type', 'sum', 'place', 'kg', 'for_kg', 'for_place', 'notation', 'category_name', 'fax_name', 'sale'],
        title: '',
      },
    };
  },
  computed: {
    cargo() {
      return this.$store.getters['cargoDebts/getCargo'];
    },
    currentCodeClientId() {
      return this.$store.getters['cargoDebts/getCurrentCodeClientId'];
    },
  },
  methods: {
    numberFormat,
    thingsFilter,
    async exportFaxData(selected) {
      let data = selected;
      if (_.isEmpty(data)) {
        data = this.cargo;
      } else {
        const searchData = this.$store.getters['cargoDebts/getCargoForSearch'];
        const newArr = [];
        _.forEach(data, ({ id }) => {
          newArr.push(_.find(searchData, { id }));
        });
        devlog.log('DSDGF', data);
        data = _.orderBy(newArr, (item) => new Date(item.created_at), 'desc');
      }
      if (!_.isEmpty(data)) {
        const { getUrl } = await import('src/tools/url');
        this.exportDataToExcel(getUrl('exportCargoData'), {
          data,
        }, 'Cargo.xlsx');
      }
    },
  },
};
</script>
