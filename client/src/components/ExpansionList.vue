<template>
  <div>
    <q-input
        v-model="search"
        placeholder="Поиск"
        debounce="800"
        clearable
        filled
        dense
    />
    <q-virtual-scroll
        :style="`max-height: ${mainBlock}px;`"
        :items="enterValues"
        separator
    >
      <template #default="{ item }">
        <q-list
            separator
            bordered
            class="rounded-borders"
        >
          <q-expansion-item
              v-model="item.expanded"
              @before-show="beforeShowExpansionPanel(item, childrenValues)"
          >
            <template #header>
              <q-item-section>
                <q-item-label @click.stop="labelCl">
                  {{ item.fax_name }}
                  <q-icon
                      name="link"
                      size="sm"
                      @click.stop="$router.push({ name: 'fax', params: { id: item.fax_id, searchField: 'code_client_name', searchValue: item.code_client_name }})"
                  />
                </q-item-label>
              </q-item-section>
              <q-item-section>
                <q-item-label>{{ `${item.place}м/${item.kg}кг` }}</q-item-label>
              </q-item-section>
              <q-item-section>
                <q-item-label>{{ statusFilter(item.fax_status) }}</q-item-label>
              </q-item-section>
            </template>

            <q-list
                v-if="!$q.screen.xs"
                bordered
                separator
                dense
            >
              <q-item class="text-bold">
                <q-item-section
                    v-for="({ label }, i) in headerColumns"
                    :key="i"
                >
                  <q-item-label>{{ label }}</q-item-label>
                </q-item-section>
              </q-item>

              <q-item
                  v-for="(elem, index) in item.children"
                  :key="index"
              >
                <q-item-section
                    v-for="({field}, i) in headerColumns"
                    :key="i"
                >
                  <q-item-label v-if="field === 'things'">
                    {{ thingsFilter(elem[field]) }}
                  </q-item-label>
                  <q-item-label v-else-if="field === 'created_at'">
                    {{ formatToDotDate(elem[field]) }}
                  </q-item-label>
                  <q-item-label v-else>{{ elem[field] }}</q-item-label>
                </q-item-section>
              </q-item>
            </q-list>
            <div v-else>
              <q-list
                  v-for="(itemm, index) in item.children"
                  :key="index"
                  bordered
                  separator
                  dense
                  style="margin-bottom: 5px;border-color: #00e676;"
              >
                <q-item
                    v-for="(el, ind) in headerColumns"
                    :key="ind"
                >
                  <q-item-section>
                    <q-item-label>{{ el.label }}</q-item-label>
                  </q-item-section>
                  <q-item-section side>
                    <q-item-label v-if="el.field === 'things'">
                      {{ thingsFilter(itemm[el.field]) }}
                    </q-item-label>
                    <q-item-label v-else-if="el.field === 'created_at'">
                      {{ formatToDotDate(itemm[el.field]) }}
                    </q-item-label>
                    <q-item-label v-else>
                      {{ itemm[el.field] }}
                    </q-item-label>
                  </q-item-section>
                </q-item>
              </q-list>
            </div>
            <CountCargoCategories
                :list="item.children"
                style="max-width: 600px;margin:0 auto;"
            />
          </q-expansion-item>
        </q-list>
      </template>
    </q-virtual-scroll>
  </div>
</template>

<script>
import CountCargoCategories from 'src/components/CargoDebts/CountCargoCategories.vue';
import { formatToDotDate } from 'src/utils/formatDate';
import getFromSettings from 'src/tools/settings';
import { thingsFilter } from 'src/utils';

export default {
  name: 'ExpansionList',
  components: {
    CountCargoCategories,
  },
  props: {
    enterValues: {
      type: Array,
      required: true,
    },
    headerColumns: {
      type: Array,
      required: true,
    },
    childrenValues: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      search: null,
    };
  },
  computed: {
    mainBlock() {
      return document.documentElement.clientHeight - 220;
    },
  },
  methods: {
    labelCl() {
      devlog.log('labelCl');
    },
    formatToDotDate,
    thingsFilter,
    beforeShowExpansionPanel(item, childrenValues) {
      if (_.isEmpty(item.children)) {
        const find = _.find(childrenValues, { faxId: item.fax_id });
        if (find) {
          item.children = find.values;
        }
      }
    },
    statusFilter(value) {
      const options = getFromSettings('transportStatusOptions');
      return _.get(_.find(options, { value }), 'label');
    },
  },
};
</script>
