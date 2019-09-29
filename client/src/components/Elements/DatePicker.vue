<template>
    <div
        data-vue-component-name="DatePicker"
    >
        <q-input
            v-model="value"
            no-error-icon
            :placeholder="inputData.placeholder"
            :error-message="viewError()"
            :error="isError"
            filled
            @input="changeErrors"
            @keydown.prevent
        >
            <template v-slot:append>
                <q-icon
                    v-if="value"
                    name="close"
                    class="cursor-pointer"
                    @click="updateValue('')"
                />
                <q-icon
                    name="event"
                    class="cursor-pointer"
                >
                    <q-popup-proxy
                        ref="qDateProxy"
                        emit-immediately
                        transition-show="scale"
                        transition-hide="scale"
                    >
                        <q-date
                            v-model="date"
                            today-btn
                            :locale="myLocale"
                            first-day-of-week="1"
                            @input="() => $refs.qDateProxy.hide()"
                        />
                    </q-popup-proxy>
                </q-icon>
            </template>
        </q-input>
    </div>
</template>

<script>
    import ErrorsServerMixin from 'src/mixins/ViewErrors';
    import { today } from 'src/utils/formatDate';

    export default {
        name: 'DatePicker',
        mixins: [ErrorsServerMixin],
        props: {
            inputData: {
                type: Object,
                default: () => ({}),
            },
            value: {
                type: String,
                default: today(),
            },
            errors: {
                type: Object,
                default: () => ({}),
            },
        },
        data() {
            return {
                myLocale: {
                    /* starting with Sunday */
                    days: 'Воскресенье_Понедельник_Вторник_Среда_Четверг_Пятница_Суббота'.split('_'),
                    daysShort: 'Вс_Пн_Вт_Ср_Чт_Пт_Сб'.split('_'),
                    months: 'Январь_Февраль_Март_Апрель_Май_Июнь_Июль_Август_Сентябрь_Октябрь_Ноябрь_Декабрь'.split('_'),
                    monthsShort: 'Янв_Фев_Мар_Апр_Май_Июн_Июл_Авг_Сен_Окт_Ноя_Дек'.split('_'),
                },
            };
        },
        computed: {
            date: {
                get: function getData() {
                    return this.value;
                },
                set: function setData(newValue) {
                    this.updateValue(newValue);
                },
            },
        },
        created() {
            if (!this.value) {
                this.date = today();
            }
        },
        methods: {
            updateValue(val) {
                this.$emit('update:value', val);
            },
        },
    };
</script>
