<template>
  <Dialog
    :dialog.sync="show"
    title="Transporter"
    :persistent="true"
    data-vue-component-name="DialogAddTransporter"
  >
    <Card style="min-width: 320px;width: 100%;max-width: 500px;">
      <CardSection class="row justify-between bg-grey q-mb-sm">
        <span class="text-h6">Новый перевожчик</span>
        <div>
          <IconBtn
            dense
            icon="clear"
            tooltip="Закрыть"
            @iconBtnClick="close"
          />
        </div>
      </CardSection>
      <CardSection>
        <BaseInput
          v-model.trim="transporterData.name.value"
          label="Перевожчик"
          :dense="$q.screen.xs || $q.screen.sm"
          :field="transporterData.name.field"
          :autofocus="transporterData.name.autofocus"
          :errors="errorsData"
          @onKeyUp="keyUp"
        />
      </CardSection>
      <Separator />
      <CardActions>
        <OutlineBtn
          label="Сохранить"
          color="positive"
          :dense="$q.screen.xs || $q.screen.sm"
          @clickOutlineBtn="checkErrors(transporterData, saveData)"
        />

        <OutlineBtn
          label="Закрыть"
          color="negative"
          :dense="$q.screen.xs || $q.screen.sm"
          @clickOutlineBtn="close"
        />
      </CardActions>
    </Card>
  </Dialog>
</template>

<script>
    import { getUrl } from 'src/tools/url';
    import CheckErrorsMixin from 'src/mixins/CheckErrors';
    import showNotif from 'src/mixins/showNotif';

    export default {
        name: 'DialogAddTransporter',
        components: {
            Dialog: () => import('src/components/Dialogs/Dialog.vue'),
            // AddCustomers: () => import('src/components/Dialogs/AddCustomers.vue'),
            OutlineBtn: () => import('src/components/Buttons/OutlineBtn.vue'),
            // BaseBtn: () => import('src/components/Buttons/BaseBtn.vue'),
            BaseInput: () => import('src/components/Elements/BaseInput.vue'),
            Card: () => import('src/components/Elements/Card/Card.vue'),
            CardActions: () => import('src/components/Elements/Card/CardActions.vue'),
            CardSection: () => import('src/components/Elements/Card/CardSection.vue'),
            Separator: () => import('src/components/Separator.vue'),
            IconBtn: () => import('src/components/Buttons/IconBtn.vue'),
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
                show: false,
                transporterData: {
                    name: {
                        type: 'text',
                        label: this.$t('transporter'),
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
                },
            };
        },
        watch: {
            showDialog(val) {
                this.show = val;
            },
            show(val) {
                this.$emit('update:showDialog', val);
            },
        },
        methods: {
            saveData({ name }) {
                devlog.log('S_VAL', name.value);
                this.$q.loading.show();
                this.$axios.post(getUrl('addTransporter'), { name: _.startCase(name.value) })
                  .then(({ data }) => {
                      this.$q.loading.hide();
                      if (!_.isEmpty(this.$store.getters['transporter/getTransporters'])) {
                          this.$store.dispatch('transporter/AddTransporter', data.transporter);
                      }
                      this.showNotif('success', 'Перевожчик успешно добавлен.', 'center');
                  })
                  .catch(({ response: { data: { errors } } }) => {
                      this.errorsData.errors = errors;
                      this.$q.loading.hide();
                  });
            },
            close() {
                this.transporterData.name.value = '';
                this.show = false;
            },
            keyUp(event) {
                const { code = '', key = '' } = event;
                if (event.code === 'Enter' || code === 'NumpadEnter' || key === 'Enter' || key === 'NumpadEnter') {
                    this.checkErrors(this.categoryData, this.saveData);
                }
            },
        },
    };
</script>
