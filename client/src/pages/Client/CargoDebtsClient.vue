<template>
  <q-page
      data-vue-component-name="CargoDebtsClient"
  >
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
          <q-tab
              name="storehouseData"
              label="Статус"
          >
            <q-badge color="red">
              {{ storehouseData.length }}
            </q-badge>
          </q-tab>
          <q-tab
              name="cargo"
              label="Получено"
          >
            <q-badge color="red">
              {{ cargo.length }}
            </q-badge>
          </q-tab>
          <q-tab
              name="debts"
              label="Переводы"
          >
            <q-badge color="red">
              {{ debts.length }}
            </q-badge>
          </q-tab>
        </q-tabs>

        <q-separator />

        <q-tab-panels
            v-model="tab"
            animated
            swipeable
        >
          <q-tab-panel
              name="cargo"
              style="padding: 0;"
          >
            <Cargo
                :refresh="refresh"
            />
          </q-tab-panel>
          <q-tab-panel
              name="debts"
              style="padding: 0;"
          >
            <Debts
                :refresh="refresh"
            />
          </q-tab-panel>
          <q-tab-panel
              name="storehouseData"
              style="padding: 0;"
          >
            <Storehouse
                :storehouse-data="storehouseData"
                :func="getStorehouseData"
            />
          </q-tab-panel>
        </q-tab-panels>
      </q-card>
    </div>
  </q-page>
</template>

<script>
import showNotif from 'src/mixins/showNotif';
import Cargo from 'src/components/Client/Cargo.vue';
import Debts from 'src/components/Client/Debts.vue';
import Storehouse from 'src/components/Storehouse.vue';

export default {
  name: 'CargoDebtsClient',
  components: {
    Cargo,
    Debts,
    Storehouse,
  },
  mixins: [showNotif],
  data() {
    return {
      tab: 'cargo',
      storehouseData: [],
    };
  },
  computed: {
    cargo() {
      return this.$store.getters['cargoDebts/getCargo'];
    },
    debts() {
      return this.$store.getters['cargoDebts/getDebts'];
    },
  },
  watch: {
    tab(val) {
      if (val === 'storehouseData' && _.isEmpty(this.storehouseData)) {
        this.$q.loading.show();
        this.getStorehouseData();
      }
    },
  },
  created() {
    this.getClientData();
  },
  methods: {
    getClientData() {
      return this.$store.dispatch('cargoDebts/getCargoDebts', null);
    },
    async getStorehouseData() {
      const { getUrl } = await import('src/tools/url');
      return this.$axios.get(getUrl('getClientStorehouseData'))
          .then(({ data: { clientStorehouseData } }) => {
            this.storehouseData = _.uniqBy(clientStorehouseData, 'code_place');
            this.$q.loading.hide();
          });
    },
    async refresh(done) {
      if (!done) {
        this.$q.loading.show();
      }
      const { callFunction } = await import('src/utils/index');
      this.getClientData()
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
  },
};
</script>
