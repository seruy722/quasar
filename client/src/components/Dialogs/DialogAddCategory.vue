<template>
  <Dialog
    v-model:dialog="show"
    title="Category"
    :persistent="true"
    data-vue-component-name="DialogAddCategory"
  >
    <Card style="min-width: 320px;width: 100%;max-width: 500px;">
      <CardSection class="row justify-between bg-grey q-mb-sm">
        <span class="text-h6">Новая категория</span>
        <div>
          <IconBtn
            dense
            icon="clear"
            tooltip="Закрыть"
            @icon-btn-click="close"
          />
        </div>
      </CardSection>
      <CardSection class="q-mt-md">
        <BaseInput
          v-model.trim="categoryData.name.value"
          label="Категория"
          :dense="$q.screen.xs || $q.screen.sm"
          :field="categoryData.name.field"
          :autofocus="categoryData.name.autofocus"
          :errors="errorsData"
          @on-key-up="keyUp"
        />
      </CardSection>

      <Separator />

      <CardActions>
        <OutlineBtn
          label="Сохранить"
          color="positive"
          :dense="$q.screen.xs || $q.screen.sm"
          @click-outline-btn="checkErrors(categoryData, saveData)"
        />

        <OutlineBtn
          label="Закрыть"
          color="negative"
          :dense="$q.screen.xs || $q.screen.sm"
          @click-outline-btn="close"
        />
      </CardActions>
    </Card>
  </Dialog>
</template>

<script>
// import { mapGetters } from 'vuex';
import { getUrl } from 'src/tools/url';
// import { getLSKey } from 'src/tools/lsKeys';
import CheckErrorsMixin from 'src/mixins/CheckErrors';
import showNotif from 'src/mixins/showNotif';

export default {
  name: 'DialogAddCategory',
  components: {
    Dialog: () => import('src/components/Dialogs/Dialog.vue'),
    // AddCustomers: () => import('src/components/Dialogs/AddCustomers.vue'),
    OutlineBtn: () => import('src/components/Buttons/OutlineBtn.vue'),
    // BaseBtn: () => import('src/components/Buttons/BaseBtn.vue'),
    BaseInput: () => import('src/components/Elements/BaseInput.vue'),
    Card: () => import('src/components/Elements/Card/Card.vue'),
    CardActions: () => import('src/components/Elements/Card/CardActions.vue'),
    CardSection: () => import('src/components/Elements/Card/CardSection.vue'),
    Separator: () => import('src/components/Separator.vue'),
    IconBtn: () => import('src/components/Buttons/IconBtn.vue'),
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
      categoryData: {
        name: {
          type: 'text',
          label: this.$t('category'),
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
      },
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
    async saveData({ name: { value } }) {
      devlog.log('S_VAL', value);
      this.$q.loading.show();
      await this.$axios.post(getUrl('addCategory'), { name: _.startCase(value) })
        .then(({ data: { category } }) => {
          if (!_.isEmpty(this.$store.getters['category/getCategories'])) {
            this.$store.dispatch('category/AddCategory', category);
          }
          this.$q.loading.hide();
          this.showNotif('success', 'Категория успешно добавлена.', 'center');
        })
        .catch(({ response: { data: { errors } } }) => {
          this.errorsData.errors = errors;
          this.$q.loading.hide();
        });
    },
    close() {
      this.categoryData.name.value = '';
      this.show = false;
    },
    keyUp(event) {
      const {
        code = '',
        key = '',
      } = event;
      if (event.code === 'Enter' || code === 'NumpadEnter' || key === 'Enter' || key === 'NumpadEnter') {
        this.checkErrors(this.categoryData, this.saveData);
      }
    },
  },
};
</script>
