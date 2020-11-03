<template>
  <Dialog
    :dialog.sync="show"
    title="Код"
    :persistent="true"
    data-vue-component-name="DialogAddCodePrice"
  >
    <Card style="min-width: 320px;width: 100%;max-width: 600px;">
      <CardSection class="row justify-between bg-grey q-mb-sm">
        <span class="text-h6">{{ title }}</span>
        <div>
          <IconBtn
            dense
            icon="clear"
            tooltip="Закрыть"
            @icon-btn-click="close(codePriceData)"
          />
        </div>
      </CardSection>

      <CardSection>
        <div class="row justify-between">
          <SearchSelect
            v-model="codePriceData.category_id.value"
            :label="codePriceData.category_id.label"
            :dense="$q.screen.xs || $q.screen.sm"
            :field="codePriceData.category_id.field"
            :change-value.sync="codePriceData.category_id.changeValue"
            :options="categories"
            :errors="errorsData"
          />
          <BaseInput
            v-model="codePriceData.for_kg.value"
            type="number"
            :label="codePriceData.for_kg.label"
            :dense="$q.screen.xs || $q.screen.sm"
            :field="codePriceData.for_kg.field"
            :change-value.sync="codePriceData.for_kg.changeValue"
            :errors="errorsData"
          />
          <BaseInput
            v-model="codePriceData.for_place.value"
            type="number"
            :label="codePriceData.for_place.label"
            :dense="$q.screen.xs || $q.screen.sm"
            :field="codePriceData.for_place.field"
            :change-value.sync="codePriceData.for_place.changeValue"
            :errors="errorsData"
          />
          <BaseInput
            v-model="codePriceData.commission.value"
            type="number"
            :label="codePriceData.commission.label"
            :dense="$q.screen.xs || $q.screen.sm"
            :field="codePriceData.commission.field"
            :change-value.sync="codePriceData.commission.changeValue"
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
          @click-base-btn="close(codePriceData)"
        />

        <BaseBtn
          label="Сохранить"
          color="positive"
          :dense="$q.screen.xs || $q.screen.sm"
          @click-base-btn="checkErrors(codePriceData, saveData)"
        />
      </CardActions>
    </Card>
  </Dialog>
</template>

<script>
import { getUrl } from 'src/tools/url';
import CheckErrorsMixin from 'src/mixins/CheckErrors';
import showNotif from 'src/mixins/showNotif';
import { setDefaultData, getCategories } from 'src/utils/FrequentlyCalledFunctions';

export default {
  name: 'DialogAddCodePrice',
  components: {
    Dialog: () => import('src/components/Dialogs/Dialog.vue'),
    BaseBtn: () => import('src/components/Buttons/BaseBtn.vue'),
    BaseInput: () => import('src/components/Elements/BaseInput.vue'),
    Separator: () => import('src/components/Separator.vue'),
    Card: () => import('src/components/Elements/Card/Card.vue'),
    CardActions: () => import('src/components/Elements/Card/CardActions.vue'),
    CardSection: () => import('src/components/Elements/Card/CardSection.vue'),
    IconBtn: () => import('src/components/Buttons/IconBtn.vue'),
    SearchSelect: () => import('src/components/Elements/SearchSelect.vue'),
  },
  mixins: [showNotif, CheckErrorsMixin],
  props: {
    showDialog: {
      type: Boolean,
      default: false,
    },
    codeId: {
      type: Number,
      default: 0,
    },
    entryData: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      show: false,
      codePriceData: {
        category_id: {
          type: 'number',
          label: 'Категория',
          field: 'category_id',
          require: true,
          requireError: 'Поле обьзательное для заполнения.',
          changeValue: false,
          default: null,
          value: null,
        },
        for_kg: {
          type: 'number',
          label: 'За кг',
          field: 'for_kg',
          changeValue: false,
          default: 0,
          value: 0,
        },
        for_place: {
          type: 'number',
          label: 'За место',
          field: 'for_place',
          changeValue: false,
          default: 0,
          value: 0,
        },
        commission: {
          type: 'number',
          label: 'Комиссия',
          field: 'commission',
          changeValue: false,
          default: 0,
          value: 0,
        },
      },
    };
  },
  computed: {
    categories() {
      return this.$store.getters['category/getCategories'];
    },
    title() {
      return _.isEmpty(this.entryData) ? 'Добавление' : 'Редактирование';
    },
  },
  watch: {
    showDialog(val) {
      if (val) {
        this.$q.loading.show();
        Promise.all([getCategories(this.$store)])
          .then(() => {
            this.show = val;
            this.$q.loading.hide();
          });
      }
    },
    show(val) {
      this.$emit('update:showDialog', val);
    },
    entryData(val) {
      if (!_.isEmpty(val)) {
        devlog.log('vALIN_ADD', val);
        _.forEach(this.codePriceData, (item, index) => {
          if (_.get(this.entryData, index)) {
            _.set(item, 'value', _.get(this.entryData, index));
          }
        });
        this.codePriceData.category_id.require = false;
      }
    },
  },
  methods: {
    saveData(values) {
      devlog.log('S_VAL', values);
      const sendData = _.reduce(values, (result, item) => {
        if (item.changeValue) {
          result[item.field] = item.value;
        }
        return result;
      }, {});
      devlog.log('dd', sendData);

      if (!_.isEmpty(sendData)) {
        sendData.code_id = this.codeId;
        this.$q.loading.show();
        // UPDATE
        if (!_.isEmpty(this.entryData)) {
          sendData.id = this.entryData.id;
          this.$axios.post(getUrl('updateCodePrice'), sendData)
            .then(({ data: { updatedCodePrice, error } }) => {
              if (error) {
                this.$q.loading.hide();
                this.showNotif('error', error, 'center');
              } else {
                devlog.log('C_DATA', updatedCodePrice);
                this.$store.dispatch('codesPrices/updateCodePrice', updatedCodePrice);
                this.close(this.codePriceData);
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
          this.$axios.post(getUrl('addCodePrice'), sendData)
            .then(({ data: { codePrice } }) => {
              devlog.log('C_DATA', codePrice);
              this.$store.dispatch('codesPrices/addNewCodePrice', codePrice);
              this.close(this.codePriceData);
              this.$q.loading.hide();
              this.showNotif('success', `Категория <<${codePrice.category_name}>> успешно добавлена клиенту - ${codePrice.code_client_name}`, 'center');
            })
            .catch(({ response: { data: { errors } } }) => {
              this.errorsData.errors = errors;
              this.$q.loading.hide();
            });
        }
      }
    },
    close(data) {
      this.show = false;
      setDefaultData(data);
      this.$emit('update:codeId', 0);
      this.$emit('update:entryData', {});
    },
  },
};
</script>
