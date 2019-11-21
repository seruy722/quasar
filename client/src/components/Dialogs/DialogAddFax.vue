<template>
  <div
    data-vue-component-name="DialogAddFax"
  >
    <Dialog
      :dialog.sync="show"
      title="Fax"
      :persistent="true"
    >
      <template v-slot:body>
        <q-card-section class="q-mt-md">
          <!--          <BaseInput-->
          <!--            v-model="faxData.departureDate.value"-->
          <!--            label="Дата отправления"-->
          <!--            type="text"-->
          <!--            filled-->
          <!--            :field="faxData.departureDate.field"-->
          <!--            :dense="$q.screen.xs || $q.screen.sm"-->
          <!--            :errors="errorsData"-->
          <!--          >-->
          <!--            <template v-slot:append>-->
          <!--              <Date :value.sync="faxData.departureDate.value" />-->
          <!--            </template>-->
          <!--          </BaseInput>-->

          <BaseInput
            v-model="faxData.name.value"
            :label="faxData.name.label"
            :type="faxData.name.type"
            filled
            :autofocus="true"
            :field="faxData.name.field"
            :dense="$q.screen.xs || $q.screen.sm"
            :errors="errorsData"
          />

          <SelectWithSearchInput
            v-model="faxData.transporter_id.value"
            :label="faxData.transporter_id.label"
            :dense="$q.screen.xs || $q.screen.sm"
            :field="faxData.transporter_id.field"
            :options="transporters"
            :errors="errorsData"
          />

          <SelectWithSearchInput
            v-model="faxData.transport_id.value"
            :label="faxData.transport_id.label"
            :dense="$q.screen.xs || $q.screen.sm"
            :field="faxData.transport_id.field"
            :options="transport"
            :errors="errorsData"
          />

          <SelectWithSearchInput
            v-model="faxData.status.value"
            :label="faxData.status.label"
            :dense="$q.screen.xs || $q.screen.sm"
            :field="faxData.status.field"
            :options="faxData.status.options"
            :errors="errorsData"
          />

        </q-card-section>

        <q-separator />

        <q-card-actions align="right">
          <OutlineBtn
            label="Сохранить"
            color="positive"
            @clickOutlineBtn="checkErrors(faxData, saveFax)"
          />

          <OutlineBtn
            label="Закрыть"
            color="negative"
            @clickOutlineBtn="show = false"
          />
        </q-card-actions>
      </template>
    </Dialog>
  </div>
</template>

<script>
    import { getUrl } from 'src/tools/url';
    import showNotif from 'src/mixins/showNotif';
    import CheckErrorsMixin from 'src/mixins/CheckErrors';
    import getFromSettings from 'src/tools/settings';

    export default {
        name: 'DialogAddFax',
        components: {
            Dialog: () => import('src/components/Dialogs/Dialog.vue'),
            OutlineBtn: () => import('src/components/Buttons/OutlineBtn.vue'),
            BaseInput: () => import('src/components/Elements/BaseInput.vue'),
            SelectWithSearchInput: () => import('src/components/Elements/SelectWithSearchInput.vue'),
        },
        mixins: [showNotif, CheckErrorsMixin],
        props: {
            showDialog: {
                type: Boolean,
                default: false,
            },
        },
        data() {
            return {
                value: '',
                show: false,
                faxData: {
                    name: {
                        type: 'text',
                        label: 'Название',
                        field: 'name',
                        autofocus: true,
                        rules: [
                            {
                                name: 'isLength',
                                error: 'Минимальное количество символов 2.',
                                options: {
                                    min: 2,
                                    max: 100,
                                },
                            },
                        ],
                        require: true,
                        requireError: 'Поле обьзательное для заполнения.',
                        default: '',
                        value: '',
                    },
                    status: {
                        type: 'select',
                        label: 'Статус',
                        field: 'status',
                        options: getFromSettings('transportOptionsData'),
                        require: true,
                        requireError: 'Поле обьзательное для заполнения.',
                        default: 0,
                        value: 0,
                    },
                    transporter_id: {
                        type: 'number',
                        label: 'Перевожчик',
                        field: 'transporter_id',
                        require: true,
                        requireError: 'Поле обьзательное для заполнения.',
                        default: 0,
                        value: 0,
                    },
                    transport_id: {
                        type: 'number',
                        label: 'Транспорт',
                        field: 'transport_id',
                        require: true,
                        requireError: 'Поле обьзательное для заполнения.',
                        default: 0,
                        value: 0,
                    },
                },
            };
        },
        computed: {
            transporters() {
                return this.$store.getters['transporter/getTransporters'];
            },
            transport() {
                return this.$store.getters['transport/getTransports'];
            },
        },
        watch: {
            showDialog(val) {
                this.show = val;
            },
            show(val) {
                this.$emit('update:showDialog', val);
            },
        },
        created() {
            this.getTransporters();
            this.getTransports();
        },
        methods: {
            getTransports() {
                if (_.isEmpty(this.transport)) {
                    this.$store.dispatch('transport/fetchTransports');
                }
            },
            getTransporters() {
                if (_.isEmpty(this.transporters)) {
                    this.$store.dispatch('transporter/fetchTransporters');
                }
            },
            collectData() {
                return _.reduce(this.faxData, (result, item, index) => {
                    if (index === 'name') {
                        result[item.field] = _.upperFirst(item.value);
                    }
                    result[item.field] = item.value;

                    return result;
                }, {});
            },
            saveFax() {
                this.$q.loading.show();
                this.$axios.post(getUrl('addFax'), this.collectData())
                  .then(({ data }) => {
                      this.$q.loading.hide();
                      this.$store.dispatch('faxes/addFax', data.fax);
                      this.showNotif('success', 'Факс успешно добавлен.', 'center');
                  })
                  .catch(({ response }) => {
                      this.errorsData.errors = _.get(response, 'data.errors');
                      this.$q.loading.hide();
                  });
            },
        },
    };
</script>
