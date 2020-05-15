<template>
  <Dialog
    :dialog.sync="show"
    title="Факсы"
    :persistent="true"
    data-vue-component-name="DialogMoveToFax"
  >
    <q-card style="min-width: 320px;width: 100%;max-width: 500px;">
      <q-card-section class="row justify-between bg-grey q-mb-sm">
        <span class="text-h6">Перемещение в факс</span>
        <div>
          <IconBtn
            dense
            icon="clear"
            tooltip="Закрыть"
            @iconBtnClick="close"
          />
        </div>
      </q-card-section>

      <q-card-section>
        <SearchSelect
          v-model="faxData.faxId.value"
          :label="faxData.faxId.label"
          :field="faxData.faxId.field"
          :dense="$q.screen.xs || $q.screen.sm"
          :options="allFaxes"
          :errors="errorsData"
        />
      </q-card-section>

      <Separator />

      <q-card-section>
        <BaseBtn
          label="Отмена"
          color="negative"
          :dense="$q.screen.xs || $q.screen.sm"
          class="q-mr-md"
          @clickBaseBtn="close"
        />

        <BaseBtn
          label="Сохранить"
          color="positive"
          :dense="$q.screen.xs || $q.screen.sm"
          @clickBaseBtn="checkErrors(faxData, saveData)"
        />
      </q-card-section>
    </q-card>
  </Dialog>
</template>

<script>
    // import { getFaxes } from 'src/utils/FrequentlyCalledFunctions';
    import { getUrl } from 'src/tools/url';
    import CheckErrorsMixin from 'src/mixins/CheckErrors';
    import showNotif from 'src/mixins/showNotif';

    export default {
        name: 'DialogMoveToFax',
        components: {
            SearchSelect: () => import('src/components/Elements/SearchSelect.vue'),
            BaseBtn: () => import('src/components/Buttons/BaseBtn.vue'),
            Dialog: () => import('src/components/Dialogs/Dialog.vue'),
            Separator: () => import('src/components/Separator.vue'),
            IconBtn: () => import('src/components/Buttons/IconBtn.vue'),
        },
        mixins: [showNotif, CheckErrorsMixin],
        props: {
            show: {
                type: Boolean,
                default: false,
            },
            values: {
                type: Array,
                default: () => [],
            },
        },
        data() {
            return {
                faxId: null,
                allFaxes: [],
                faxData: {
                    faxId: {
                        label: 'Факсы',
                        field: 'faxId',
                        require: true,
                        requireError: 'Выберите значение.',
                        default: null,
                        value: null,
                    },
                },
            };
        },
        computed: {
            faxes() {
                return this.$store.getters['faxes/getFaxes'];
            },
        },
        watch: {
            faxes(val) {
                if (!_.isEmpty(val)) {
                    this.setAllFaxes(val);
                } else {
                    this.$emit('update:show', false);
                    this.showNotif('warning', 'Список факсов пуст! Добавте факс.', 'center');
                }
                // if (!_.isEmpty(val)) {
                //     this.allFaxes = _.map(_.filter(val, { uploaded_to_cargo: 0 }), ({ name, id }) => ({
                //         label: name,
                //         value: id,
                //     }));
                // }
            },
            show(val) {
                if (val) {
                    this.$q.loading.show();
                    Promise.all([this.$store.dispatch('faxes/fetchFaxes')])
                      .finally(() => {
                          this.$q.loading.hide();
                      });
                }
            },
        },
        created() {
            if (!_.isEmpty(this.faxes)) {
                this.setAllFaxes(this.faxes);
            }
        },
        methods: {
            setAllFaxes(faxes) {
                this.allFaxes = _.map(_.filter(faxes, { uploaded_to_cargo: 0 }), ({ name, id }) => ({
                    label: name,
                    value: id,
                }));
            },
            saveData({ faxId: { value } }) {
                devlog.log('VALS', value);
                this.$q.loading.show();
                const ids = _.map(this.values, 'id');
                this.$axios.post(getUrl('moveFromStorehouseToFax'), {
                    ids,
                    faxId: value,
                })
                  .then(({ data }) => {
                      devlog.log('DDDDA', data);
                      this.$store.dispatch('storehouse/destroyStorehouseData', ids);
                      this.$q.loading.hide();
                      this.close();
                      this.showNotif('success', _.size(ids) > 1 ? 'Записи успешно перемещены.' : 'Запись успешно перемещена.', 'center');
                  })
                  .catch((errors) => {
                      this.$q.loading.hide();
                      devlog.error('Ошибка запроса moveFromStorehouseToFax ', errors);
                  });
            },
            close() {
                this.$emit('update:show', false);
                this.$emit('update:values', []);
                this.faxData.faxId.value = null;
            },
        },
    };
</script>
