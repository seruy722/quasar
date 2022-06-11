<template>
  <DialogComponent
    v-model:dialog="show"
    title="Добавление коду категории"
    :persistent="true"
    data-vue-component-name="DialogAddNewCodePrice"
  >
    <Card style="min-width: 320px;width: 100%;max-width: 1000px;">
      <CardSection class="row justify-between bg-grey">
        <span class="text-h6">Добавление категории клиенту</span>
        <div>
          <IconBtn
            dense
            icon="clear"
            tooltip="Закрыть"
            @icon-btn-click="close(codePriceData)"
          />
        </div>
      </CardSection>

      <CardSection :class="$q.screen.xs ? 'justify-center': 'justify-between row'">
        <!--        <div class="row" :class="$q.screen.xs ? 'justify-center': 'justify-between'">-->
        <SearchSelect
          v-model.number="codePriceData.code_id.value"
          v-model:change-value="codePriceData.code_id.changeValue"
          :label="codePriceData.code_id.label"
          :dense="$q.screen.xs || $q.screen.sm"
          :field="codePriceData.code_id.field"
          :options="clientCodes"
          :errors="errorsData"
        />
        <SearchSelect
          v-model.number="codePriceData.category_id.value"
          v-model:change-value="codePriceData.category_id.changeValue"
          :label="codePriceData.category_id.label"
          :dense="$q.screen.xs || $q.screen.sm"
          :field="codePriceData.category_id.field"
          :options="categories"
          :errors="errorsData"
        />
        <BaseInput
          v-model.number="codePriceData.for_kg.value"
          v-model:change-value="codePriceData.for_kg.changeValue"
          :label="codePriceData.for_kg.label"
          :dense="$q.screen.xs || $q.screen.sm"
          :field="codePriceData.for_kg.field"
          :errors="errorsData"
        />
        <BaseInput
          v-model.number="codePriceData.for_place.value"
          v-model:change-value="codePriceData.for_place.changeValue"
          :label="codePriceData.for_place.label"
          :dense="$q.screen.xs || $q.screen.sm"
          :field="codePriceData.for_place.field"
          :errors="errorsData"
        />
        <!--        </div>-->
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
  </DialogComponent>
</template>

<script>
import { getUrl } from 'src/tools/url';
import CheckErrorsMixin from 'src/mixins/CheckErrors';
import showNotif from 'src/mixins/showNotif';
import { setDefaultData, getCategories, getClientCodes } from 'src/utils/FrequentlyCalledFunctions';
import DialogComponent from 'src/components/Dialogs/DialogComponent.vue';
import BaseBtn from 'src/components/Buttons/BaseBtn.vue';
import BaseInput from 'src/components/Elements/BaseInput.vue';
import Separator from 'src/components/Separator.vue';
import Card from 'src/components/Elements/Card/Card.vue';
import CardActions from 'src/components/Elements/Card/CardActions.vue';
import CardSection from 'src/components/Elements/Card/CardSection.vue';
import IconBtn from 'src/components/Buttons/IconBtn.vue';
import SearchSelect from 'src/components/Elements/SearchSelect.vue';

export default {
  name: 'DialogAddNewCodePrice',
  components: {
    DialogComponent,
    BaseBtn,
    BaseInput,
    Separator,
    Card,
    CardActions,
    CardSection,
    IconBtn,
    SearchSelect,
  },
  mixins: [showNotif, CheckErrorsMixin],
  props: {
    showDialog: {
      type: Boolean,
      default: false,
    },
  },
  emits: ['update:showDialog'],
  data() {
    return {
      show: false,
      codePriceData: {
        code_id: {
          type: 'number',
          label: 'Код',
          field: 'code_id',
          require: true,
          requireError: 'Поле обьзательное для заполнения.',
          changeValue: false,
          default: null,
          value: null,
        },
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
          label: 'За кг',
          field: 'for_place',
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
    clientCodes() {
      return this.$store.getters['codes/getCodes'];
    },
  },
  watch: {
    showDialog(val) {
      if (val) {
        this.$q.loading.show();
        Promise.all([getCategories(this.$store), getClientCodes(this.$store)])
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
    saveData(values) {
      devlog.log('S_VAL', values);
      const sendData = _.reduce(values, (result, item) => {
        if (item.changeValue) {
          result[item.field] = item.value;
        }
        return result;
      }, {});
      devlog.log('dd', sendData);
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
    },
    close(data) {
      this.show = false;
      setDefaultData(data);
    },
  },
};
</script>
