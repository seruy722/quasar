<template>
  <q-page
    data-vue-component-name="CargoDebts"
    class="q-pa-md"
  >
    <SearchSelect
      v-model="clientCode"
      label="Клиент"
      :dense="$q.screen.xs || $q.screen.sm"
      :options="clientCodes"
      style="max-width: 320px;"
    />
    <div class="q-gutter-y-md">
      <q-card>
        <q-tabs
          v-model="tab"
          dense
          class="text-grey"
          active-color="primary"
          indicator-color="primary"
          align="justify"
        >
          <q-tab name="cargo" label="Карго" />
          <q-tab name="debts" label="Долги" />
        </q-tabs>

        <q-separator />

        <q-tab-panels
          v-model="tab"
          animated
          swipeable
        >
          <q-tab-panel name="cargo">
            Lorem ipsum dolor sit amet consectetur adipisicing elit.
          </q-tab-panel>

          <q-tab-panel name="debts">
            Lorem ipsum dolor sit amet consectetur adipisicing elit.
          </q-tab-panel>
        </q-tab-panels>
      </q-card>
    </div>
  </q-page>
</template>

<script>
    import {
        getClientCodes,
        // getCategories,
        // getShopsList,
        // setFormatedDate,
        // setChangeValue,
    } from 'src/utils/FrequentlyCalledFunctions';

    export default {
        name: 'CargoDebts',
        components: {
            SearchSelect: () => import('src/components/Elements/SearchSelect.vue'),
        },
        data() {
            return {
                clientCode: null,
                tab: 'cargo',
            };
        },
        computed: {
            clientCodes() {
                return this.$store.getters['codes/getCodes'];
            },
        },
        created() {
            getClientCodes(this.$store);
        },
    };
</script>
