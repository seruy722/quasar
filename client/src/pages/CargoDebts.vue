<template>
  <q-page
    data-vue-component-name="CargoDebts"
  >
    <PullRefresh @refresh="refresh">
      <div
        v-show="tab !== 'general'"
        class="row justify-center"
      >
        <SearchSelect
          v-model="currentCodeClientId"
          label="Клиент"
          :dense="$q.screen.xs || $q.screen.sm"
          :options="clientCodes"
          style="max-width: 320px;"
        />
        <CargoDebtsSearch v-model:settings="settings" />
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
              name="general"
              label="Общее"
            />
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
            <q-tab-panel name="general">
              <q-card
                bordered
                style="margin-bottom: 30px;"
              >
                <q-card-section class="text-bold text-center text-uppercase">
                  карго
                </q-card-section>
                <q-card-section>
                  <q-list
                    bordered
                    separator
                  >
                    <q-item style="background-color: lightgrey;">
                      <q-item-section>
                        <q-item-label>Вес</q-item-label>
                      </q-item-section>
                      <q-item-section>
                        <q-item-label>Мест</q-item-label>
                      </q-item-section>
                      <q-item-section>
                        <q-item-label>Скидки</q-item-label>
                      </q-item-section>
                      <q-item-section>
                        <q-item-label>Сумма</q-item-label>
                      </q-item-section>
                    </q-item>
                    <q-item>
                      <q-item-section>
                        <q-item-label>
                          <q-badge>
                            {{ numberFormat(generalCargoData.kg) }}
                          </q-badge>
                        </q-item-label>
                      </q-item-section>
                      <q-item-section>
                        <q-item-label>
                          <q-badge>
                            {{ numberFormat(generalCargoData.place) }}
                          </q-badge>
                        </q-item-label>
                      </q-item-section>
                      <q-item-section>
                        <q-item-label>
                          <q-badge>
                            {{ numberFormat(generalCargoData.sale) }}
                          </q-badge>
                        </q-item-label>
                      </q-item-section>
                      <q-item-section>
                        <q-item-label>
                          <q-badge>
                            {{ numberFormat(generalCargoData.sum) }}
                          </q-badge>
                        </q-item-label>
                      </q-item-section>
                    </q-item>
                  </q-list>
                  <q-card-actions align="right">
                    <ExportMenuGeneralCargo
                      title="Выгрузка по карго"
                      url-name="exportG"
                      url-name-clients="exportGeneralDataByClients"
                      model="cargo"
                    />
                  </q-card-actions>
                </q-card-section>
                <q-inner-loading :showing="generalCargoData.visible">
                  <q-spinner-gears
                    size="50px"
                    color="primary"
                  />
                </q-inner-loading>
              </q-card>

              <q-card
                bordered
              >
                <q-card-section class="text-bold text-center text-uppercase">
                  долги
                </q-card-section>
                <q-card-section>
                  <q-list
                    bordered
                    separator
                  >
                    <q-item style="background-color: lightgrey;">
                      <q-item-section>
                        <q-item-label>Комиссия</q-item-label>
                      </q-item-section>
                      <q-item-section>
                        <q-item-label>Сумма</q-item-label>
                      </q-item-section>
                    </q-item>
                    <q-item>
                      <q-item-section>
                        <q-item-label>
                          <q-badge>
                            {{ numberFormat(generalDebtsData.commission) }}
                          </q-badge>
                        </q-item-label>
                      </q-item-section>
                      <q-item-section>
                        <q-item-label>
                          <q-badge>
                            {{ numberFormat(generalDebtsData.sum) }}
                          </q-badge>
                        </q-item-label>
                      </q-item-section>
                    </q-item>
                  </q-list>
                  <q-card-actions align="right">
                    <ExportMenuGeneralCargo
                      title="Выгрузка по долгах"
                      url-name="exportGeneralDebtsData"
                      url-name-clients="exportGeneralDataByClients"
                      model="debts"
                    />
                  </q-card-actions>
                </q-card-section>
                <q-inner-loading :showing="generalDebtsData.visible">
                  <q-spinner-gears
                    size="50px"
                    color="primary"
                  />
                </q-inner-loading>
              </q-card>
            </q-tab-panel>

            <q-tab-panel
              name="cargo"
              style="padding: 0;"
            >
              <Cargo
                :refresh="refresh"
                :settings="settings"
              />
            </q-tab-panel>

            <q-tab-panel
              name="debts"
              style="padding: 0;"
            >
              <Debts
                :refresh="refresh"
                :settings="settings"
              />
            </q-tab-panel>
          </q-tab-panels>
        </q-card>
      </div>
    </PullRefresh>
  </q-page>
</template>

<script>
import showNotif from 'src/mixins/showNotif';
import { numberFormat } from 'src/utils';
import SearchSelect from 'src/components/Elements/SearchSelect.vue';
import PullRefresh from 'src/components/PullRefresh.vue';
import Cargo from 'src/components/CargoDebts/Cargo.vue';
import Debts from 'src/components/CargoDebts/Debts.vue';
import CargoDebtsSearch from 'src/components/CargoDebts/CargoDebtsSearch.vue';
import ExportMenuGeneralCargo from 'src/components/CargoDebts/ExportMenuGeneralCargo.vue';

export default {
  name: 'CargoDebts',
  components: {
    SearchSelect,
    PullRefresh,
    Cargo,
    Debts,
    CargoDebtsSearch,
    ExportMenuGeneralCargo,
  },
  mixins: [showNotif],
  data() {
    return {
      tab: 'general',
      settings: {},
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
    generalCargoData() {
      return _.get(this.$store.getters['cargoDebts/getGeneralData'], 'cargo') || {
        kg: 0,
        sale: 0,
        place: 0,
        sum: 0,
        visible: true,
      };
    },
    generalDebtsData() {
      return _.get(this.$store.getters['cargoDebts/getGeneralData'], 'debts') || {
        sum: 0,
        commission: 0,
        visible: true,
      };
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
    const { getClientCodes } = await import('src/utils/FrequentlyCalledFunctions');
    getClientCodes(this.$store);
    this.$store.dispatch('cargoDebts/getGeneralData');
  },
  methods: {
    numberFormat,
    getClientData(clientId) {
      this.$q.loading.show();
      this.$store.dispatch('cargoDebts/getCargoDebts', clientId)
        .then(() => {
          this.$q.loading.hide();
        })
        .catch(() => {
          this.$q.loading.hide();
          this.showNotif('error', 'Произошла ошибка при получении данных', 'center');
        });
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
