<template>
  <Dialog
    :dialog.sync="show"
    title="Fax"
    :persistent="true"
    data-vue-component-name="DialogAddFax"
  >
    <Card style="min-width: 320px;width: 100%;max-width: 500px;">
      <CardSection class="row justify-between bg-grey q-mb-sm">
        <span class="text-h6">Новый факс</span>
        <div>
          <IconBtn
            dense
            icon="clear"
            tooltip="Закрыть"
            @iconBtnClick="close(faxData)"
          />
        </div>
      </CardSection>
      <CardSection>
        <BaseInput
          v-model="faxData.name.value"
          :label="faxData.name.label"
          :type="faxData.name.type"
          :autofocus="true"
          :field="faxData.name.field"
          :dense="$q.screen.xs || $q.screen.sm"
          :errors="errorsData"
        />

        <SearchSelect
          v-model="faxData.transporter_id.value"
          :label="faxData.transporter_id.label"
          :dense="$q.screen.xs || $q.screen.sm"
          :field="faxData.transporter_id.field"
          :func-load-data="faxData.transporter_id.funcLoadData"
          :options="transporters"
          :errors="errorsData"
        />

        <BaseSelect
          v-model="faxData.transport_id.value"
          :label="faxData.transport_id.label"
          :dense="$q.screen.xs || $q.screen.sm"
          :field="faxData.transport_id.field"
          :options="transport"
          :errors="errorsData"
        />

        <BaseSelect
          v-model="faxData.status.value"
          :label="faxData.status.label"
          :dense="$q.screen.xs || $q.screen.sm"
          :field="faxData.status.field"
          :options="faxData.status.options"
          :errors="errorsData"
        />
      </CardSection>

      <Separator />
      <CardActions>
        <OutlineBtn
          label="Сохранить"
          color="positive"
          @clickOutlineBtn="checkErrors(faxData, saveFax)"
        />

        <OutlineBtn
          label="Закрыть"
          color="negative"
          @clickOutlineBtn="close(faxData)"
        />
      </CardActions>
    </Card>
  </Dialog>
</template>

<script>
    import { getUrl } from 'src/tools/url';
    import showNotif from 'src/mixins/showNotif';
    import CheckErrorsMixin from 'src/mixins/CheckErrors';
    import getFromSettings from 'src/tools/settings';
    import { setDefaultData, getTransports, getTransporters } from 'src/utils/FrequentlyCalledFunctions';

    export default {
        name: 'DialogAddFax',
        components: {
            Dialog: () => import('src/components/Dialogs/Dialog.vue'),
            OutlineBtn: () => import('src/components/Buttons/OutlineBtn.vue'),
            BaseInput: () => import('src/components/Elements/BaseInput.vue'),
            SearchSelect: () => import('src/components/Elements/SearchSelect.vue'),
            Card: () => import('src/components/Elements/Card/Card.vue'),
            CardActions: () => import('src/components/Elements/Card/CardActions.vue'),
            CardSection: () => import('src/components/Elements/Card/CardSection.vue'),
            Separator: () => import('src/components/Separator.vue'),
            IconBtn: () => import('src/components/Buttons/IconBtn.vue'),
            BaseSelect: () => import('src/components/Elements/BaseSelect.vue'),
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
                        funcLoadData: getTransporters,
                        default: null,
                        value: null,
                    },
                    transport_id: {
                        type: 'number',
                        label: 'Транспорт',
                        field: 'transport_id',
                        require: true,
                        requireError: 'Поле обьзательное для заполнения.',
                        default: null,
                        value: null,
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
                if (val) {
                    getTransports(this.$store);
                    this.show = val;
                }
            },
            show(val) {
                this.$emit('update:showDialog', val);
            },
        },
        methods: {
            saveFax(faxData) {
                this.$q.loading.show();
                const sendData = _.reduce(faxData, (result, item, index) => {
                    if (index === 'name') {
                        result[item.field] = _.startCase(item.value);
                    }
                    result[item.field] = item.value;

                    return result;
                }, {});

                this.$axios.post(getUrl('addFax'), sendData)
                  .then(({ data: { fax } }) => {
                      this.$q.loading.hide();
                      if (!_.isEmpty(this.$store.getters['faxes/getFaxes'])) {
                          this.$store.dispatch('faxes/addFax', fax);
                      }
                      this.showNotif('success', 'Факс успешно добавлен.', 'center');
                  })
                  .catch(({ response: { data: { errors } } }) => {
                      this.errorsData.errors = errors;
                      this.$q.loading.hide();
                  });
            },
            close(data) {
                this.show = false;
                setDefaultData(data);
            },
        },
    };
</script>
