<template>
  <div
    class="q-pa-md q-gutter-sm"
    data-vue-component-name="DialogNotDeliveredCargo"
  >
    <q-dialog
      v-model="dialog"
      persistent
      maximized
      transition-show="slide-up"
      transition-hide="slide-down"
    >
      <q-card>
        <q-bar>
          <q-space />
          <q-btn
            v-close-popup
            dense
            flat
            icon="close"
            color="negative"
          >
            <q-tooltip content-class="bg-white text-primary">Закрыть</q-tooltip>
          </q-btn>
        </q-bar>

        <q-card-section>
          <div class="text-h6">
            Не доставленные места
            <q-btn
              label="Показать"
              color="primary"
              @click="getNotDeliveredCargo"
            />
          </div>
        </q-card-section>

        <q-card-section class="q-pt-none">
          <div class="q-gutter-y-md">
            <q-card>
              <Cargo
                :list="cargo"
              />
            </q-card>
          </div>
        </q-card-section>
      </q-card>
    </q-dialog>
  </div>
</template>

<script>
    export default {
        name: 'DialogNotDeliveredCargo',
        components: {
            Cargo: () => import('components/PaymentArrears/Cargo.vue'),
        },
        props: {
            show: {
                type: Boolean,
                default: false,
            },
        },
        data() {
            return {
                cargo: [],
            };
        },
        computed: {
            dialog: {
                get: function get() {
                    return this.show;
                },
                set: function set(val) {
                    this.$emit('update:show', val);
                },
            },
        },
        methods: {
            async getNotDeliveredCargo() {
                this.$q.loading.show();
                const { getUrl } = await import('src/tools/url');
                this.$axios.get(getUrl('getNotDeliveredCargo'))
                  .then(({ data: { cargo } }) => {
                      this.cargo = cargo;
                      devlog.log(cargo);
                      this.$q.loading.hide();
                  });
            },
        },
    };
</script>
