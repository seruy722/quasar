<template>
  <q-page
      data-vue-component-name="InfoCodesActivity"
  >
    <q-card>
      <q-card-section>
        <div class="text-h6 text-center">
          Данные по клиентам которые не получают товар больше месяца
        </div>
      </q-card-section>
      <q-card-section>
        <q-table
            dense
            row-key="id"
            :rows="infoTableData.data"
            :columns="infoTableData.columns"
            :loading="loading"
            :rows-per-page-options="[20, 50, 100]"
            :filter="filter"
            :grid="$q.screen.lt.sm"
        >
          <template #header="props">
            <q-tr :props="props">
              <q-th auto-width />
              <q-th
                  v-for="col in props.cols"
                  :key="col.name"
                  :props="props"
              >
                {{ col.label }}
              </q-th>
            </q-tr>
          </template>

          <template #item="props">
            <div
                class="q-pa-xs col-xs-12 col-sm-6 col-md-4 col-lg-3 grid-style-transition"
            >
              <q-expansion-item
                  expand-separator
                  class="shadow-1 overflow-hidden"
                  style="border-radius: 30px;border: 1px solid #26A69A;"
                  :class="`border_${props.row.id ? 'secondary':'grey'}`"
                  :header-class="`bg-${props.row.id ? 'secondary':'grey'} text-white`"
              >
                <template #header>
                  <q-item-section>
                    <q-item-label lines="5">
                      {{ props.row.code_name }}
                    </q-item-label>
                  </q-item-section>

                  <q-item-section>
                    <q-item-label>
                      {{ props.row.last_active_cargo }}
                    </q-item-label>
                  </q-item-section>

                  <q-item-section>
                    <q-item-label>
                      {{ props.row.city_name }}
                    </q-item-label>
                  </q-item-section>
                </template>

                <q-list
                    separator
                    dense
                >
                  <q-item
                      v-for="col in props.cols.filter(col => col.name !== 'desc' && col.name !== 'phones')"
                      :key="col.name"
                  >
                    <q-item-section>
                      <q-item-label>{{ `${col.label}:` }}</q-item-label>
                    </q-item-section>
                    <q-item-section side>
                      <q-item-label
                          v-if="col.name === 'code'"
                          :lines="3"
                      >
                        <q-badge>{{ col.value }}</q-badge>
                      </q-item-label>
                      <q-item-label
                          v-else
                          lines="5"
                      >
                        {{ col.value }}
                      </q-item-label>
                    </q-item-section>
                  </q-item>
                </q-list>
                <div class="text-center text-bold">
                  Все комментарии
                </div>
                <q-input
                    v-model="comment"
                    placeholder="Комментарий"
                    filled
                    autofocus
                    autogrow
                    clearable
                    dense
                    style="max-width: 500px"
                />
                <q-btn
                    v-show="comment"
                    label="Добавить"
                    color="positive"
                    class="mr-3"
                    dense
                    @click="addComment(props.row.id, comment, props.row.comments)"
                />
                <q-list
                    bordered
                    separator
                    dense
                >
                  <q-item
                      v-for="(item, index) in props.row.comments"
                      :key="index"
                  >
                    <q-item-section avatar>
                      {{ formatToDotDate(item.created_at) }}
                    </q-item-section>
                    <q-item-section>
                      <q-item-label lines="5">
                        {{ item.comment }}
                      </q-item-label>
                    </q-item-section>
                  </q-item>
                </q-list>
              </q-expansion-item>
            </div>
          </template>

          <template #top-right>
            <q-input
                v-model="filter"
                borderless
                dense
                filled
                clearable
                debounce="300"
                placeholder="Поиск"
            >
              <template #append>
                <q-icon name="search" />
              </template>
            </q-input>
          </template>

          <template #body="props">
            <q-tr :props="props">
              <q-td auto-width>
                <q-btn
                    size="sm"
                    color="accent"
                    round
                    dense
                    :icon="props.expand ? 'expand_less' : 'expand_more'"
                    @click="props.expand = !props.expand"
                />
              </q-td>
              <q-td
                  v-for="col in props.cols"
                  :key="col.name"
                  :props="props"
              >
                {{ col.value }}
              </q-td>
            </q-tr>
            <q-tr
                v-show="props.expand"
                :props="props"
            >
              <q-td colspan="100%">
                <div
                    class="q-mb-lg"
                >
                  <q-input
                      v-model="comment"
                      placeholder="Комментарий"
                      filled
                      autofocus
                      autogrow
                      clearable
                      dense
                      style="max-width: 500px"
                  />
                  <q-btn
                      v-show="comment"
                      label="Добавить"
                      color="positive"
                      class="mr-3"
                      dense
                      @click="addComment(props.row.id, comment, props.row.comments)"
                  />
                </div>

                <q-list
                    bordered
                    separator
                    dense
                >
                  <q-item
                      v-for="(item, index) in props.row.comments"
                      :key="index"
                  >
                    <q-item-section avatar>
                      {{ formatToDotDate(item.created_at) }}
                    </q-item-section>
                    <q-item-section>{{ item.comment }}</q-item-section>
                    <q-item-section side>
                      <q-btn
                          label="Удалить"
                          color="negative"
                          dense
                          @click="removeCodesComments(item.id, props.row.comments)"
                      />
                    </q-item-section>
                  </q-item>
                </q-list>
              </q-td>
            </q-tr>
          </template>
        </q-table>
      </q-card-section>
    </q-card>
  </q-page>
