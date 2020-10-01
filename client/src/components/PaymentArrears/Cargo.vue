<template>
  <div
    data-vue-component-name="Cargo"
  >
    <Table
      :table-properties="cargoTableProperties"
      :table-data="list"
      :table-reactive-properties="cargoTableReactiveProperties"
    >
      <template v-slot:top-buttons>
        <IconBtn
          v-show="cargoTableReactiveProperties.selected.length"
          dense
          icon="vertical_align_center"
          color="accent"
          tooltip="Доставлено"
          @icon-btn-click="dialogSelectDeliveredPlace = true"
        />
      </template>
      <!--ОТОБРАЖЕНИЕ КОНТЕНТА НА МАЛЕНЬКИХ ЭКРАНАХ-->
      <template v-slot:inner-item="{props}">
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
            <template v-slot:header>
              <q-item-section>
                <q-item-label>
                  {{ props.row.created_at.slice(0,5) }}
                </q-item-label>
              </q-item-section>

              <q-item-section>
                <q-item-label>
                  {{ props.row.sum | numberFormatFilter }}
                </q-item-label>
              </q-item-section>

              <q-item-section>
                <q-item-label>
                  {{ props.row.code_client_name }}
                </q-item-label>
              </q-item-section>

              <q-item-section>
                <q-item-label :lines="2">
                  {{ props.row.kg ? `${props.row.place}м/${props.row.kg}кг ${props.row.fax_name || ''}` : props.row.type
                  ?
                  props.row.sum : props.row.notation
                  }}
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
                    {{ col.value | thingsFilter }}
                  </q-item-label>
                  <q-item-label
                    v-else-if="col.field === 'kg'"
                  >
                    {{ col.value | numberFormatFilter }}
                  </q-item-label>
                  <q-item-label
                    v-else-if="col.field === 'sum'"
                  >
                    {{ col.value | numberFormatFilter }}
                  </q-item-label>
                  <q-item-label
                    v-else-if="col.field === 'paid'"
                  >
                    <q-badge :color="props.row.paid ? 'positive' : 'negative'">{{ props.row.paid ? 'Да':
                      props.row.type ? null : 'Нет' }}
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
            </q-list>
          </q-expansion-item>
        </div>
      </template>

      <template v-slot:inner-body="{props}">
        <q-tr
          :props="props"
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
            class="text-bold"
            :props="props"
          >
            {{ props.row.type ? null : props.row.for_kg }}
          </q-td>

          <q-td
            key="for_place"
            class="text-bold"
            :props="props"
          >
            {{ props.row.type ? null : props.row.for_place }}
          </q-td>

          <q-td
            key="sum"
            :props="props"
          >
            {{ props.row.sum | numberFormatFilter }}
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
            <q-badge :color="props.row.paid ? 'positive' : 'negative'">{{ props.row.paid ? 'Да':
              props.row.type ? null : 'Нет' }}
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
            <router-link :to="{ name: 'fax', params: { id: props.row.fax_id }}">
              {{ props.row.fax_name }}
            </router-link>
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
    <q-dialog v-model="dialogSelectDeliveredPlace" persistent transition-show="scale" transition-hide="scale">
      <q-card style="width: 300px">
        <q-card-section>
          <div class="text-h6 text-teal">
            Доставлено
          </div>
        </q-card-section>

        <q-card-section class="q-pt-none">
          <BaseSelect
            v-model="deliveredPlace"
            outlined
            :options="[{label: 'Да', value: 1,}, {label: 'Нет', value: 0,}]"
          />
        </q-card-section>

        <q-card-actions align="right" class="bg-white text-teal">
          <q-btn
            v-close-popup
            label="Отмена"
            color="negative"
          />
          <q-btn
            v-close-popup
            color="positive"
            label="OK"
            @click="setDeliveredPlace(cargoTableReactiveProperties.selected, deliveredPlace)"
          />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </div>
</template>

<script>
    import { getUrl } from 'src/tools/url';

    export default {
        name: 'Cargo',
        components: {
            Table: () => import('src/components/Elements/Table/Table.vue'),
            BaseSelect: () => import('components/Elements/BaseSelect.vue'),
            IconBtn: () => import('components/Buttons/IconBtn.vue'),
        },
        props: {
            list: {
                type: Array,
                default: () => ([]),
            },
        },
        data() {
            return {
                deliveredPlace: 1,
                dialogSelectDeliveredPlace: false,
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
                            label: this.$t('category'),
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
                            label: this.$t('notation'),
                            field: 'notation',
                            align: 'center',
                            sortable: true,
                        },
                    ],
                },
                cargoTableReactiveProperties: {
                    selected: [],
                    visibleColumns: ['code_client_name', 'paid', 'created_at', 'type', 'sum', 'place', 'kg', 'for_kg', 'for_place', 'notation', 'category_name', 'fax_name'],
                    title: '',
                },
            };
        },
        methods: {
            setDeliveredPlace(data, flag) {
                this.$q.loading.show();
                const ids = [];
                _.forEach(data, (item) => {
                    if (item.arr) {
                        ids.push(..._.map(item.arr, 'id'));
                    } else {
                        ids.push(item.id);
                    }
                });
                devlog.log('ids', ids);
                this.$axios.post(getUrl('setDeliveredCargo'), {
                    ids,
                    flag,
                })
                  .then(({ data: { cargo } }) => {
                      _.set(this, 'list', cargo);
                      this.cargoTableReactiveProperties.selected = [];
                      this.$q.loading.hide();
                  })
                  .catch(() => {
                      this.cargoTableReactiveProperties.selected = [];
                      this.$q.loading.hide();
                  });
            },
        },
    };
</script>
