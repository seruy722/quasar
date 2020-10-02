<template>
  <div
    data-vue-component-name="Cargo"
  >
    <Table
      :table-properties="cargoTableProperties"
      :table-data="list"
      :table-reactive-properties="cargoTableReactiveProperties"
    >
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
                  {{ props.row.created_at | formatToDashDate }}
                </q-item-label>
              </q-item-section>

              <q-item-section>
                <q-item-label>
                  {{ props.row.kg }}
                </q-item-label>
              </q-item-section>

              <q-item-section>
                <q-item-label>
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
                    v-if="col.field === 'kg'"
                  >
                    {{ col.value | numberFormatFilter }}
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
            {{ props.row.created_at | formatToDashDate }}
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
            key="category_name"
            :props="props"
          >
            {{ props.row.category_name }}
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
  </div>
</template>

<script>
    export default {
        name: 'Cargo',
        components: {
            Table: () => import('src/components/Elements/Table/Table.vue'),
        },
        props: {
            list: {
                type: Array,
                default: () => ([]),
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
                            name: 'category_name',
                            label: this.$t('category'),
                            field: 'category_name',
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
                    visibleColumns: ['code_client_name', 'created_at', 'place', 'kg', 'notation', 'category_name'],
                    title: '',
                },
            };
        },
    };
</script>
