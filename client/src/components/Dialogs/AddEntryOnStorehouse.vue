<template>
  <div
    data-vue-component-name="AddEntryOnStorehouse"
  >
    <!--        <slot name="button">-->
    <!--            <IconBtn-->
    <!--                color="positive"-->
    <!--                icon="add_box"-->
    <!--                :tooltip="$t('add')"-->
    <!--                @iconBtnClick="show = !show"-->
    <!--            />-->
    <!--        </slot>-->

    <Dialog
      :dialog.sync="show"
      :persistent="true"
      title="Запись"
    >
      <template v-slot:body>
        <ScrollArea>
          <div class="fit row wrap justify-start items-start content-start">
            <div
              v-for="(item, index) in storehouseData"
              :key="index"
              class="col-xs-12 col-sm-4 col-md-3 col-lg-2 q-pt-md q-px-sm bg-white"
            >
              <BaseInput
                v-if="item.type === 'text'"
                v-model.trim="item.value"
                :label="item.label"
                :type="item.type"
                :mask="item.mask"
                :dense="$q.screen.xs || $q.screen.sm"
                :field="index"
                :disable="item.disable"
                :errors="errorsData"
              />

              <BaseInput
                v-else-if="item.type === 'number'"
                v-model.number="item.value"
                :label="item.label"
                :type="item.type"
                :mask="item.mask"
                :dense="$q.screen.xs || $q.screen.sm"
                :field="index"
                :disable="item.disable"
                :errors="errorsData"
              />

              <SearchSelect
                v-else
                v-model="item.value"
                :label="item.label"
                :field="index"
                :dense="$q.screen.xs || $q.screen.sm"
                :options="item.options"
                :errors="errorsData"
              />
            </div>
          </div>

          <q-checkbox
            v-model="withoutThings"
            label="Без описи вложения"
          />


          <div v-show="!withoutThings">
            <List
              dense
              bordered
              padding
              separator
              class="q-px-sm"
            >
              <ItemLabel>
                <div class="row items-center">
                  <div>
                    Опись вложения
                  </div>
                  <AddThings
                    :things.sync="things"
                    :show-dialog.sync="showThingsDialog"
                  />
                </div>
              </ItemLabel>
              <ListItem>
                <ItemSection>Название</ItemSection>
                <ItemSection>Количество</ItemSection>
                <ItemSection>Действие</ItemSection>
              </ListItem>

              <ListItem
                v-for="(item, index) in thingsList"
                :key="index"
                v-ripple
                clickable
              >
                <ItemSection>{{ item.title }}</ItemSection>
                <ItemSection>{{ item.count }}</ItemSection>
                <ItemSection>
                  <IconBtn
                    color="negative"
                    icon="delete"
                    :dense="$q.screen.xs || $q.screen.sm"
                    :tooltip="$t('delete')"
                    @iconBtnClick="remove(index)"
                  />
                </ItemSection>
              </ListItem>
            </List>
          </div>
        </ScrollArea>
      </template>

      <template v-slot:actions>
        <div
          class="row justify-end"
          style="width: 100%;"
        >
          <div class="q-px-sm">
            <OutlineBtn
              :label="$t('save')"
              color="positive"
              icon="save"
              :size="size"
              @clickOutlineBtn="checkErrors(storehouseData, saveData)"
            />
          </div>

          <div class="q-px-sm">
            <OutlineBtn
              :label="$t('clear')"
              color="negative"
              icon="clear"
              :size="size"
              @clickOutlineBtn="clear(storehouseData)"
            />
          </div>

          <div class="q-px-sm">
            <OutlineBtn
              :label="$t('close')"
              color="negative"
              icon="cancel"
              :size="size"
              @clickOutlineBtn="close(storehouseData)"
            />
          </div>
        </div>
      </template>
    </Dialog>
  </div>
</template>

