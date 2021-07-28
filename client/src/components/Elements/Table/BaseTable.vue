<template>
  <q-table
    v-model:pagination="pagination"
    :rows="entryData"
    :columns="entryColumns"
    row-key="id"
    :grid="$q.screen.lt.sm"
    :filter="search"
    :rows-per-page-options="[10, 20, 50, 100, 0]"
    separator="cell"
    dense
    data-vue-component-name="BaseTable"
  >
    <template #top>
      <div class="col-2 q-mr-md text-bold">
        {{ title }}
      </div>
      <q-space />
      <Search v-model="search" />
    </template>

    <template #item="props">
      <slot
        name="inner-item"
        :props="props"
      />
    </template>

    <template #body="props">
      <slot
        name="inner-body"
        :props="props"
      />
    </template>
  </q-table>
</template>

<script>
import Search from 'src/components/Search.vue';

export default {
  name: 'BaseTable',
  components: {
    Search,
  },
  props: {
    title: {
      type: String,
      default: 'Таблица',
    },
    entryData: {
      type: Array,
      default: () => [],
    },
    entryColumns: {
      type: Array,
      default: () => [],
    },
  },
  data() {
    return {
      search: null,
      pagination: {
        rowsPerPage: 20,
      },
    };
  },
};
</script>
