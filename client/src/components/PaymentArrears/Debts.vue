<template>
  <div
    data-vue-component-name="Debts"
  >
    <Table
      :table-properties="debtsTableProperties"
      :table-data="list"
      :table-reactive-properties="debtsTableReactiveProperties"
    >
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
                <q-item-label :lines="2">
                  {{ `${props.row.sum} / ${props.row.commission}` }}
                </q-item-label>
              </q-item-section>

              <q-item-section>
                <q-item-label>
                  {{ props.row.code_client_name }}
                </q-item-label>
              </q-item-section>

              <q-item-section>
                <q-item-label>
                  {{ props.row.user_name }}
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
                  <q-item-label v-if="col.field === 'sum'">
                    {{ col.value | numberFormatFilter }}
                  </q-item-label>
                  <q-item-label v-else-if="col.field === 'commission'">
                    {{ col.value | numberFormatFilter }}
                  </q-item-label>
                  <q-item-label v-else-if="col.field === 'paid'">
                    <q-badge :color="props.row.paid ? 'positive' : 'negative'">{{ props.row.paid ? 'Да':
                      props.row.type ? null : 'Нет' }}
                    </q-badge>
                  </q-item-label>
                  <q-item-label v-else-if="col.field === 'type'">
                    {{ props.row.type ? 'Оплата' : 'Долг' }}
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
          :class="{table__tr_red_bg: !props.row.type, table__tr_green_bg: props.row.type}"
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
            {{ props.row.sum | numberFormatFilter }}
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
            <q-badge :color="props.row.paid ? 'positive' : 'negative'">{{ props.row.paid ? 'Да':
              props.row.type ? null : 'Нет' }}
            </q-badge>
          </q-td>

          <q-td
            key="notation"
            :props="props"
          >
            {{ props.row.notation }}
          </q-td>

          <q-td
            key="user_name"
            :props="props"
          >
            {{ props.row.user_name }}
          </q-td>
        </q-tr>
      </template>
    </Table>
    <q-list separator bordered>
      <q-item
        v-for="(user, id) in countTransfersStatisticsData"
        :key="id"
      >
        <q-item-section>
          <q-item-label>{{ user.name }}</q-item-label>
        </q-item-section>
        <q-item-section>
          <q-item-label>{{ user.all | numberFormatFilter }}</q-item-label>
        </q-item-section>
        <q-item-section>
          <q-item-label>{{ user.allSum | numberFormatFilter }}</q-item-label>
        </q-item-section>
      </q-item>
    </q-list>
  </div>
</template>

<script>
    export default {
        name: 'Debts',
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
                            label: this.$t('notation'),
                            field: 'notation',
                            align: 'center',
                            sortable: true,
                        },
                        {
                            name: 'user_name',
                            label: 'Пользователь',
                            field: 'user_name',
                            align: 'center',
                            sortable: true,
                        },
                    ],
                },
                debtsTableReactiveProperties: {
                    selected: [],
                    visibleColumns: ['code_client_name', 'paid', 'created_at', 'type', 'sum', 'notation', 'commission', 'user_name'],
                    title: '',
                },
            };
        },
        computed: {
            countTransfersStatisticsData() {
                const userObj = {};
                _.forEach(this.list, (item) => {
                    const userData = userObj[item.user_name];
                    if (userData) {
                        userData.all += 1;
                        userData.allSum += item.sum;
                    } else {
                        userObj[item.user_name] = {
                            name: item.user_name,
                            all: 1,
                            allSum: item.sum,
                        };
                    }
                });
                const usersAr = _.values(userObj);
                usersAr.sort((a, b) => a.allSum - b.allSum);
                return usersAr;
            },
        },
    };
</script>
