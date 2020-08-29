<template>
  <q-page
    data-vue-component-name="PaymentArrears"
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
            <Cargo
              :list="cargo"
            />
          </q-tab-panel>

          <q-tab-panel
            name="debts"
            style="padding: 0;"
          >
            <Debts
              :list="debts"
            />
          </q-tab-panel>
        </q-tab-panels>
      </q-card>
    </div>
  </q-page>
</template>

<script>
    export default {
        name: 'PaymentArrears',
        components: {
            Cargo: () => import('components/PaymentArrears/Cargo.vue'),
            Debts: () => import('components/PaymentArrears/Debts.vue'),
        },
        data() {
            return {
                tab: 'cargo',
                debts: [],
                cargo: [],
            };
        },
        async created() {
            this.getPaymentArrears();
        },
        methods: {
            async getPaymentArrears() {
                const { getUrl } = await import('src/tools/url');
                this.$q.loading.show();
                return this.$axios.get(getUrl('paymentArrears'))
                  .then(async ({ data: { debts, cargo } }) => {
                      const { fullDate } = await import('src/utils/formatDate');
                      const { combineCargoData } = await import('src/utils/FrequentlyCalledFunctions');
                      this.debts = _.map(_.orderBy(debts, (item) => new Date(item.created_at), 'asc'), (item) => _.assign({}, item, { created_at: fullDate(item.created_at) }));
                      const clientIds = _.uniq(_.map(cargo, 'code_client_id'));
                      const cargoArr = [];
                      _.forEach(clientIds, (id) => {
                          cargoArr.push(...combineCargoData(_.filter(cargo, { code_client_id: id })));
                      });
                      this.cargo = _.map(_.orderBy(cargoArr, (item) => new Date(item.created_at), 'asc'), (item) => _.assign({}, item, { created_at: fullDate(item.created_at) }));
                      this.$q.loading.hide();
                  })
                  .catch(() => {
                      devlog.error('PaymentArrear');
                  });
            },
        },
    };
</script>
