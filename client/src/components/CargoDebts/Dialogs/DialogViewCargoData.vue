<template>
  <DialogComponent
      :dialog="show"
      title="Записи"
      :persistent="true"
      data-vue-component-name="DialogViewCargoData"
      class="q-pa-md"
  >
    <q-card style="min-width: 320px;width: 100%;max-width: 1900px;">
      <q-card-section class="row justify-between bg-grey q-mb-sm">
        <span class="text-h6">Просмотр записей долгов</span>
        <div>
          <IconBtn
              dense
              icon="clear"
              tooltip="Закрыть"
              @icon-btn-click="close"
          />
        </div>
      </q-card-section>

      <q-card-section>
        <div class="q-gutter-y-md">
          <Table
              :table-properties="faxTableProperties"
              :table-data="values"
              :table-reactive-properties="faxTableReactiveProperties"
          >
            <template #top-buttons>
              <!--                <IconBtn-->
              <!--                  v-show="addToSaveArray.length"-->
              <!--                  color="positive"-->
              <!--                  icon="save"-->
              <!--                  tooltip="Сохранить"-->
              <!--                  @icon-btn-click="saveDataInCombineTable(addToSaveArray)"-->
              <!--                />-->

              <!--                <IconBtn-->
              <!--                  color="positive"-->
              <!--                  icon="explicit"-->
              <!--                  tooltip="excel"-->
              <!--                  @icon-btn-click="exportFaxData"-->
              <!--                />-->

              <!--                <IconBtn-->
              <!--                  v-show="faxTableReactiveProperties.selected.length"-->
              <!--                  color="negative"-->
              <!--                  icon="delete"-->
              <!--                  :tooltip="$t('delete')"-->
              <!--                  @icon-btn-click="destroyEntry(faxTableReactiveProperties.selected)"-->
              <!--                />-->

              <!--        <IconBtn-->
              <!--          icon="data_usage"-->
              <!--          color="orange"-->
              <!--          tooltip="Обновить цены"-->
              <!--          @icon-btn-click="updatePricesInFax(currentFaxItem.id)"-->
              <!--        />-->
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
                        @click="viewEditDialog(props)"
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
                            @click-base-btn="getStorehouseDataHistory(props.row.id, props.cols)"
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
                  class="cursor-pointer"
                  :class="{table__tr_bold_text: props.row.brand, table__tr_red_bg: !props.row.type, table__tr_green_bg: props.row.type}"
                  @click.stop="viewEditDialog(props, $event)"
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
                    class="text-bold cursor-pointer"
                    :props="props"
                >
                  {{ props.row.for_kg }}
                </q-td>

                <q-td
                    key="for_place"
                    class="text-bold cursor-pointer"
                    :props="props"
                >
                  {{ numberFormat(props.row.for_place) }}
                </q-td>

                <q-td
                    key="sum"
                    :props="props"
                >
                  {{ numberFormat(props.row.sum) }}
                </q-td>
                <q-td
                    key="paid"
                    :props="props"
                >
                  {{ props.row.paid ? 'Да' : 'Нет' }}
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
        </div>
      </q-card-section>
      <q-card-section>
        <CountCargoCategories
            :list="values"
            style="max-width: 500px;margin:0 auto;"
        />
      </q-card-section>
    </q-card>
    <DialogAddCargoDebtEntry
        v-model:entry-data="dialogAddCargoDebtEntryData"
        v-model:show-dialog="showDialogAddCargoDebtEntry"
    />
  </DialogComponent>
</template>

<script>
import BaseBtn from 'src/components/Buttons/BaseBtn.vue';
import DialogComponent from 'src/components/Dialogs/DialogComponent.vue';
import IconBtn from 'src/components/Buttons/IconBtn.vue';
import Table from 'src/components/Elements/Table/Table.vue';
import CountCargoCategories from 'src/components/CargoDebts/CountCargoCategories.vue';
import DialogAddCargoDebtEntry from 'src/components/CargoDebts/Dialogs/DialogAddCargoDebtEntry.vue';
import { thingsFilter, numberFormat } from 'src/utils';

export default {
  name: 'DialogViewCargoData',
  components: {
    BaseBtn,
    DialogComponent,
    IconBtn,
    Table,
    CountCargoCategories,
    DialogAddCargoDebtEntry,
  },
  props: {
    values: {
      type: Array,
      default: () => [],
    },
    show: {
      type: Boolean,
      default: false,
    },
  },
  emits: ['update:values', 'update:show'],
  data() {
    return {
      dialogAddCargoDebtEntryData: {},
      showDialogAddCargoDebtEntry: false,
      clientCode: null,
      tab: 'cargo',
      faxTableProperties: {
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
      faxTableReactiveProperties: {
        selected: [],
        visibleColumns: ['code_place', 'code_client_name', 'paid', 'created_at', 'type', 'sum', 'place', 'kg', 'for_kg', 'for_place', 'shop', 'notation', 'category_name', 'things', 'delivery_method_name', 'department', 'fax_name'],
        title: '',
      },
    };
  },
  computed: {
    clientCodes() {
      return this.$store.getters['codes/getCodes'];
    },
  },
  watch: {
    showDialogAddCargoDebtEntry(val) {
      if (!val) {
        this.close();
      }
    },
  },
  methods: {
    thingsFilter,
    numberFormat,
    close() {
      this.$emit('update:show', false);
      this.$emit('update:values', []);
    },
    viewEditDialog(data) {
      this.dialogAddCargoDebtEntryData = data;
      this.showDialogAddCargoDebtEntry = true;
    },
  },
};
</script>