<script>
    import CheckErrorsMixin from 'src/mixins/CheckErrors';
    import { getUrl } from 'src/tools/url';
    import showNotif from 'src/mixins/showNotif';
    import FaxesMixin from 'src/mixins/Faxes';

    export default {
        name: 'AddEntryOnStorehouse',
        components: {
            Dialog: () => import('src/components/Dialogs/Dialog.vue'),
            AddThings: () => import('src/components/Dialogs/AddThings.vue'),
            IconBtn: () => import('src/components/Buttons/IconBtn.vue'),
            OutlineBtn: () => import('src/components/Buttons/OutlineBtn.vue'),
            BaseInput: () => import('src/components/Elements/BaseInput.vue'),
            SearchSelect: () => import('src/components/Elements/SearchSelect.vue'),
            ScrollArea: () => import('src/components/ScrollArea.vue'),
            List: () => import('src/components/Elements/List/List.vue'),
            ItemSection: () => import('src/components/Elements/List/ItemSection.vue'),
            ItemLabel: () => import('src/components/Elements/List/ItemLabel.vue'),
            ListItem: () => import('src/components/Elements/List/ListItem.vue'),
        },
        mixins: [CheckErrorsMixin, showNotif, FaxesMixin],
        props: {
            showDialog: {
                type: Boolean,
                default: false,
            },
        },
        data() {
            return {
                show: false,
                withoutThings: false,
                showThingsDialog: false,
                things: '',
                storehouseData: {
                    code_place: {
                        type: 'text',
                        label: this.$t('code'),
                        mask: '###/###/###',
                        rules: [
                            {
                                name: 'isLength',
                                error: 'Минимальное количество символов 11.',
                                options: {
                                    min: 11,
                                    max: 11,
                                },
                            },
                        ],
                        require: true,
                        requireError: 'Поле обьзательное для заполнения.',
                        default: '',
                        value: '',
                    },
                    code_client_id: {
                        type: 'select',
                        label: this.$t('client'),
                        options: [],
                        require: true,
                        requireError: 'Выберите значение.',
                        default: 0,
                        value: 0,
                    },
                    kg: {
                        type: 'number',
                        label: this.$t('kg'),
                        require: true,
                        requireError: 'Поле обьзательное для заполнения.',
                        default: 0,
                        value: 0,
                    },
                    category_id: {
                        type: 'select',
                        label: this.$t('category'),
                        options: [
                            {
                                label: this.$t('notChosen'),
                                value: 0,
                            },
                        ],
                        require: true,
                        requireError: 'Выберите значение.',
                        default: 0,
                        value: 0,
                    },
                    shop: {
                        type: 'text',
                        label: this.$t('shop'),
                        rules: [
                            {
                                name: 'isLength',
                                error: 'Минимальное количество символов 2.',
                                options: {
                                    min: 2,
                                    max: 255,
                                },
                            },
                        ],
                        default: '',
                        value: '',
                    },
                    for_kg: {
                        type: 'number',
                        label: this.$t('forKg'),
                        default: 0,
                        value: 0,
                    },
                    for_place: {
                        type: 'number',
                        label: this.$t('forPlace'),
                        default: 0,
                        value: 0,
                    },
                    // fax_id: {
                    //     type: 'select',
                    //     label: this.$t('fax'),
                    //     options: [
                    //         {
                    //             label: this.$t('notChosen'),
                    //             value: 0,
                    //         },
                    //     ],
                    //     default: 0,
                    //     value: 0,
                    // },
                    notation: {
                        type: 'text',
                        label: this.$t('notation'),
                        rules: [
                            {
                                name: 'isLength',
                                error: 'Минимальное количество символов 2.',
                                options: {
                                    min: undefined,
                                    max: 255,
                                },
                            },
                        ],
                        default: '',
                        value: '',
                    },
                    sum: {
                        type: 'text',
                        label: this.$t('sum'),
                        disable: true,
                        default: 0,
                        value: 0,
                    },
                },
            };
        },
        computed: {
            thingsList() {
                if (this.things) {
                    return JSON.parse(this.things);
                }
                return this.things;
            },
            categories() {
                return this.$store.getters['category/getCategories'];
            },
            clientCodes() {
                return this.$store.getters['clientCodes/getCodes'];
            },
            size() {
                const {
                    sm,
                    xs,
                    md,
                    lg,
                } = this.$q.screen;

                let size = '';
                if (sm) {
                    size = 'sm';
                } else if (xs) {
                    size = 'xs';
                } else if (md) {
                    size = 'md';
                } else if (lg) {
                    size = 'lg';
                }
                return size;
            },
        },
        watch: {
            categories: {
                handler: function set(val) {
                    this.storehouseData.category_id.options.push(...val);
                },
                immediate: true,
            },
            clientCodes: {
                handler: function set(val) {
                    this.storehouseData.code_client_id.options.push(...val);
                },
                immediate: true,
            },
            'storehouseData.for_kg.value': function wForKg(val) {
                _.set(this.storehouseData, 'sum.value', this.sum(val));
            },
            'storehouseData.for_place.value': function wForPlace(val) {
                _.set(this.storehouseData, 'sum.value', this.sum(val));
            },
            'storehouseData.kg.value': function wForPlace(val) {
                _.set(this.storehouseData, 'sum.value', this.sum(val));
            },
            showDialog(val) {
                this.show = val;
            },
            show(val) {
                this.$emit('update:showDialog', val);
            },
        },
        methods: {
            saveData() {
                const obj = {};
                if (this.things && !this.withoutThings) {
                    obj.things = this.things;
                }
                _.forEach(this.storehouseData, (item, index) => {
                    obj[index] = item.value;
                });
                if (this.withoutThings || obj.things) {
                    this.$q.loading.show();
                    // _.pickBy(obj, _.identity)
                    this.$axios.post(getUrl('addStorehouseData'), obj)
                      .then(({ data }) => {
                          // this.$store.dispatch('storehouse/setStorehouseData', this.prepareFaxTableData(data.storehouseData));
                          this.$store.dispatch('storehouse/setStorehouseData', data.storehouseData);
                          data.categories.push(this.sumObjectForCategories(data.categories));
                          this.$store.dispatch('storehouse/setStorehouseCategoriesData', data.categories);
                          this.$q.loading.hide();
                          this.showNotif('success', 'Данные успешно добавлены.', 'center');
                      })
                      .catch((errors) => {
                          this.errorsData.errors = _.get(errors, 'response.data.errors');
                          // this.showNotif('warning', _.get(errors, 'response.data.errors.things'), 'center');
                          this.$q.loading.hide();
                      });
                } else {
                    this.showThingsDialog = true;
                }
            },
            clear(data) {
                _.forEach(data, (item) => {
                    item.value = item.default;
                });
                this.things = '';
            },
            remove(index) {
                const things = JSON.parse(this.things);
                things.splice(index, 1);
                this.things = JSON.stringify(things);
            },
            sum(val) {
                if (_.isNumber(val)) {
                    return _.round((_.get(this.storehouseData, 'for_kg.value') * _.get(this.storehouseData, 'kg.value')) + _.get(this.storehouseData, 'for_place.value'), 1);
                }
                return 0;
            },
            close(data) {
                this.clear(data);
                this.show = false;
            },
        },
    };
</script>