</template>

<script>
import { formatToDotDate } from 'src/utils/formatDate';
import { getUrl } from 'src/tools/url';

export default {
  name: 'InfoCodesActivity',
  data() {
    return {
      showAddCommentDialog: false,
      comment: '',
      filter: '',
      loading: false,
      infoTableData: {
        data: [],
        selected: [],
        columns: [
          {
            name: 'code_name',
            label: 'Код',
            align: 'center',
            field: 'code_name',
            sortable: true,
          },
          {
            name: 'client_name',
            label: 'Клиент',
            align: 'center',
            field: 'client_name',
            sortable: true,
          },
          {
            name: 'city_name',
            label: 'Город',
            align: 'center',
            field: 'city_name',
            sortable: true,
          },
          // {
          //   name: 'place',
          //   label: 'Получил за этот год мест',
          //   field: 'place',
          //   align: 'center',
          //   sortable: true,
          // },
          // {
          //   name: 'kg',
          //   label: 'Получил за этот год вес',
          //   field: 'kg',
          //   align: 'center',
          //   sortable: true,
          // },
          {
            name: 'last_active_cargo',
            label: 'Последнее получения товара',
            field: 'last_active_cargo',
            align: 'center',
            sortable: true,
          },
          // {
          //   name: 'last_active_debt',
          //   label: 'Последний перевод',
          //   field: 'last_active_debt',
          //   align: 'center',
          //   sortable: true,
          // },
          {
            name: 'last_comment',
            label: 'Последный комментарий',
            field: 'last_comment',
            align: 'center',
            sortable: true,
          },
          {
            name: 'created_at',
            label: 'Дата добавления клиента',
            field: 'created_at',
            format: (val) => formatToDotDate(val),
            align: 'center',
            sortable: true,
          },
        ],
        visibleColumns: ['code_place', 'code_client_name', 'place', 'kg', 'created_at', 'last_active_cargo', 'last_active_debt', 'city_name'],
      },
    };
  },
  created() {
    this.fetch();
  },
  methods: {
    removeCodesComments(id, comments) {
      this.$axios.get(`${getUrl('removeCodesComments')}/${id}`)
          .then(() => {
            devlog.log('comments', comments);
            const index = _.findIndex(comments, { id });
            if (index !== -1) {
              comments.splice(index, 1);
            }
          });
    },
    addComment(codeId, comment, comments) {
      this.$axios.post(getUrl('addCodesComments'), {
        code_id: codeId,
        comment,
      })
          .then(({ data: { comment: newComment } }) => {
            this.comment = '';
            comments.unshift(newComment);
          });
    },
    fetch() {
      this.loading = true;
      this.$axios.get(getUrl('statisticsForCodes'))
          .then(({ data: { general } }) => {
            devlog.log('RESPON', general);
            const arr = _.map(general, (item) => {
              item.last_comment = _.get(_.first(item.comments), 'comment');
              return item;
            });
            this.infoTableData.data = _.orderBy(arr, ['index'], ['asc']);
            this.loading = false;
          })
          .catch(() => {
            this.loading = false;
          });
    },
    formatToDotDate,
  },
};
</script>
