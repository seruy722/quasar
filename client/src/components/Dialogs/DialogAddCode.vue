<template>
  <div
    data-vue-component-name="AddCategory"
  >
    <Dialog
      :dialog.sync="show"
      title="Код"
      :persistent="true"
    >
      <template v-slot:body>
        <q-card-section class="q-mt-md">
          <BaseInput
            v-model.trim="codeData.code.value"
            :label="codeData.code.label"
            :dense="$q.screen.xs || $q.screen.sm"
            :field="codeData.code.field"
            :autofocus="codeData.code.autofocus"
            :errors="errorsData"
          />
        </q-card-section>

        <q-separator />

        <q-card-actions align="right">
          <OutlineBtn
            label="Сохранить"
            color="positive"
            :dense="$q.screen.xs || $q.screen.sm"
            @clickOutlineBtn="checkErrors(codeData, saveData)"
          />

          <OutlineBtn
            label="Закрыть"
            color="negative"
            :dense="$q.screen.xs || $q.screen.sm"
            @clickOutlineBtn="close"
          />
        </q-card-actions>
      </template>
    </Dialog>

    <DialogAddCustomer
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
        name: 'AddCategory',
        components: {
            Dialog: () => import('src/components/Dialogs/Dialog.vue'),
            OutlineBtn: () => import('src/components/Buttons/OutlineBtn.vue'),
            BaseInput: () => import('src/components/Elements/BaseInput.vue'),
            DialogAddCustomer: () => import('src/components/Dialogs/DialogAddCustomer.vue'),
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
                this.$axios.post(getUrl('storeCode'), { code: _.upperFirst(code.value) })
                  .then(({ data }) => {
                      devlog.log('C_DATA', data);
                      this.$store.dispatch('clientCodes/addCode', data.code);
                      this.showNotif('success', 'Код успешно добавлен.', 'center');
                      this.showCustomerDialog = true;
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
