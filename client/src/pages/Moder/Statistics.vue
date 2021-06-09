<template>
  <div data-vue-component-name="Statistics">
    <div id="chart"></div>
    <PullRefresh @refresh="refresh">
      <Table
        :table-properties="cargoTableProperties"
        :table-data="tasks"
        :table-reactive-properties="cargoTableReactiveProperties"
        title="Расходы"
      >
        <template #top-buttons>
          <MenuBtn :list="menuList">
            <q-menu
              transition-show="scale"
              transition-hide="scale"
            >
              <q-list
                separator
                style="min-width: 100px"
              >
                <q-item
                  v-close-popup
                  clickable
                  @click="showDialogAddExpense = true"
                >
                  <q-item-section avatar>
                    <q-icon
                      name="add"
                      color="positive"
                    />
                  </q-item-section>
                  <q-item-section>Добавить</q-item-section>
                </q-item>
                <q-item
                  v-show="cargoTableReactiveProperties.selected.length"
                  v-close-popup
                  clickable
                  @click="destroyEntry(cargoTableReactiveProperties.selected, tasks)"
                >
                  <q-item-section avatar>
                    <q-icon
                      name="delete"
                      color="negative"
                    />
                  </q-item-section>
                  <q-item-section>Удалить</q-item-section>
                </q-item>
              </q-list>
            </q-menu>
          </MenuBtn>
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
                    {{ props.row.date }}
                  </q-item-label>
                </q-item-section>

                <q-item-section>
                  <q-item-label>
                    {{ props.row.start_sum }}
                  </q-item-label>
                </q-item-section>

                <q-item-section
                  side
                >
                  <q-item-label>
                    {{ props.row.end_sum }}
                  </q-item-label>
                </q-item-section>
              </template>

              <q-list
                dense
                separator
                bordered
                padding
                class="rounded-borders"
              >
                <q-item>
                  <q-item-section>
                    <q-item-label>
                      Название
                    </q-item-label>
                  </q-item-section>

                  <q-item-section>
                    <q-item-label>
                      Сумма
                    </q-item-label>
                  </q-item-section>

                  <q-item-section>
                    <q-item-label>
                      Дата
                    </q-item-label>
                  </q-item-section>
                </q-item>
                <q-item
                  v-for="(item, index) in props.row.data"
                  :key="index"
                >
                  <q-item-section>
                    <q-item-label>
                      {{ item.expense_name }}
                    </q-item-label>
                  </q-item-section>

                  <q-item-section>
                    <q-item-label>
                      {{ item.sum }}
                    </q-item-label>
                  </q-item-section>

                  <q-item-section>
                    <q-item-label>
                      {{ item.created_at | formatToDotDate }}
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
          >
            <q-td
              auto-width
              class="select_checkbox"
            >
              <q-btn
                size="sm"
                color="accent"
                round
                dense
                :icon="props.expand ? 'remove' : 'add'"
                @click="props.expand = !props.expand"
              />
            </q-td>

            <q-td
              key="date"
              :props="props"
            >
              {{ props.row.date }}
            </q-td>

            <q-td
              key="start_sum"
              :props="props"
            >
              {{ props.row.start_sum }}
            </q-td>

            <q-td
              key="end_sum"
              :props="props"
            >
              {{ props.row.end_sum }}
            </q-td>
          </q-tr>
          <q-tr
            v-show="props.expand"
            :props="props"
          >
            <q-td colspan="100%">
              <q-list
                dense
                separator
                bordered
                padding
                class="rounded-borders"
              >
                <q-item>
                  <q-item-section>
                    <q-item-label>
                      Название
                    </q-item-label>
                  </q-item-section>

                  <q-item-section>
                    <q-item-label>
                      Сумма
                    </q-item-label>
                  </q-item-section>

                  <q-item-section>
                    <q-item-label>
                      Дата
                    </q-item-label>
                  </q-item-section>
                </q-item>
                <q-item
                  v-for="(item, index) in props.row.data"
                  :key="index"
                >
                  <q-item-section>
                    <q-item-label>
                      {{ item.expense_name }}
                    </q-item-label>
                  </q-item-section>

                  <q-item-section>
                    <q-item-label>
                      {{ item.sum }}
                    </q-item-label>
                  </q-item-section>

                  <q-item-section>
                    <q-item-label>
                      {{ item.created_at }}
                    </q-item-label>
                  </q-item-section>
                </q-item>
              </q-list>
            </q-td>
          </q-tr>
        </template>
      </Table>
    </PullRefresh>
    <DialogAddExpense :show-dialog.sync="showDialogAddExpense" />
  </div>
</template>

<script>
import showNotif from 'src/mixins/showNotif';
import ApexCharts from 'apexcharts';

