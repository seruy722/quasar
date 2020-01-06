<template>
  <div
    data-vue-component-name="DialogAddCode"
  >
    <Dialog
      :dialog.sync="show"
      title="Код"
      :persistent="true"
    >
      <Card style="min-width: 320px;width: 100%;max-width: 500px;">
        <CardSection class="row justify-between bg-grey q-mb-sm">
          <span class="text-h6">Новый код клиента</span>
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
            v-model.trim="codeData.code.value"
            :label="codeData.code.label"
            :dense="$q.screen.xs || $q.screen.sm"
            :field="codeData.code.field"
            :autofocus="codeData.code.autofocus"
            :errors="errorsData"
          />
        </CardSection>

        <Separator />

        <CardActions>
          <BaseBtn
            label="Отмена"
            color="negative"
            :dense="$q.screen.xs || $q.screen.sm"
            @clickBaseBtn="close"
          />

          <BaseBtn
            label="Сохранить"
            color="positive"
            :dense="$q.screen.xs || $q.screen.sm"
            @clickBaseBtn="checkErrors(codeData, saveData)"
          />
        </CardActions>
      </Card>
    </Dialog>

    <DialogAddClient
      :show-dialog.sync="showCustomerDialog"
      :code-id="codeId"
    />
  </div>
</template>

<script>
    // import { mapGetters } from 'vuex';
    import { getUrl } from 'src/tools/url';
    // import { getLSKey } from 'src/tools/lsKeys';
    import CheckErrorsMixin from 'src/mixins/CheckErrors';
    import OnKeyUp from 'src/mixins/OnKeyUp';
    import showNotif from 'src/mixins/showNotif';

    export default {
        name: 'DialogAddCode',
        components: {
            Dialog: () => import('src/components/Dialogs/Dialog.vue'),
            // OutlineBtn: () => import('src/components/Buttons/OutlineBtn.vue'),
            BaseBtn: () => import('src/components/Buttons/BaseBtn.vue'),
            BaseInput: () => import('src/components/Elements/BaseInput.vue'),
            DialogAddClient: () => import('src/components/Dialogs/DialogAddClient.vue'),
            Separator: () => import('src/components/Separator.vue'),
            Card: () => import('src/components/Elements/Card/Card.vue'),
            CardActions: () => import('src/components/Elements/Card/CardActions.vue'),
            CardSection: () => import('src/components/Elements/Card/CardSection.vue'),
            IconBtn: () => import('src/components/Buttons/IconBtn.vue'),
        },
        mixins: [OnKeyUp, showNotif, CheckErrorsMixin],
        props: {
            showDialog: {
                type: Boolean,
                default: false,
            },
        },
        data() {
            return {
                show: false,
                codeData: {
                    code: {
                        type: 'text',
                        label: 'Код',
                        field: 'code',
                        autofocus: true,
                        rules: [
                            {
                                name: 'isLength',
                                error: 'Минимальное количество символов 2.',
                                options: {
                                    min: 2,
                                    max: 255,
                                },
                            },
                        ],
                        require: true,
                        requireError: 'Поле обьзательное для заполнения.',
                        default: '',
                        value: '',
                    },
                },
                showCustomerDialog: false,
                codeId: 0,
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
            saveData({ code }) {
                devlog.log('S_VAL', code.value);
                this.$q.loading.show();
                this.$axios.post(getUrl('storeCode'), { code: _.startCase(code.value) })
                  .then(({ data }) => {
                      this.showCustomerDialog = true;
                      devlog.log('C_DATA', data);
                      this.$store.dispatch('clientCodes/addCode', data.code);
                      // this.showNotif('success', 'Код успешно добавлен.', 'center');
                      this.codeId = _.get(data, 'code.value');
                      this.$q.loading.hide();
                  })
                  .catch((errors) => {
                      this.errorsData.errors = _.get(errors, 'response.data.errors');
                      this.$q.loading.hide();
                  });
            },
            close() {
                this.codeData.code.value = '';
                this.show = false;
            },
        },
    };
</script>
