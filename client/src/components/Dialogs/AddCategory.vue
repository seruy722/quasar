<template>
  <div
    data-vue-component-name="AddCategory"
  >
    <Dialog
      :dialog.sync="show"
      title="Category"
      :persistent="true"
    >
      <template v-slot:body>
        <q-card-section class="q-mt-md">
          <BaseInput
            v-model.trim="categoryData.name.value"
            label="Category"
            :dense="$q.screen.xs || $q.screen.sm"
            :field="categoryData.name.field"
            :autofocus="categoryData.name.autofocus"
            :errors="errorsData"
          />
        </q-card-section>

        <q-separator />

        <q-card-actions align="right">
          <OutlineBtn
            label="Сохранить"
            color="positive"
            :dense="$q.screen.xs || $q.screen.sm"
            @clickOutlineBtn="checkErrors(categoryData, saveData)"
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
            // AddCustomers: () => import('src/components/Dialogs/AddCustomers.vue'),
            OutlineBtn: () => import('src/components/Buttons/OutlineBtn.vue'),
            // BaseBtn: () => import('src/components/Buttons/BaseBtn.vue'),
            BaseInput: () => import('src/components/Elements/BaseInput.vue'),
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
        created() {
        },
        methods: {
            saveData({ name }) {
                devlog.log('S_VAL', name.value);
                this.$q.loading.show();
                this.$axios.post(getUrl('addCategory'), { name: _.upperFirst(name.value) })
                  .then(({ data }) => {
                      this.$q.loading.hide();
                      this.$store.dispatch('category/AddCategory', data.category);
                      this.showNotif('success', 'Category успешно добавлена.', 'center');
                      devlog.log(this.$store.getters['category/getCategories']);
                  })
                  .catch((errors) => {
                      this.$q.loading.hide();
                      this.errorsData.errors = _.get(errors, 'response.data.errors');
                  });
            },
            close() {
                this.categoryData.name.value = '';
                this.show = false;
            },
        },
    };
</script>
