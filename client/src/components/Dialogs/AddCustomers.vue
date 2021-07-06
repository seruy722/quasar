<template>
    <div
        data-vue-component-name="AddCustomers"
        class="row items-center justify-center"
    >
        <Dialog :dialog.sync="dialogAddClientData">
            <template #body>
                <q-card-section>
                    <div
                        v-for="(input, index) in inputs"
                        :key="index"
                    >
                        <BaseSelect
                            v-if="input.type==='select'"
                            :input-data="input"
                            :options="input.options"
                            :value.sync="input.value"
                            :errors="errorsData"
                        />
                        <BaseInput
                            v-else
                            :input-data="input"
                            :errors="errorsData"
                        />
                    </div>
                </q-card-section>

                <q-separator />

                <q-card-actions align="center">
                    <OutlineBtn
                        :btn-data="btnData"
                        @click-outline-btn="checkValidCustomerData"
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

    export default {
        name: 'AddCustomers',
        components: {
            BaseInput: () => import('src/components/Elements/BaseInput.vue'),
            OutlineBtn: () => import('src/components/Buttons/OutlineBtn.vue'),
            Dialog: () => import('src/components/Dialogs/Dialog.vue'),
            BaseSelect: () => import('src/components/Elements/BaseSelect.vue'),
        },
        props: {
            customersData: {
                type: Object,
                default: () => ({}),
            },
        },
        data() {
            this.$_emptyInputs = [
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
                    icon: 'phone',
                    autofocus: false,
                    lang: 'phone',
                    mask: '+## (###) ###-##-##',
                    unmasked: true,
                    dark: false,
                },
                {
                    type: 'text',
                    value: '',
                    field: 'city',
                    icon: 'location_city',
                    autofocus: false,
                    lang: 'city',
                    dark: false,
                },
                {
                    type: 'select',
                    value: null,
                    field: 'sex',
                    icon: 'wc',
                    options: [
                        {
                            label: 'Не выбрано',
                            value: null,
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
            ];
            return {
                dialogAddClientData: {
                    title: 'Новый',
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
                        icon: 'phone',
                        autofocus: false,
                        lang: 'phone',
                        mask: '+## (###) ###-##-##',
                        unmasked: true,
                        dark: false,
                    },
                    {
                        type: 'text',
                        value: '',
                        field: 'city',
                        icon: 'location_city',
                        autofocus: false,
                        lang: 'city',
                        dark: false,
                    },
                    {
                        type: 'select',
                        value: null,
                        field: 'sex',
                        icon: 'wc',
                        options: [
                            {
                                label: 'Не выбрано',
                                value: null,
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
                    title: 'add',
                    icon: 'lock',
                    dark: false,
                },
            };
        },
        methods: {
            checkValidCustomerData() {
                const inputsData = _.reduce(this.inputs, (result, item) => {
                    if (item.field === 'phone') {
                        result[item.field] = `+${item.value}`;
                    } else {
                        result[item.field] = item.value;
                    }
                    result.code = this.customersData.code;

                    return result;
                }, {});
                this.$axios.post(getUrl('validCustomerData'), inputsData)
                  .then(({ data }) => {
                      if (data.status) {
                          this.dialogAddClientData.value = false;
                          // eslint-disable-next-line vue/no-mutating-props
                          this.customersData.customerList.push(inputsData);
                          this.clearData();
                      }
                  })
                  .catch((errors) => {
                      this.errorsData.errors = _.get(errors, 'response.data.errors');
                  });
            },
            clearData() {
                this.inputs = this.$_emptyInputs;
            },
        },
    };
</script>
