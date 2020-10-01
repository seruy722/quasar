<template>
  <Dialog
    :dialog.sync="show"
    title="Клиент"
    :persistent="true"
    data-vue-component-name="DialogAddTask"
  >
    <q-card style="min-width: 320px;width: 100%;max-width: 500px;">
      <q-card-section class="row justify-between bg-grey q-mb-sm">
        <span class="text-h6">{{ dialogTitle }}</span>
        <div>
<!--          <IconBtn-->
<!--            v-show="entryData.id"-->
<!--            dense-->
<!--            icon="delete"-->
<!--            color="negative"-->
<!--            tooltip="Удалить"-->
<!--            @icon-btn-click="destroyCustomer(entryData)"-->
<!--          />-->
          <IconBtn
            dense
            icon="clear"
            tooltip="Закрыть"
            @icon-btn-click="close(tasksData)"
          />
        </div>
      </q-card-section>

      <q-card-section>
        <div
          v-for="(item, index) in tasksData"
          :key="index"
        >
          <SearchSelect
            v-if="item.type==='searchSelect'"
            v-model.number="item.value"
            :label="item.label"
            :dense="$q.screen.xs || $q.screen.sm"
            :field="item.field"
            :options="item.options"
            :func-load-data="item.funcLoadData"
            :change-value.sync="item.changeValue"
            :errors="errorsData"
          />

          <BaseInput
            v-else
            v-model="item.value"
            :label="item.label"
            :dense="$q.screen.xs || $q.screen.sm"
            :field="item.field"
            :mask="item.mask"
            :unmasked-value="item.unmaskedValue"
            :change-value.sync="item.changeValue"
            :autofocus="item.autofocus"
            :errors="errorsData"
          />
        </div>
      </q-card-section>
      <Separator />
      <q-card-actions align="right">
        <BaseBtn
          label="Отмена"
          color="negative"
          :dense="$q.screen.xs || $q.screen.sm"
          @click-base-btn="close(tasksData)"
        />

        <BaseBtn
          label="Сохранить"
          color="positive"
          :dense="$q.screen.xs || $q.screen.sm"
          @click-base-btn="checkErrors(tasksData, saveData)"
        />
      </q-card-actions>
    </q-card>
  </Dialog>
</template>

