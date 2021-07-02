<template>
  <div data-vue-component-name="Storehouse">
    <Table
      :table-properties="storehouseTableProperties"
      :table-data="storehouseData"
      :table-reactive-properties="storehouseTableReactiveProperties"
      title="Склад"
    >
      <template #top-buttons>
        <UpdateBtn
          @update-btn-click="refresh"
        />
        <IconBtn
          v-show="storehouseData.length"
          color="positive"
          icon="explicit"
          tooltip="Excel"
          @icon-btn-click="exportStorehouseData"
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
                <q-item-label>
                  {{ props.row.kg }}
                </q-item-label>
              </q-item-section>
              <q-item-section>
                <q-item-label>
                  {{ props.row.category_name }}
                </q-item-label>
              </q-item-section>
              <q-item-section>
                <q-item-label>
                  <q-badge>
                    {{ statusFilter(props.row.status) }}
                  </q-badge>
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
                    {{ thingsFilter(col.value) }}
                  </q-item-label>
                  <q-item-label
                    v-else-if="col.field === 'kg'"
                  >
                    {{ numberFormat(col.value) }}
                  </q-item-label>
                  <q-item-label
                    v-else-if="col.field === 'status'"
                  >
                    <q-badge>{{ statusFilter(col.value) }}</q-badge>
                  </q-item-label>
                  <q-item-label
                    v-else-if="col.field === 'created_at'"
                  >
                    {{ fullDate(col.value) }}
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
          :class="{table__tr_bold_text: props.row.brand }"
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
            key="status"
            :props="props"
          >
            <q-badge>{{ statusFilter(props.row.status) }}</q-badge>
          </q-td>
          <q-td
            key="code_place"
            :props="props"
          >
            {{ props.row.code_place }}
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
            key="category_name"
            :props="props"
          >
            {{ props.row.category_name }}
          </q-td>

          <q-td
            key="shop"
            :props="props"
          >
            {{ props.row.shop }}
          </q-td>

          <q-td
            key="notation"
            :props="props"
          >
            {{ props.row.notation }}
          </q-td>

          <q-td
            key="things"
            :props="props"
          >
            {{ thingsFilter(props.row.things) }}
          </q-td>

          <q-td
            key="created_at"
            :props="props"
          >
            {{ formatToDotDate(props.row.created_at) }}
          </q-td>
        </q-tr>
      </template>
    </Table>

    <CountCategories
      :list="storehouseData"
      style="max-width: 500px;margin:0 auto;"
    />
  </div>
</template>
<script>
import { getUrl } from 'src/tools/url';
import showNotif from 'src/mixins/showNotif';
import ExportDataMixin from 'src/mixins/ExportData';
import { callFunction, thingsFilter, numberFormat } from 'src/utils';
import { fullDate } from 'src/utils/formatDate';

export default {
  name: 'Storehouse',
  components: {
    Table: () => import('src/components/Elements/Table/Table.vue'),
    CountCategories: () => import('src/components/CountCategories.vue'),
    UpdateBtn: () => import('src/components/Buttons/UpdateBtn.vue'),
    IconBtn: () => import('components/Buttons/IconBtn.vue'),
  },
  mixins: [showNotif, ExportDataMixin],
  props: {
    storehouseData: {
      type: Array,
      default: () => [],
    },
    func: {
      type: Function,
      default: () => {
      },
    },
  },
  data() {
    return {
      storehouseTableProperties: {
        title: this.$t('storehouse'),
        viewBody: true,
        viewTop: true,
        hideBottom: false,
        columns: [
          {
            name: 'status',
            label: 'Статус',
            align: 'center',
            field: 'status',
            sortable: true,
          },
          {
            name: 'code_place',
            label: this.$t('code'),
            align: 'center',
            field: 'code_place',
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
            name: 'shop',
            label: this.$t('shop'),
            field: 'shop',
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
            name: 'things',
            label: this.$t('things'),
            field: 'things',
            align: 'center',
            sortable: true,
          },
          {
            name: 'created_at',
            label: 'Добавлено',
            field: 'created_at',
            align: 'center',
            sortable: true,
          },
        ],
      },
      storehouseTableReactiveProperties: {
        selected: [],
        visibleColumns: ['place', 'kg', 'category_name', 'code_place', 'notation', 'shop', 'things', 'status', 'created_at'],
      },
    };
  },
  methods: {
    numberFormat,
    thingsFilter,
    fullDate,
    exportStorehouseData() {
      const data = _.isEmpty(this.storehouseTableReactiveProperties.selected) ? this.storehouseData : this.storehouseTableReactiveProperties.selected;
      devlog.log('DATA', data);
      if (!_.isEmpty(data)) {
        this.exportDataToExcel(getUrl('exportStorehouseData'), {
          ids: _.map(data, 'id'),
        }, 'На складе.xlsx');
      }
    },
    async refresh(done) {
      if (!done) {
        this.$q.loading.show();
      }
      this.func()
        .then(() => {
          callFunction(done);
          this.$q.loading.hide();
          this.showNotif('success', 'Данные успешно обновлены.', false);
        })
        .catch(() => {
          this.$q.loading.hide();
          callFunction(done);
        });
    },
  },
};
</script>
