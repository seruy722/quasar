<template>
    <div
        data-vue-component-name="AddCode"
    >
        <Dialog :dialog.sync="dialogAddClientData">
            <template v-slot:body>
                <q-card-section>
                    <q-stepper
                        v-model="step"
                        ref="stepper"
                        color="primary"
                        animated
                    >
                        <q-step
                            :name="1"
                            title="Введите код клиента"
                            icon="settings"
                            :error="false"
                            :done="step > 1"
                        >
                            <BaseInput
                                ref="codeInput"
                                :inputData.sync="inputCode"
                                :errors="errorsData"
                                @on-key-up="keyUp"
                            />
                        </q-step>

                        <q-step
                            :name="2"
                            title="Добавте клиентов"
                            icon="add"
                            :done="step > 2"
                        >
                            <q-list bordered class="rounded-borders" separator style="max-width: 600px">
                                <q-item-label header>Клиенты</q-item-label>

                                <q-item
                                    v-for="(customer, index) in customersData.customerList"
                                    :key="index"
                                >
                                    <q-item-section>
                                        {{ customer.name }}
                                    </q-item-section>
                                    <q-item-section>{{ customer.phone }}</q-item-section>
                                    <q-item-section side>
                                        <q-btn class="gt-xs" size="12px" flat dense round icon="delete"
                                               @click="removeCustomerFromList(index)" />
                                    </q-item-section>
                                </q-item>

                            </q-list>
                        </q-step>

                        <template v-slot:navigation>
                            <q-stepper-navigation class="row justify-between align-center">
                                <BaseBtn
                                    v-if="step === 1"
                                    label="next"
                                    @click-base-btn="checkCodeExist"
                                />
                                <BaseBtn
                                    v-else
                                    label="back"
                                    @click-base-btn="$refs.stepper.previous()"
                                />

                                <AddCustomers v-if="step > 1" :customersData.sync="customersData" />
                            </q-stepper-navigation>
                        </template>
                    </q-stepper>
                </q-card-section>

                <q-separator />

                <q-card-actions align="right">
                    <OutlineBtn
                        v-show="step > 1"
                        :btnData="btnData"
                        @click-outline-btn="saveCodeOnServer"
                    />
                </q-card-actions>
            </template>
        </Dialog>
    </div>
</template>

<script>
    // import { mapGetters } from 'vuex';
    import { getUrl } from 'src/tools/url';
    // import { getLSKey } from 'src/tools/lsKeys';
    import OnKeyUp from 'src/mixins/OnKeyUp';
    import showNotif from 'src/mixins/showNotif';

    export default {
        name: 'AddCode',
        components: {
            Dialog: () => import('src/components/Dialogs/Dialog.vue'),
            AddCustomers: () => import('src/components/Dialogs/AddCustomers.vue'),
            OutlineBtn: () => import('src/components/Buttons/OutlineBtn.vue'),
            BaseBtn: () => import('src/components/Buttons/BaseBtn.vue'),
            BaseInput: () => import('src/components/Elements/BaseInput.vue'),
        },
        mixins: [OnKeyUp, showNotif],
        data() {
            this.$_dismissNotify = () => {
            };
            return {
                customersData: {
                    customerList: [],
                    codeId: '',
                },
                step: 1,
                dialogAddClientData: {
                    title: 'Добавление клиента',
                    value: false,
                    labelBtn: 'Добавить клиента',
                },
                errorsData: {
                    errors: {},
                },
                inputs: [
                    {
                        type: 'text',
                        value: '',
                        field: 'name',
                        icon: 'person',
                        autofocus: true,
                        lang: 'name',
                        dark: false,
                    },
                    {
                        type: 'tel',
                        value: '',
                        field: 'phone',
                        icon: 'person',
                        autofocus: false,
                        lang: 'phone',
                        mask: '+## (###) ###-##-##',
                        dark: false,
                    },
                    {
                        type: 'text',
                        value: '',
                        field: 'city',
                        icon: 'person',
                        autofocus: false,
                        lang: 'city',
                        dark: false,
                    },
                    {
                        type: 'select',
                        value: 0,
                        field: 'sex',
                        icon: 'person',
                        options: [
                            {
                                label: 'Не выбрано',
                                value: 0,
                            },
                            {
                                label: 'Женский',
                                value: 1,
                            },
                            {
                                label: 'Мужской',
                                value: 2,
                            },
                        ],
                        autofocus: false,
                        lang: 'sex',
                        dark: false,
                    },
                ],
                btnData: {
                    title: 'save',
                    icon: 'lock',
                    dark: false,
                },
                inputCode: {
                    type: 'text',
                    value: '',
                    field: 'code',
                    lang: 'code',
                    icon: 'code',
                    autofocus: true,
                    dark: false,
                },
            };
        },
        watch: {
            'dialogAddClientData.value': function dialog(val) {
                devlog.log('D_VAL', val);
                if (!val) {
                    this.step = 1;
                }
            },
        },
        created() {
            this.onKeyUpFunc = this.checkCodeExist;
        },
        methods: {
            removeCustomerFromList(index) {
                this.customersData.customerList.splice(index, 1);
            },
            checkCodeExist() {
                if (this.inputCode.value) {
                    this.$q.loading.show();
                    this.$axios.get(`${getUrl('codeExist')}${this.inputCode.value}`)
                      .then((response) => {
                          if (_.get(response, 'data.status')) {
                              this.$refs.stepper.next();
                              this.$q.loading.hide();
                          }
                      })
                      .catch((errors) => {
                          this.$q.loading.hide();
                          this.errorsData.errors = _.get(errors, 'response.data.errors');
                      });
                } else {
                    this.errorsData.errors = { code: ['Поле обязательное.'] };
                    this.$refs.codeInput.$refs.baseInput.focus();
                }
            },
            getCodesWithoutInfo() {
                // this.$axios.get(getUrl('drafts.codesWithoutInfo'));
                this.$axios({
                    url: getUrl('drafts.codesWithoutInfo'),
                    method: 'GET',
                    responseType: 'blob', // important
                })
                  .then((response) => {
                      devlog.log('RES_BLOB', response);
                      if (!window.navigator.msSaveOrOpenBlob) {
                          // BLOB NAVIGATOR
                          const url = window.URL.createObjectURL(new Blob([response.data]));
                          const link = document.createElement('a');
                          link.href = url;
                          link.setAttribute('download', 'codes.xlsx');
                          document.body.appendChild(link);
                          link.click();
                      } else {
                          // BLOB FOR EXPLORER 11
                          window.navigator.msSaveOrOpenBlob(new Blob([response.data]), 'codes.xlsx');
                      }
                  });
            },
            saveCodeOnServer() {
                if (!_.isEmpty(this.customersData.customerList) && this.inputCode.value) {
                    this.$axios.post(getUrl('storeCode'), { code: this.inputCode.value })
                      .then(({ data }) => {
                          if (data.status && data.codeID) {
                              _.forEach(this.customersData.customerList, (item) => {
                                  item.codeId = data.codeID;
                              });
                              this.$axios.post(getUrl('storeCustomers'), this.customersData.customerList)
                                .then((response) => {
                                    if (response.data.status) {
                                        this.customersData.customerList = [];
                                        this.showNotif('success', 'Данные успешно сохранены.', 'center');
                                    }
                                })
                                .catch((errors) => {
                                    devlog.log(errors);
                                });
                          }
                      })
                      .catch((errors) => {
                          devlog.log('ERR', errors);
                      });
                } else {
                    this.showNotif('info', 'Добавте клиента - нажмите на кнопку добавить клиента.', 'top');
                }
            },
        },
    };
</script>
