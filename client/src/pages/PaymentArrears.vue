<template>
  <q-page
    data-vue-component-name="PaymentArrears"
  >
    <PullRefresh @refresh="refresh">
      <!--      <div-->
      <!--        v-show="tab !== 'general'"-->
      <!--        class="row justify-center"-->
      <!--      >-->
      <!--        <SearchSelect-->
      <!--          v-model="currentCodeClientId"-->
      <!--          label="Клиент"-->
      <!--          :dense="$q.screen.xs || $q.screen.sm"-->
      <!--          :options="clientCodes"-->
      <!--          style="max-width: 320px;"-->
      <!--        />-->
      <!--        <CargoDebtsSearch />-->
      <!--      </div>-->
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
              <Cargo :refresh="refresh" />
            </q-tab-panel>

            <q-tab-panel
              name="debts"
              style="padding: 0;"
            >
              <Debts :refresh="refresh" />
            </q-tab-panel>
          </q-tab-panels>
        </q-card>
      </div>
    </PullRefresh>
  </q-page>
</template>

<script>
    import showNotif from 'src/mixins/showNotif';

    export default {
        name: 'PaymentArrears',
        components: {
            // SearchSelect: () => import('src/components/Elements/SearchSelect.vue'),
            PullRefresh: () => import('src/components/PullRefresh.vue'),
            Cargo: () => import('src/components/CargoDebts/Cargo.vue'),
            Debts: () => import('src/components/CargoDebts/Debts.vue'),
            // CargoDebtsSearch: () => import('src/components/CargoDebts/CargoDebtsSearch.vue'),
            // ExportMenuGeneralCargo: () => import('src/components/CargoDebts/ExportMenuGeneralCargo.vue'),
        },
        mixins: [showNotif],
        data() {
            return {
                tab: 'cargo',
                debts: [],
                cargo: [],
            };
        },
        computed: {
            // clientCodes() {
            //     return this.$store.getters['codes/getCodes'];
            // },
            // cargo() {
            //     return this.$store.getters['cargoDebts/getCargo'];
            // },
            // debts() {
            //     return this.$store.getters['cargoDebts/getDebts'];
            // },
            // currentCodeClientId: {
            //     get: function get() {
            //         return this.$store.getters['cargoDebts/getCurrentCodeClientId'];
            //     },
            //     set: function set(val) {
            //         this.$store.dispatch('cargoDebts/setCurrentCodeClientId', val);
            //     },
            // },
        },
        // watch: {
        //     currentCodeClientId(id) {
        //         if (id) {
        //             this.getClientData(id);
        //         }
        //     },
        // },
        async created() {
            // const { getClientCodes } = await import('src/utils/FrequentlyCalledFunctions');
            // getClientCodes(this.$store);
            // this.$store.dispatch('cargoDebts/getGeneralData');
            // const { getUrl } = await import('src/tools/url');
            // this.$axios.get(getUrl('paymentArrears'))
            //   .then(({ data }) => {
            //       devlog.log(data);
            //   })
            //   .catch(() => {
            //       devlog.error('PaymentArrear');
            //   });
            this.getPaymentArrears();
        },
        methods: {
            getClientData(clientId) {
                this.$store.dispatch('cargoDebts/getCargoDebts', clientId);
            },
            async getPaymentArrears() {
                const { getUrl } = await import('src/tools/url');
                return this.$axios.get(getUrl('paymentArrears'))
                  .then(({ data: { debts, cargo } }) => {
                      this.debts = debts;
                      this.cargo = cargo;
                  })
                  .catch(() => {
                      devlog.error('PaymentArrear');
                  });
            },
            async refresh(done) {
                if (!done) {
                    this.$q.loading.show();
                }
                const { callFunction } = await import('src/utils/index');
                this.getPaymentArrears()
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
