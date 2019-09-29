/**
 * Миксин для вывода ошибок в поле input
 *
 * Для работы миксина нужно:
 * - подключить его
 * - добавить инпуту свойства :error-message="viewError()" и :error="isError"
 * - добавить событие @input="changeErrors"
 * - в props инпута добавить обьекты
 inputData: {
        type: Object,
        default: () => ({}),
   },
 errors: {
        type: Object,
        default: () => ({}),
 },
 * - передать в компонент инпута в обьекте inputData:
 * field: 'phone',
 * - errors нужно передавать в виде:
 * {
 *   errors: {
 *     phone: ['Неправильный номер']
 *   }
 * }
 *
 */
export default {
    data() {
        return {
            isError: false,
        };
    },
    watch: {
        'errors.errors': function error(val) {
            this.isError = !!_.get(val, [this.inputData.field]);
        },
    },
    methods: {
        viewError() {
            return _.join(_.get(this.errors, `errors['${this.inputData.field}']`), ',');
        },
        changeErrors() {
            this.inputData.changed = true;
            this.isError = false;
            _.set(this.objIndex, 'index', this.index);
            // this.objIndex.index = this.index;
            if (!_.isEmpty(this.errors.errors)) {
                delete this.errors.errors[this.inputData.field];
            }
        },
    },
};
