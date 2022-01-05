<template>
  <q-page
      data-vue-component-name="SearchPage"
  >
    <q-toggle
        v-model="visible"
        label="Показать панель поиска"
    />
    <q-slide-transition>
      <div v-show="visible">
        <q-card-section>
          <div class="text-h6 text-center">
            Поиск данных по таблицам
          </div>
          <div style="max-width: 300px; text-align: center;">
            <q-select
                v-model="model"
                :options="modelOptions"
                label="Выберите поле для поиска"
            />
            <SearchSelect
                v-show="model === 'Клиент'"
                v-model="clientId"
                label="Выберите код клиента"
                style="max-width: 300px;"
                :dense="$q.screen.xs || $q.screen.sm"
                :func-load-data="fetchClientList"
                :options="clientsOptions"
            />
            <q-input
                v-show="model === 'Код'"
                v-model="codePlace"
                mask="###/###/###"
                label="Введите код"
                @keyup.enter="getClientData(clientId, codePlace)"
            />
            <q-btn
                label="Найти"
                color="positive"
                class="q-mt-md"
                dense
                :loading="loading"
                @click.stop="getClientData(clientId, codePlace)"
            />
          </div>
        </q-card-section>

      </div>
    </q-slide-transition>
    <q-tabs
        v-model="tab"
        indicator-color="yellow"
        class="bg-primary text-white shadow-2"
    >
      <q-tab
          name="storehouse"
          label="СКЛАД"
      >
        <q-badge
            floating
            color="red"
        >
          {{ storehouseTableData.data.length }}
        </q-badge>
      </q-tab>
      <q-tab
          name="faxes"
          label="ФАКСЫ"
      >
        <q-badge
            floating
            color="red"
        >
          {{ faxesTableData.data.length }}
        </q-badge>
      </q-tab>
      <q-tab
          name="basket"
          label="Корзина"
      >
        <q-badge
            floating
            color="red"
        >
          {{ basketTableData.data.length }}
        </q-badge>
      </q-tab>
    </q-tabs>

    <q-separator />

    <q-tab-panels
        v-model="tab"
    >
      <q-tab-panel
          name="storehouse"
          class="tab-panel"
      >
        <BaseTable
            title="Склад"
            :entry-data="storehouseTableData.data"
            :entry-columns="storehouseTableData.columns"
        >
          <template #inner-item="{props}">
            <div
                class="q-pa-xs col-xs-12 col-sm-6 col-md-4 col-lg-3 grid-style-transition"
                :style="props.selected ? 'transform: scale(0.95);' : ''"
            >
              <q-card :class="props.selected ? 'bg-grey-2' : ''">
                <q-list
                    dense
                    separator
                >
                  <q-item
                      v-for="col in props.cols.filter(col => col.name !== 'desc')"
                      :key="col.name"
                  >
                    <q-item-section>
                      <q-item-label>{{ col.label }}</q-item-label>
                    </q-item-section>
                    <q-item-section side>
                      <q-item-label
                          v-if="col.field === 'things'"
                          caption
                          lines="4"
                      >
                        {{ thingsFilter(col.value) }}
                      </q-item-label>
                      <q-item-label
                          v-else-if="col.field === 'created_at'"
                          caption
                      >
                        {{ formatToDotDate(col.value) }}
                      </q-item-label>
                      <q-item-label
                          v-else
                          caption
                      >
                        {{ col.value }}
                      </q-item-label>
                    </q-item-section>
                  </q-item>
                </q-list>
              </q-card>
            </div>
          </template>

          <template #inner-body="{props}">
            <q-tr :props="props">
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
                {{ props.row.place }}
              </q-td>
              <q-td
                  key="kg"
                  :props="props"
              >
                {{ props.row.kg }}
              </q-td>
              <q-td
                  key="category_name"
                  :props="props"
              >
                {{ props.row.category_name }}
              </q-td>
              <q-td
                  key="user_name"
                  :props="props"
              >
                {{ props.row.user_name }}
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
                  key="created_at"
                  :props="props"
              >
                {{ formatToDotDate(props.row.created_at) }}
              </q-td>
              <q-td
                  key="things"
                  :props="props"
              >
                {{ thingsFilter(props.row.things) }}
              </q-td>
            </q-tr>
          </template>
        </BaseTable>
        <CountCategories
            :list="storehouseTableData.data"
            style="max-width: 600px;margin:0 auto;"
        />
      </q-tab-panel>
      <q-tab-panel
          name="faxes"
          class="tab-panel"
      >
        <keep-alive>
          <ExpansionList
              :enter-values="faxesTableData.data"
              :header-columns="headerColumns"
              :children-values="childrenValues"
          />
        </keep-alive>
      </q-tab-panel>
      <q-tab-panel
          name="basket"
          class="tab-panel"
      >
        <BaseTable
            title="Корзина"
            :entry-data="basketTableData.data"
            :entry-columns="basketTableData.columns"
        >
          <template #inner-item="{props}">
            <div
                class="q-pa-xs col-xs-12 col-sm-6 col-md-4 col-lg-3 grid-style-transition"
                :style="props.selected ? 'transform: scale(0.95);' : ''"
            >
              <q-card :class="props.selected ? 'bg-grey-2' : ''">
                <q-list
                    dense
                    separator
                >
                  <q-item
                      v-for="col in props.cols.filter(col => col.name !== 'desc')"
                      :key="col.name"
                  >
                    <q-item-section>
                      <q-item-label>{{ col.label }}</q-item-label>
                    </q-item-section>
                    <q-item-section side>
                      <q-item-label
                          v-if="col.field === 'things'"
                          caption
                          lines="4"
                      >
                        {{ thingsFilter(col.value) }}
                      </q-item-label>
                      <q-item-label
                          v-else-if="col.field === 'created_at'"
                          caption
                      >
                        {{ formatToDotDate(col.value) }}
                      </q-item-label>
                      <q-item-label
                          v-else
                          caption
                      >
                        {{ col.value }}
                      </q-item-label>
                    </q-item-section>
                  </q-item>
                </q-list>
              </q-card>
            </div>
          </template>

          <template #inner-body="{props}">
            <q-tr :props="props">
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
                {{ props.row.place }}
              </q-td>
              <q-td
                  key="kg"
                  :props="props"
              >
                {{ props.row.kg }}
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
                <router-link :to="{ name: 'fax', params: { id: props.row.fax_id }}">
                  {{ props.row.fax_name }}
                </router-link>
              </q-td>
              <q-td
                  key="user_name"
                  :props="props"
              >
                {{ props.row.user_name }}
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
                  key="created_at"
                  :props="props"
              >
                {{ formatToDotDate(props.row.created_at) }}
              </q-td>
              <q-td
                  key="things"
                  :props="props"
              >
                {{ thingsFilter(props.row.things) }}
              </q-td>
            </q-tr>
          </template>
        </BaseTable>
      </q-tab-panel>
    </q-tab-panels>
  </q-page>
