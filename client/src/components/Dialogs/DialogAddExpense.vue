<template>
  <Dialog
    :dialog.sync="show"
    title="Код"
    :persistent="true"
    data-vue-component-name="DialogAddExpense"
  >
    <Card style="min-width: 320px;width: 100%;max-width: 600px;">
      <CardSection class="row justify-between bg-grey q-mb-sm">
        <span class="text-h6">{{ title }}</span>
        <div>
          <IconBtn
            dense
            icon="clear"
            tooltip="Закрыть"
            @icon-btn-click="close(expenseData)"
          />
        </div>
      </CardSection>

      <CardSection>
        <div class="row justify-between">
          <SelectChips
            v-model="expenseData.expense.value"
            :label="expenseData.expense.label"
            :field="expenseData.expense.field"
            :dense="$q.screen.xs || $q.screen.sm"
            :options="expenseData.expense.options"
            :change-value.sync="expenseData.expense.changeValue"
            :func-load-data="expenseData.expense.funcLoadData"
            :errors="errorsData"
          />
          <BaseInput
            v-model="expenseData.sum.value"
            type="number"
            :label="expenseData.sum.label"
            :dense="$q.screen.xs || $q.screen.sm"
            :field="expenseData.sum.field"
            :change-value.sync="expenseData.sum.changeValue"
            :errors="errorsData"
          />
          <DateWithInputForCargo
            :value.sync="expenseData.date.value"
            :change-value.sync="expenseData.date.changeValue"
          />
        </div>
      </CardSection>

      <Separator />

      <CardActions>
        <BaseBtn
          label="Отмена"
          color="negative"
          :dense="$q.screen.xs || $q.screen.sm"
          @click-base-btn="close(expenseData)"
        />

        <BaseBtn
          label="Сохранить"
          color="positive"
          :dense="$q.screen.xs || $q.screen.sm"
          @click-base-btn="checkErrors(expenseData, saveData)"
        />
      </CardActions>
    </Card>
  </Dialog>
</template>

<script>
import { getUrl } from 'src/tools/url';
import CheckErrorsMixin from 'src/mixins/CheckErrors';
import showNotif from 'src/mixins/showNotif';
import { setDefaultData } from 'src/utils/FrequentlyCalledFunctions';

export default {
  name: 'DialogAddExpense',
  components: {
    Dialog: () => import('src/components/Dialogs/Dialog.vue'),
    BaseBtn: () => import('src/components/Buttons/BaseBtn.vue'),
    BaseInput: () => import('src/components/Elements/BaseInput.vue'),
    Separator: () => import('src/components/Separator.vue'),
    Card: () => import('src/components/Elements/Card/Card.vue'),
    CardActions: () => import('src/components/Elements/Card/CardActions.vue'),
    CardSection: () => import('src/components/Elements/Card/CardSection.vue'),
    IconBtn: () => import('src/components/Buttons/IconBtn.vue'),
    SelectChips: () => import('src/components/Elements/SelectChips.vue'),
    DateWithInputForCargo: () => import('src/components/DateWithInputForCargo.vue'),
    // SearchSelect: () => import('src/components/Elements/SearchSelect.vue'),
  },
  mixins: [showNotif, CheckErrorsMixin],
  props: {
    showDialog: {
      type: Boolean,
      default: false,
    },
    entryData: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      show: false,
      expenseData: {
        expense: {
          type: 'chips',
          label: 'Статья расходов',
          field: 'expense',
          require: true,
          requireError: 'Поле обьзательное для заполнения.',
          changeValue: false,
          options: [],
          funcLoadData: this.getExpenses,
          default: null,
          value: null,
        },
        sum: {
          type: 'number',
          label: 'Сумма',
          field: 'sum',
          changeValue: false,
          require: true,
          requireError: 'Поле обьзательное для заполнения.',
          default: 0,
          value: 0,
        },
        date: {
          type: 'text',
          label: 'Дата',
          field: 'date',
          changeValue: false,
          require: false,
          requireError: 'Поле обьзательное для заполнения.',
          default: new Date().toISOString()
            .slice(0, 10)
            .split('-')
            .join('-'),
          value: new Date().toISOString()
            .slice(0, 10)
            .split('-')
            .join('-'),
        },
      },
    };
  },
  computed: {
    title() {
      return _.isEmpty(this.entryData) ? 'Добавление' : 'Редактирование';
    },
  },
  watch: {
    showDialog(val) {
      this.show = val;
    },
    show(val) {
      this.$emit('update:showDialog', val);
    },
    entryData(val) {
      if (!_.isEmpty(val)) {
        devlog.log('vALIN_ADD', val);
        _.forEach(this.expenseData, (item, index) => {
          if (_.get(this.entryData, index)) {
            _.set(item, 'value', _.get(this.entryData, index));
          }
        });
        this.expenseData.category_id.require = false;
      }
    },
  },
  methods: {
    getExpenses() {
      return this.$axios.get(getUrl('getExpenses'))
        .then(({ data }) => {
          devlog.log('getExpenses', data);
          this.expenseData.expense.options = data;
        });
    },
    saveData(values) {
      devlog.log('S_VAL', values);
      const sendData = _.reduce(values, (result, item) => {
        if (item.changeValue) {
          result[item.field] = item.value;
        }
        if (item.field === 'date' && item.changeValue) {
          result[item.field] = new Date(item.value.split('-')
            .join(',')).toISOString();
        }
        return result;
      }, {});
      devlog.log('dd', sendData);

      if (!_.isEmpty(sendData)) {
        this.$q.loading.show();
        // UPDATE
        if (!_.isEmpty(this.entryData)) {
          sendData.id = this.entryData.id;
          this.$axios.post(getUrl('updateCodePrice'), sendData)
            .then(({
                     data: {
                       updatedCodePrice,
                       error,
                     },
                   }) => {
              if (error) {
                this.$q.loading.hide();
                this.showNotif('error', error, 'center');
              } else {
                devlog.log('C_DATA', updatedCodePrice);
                this.$store.dispatch('codesPrices/updateCodePrice', updatedCodePrice);
                this.close(this.expenseData);
                this.$q.loading.hide();
                this.showNotif('success', `Категория <<${this.entryData.category_name}>> успешно обновлена`, 'center');
              }
            })
            .catch(({ response: { data: { errors } } }) => {
              this.errorsData.errors = errors;
              this.$q.loading.hide();
            });
        } else {
          // CREATE
          this.$axios.post(getUrl('addExpense'), sendData)
            .then(({
                     data: {
                       expenseData,
                       expense,
                     },
                   }) => {
              if (_.indexOf(_.get(this, 'expenseData.expense.options'), expense) === -1) {
                this.expenseData.expense.options.push(expense);
              }
              devlog.log('C_DATA', expenseData);
              this.$store.dispatch('statistics/addStatistic', expenseData);
              this.showNotif('success', 'Запись успешно добавлена');
              // this.$store.dispatch('codesPrices/addNewCodePrice', expenseData);
              // this.close(this.expenseData);
              this.$q.loading.hide();
              // this.showNotif('success', `Категория <<${expenseData.category_name}>> успешно добавлена клиенту - ${expenseData.code_client_name}`, 'center');
            })
            .catch(({ response: { data: { errors } } }) => {
              this.errorsData.errors = errors;
              this.$q.loading.hide();
              this.showNotif('error', 'Произошла ошибка при добавлении записи');
            });
        }
      }
    },
    close(data) {
      this.show = false;
      setDefaultData(data);
      this.$emit('update:entryData', {});
    },
  },
};
</script>