export default {
  name: 'Statistics',
  components: {
    DialogAddExpense: () => import('src/components/Dialogs/DialogAddExpense.vue'),
    Table: () => import('src/components/Elements/Table/Table.vue'),
    MenuBtn: () => import('src/components/Buttons/MenuBtn.vue'),
    PullRefresh: () => import('src/components/PullRefresh.vue'),
  },
  mixins: [showNotif],
  data() {
    return {
      options: {
        chart: {
          type: 'bar',
          height: '100%',
        },
        colors: ['#06F30CFF', '#F80319FF'],
        series: [
          {
            name: 'Доход',
            data: [],
          },
          {
            name: 'Затраты',
            data: [],
          },
        ],
        xaxis: {
          categories: ['Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
        },
      },
      cargoTableProperties: {
        columns: [
          {
            name: 'date',
            label: 'Месяц',
            align: 'center',
            field: 'date',
            sortable: true,
          },
          {
            name: 'start_sum',
            label: 'Начало месяца',
            align: 'center',
            field: 'start_sum',
            sortable: true,
          },
          {
            name: 'end_sum',
            label: 'Конец месяца',
            align: 'center',
            field: 'end_sum',
            sortable: true,
          },
        ],
      },
      cargoTableReactiveProperties: {
        selected: [],
        visibleColumns: ['date', 'start_sum', 'end_sum'],
        title: '',
      },
      menuList: [],
      showDialogAddExpense: false,
    };
  },
  computed: {
    tasks() {
      return this.$store.getters['statistics/getStatistics'];
    },
  },
  watch: {
    tasks(val) {
      _.forEach(val, (item) => {
        if (item.start_sum && item.end_sum) {
          this.options.series[0].data.push(item.end_sum - item.start_sum);
        }
        this.options.series[1].data.push(_.sumBy(item.data, 'sum') * -1);
      });
      const size0 = _.size(this.options.series[0].data);
      const size1 = _.size(this.options.series[1].data);
      const sizeCategories = _.size(this.options.xaxis.categories);
      if (size0 !== sizeCategories) {
        this.options.series[0].data.push(..._.fill(Array(sizeCategories - size0), 0));
      }
      if (size1 !== sizeCategories) {
        this.options.series[1].data.push(..._.fill(Array(sizeCategories - size1), 0));
      }

      this.$nextTick(() => {
        const chart = new ApexCharts(document.getElementById('chart'), this.options);
        chart.render();
      });
    },
  },
  created() {
    if (_.isEmpty(this.tasks)) {
      this.$q.loading.show();
      this.$store.dispatch('statistics/fetchStatistics')
        .finally(() => {
          this.$q.loading.hide();
        });
    }
  },
  methods: {
    fillData() {
      this.datacollection = {
        labels: [this.getRandomInt(), this.getRandomInt()],
        datasets: [
          {
            label: 'Data One',
            backgroundColor: '#f87979',
            data: [this.getRandomInt(), this.getRandomInt()],
          }, {
            label: 'Data One',
            backgroundColor: '#f87979',
            data: [this.getRandomInt(), this.getRandomInt()],
          },
        ],
      };
    },
    getRandomInt() {
      return Math.floor(Math.random() * (50 - 5 + 1)) + 5;
    },
    async refresh(done) {
      if (!done) {
        this.$q.loading.show();
      }
      const { callFunction } = await import('src/utils/index');
      await this.$store.dispatch('tasks/fetchTasks')
        .then(() => {
          callFunction(done);
          this.$q.loading.hide();
          this.showNotif('success', 'Данные успешно обновлены.', 'center');
        })
        .catch(() => {
          this.$q.loading.hide();
          callFunction(done);
        });
    },
    destroyEntry(selected, tasks) {
      const items = !_.isEmpty(selected) ? selected : tasks;
      const ids = _.map(items, 'id');
      this.showNotif('warning', _.size(ids) > 1 ? 'Удалить записи?' : 'Удалить запись?', 'center', [
        {
          label: 'Отмена',
          color: 'white',
          handler: () => {
            this.cargoTableReactiveProperties.selected = [];
          },
        },
        {
          label: 'Удалить',
          color: 'white',
          handler: async () => {
            const { getUrl } = await import('src/tools/url');
            this.$axios.post(getUrl('deleteTasks'), { ids })
              .then(() => {
                this.cargoTableReactiveProperties.selected = [];
                this.$store.dispatch('tasks/deleteTasks', ids);
                this.$q.loading.hide();
                this.showNotif('success', `${_.size(ids > 1) ? 'Записи успешно удалены' : 'Запись успешно удалена'}`, 'center');
              })
              .catch((error) => {
                devlog.error('Ошибка', error);
                this.$q.loading.hide();
              });
          },
        },
      ]);
    },
    viewEditDialog(val, event) {
      if (!_.includes(_.get(event, 'target.classList'), 'select_checkbox')) {
        this.entryData = val;
        this.cargoTableReactiveProperties.selected = [];
        devlog.log('VAL', val);
        setTimeout(() => {
          val.selected = !val.selected;
        }, 100);
        this.showDialogAddTask = true;
      }
    },
    update() {
      this.entryData = { row: _.first(this.cargoTableReactiveProperties.selected) };
      this.showDialogAddTask = true;
    },
  },
};
</script>