<script>
    import { getUrl } from 'src/tools/url';
    import CheckErrorsMixin from 'src/mixins/CheckErrors';
    import showNotif from 'src/mixins/showNotif';
    import {
        setDefaultData,
        setChangeValue,
    } from 'src/utils/FrequentlyCalledFunctions';
    import getFromSettings from 'src/tools/settings';

    export default {
        name: 'DialogAddTask',
        components: {
            Dialog: () => import('src/components/Dialogs/Dialog.vue'),
            BaseInput: () => import('src/components/Elements/BaseInput.vue'),
            SearchSelect: () => import('src/components/Elements/SearchSelect.vue'),
            Separator: () => import('src/components/Separator.vue'),
            IconBtn: () => import('src/components/Buttons/IconBtn.vue'),
            BaseBtn: () => import('src/components/Buttons/BaseBtn.vue'),
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
                tasksData: {
                    description: {
                        type: 'text',
                        label: 'Описание',
                        field: 'description',
                        require: true,
                        requireError: 'Поле обьзательное для заполнения.',
                        changeValue: false,
                        autofocus: true,
                        default: null,
                        value: null,
                    },
                    section_id: {
                        type: 'searchSelect',
                        label: 'Раздел',
                        field: 'section_id',
                        options: getFromSettings('sectionTask'),
                        changeValue: false,
                        default: null,
                        value: null,
                    },
                    responsible_id: {
                        type: 'searchSelect',
                        label: 'Ответственный',
                        field: 'responsible_id',
                        options: [],
                        changeValue: false,
                        default: null,
                        value: null,
                    },
                    status_id: {
                        type: 'searchSelect',
                        label: 'Статус',
                        field: 'status_id',
                        options: getFromSettings('statusTask'),
                        changeValue: false,
                        default: 1,
                        value: 1,
                    },
                },
            };
        },
        computed: {
            dialogTitle() {
                return _.isEmpty(this.entryData) ? 'Новая задача' : 'Редактирование задачи';
            },
            usersList() {
                return this.$store.getters['auth/getUsersList'];
            },
        },
        watch: {
            entryData(val) {
                devlog.log('entryData', val);
                if (!_.isEmpty(val)) {
                    // _.forEach(this.tasksData, (item, index) => {
                    //     if (_.get(val, index)) {
                    //         if (index === 'code_id' || index === 'city_id' || index === 'sex') {
                    //             _.set(item, 'value', _.toNumber(_.get(val, index)));
                    //         } else {
                    //             _.set(item, 'value', _.get(val, index));
                    //         }
                    //     }
                    // });
                    _.forEach(this.tasksData, (item, index) => {
                        devlog.log('ITEM', item);
                        if (_.get(this.entryData, `row[${index}]`)) {
                            _.set(item, 'value', _.get(this.entryData, `row[${index}]`));
                        }
                    });
                }
            },
            showDialog(val) {
                this.show = val;
            },
            show(val) {
                this.$emit('update:showDialog', val);
            },
            usersList(val) {
                if (!_.isEmpty(val)) {
                    this.tasksData.responsible_id.options = val;
                }
            },
        },
        created() {
            if (_.isEmpty(this.usersList)) {
                this.$store.dispatch('auth/fetchUsersList');
            }
            this.tasksData.responsible_id.options = this.usersList;
        },
        methods: {
            saveData(values) {
                const createData = _.reduce(values, (result, { value }, index) => {
                    result[index] = value;
                    return result;
                }, {});
                if (_.isEmpty(this.entryData)) {
                    // ДОБАВЛЕНИЕ ЗАПИСИ
                    this.$q.loading.show();
                    this.$axios.post(getUrl('storeTask'), createData)
                      .then(({ data: { task } }) => {
                          this.$q.loading.hide();
                          devlog.log('fgsdfsdf', task);
                          this.$store.dispatch('tasks/addOrUpdateTask', task);
                          this.close(this.tasksData);
                          this.showNotif('success', 'Клиент успешно добавлен.', 'center');
                      })
                      .catch((errors) => {
                          this.errorsData.errors = _.get(errors, 'response.data.errors');
                          this.$q.loading.hide();
                      });
                } else {
                    // ОБНОВЛЕНИЕ ЗАПИСИ
                    const updateData = _.reduce(values, (result, { value, changeValue }, index) => {
                        if (changeValue) {
                            if (index === 'name') {
                                result[index] = _.startCase(value);
                            } else {
                                result[index] = value;
                            }
                        }
                        return result;
                    }, {});
                    if (!_.isEmpty(updateData)) {
                        updateData.id = this.entryData.row.id;
                        devlog.log('VALUIR', values);

                        this.$q.loading.show();
                        this.$axios.post(getUrl('updateTask'), updateData)
                          .then(({ data: { task } }) => {
                              this.$q.loading.hide();
                              this.$store.dispatch('tasks/addOrUpdateTask', task);
                              setChangeValue(values);
                              this.close(this.tasksData);
                              this.showNotif('success', 'Данные клиента успешно обновлены.', 'center');
                          })
                          .catch((errors) => {
                              this.errorsData.errors = _.get(errors, 'response.data.errors');
                              this.$q.loading.hide();
                          });
                    } else {
                        this.showNotif('info', 'Нет измененных данных', 'center');
                    }
                }
            },
            close(data) {
                this.show = false;
                setDefaultData(data);
                setChangeValue(data);
                _.set(this.entryData, 'selected', false);
                this.$emit('update:entryData', {});
            },
        },
    };
</script>
