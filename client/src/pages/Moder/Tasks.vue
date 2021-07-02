<template>
  <q-page
    data-vue-component-name="Tasks"
  >
    <PullRefresh @refresh="refresh">
      <Table
        :table-properties="cargoTableProperties"
        :table-data="tasks"
        :table-reactive-properties="cargoTableReactiveProperties"
        title="Задачи"
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
                  @click="showDialogAddTask = true"
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
                  v-show="cargoTableReactiveProperties.selected.length === 1"
                  v-close-popup
                  clickable
                  @click="update"
                >
                  <q-item-section avatar>
                    <q-icon
name="edit"
color="teal"
/>
                  </q-item-section>
                  <q-item-section>Редактировать</q-item-section>
                </q-item>
                <q-item
                  v-close-popup
                  clickable
                  @click="refresh"
                >
                  <q-item-section avatar>
                    <q-icon
name="sync"
color="primary"
/>
                  </q-item-section>
                  <q-item-section>Обновить</q-item-section>
                </q-item>
                <!--                <q-item-->
                <!--                  v-close-popup-->
                <!--                  clickable-->
                <!--                  @click="exportFaxData(cargoTableReactiveProperties.selected)"-->
                <!--                >-->
                <!--                  <q-item-section avatar>-->
                <!--                    <q-icon name="explicit" color="positive" />-->
                <!--                  </q-item-section>-->
                <!--                  <q-item-section>Excel</q-item-section>-->
                <!--                </q-item>-->
                <q-item
                  v-show="cargoTableReactiveProperties.selected.length"
                  v-close-popup
                  clickable
                  @click="destroyEntry(cargoTableReactiveProperties.selected)"
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
                    {{ props.row.created_at.slice(0, 5) }}
                  </q-item-label>
                </q-item-section>

                <q-item-section>
                  <q-item-label :lines="2">
                    {{ props.row.description }}
                  </q-item-label>
                </q-item-section>

                <q-item-section
                  side
                >
                  <q-item-label :lines="2">
                    {{ props.row.responsible_name }}
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
                    <q-item-label>
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
            @click.stop="$router.push({name: 'task', params:{id:props.row.id}})"
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
              key="author_name"
              :props="props"
            >
              {{ props.row.author_name }}
            </q-td>

            <q-td
              key="description"
              :props="props"
            >
              {{ props.row.description }}
            </q-td>

            <q-td
              key="section_name"
              :props="props"
            >
              <q-badge>
                {{ props.row.section_name }}
              </q-badge>
            </q-td>

            <q-td
              key="status_name"
              :props="props"
            >
              <q-badge :color="props.row.status_color">
                {{ props.row.status_name }}
              </q-badge>
            </q-td>

            <q-td
              key="responsible_name"
              :props="props"
            >
              {{ props.row.responsible_name }}
            </q-td>
          </q-tr>
        </template>
      </Table>
    </PullRefresh>
    <DialogAddTask
      v-model:show-dialog="showDialogAddTask"
      v-model:entry-data="entryData"
    />
  </q-page>
</template>

<script>
import showNotif from 'src/mixins/showNotif';
import CheckErrorsMixin from 'src/mixins/CheckErrors';
import getFromSettings from 'src/tools/settings';

export default {
  name: 'Tasks',
  components: {
    Table: () => import('src/components/Elements/Table/Table.vue'),
    MenuBtn: () => import('src/components/Buttons/MenuBtn.vue'),
    DialogAddTask: () => import('components/Tasks/DialogAddTask.vue'),
    PullRefresh: () => import('src/components/PullRefresh.vue'),
  },
  mixins: [showNotif, CheckErrorsMixin],
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
            name: 'author_name',
            label: 'Автор',
            align: 'center',
            field: 'author_name',
            sortable: true,
          },
          {
            name: 'description',
            label: 'Описание',
            align: 'center',
            field: 'description',
            sortable: true,
          },
          {
            name: 'section_name',
            label: 'Раздел',
            align: 'center',
            field: 'section_name',
            sortable: true,
          },
          {
            name: 'status_name',
            label: 'Статус',
            field: 'status_name',
            align: 'center',
            sortable: true,
          },
          {
            name: 'responsible_name',
            label: 'Ответственный',
            field: 'responsible_name',
            align: 'center',
            sortable: true,
          },
        ],
      },
      cargoTableReactiveProperties: {
        selected: [],
        visibleColumns: ['created_at', 'author_name', 'description', 'section_name', 'status_name', 'responsible_name'],
        title: '',
      },
      menuList: [],
      showDialogAddTask: false,
      entryData: {},
    };
  },
  computed: {
    tasks() {
      const sections = getFromSettings('sectionTask');
      const statusData = getFromSettings('statusTask');
      return _.map(this.$store.getters['tasks/getTasks'], (item) => {
        const sectionName = _.get(_.find(sections, { value: item.section_id }), 'label');
        const status = _.find(statusData, { value: item.status_id });
        const userName = _.get(_.find(this.usersList, { value: item.responsible_id }), 'label');
        return _.assign({}, item, {
          section_name: sectionName,
          status_name: status.label,
          status_color: status.color,
          responsible_name: userName,
        });
      });
    },
    usersList() {
      return this.$store.getters['auth/getUsersList'];
    },
  },
  created() {
    if (_.isEmpty(this.usersList)) {
      this.$store.dispatch('auth/fetchUsersList');
    }
    if (_.isEmpty(this.tasks)) {
      this.$q.loading.show();
      this.$store.dispatch('tasks/fetchTasks')
        .finally(() => {
          this.$q.loading.hide();
        });
    }
  },
  methods: {
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
    destroyEntry(data) {
      const ids = _.map(data, 'id');
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
