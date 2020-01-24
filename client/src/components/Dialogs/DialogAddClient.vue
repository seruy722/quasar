<template>
  <div
    data-vue-component-name="DialogAddClient"
  >
    <Dialog
      :dialog.sync="show"
      title="Клиент"
      :persistent="true"
    >
      <Card style="min-width: 320px;width: 100%;max-width: 500px;">
        <CardSection class="row justify-between bg-grey q-mb-sm">
          <span class="text-h6">Информация о клиенте</span>
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
          <div
            v-for="(item, index) in customerData"
            :key="index"
          >
            <SearchSelect
              v-if="item.type==='searchSelect'"
              v-model.number="item.value"
              :label="item.label"
              :dense="$q.screen.xs || $q.screen.sm"
              :field="item.field"
              :func-load-data="item.funcLoadData"
              :options="item.options"
              :errors="errorsData"
            />

            <BaseSelect
              v-else-if="item.type==='select'"
              v-model.number="item.value"
              :label="item.label"
              :dense="$q.screen.xs || $q.screen.sm"
              :field="item.field"
              :options="item.options"
              :errors="errorsData"
            />

            <BaseInput
              v-else
              v-model="item.value"
              :label="item.label"
              :dense="$q.screen.xs || $q.screen.sm"
              :field="item.field"
              :mask="item.mask"
              :autofocus="item.autofocus"
              :errors="errorsData"
            />
          </div>
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
            @clickBaseBtn="checkErrors(customerData, saveData)"
          />
        </CardActions>
      </Card>
    </Dialog>
  </div>
</template>

<script>
    import { getUrl } from 'src/tools/url';
    import CheckErrorsMixin from 'src/mixins/CheckErrors';
    import OnKeyUp from 'src/mixins/OnKeyUp';
    import showNotif from 'src/mixins/showNotif';
    import getFromSettings from 'src/tools/settings';
    import FrequentlyCalledFunctions from 'src/mixins/FrequentlyCalledFunctions';

    export default {
        name: 'DialogAddClient',
        components: {
            BaseSelect: () => import('src/components/Elements/BaseSelect.vue'),
            Dialog: () => import('src/components/Dialogs/Dialog.vue'),
            // OutlineBtn: () => import('src/components/Buttons/OutlineBtn.vue'),
            BaseInput: () => import('src/components/Elements/BaseInput.vue'),
            SearchSelect: () => import('src/components/Elements/SearchSelect.vue'),
            // SelectChips: () => import('src/components/Elements/SelectChips.vue'),
            Separator: () => import('src/components/Separator.vue'),
            Card: () => import('src/components/Elements/Card/Card.vue'),
            CardActions: () => import('src/components/Elements/Card/CardActions.vue'),
            CardSection: () => import('src/components/Elements/Card/CardSection.vue'),
            IconBtn: () => import('src/components/Buttons/IconBtn.vue'),
            BaseBtn: () => import('src/components/Buttons/BaseBtn.vue'),
        },
        mixins: [OnKeyUp, showNotif, CheckErrorsMixin, FrequentlyCalledFunctions],
        props: {
            showDialog: {
                type: Boolean,
                default: false,
            },
            codeId: {
                type: Number,
                default: 0,
            },
        },
        data() {
            return {
                customers: [],
                show: false,
                customerData: {
                    codeId: {
                        type: 'searchSelect',
                        label: 'Код',
                        field: 'codeId',
                        options: [],
                        require: true,
                        requireError: 'Поле обьзательное для заполнения.',
                        funcLoadData: this.getClientCodes,
                        default: null,
                        value: null,
                    },
                    name: {
                        type: 'text',
                        label: 'Имя',
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
                    phone: {
                        type: 'text',
                        label: 'Телефон',
                        field: 'phone',
                        mask: '+## (###) ###-##-##',
                        rules: [
                            {
                                name: 'isLength',
                                error: 'Минимальное количество символов 12.',
                                options: {
                                    min: 12,
                                    max: 100,
                                },
                            },
                        ],
                        require: true,
                        requireError: 'Поле обьзательное для заполнения.',
                        default: '',
                        value: '',
                    },
                    city: {
                        type: 'searchSelect',
                        label: 'Город',
                        field: 'city',
                        options: [],
                        require: true,
                        requireError: 'Поле обьзательное для заполнения.',
                        funcLoadData: this.getCities,
                        default: null,
                        value: null,
                    },
                    sex: {
                        type: 'select',
                        label: 'Пол',
                        field: 'sex',
                        options: getFromSettings('sex'),
                        require: true,
                        requireError: 'Поле обьзательное для заполнения.',
                        default: 0,
                        value: 0,
                    },
                },
            };
        },
        computed: {
            clientCodes() {
                return this.$store.getters['clientCodes/getCodes'];
            },
            cities() {
                return this.$store.getters['cities/getCities'];
            },
        },
        watch: {
            showDialog(val) {
                this.show = val;
            },
            show(val) {
                this.$emit('update:showDialog', val);
            },
            clientCodes: {
                handler: function set(val) {
                    _.set(this.customerData, 'codeId.options', val);
                },
                immediate: true,
            },
            cities: {
                handler: function set(val) {
                    _.set(this.customerData, 'city.options', val);
                },
                immediate: true,
            },
            codeId: {
                handler: function set(val) {
                    if (val) {
                        _.set(this.customerData, 'codeId.value', val);
                    }
                },
                immediate: true,
            },
        },
        // created() {
        //     this.getCities();
        // },
        methods: {
            // async getCities() {
            //     await this.$axios.get(getUrl('cities'))
            //       .then(({ data }) => {
            //           this.$store.dispatch('cities/setCities', data);
            //       });
            // },
            saveData({
                         name,
                         sex,
                         phone,
                         codeId,
                     }) {
                devlog.log('S_VAL', name.value);
                this.$q.loading.show();
                this.$axios.post(getUrl('storeCustomers'), {
                    name: _.startCase(name.value),
                    sex: sex.value,
                    phone: phone.value,
                    codeId: codeId.value,
                })
                  .then(({ data }) => {
                      if (data.status) {
                          // this.dialogAddClientData.value = false;
                          // this.clearData();
                          this.$q.loading.hide();
                          this.showNotif('success', 'Клиент успешно добавлен.', 'center');
                      }
                  })
                  .catch((errors) => {
                      this.errorsData.errors = _.get(errors, 'response.data.errors');
                      this.$q.loading.hide();
                  });
            },
            close() {
                this.show = false;
            },
        },
    };
</script>
