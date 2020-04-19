<template>
  <div>
    <Dialog
      :dialog.sync="show"
      title="Код"
      :persistent="true"
      data-vue-component-name="DialogAddCode"
    >
      <Card style="min-width: 320px;width: 100%;max-width: 500px;">
        <CardSection class="row justify-between bg-grey q-mb-sm">
          <span class="text-h6">Новый код клиента</span>
          <div>
            <IconBtn
              dense
              icon="clear"
              tooltip="Закрыть"
              @iconBtnClick="close(codeData)"
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
            @clickBaseBtn="close(codeData)"
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
      :show-dialog.sync="showClientDialog"
      :code-id="codeId"
    />
  </div>
</template>

<script>
    import { getUrl } from 'src/tools/url';
    import CheckErrorsMixin from 'src/mixins/CheckErrors';
    import showNotif from 'src/mixins/showNotif';
    import {
        getClientCodes,
        setDefaultData,
        setFormatedDate,
    } from 'src/utils/FrequentlyCalledFunctions';

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
                showClientDialog: false,
                codeId: 0,
            };
        },
        watch: {
            showDialog(val) {
                if (val) {
                    this.$q.loading.show();
                    Promise.all([getClientCodes(this.$store)])
                      .then(() => {
                          this.show = val;
                          this.$q.loading.hide();
                      });
                }
            },
            show(val) {
                this.$emit('update:showDialog', val);
            },
        },
        methods: {
            saveData({ code: { value } }) {
                devlog.log('S_VAL', value);
                this.$q.loading.show();
                this.$axios.post(getUrl('storeCode'), { code: _.startCase(value) })
                  .then(({ data: { code: addedCode, codeWithCustomers } }) => {
                      devlog.log('C_DATA', addedCode);
                      this.$store.dispatch('codes/addCode', {
                          code: addedCode,
                          codeWithCustomers: _.first(setFormatedDate(codeWithCustomers, ['created_at'])),
                      });
                      this.codeId = addedCode.value;
                      this.showClientDialog = true;
                      this.$q.loading.hide();
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
