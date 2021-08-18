<template>
  <q-page
    data-vue-component-name="Search"
  >
    <q-card>
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
            color="primary"
            class="q-mt-md"
            @click="getClientData(clientId, codePlace)"
          />
        </div>
      </q-card-section>
      <q-card-section>
        <q-tabs
          v-model="tab"
          indicator-color="yellow"
          class="bg-primary text-white shadow-2"
        >
          <!--                    <q-tab name="cargo" icon="mail" label="КАРГО">-->
          <!--                      <q-badge floating color="red">{{ badges.cargo }}</q-badge>-->
          <!--                    </q-tab>-->
          <!--                    <q-tab name="debts" icon="alarm" label="ДОЛГИ">-->
          <!--                      <q-badge floating color="red">{{ badges.debts }}</q-badge>-->
          <!--                    </q-tab>-->
          <q-tab
            name="storehouse"
            icon="store"
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
            icon="library_books"
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
            icon="delete"
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
          animated
          swipeable
        >
          <q-tab-panel name="storehouse">
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
          <q-tab-panel name="faxes">
            <BaseTable
              title="Факсы"
              :entry-data="faxesTableData.data"
              :entry-columns="faxesTableData.columns"
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
                            v-else-if="col.field === 'in_cargo'"
                            caption
                          >
                            <q-badge :color="col.value ? 'positive' : 'negative'">
                              {{ col.value ? 'Да' : 'Нет' }}
                            </q-badge>
                          </q-item-label>
                          <q-item-label
                            v-else-if="col.field === 'fax_status'"
                            caption
                          >
                            <q-badge>
                              {{ statusFilter(col.value) }}
                            </q-badge>
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
                    key="fax_status"
                    :props="props"
                  >
                    <q-badge>
                      {{ statusFilter(props.row.fax_status) }}
                    </q-badge>
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
            <!--            <CountDataForSearch :list="faxesTableData.data" title="По факсам"/>-->
          </q-tab-panel>
          <q-tab-panel name="basket">
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
      </q-card-section>
    </q-card>
  </q-page>
</template>

<script>
import getFromSettings from 'src/tools/settings';
import { thingsFilter } from 'src/utils';
import { formatToDotDate } from 'src/utils/formatDate';
import SearchSelect from 'src/components/Elements/SearchSelect.vue';
import BaseTable from 'src/components/Elements/Table/BaseTable.vue';
import CountCategories from 'src/components/CountCategories.vue';

export default {
  name: 'Search',
  components: {
    SearchSelect,
    BaseTable,
    CountCategories,
  },
  data() {
    return {
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
  created() {
    this.fetchClientList();
  },
  methods: {
    statusFilter(value) {
      const options = getFromSettings('transportStatusOptions');
      return _.get(_.find(options, { value }), 'label');
    },
    thingsFilter,
    formatToDotDate,
    async fetchClientList() {
      this.$q.loading.show();
      const { getUrl } = await import('src/tools/url');
      await this.$axios.get(getUrl('codeList'))
        .then(({ data: { codeList } }) => {
          devlog.log('RESPON', codeList);
          this.clientsOptions = codeList;
          this.$q.loading.hide();
        })
        .catch(() => {
          this.$q.loading.hide();
        });
    },
    async getClientData(codeID, codePlace) {
      devlog.log('INPUTDATA', codeID);
      this.$q.loading.show();
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
      await this.$axios.post(getUrl('searchClientData'), sendData)
        .then(({
                 data: {
                   storehouse,
                   faxes,
                   destroyed,
                 },
               }) => {
          this.storehouseTableData.data = storehouse;
          this.faxesTableData.data = faxes;
          this.basketTableData.data = _.map(destroyed, (item) => _.assign(item, JSON.parse(item.history_data)));
          this.$q.loading.hide();
        })
        .catch((errors) => {
          this.$q.loading.hide();
          devlog.log(errors);
        });
    },
  },
};
</script>