</template>

<script>
import getFromSettings from 'src/tools/settings';
import { thingsFilter } from 'src/utils';
import { formatToDotDate } from 'src/utils/formatDate';
import SearchSelect from 'src/components/Elements/SearchSelect.vue';
import BaseTable from 'src/components/Elements/Table/BaseTable.vue';
import CountCategories from 'src/components/CountCategories.vue';
// import CountCargoCategories from 'src/components/CargoDebts/CountCargoCategories.vue';
import ExpansionList from 'src/components/ExpansionList.vue';

export default {
  name: 'SearchPage',
  components: {
    SearchSelect,
    BaseTable,
    CountCategories,
    // CountCargoCategories,
    ExpansionList,
  },
  data() {
    return {
      childrenValues: [],
      loading: false,
      visible: true,
      faxesTableDataDuplicate: [],
      headerColumns: [
        {
          label: 'Клиент',
          field: 'code_client_name',
        },
        {
          label: 'Код',
          field: 'code_place',
        },
        {
          label: 'Мест',
          field: 'place',
        },
        {
          label: 'Вес',
          field: 'kg',
        },
        {
          label: 'Категория',
          field: 'category_name',
        },
        {
          label: 'Магазин',
          field: 'shop',
        },
        {
          label: 'Дата добавления',
          field: 'created_at',
        },
        {
          label: 'Опись',
          field: 'things',
        },
        {
          label: 'Примечания',
          field: 'notation',
        },
      ],
      model: 'Клиент',
      modelOptions: ['Клиент', 'Код'],
      clientId: null,
      codePlace: null,
      clientsOptions: [],
      tab: 'storehouse',
      storehouseTableData: {
        data: [],
        selected: [],
        columns: [
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
            name: 'category_name',
            label: 'Категория',
            field: 'category_name',
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
            name: 'notation',
            label: 'Примечания',
            field: 'notation',
            align: 'center',
            sortable: true,
          },
          {
            name: 'created_at',
            label: 'Дата добавления',
            field: 'created_at',
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
        ],
        visibleColumns: ['code_place', 'code_client_name', 'place', 'kg', 'category_name', 'shop', 'notation', 'things', 'created_at'],
      },
      basketTableData: {
        data: [],
        selected: [],
        columns: [
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
            name: 'user_name',
            label: 'Пользователь',
            field: 'user_name',
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
            name: 'notation',
            label: 'Примечания',
            field: 'notation',
            align: 'center',
            sortable: true,
          },
          {
            name: 'created_at',
            label: 'Дата удаления',
            field: 'created_at',
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
        ],
        visibleColumns: ['code_place', 'code_client_name', 'place', 'fax_name', 'kg', 'category_name', 'shop', 'notation', 'things', 'created_at'],
      },
      faxesTableData: {
        data: [],
        selected: [],
        columns: [
          {
            name: 'expand',
            label: 'Развернуть',
            align: 'center',
            field: 'expand',
            sortable: false,
          },
          // {
          //   name: 'code_place',
          //   label: 'Код',
          //   align: 'center',
          //   field: 'code_place',
          //   sortable: true,
          // },
          {
            name: 'code_client_name',
            label: 'Клиент',
            align: 'center',
            field: 'code_client_name',
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
          //   name: 'category_name',
          //   label: 'Категория',
          //   field: 'category_name',
          //   align: 'center',
          //   sortable: true,
          // },
          {
            name: 'fax_name',
            label: 'Факс',
            field: 'fax_name',
            align: 'center',
            sortable: true,
          },
          {
            name: 'fax_status',
            label: 'Статус',
            field: 'fax_status',
            align: 'center',
            sortable: true,
          },
          // {
          //   name: 'user_name',
          //   label: 'Пользователь',
          //   field: 'user_name',
          //   align: 'center',
          //   sortable: true,
          // },
          // {
          //   name: 'shop',
          //   label: 'Магазин',
          //   field: 'shop',
          //   align: 'center',
          //   sortable: true,
          // },
          // {
          //   name: 'notation',
          //   label: 'Примечания',
          //   field: 'notation',
          //   align: 'center',
          //   sortable: true,
          // },
          // {
          //   name: 'in_cargo',
          //   label: 'Доставлен',
          //   field: 'in_cargo',
          //   align: 'center',
          //   sortable: true,
          // },
          // {
          //   name: 'created_at',
          //   label: 'Дата',
          //   field: 'created_at',
          //   align: 'center',
          //   sortable: true,
          // },
          // {
          //   name: 'things',
          //   label: 'Опись',
          //   field: 'things',
          //   align: 'center',
          //   sortable: true,
          // },
        ],
        expandedColumns: [
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
            name: 'fax_status',
            label: 'Статус',
            field: 'fax_status',
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
          {
            name: 'shop',
            label: 'Магазин',
            field: 'shop',
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
            name: 'in_cargo',
            label: 'Доставлен',
            field: 'in_cargo',
            align: 'center',
            sortable: true,
          },
          {
            name: 'created_at',
            label: 'Дата',
            field: 'created_at',
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
        ],
        visibleColumns: ['code_place', 'code_client_name', 'place', 'fax_name', 'in_cargo', 'kg', 'category_name', 'shop', 'notation', 'things', 'created_at', 'fax_status'],
      },
    };
  },
  watch: {
    search(val) {
      if (val) {
        const valToLower = val ? val.toLowerCase() : '';
        devlog.log('valToLower', valToLower);
        this.faxesTableData.data = _.filter(this.faxesTableData.data, (item) => {
          devlog.log('eeee', item.fax_name.toLowerCase()
              .indexOf(valToLower));
          return item.fax_name.toLowerCase()
              .indexOf(valToLower) > -1;
        });
      } else {
        this.faxesTableData.data = _.clone(this.faxesTableDataDuplicate);
        _.forEach(this.faxesTableData.data, (item) => {
          if (item.expanded) {
            item.expanded = false;
          }
        });
      }
      devlog.log('res', this.faxesTableData.data);
    },
  },
  methods: {
    statusFilter(value) {
      const options = getFromSettings('transportStatusOptions');
      return _.get(_.find(options, { value }), 'label');
    },
    thingsFilter,
    formatToDotDate,
    async fetchClientList() {
      const { getUrl } = await import('src/tools/url');
      await this.$axios.get(getUrl('codeList'))
          .then(({ data: { codeList } }) => {
            devlog.log('RESPON', codeList);
            this.clientsOptions = codeList;
          })
          .catch(() => {
            devlog.error('Не загрузился список клиентов');
          });
    },
    async getClientData(codeID, codePlace) {
      if (codeID || codePlace) {
        this.loading = true;
        devlog.log('INPUTDATA', codeID);
        const sendData = {
          codeID,
          codePlace,
        };
        if (this.model === 'Код') {
          sendData.codeID = null;
        } else if (this.model === 'Клиент') {
          sendData.codePlace = null;
        }
        const { getUrl } = await import('src/tools/url');
        this.$axios.post(getUrl('searchClientData'), sendData)
            .then(({
                     data: {
                       storehouse,
                       faxes,
                       destroyed,
                     },
                   }) => {
              this.storehouseTableData.data = storehouse;
              const group = _.groupBy(faxes, 'fax_name');
              devlog.log('group', group);
              const arr = [];
              const childrenValues = [];
              _.forEach(group, (item) => {
                _.forEach(item, (elem) => {
                  elem.type = 0;
                });
                const {
                  code_client_name,
                  fax_name,
                  fax_status,
                  fax_id,
                  id,
                } = _.first(item);
                arr.push({
                  id,
                  fax_id,
                  code_client_name,
                  fax_name,
                  fax_status,
                  expanded: false,
                  place: _.sumBy(item, 'place'),
                  kg: _.sumBy(item, 'kg'),
                  children: [],
                });
                childrenValues.push({ faxId: fax_id, values: item });
              });
              devlog.log('faxesTableData', arr);
              devlog.log('chik', childrenValues);
              this.childrenValues = childrenValues;
              this.faxesTableData.data = arr;
              this.faxesTableDataDuplicate = arr;
              this.basketTableData.data = _.map(destroyed, (item) => _.assign(item, JSON.parse(item.history_data)));
              this.loading = false;
              this.visible = false;
              if (_.isEmpty(this.storehouseTableData.data)) {
                this.tab = 'faxes';
              }
            })
            .catch((errors) => {
              this.loading = false;
              devlog.log(errors);
            });
      }
    },
  },
};
</script>

<style lang="scss" scoped>
.tab-panel {
  padding: 0;
}
</style>
