<template>
  <div data-vue-component-name="StorehouseInfo">
    <div class="column items-center">
      <q-btn
        color="primary"
        label="Выбрать период"
        @click="choosePeriodDialog = true"
      />
      <div>{{ viewPeriodDate }}</div>
    </div>
    <Cargo
      :list="cargo"
    />
    <CountStorehouseCategories
      :list="cargo"
      style="max-width: 500px;margin:0 auto;"
    />
    <Dialog
      :dialog="choosePeriodDialog"
      :persistent="true"
      transition-show="flip-up"
      transition-hide="flip-down"
    >
      <q-card style="max-width: 600px;">
        <q-bar />
        <q-card-section class="q-pt-none">
          <div>
            <div class="row items-center">
              <span>От:</span>
              <DateWithInputForCargo
                v-model:value="period.from"
              />
            </div>
            <div class="row items-center">
              <span>До:</span>
              <DateWithInputForCargo
                v-model:value="period.to"
              />
            </div>
          </div>
        </q-card-section>
        <q-card-actions align="right">
          <OutlineBtn
            label="Отмена"
            dense
            color="negative"
            @click-outline-btn="choosePeriodDialog = false"
          />
          <OutlineBtn
            dense
            color="positive"
            @click-outline-btn="getStorehousePeriodData(period)"
          />
        </q-card-actions>
      </q-card>
    </Dialog>
  </div>
</template>

<script>
import DateWithInputForCargo from 'src/components/DateWithInputForCargo.vue';
import OutlineBtn from 'src/components/Buttons/OutlineBtn.vue';
import Dialog from 'src/components/Dialogs/Dialog.vue';
import Cargo from 'components/Storehouse/Cargo.vue';
import CountStorehouseCategories from 'components/Storehouse/CountStorehouseCategories.vue';

export default {
  name: 'StorehouseInfo',
  components: {
    DateWithInputForCargo,
    OutlineBtn,
    Dialog,
    Cargo,
    CountStorehouseCategories,
  },
  data() {
    const date = new Date().toLocaleDateString()
      .split('.')
      .reverse()
      .join('-');
    return {
      choosePeriodDialog: false,
      viewPeriodDate: '',
      period: {
        from: date,
        to: date,
      },
      cargo: [],
    };
  },
  methods: {
    async getStorehousePeriodData(data) {
      this.choosePeriodDialog = false;
      this.$q.loading.show();
      const { getUrl } = await import('src/tools/url');
      devlog.log(data);
      this.$axios.post(getUrl('getStorehousePeriodData'), data)
        .then(({ data: { cargo } }) => {
          this.cargo = cargo;
          this.$q.loading.hide();
        })
        .catch(() => {
          this.$q.loading.hide();
        });
    },
  },
};
</script>
