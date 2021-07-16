<template>
  <q-page
    data-vue-component-name="CargoDebtsAssistant"
  >
    <PullRefresh @refresh="refresh">
      <div
        class="row justify-center"
      >
        <SearchSelect
          v-model="currentCodeClientId"
          label="Клиент"
          :dense="$q.screen.xs || $q.screen.sm"
          :options="clientCodes"
          style="max-width: 320px;"
        />
        <CargoDebtsSearch />
      </div>
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
              name="cargo"
              label="Карго"
            >
              <q-badge color="red">
                {{ cargo.length }}
              </q-badge>
            </q-tab>
            <q-tab
              name="debts"
              label="Долги"
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
              <CargoAssistant :refresh="refresh" />
            </q-tab-panel>

            <q-tab-panel
              name="debts"
              style="padding: 0;"
            >
              <DebtsAssistant :refresh="refresh" />
            </q-tab-panel>
          </q-tab-panels>
        </q-card>
      </div>
    </PullRefresh>
  </q-page>
</template>

<script>
import SearchSelect from 'src/components/Elements/SearchSelect.vue';
import PullRefresh from 'src/components/PullRefresh.vue';
import CargoAssistant from 'src/components/CargoDebts/Assistant/CargoAssistant.vue';
import DebtsAssistant from 'src/components/CargoDebts/Assistant/DebtsAssistant.vue';
import CargoDebtsSearch from 'src/components/CargoDebts/CargoDebtsSearch.vue';

export default {
  name: 'CargoDebtsAssistant',
  components: {
    SearchSelect,
    PullRefresh,
    CargoAssistant,
    DebtsAssistant,
    CargoDebtsSearch,
  },
  data() {
    return {
      tab: 'cargo',
    };
  },
  computed: {
    clientCodes() {
      return this.$store.getters['codes/getCodes'];
    },
    cargo() {
      return this.$store.getters['cargoDebts/getCargo'];
    },
    debts() {
      return this.$store.getters['cargoDebts/getDebts'];
    },
    currentCodeClientId: {
      get: function get() {
        return this.$store.getters['cargoDebts/getCurrentCodeClientId'];
      },
      set: function set(val) {
        this.$store.dispatch('cargoDebts/setCurrentCodeClientId', val);
      },
    },
  },
  watch: {
    currentCodeClientId(id) {
      if (id) {
        this.getClientData(id);
      }
    },
  },
  async created() {
    const { getUrl } = await import('src/tools/url');
    if (_.isEmpty(this.clientCodes)) {
      this.$axios.get(getUrl('codesAssistant'))
        .then(({ data: { codes } }) => {
          this.$store.dispatch('codes/setCodesAssistant', codes);
        })
        .catch(() => {
          devlog.log('EROR', 'codesAssistant');
        });
    }
  },
  methods: {
    getClientData(clientId) {
      this.$store.dispatch('cargoDebts/getCargoDebts', clientId);
    },
    async refresh(done) {
      if (!done) {
        this.$q.loading.show();
      }
      const { callFunction } = await import('src/utils/index');
      this.$store.dispatch('cargoDebts/getGeneralData');
      this.$store.dispatch('cargoDebts/getCargoDebts', this.currentCodeClientId)
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
