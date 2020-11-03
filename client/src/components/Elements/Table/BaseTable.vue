<template>
  <q-table
    :data="entryData"
    :columns="entryColumns"
    row-key="id"
    :grid="$q.screen.lt.sm"
    :filter="search"
    :pagination.sync="pagination"
    :rows-per-page-options="[10, 20, 50, 100, 0]"
    separator="cell"
    dense
    data-vue-component-name="BaseTable"
  >
    <template #top>
      <div class="col-2 q-mr-md text-bold">{{ title }}</div>
      <q-space />
      <Search v-model="search" />
    </template>

    <template #item="props">
      <slot name="inner-item" :props="props"></slot>
    </template>

    <template #body="props">
      <slot name="inner-body" :props="props"></slot>
    </template>

  </q-table>
</template>

<script>
    export default {
        name: 'BaseTable',
        components: {
            Search: () => import('src/components/Search.vue'),
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
