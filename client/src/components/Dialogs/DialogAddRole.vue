<template>
  <div>
    <Dialog
      :dialog.sync="show"
      title="Роле"
      :persistent="true"
      data-vue-component-name="DialogAddRole"
    >
      <Card style="min-width: 320px;width: 100%;max-width: 500px;">
        <CardSection class="row justify-between bg-grey q-mb-sm">
          <span class="text-h6">{{ roleName ? 'Редактирование роли' : 'Новая роль' }}</span>
          <div>
            <IconBtn
              dense
              icon="clear"
              tooltip="Закрыть"
              @icon-btn-click="close(roleData)"
            />
          </div>
        </CardSection>

        <CardSection>
          <BaseInput
            v-model.trim="roleData.name.value"
            :label="roleData.name.label"
            :dense="$q.screen.xs || $q.screen.sm"
            :field="roleData.name.field"
            :readonly="roleData.name.readonly"
            :autofocus="roleData.name.autofocus"
            :errors="errorsData"
          />

          <SearchSelect
            v-model.number="roleData.users.value"
            :label="roleData.users.label"
            :dense="$q.screen.xs || $q.screen.sm"
            :field="roleData.users.field"
            :multiple="true"
            :options="users"
            :errors="errorsData"
          />
        </CardSection>

        <Separator />

        <CardActions>
          <BaseBtn
            label="Отмена"
            color="negative"
            :dense="$q.screen.xs || $q.screen.sm"
            @click-base-btn="close(roleData)"
          />

          <BaseBtn
            label="Сохранить"
            color="positive"
            :dense="$q.screen.xs || $q.screen.sm"
            @click-base-btn="checkErrors(roleData, saveData)"
          />
        </CardActions>
      </Card>
    </Dialog>
  </div>
</template>

<script>
    import { getUrl } from 'src/tools/url';
    import CheckErrorsMixin from 'src/mixins/CheckErrors';
    import showNotif from 'src/mixins/showNotif';
    import { setDefaultData } from 'src/utils/FrequentlyCalledFunctions';

    export default {
        name: 'DialogAddRole',
        components: {
            Dialog: () => import('src/components/Dialogs/Dialog.vue'),
            // OutlineBtn: () => import('src/components/Buttons/OutlineBtn.vue'),
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
            users: {
                type: Array,
                default: () => [],
            },
            roleName: {
                type: String,
                default: '',
            },
        },
        data() {
            return {
                show: false,
                roleData: {
                    name: {
                        type: 'text',
                        label: 'Роль',
                        field: 'name',
                        autofocus: true,
                        readonly: false,
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
                        require: true,
                        requireError: 'Поле обьзательное для заполнения.',
                        default: '',
                        value: '',
                    },
                    users: {
                        type: 'select',
                        label: 'Пользователи',
                        field: 'users',
                        require: true,
                        requireError: 'Поле обьзательное для заполнения.',
                        default: [],
                        value: [],
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
            roleName(val) {
                devlog.log('RN', val);
                if (_.trim(val)) {
                    this.roleData.name.value = val;
                    this.roleData.name.readonly = true;
                }
            },
        },
        methods: {
            saveData({ name: { value }, users: { value: usersValue } }) {
                devlog.log('S_VAL', value);
                this.$q.loading.show();
                // UPDATE
                if (this.roleName) {
                    this.$axios.post(getUrl('assignRoleToUsers'), {
                        name: value,
                        users: usersValue,
                    })
                      .then(({ data: { users } }) => {
                          this.$store.dispatch('auth/setUsersWithRolesAndPermissions', users);
                          this.showNotif('success', `Роль ${value} успешно задана пользователям ${usersValue.join(',')}.`, 'center');
                          this.close(this.roleData);
                          this.$q.loading.hide();
                      })
                      .catch(({ response: { data: { errors } } }) => {
                          this.errorsData.errors = errors;
                          this.$q.loading.hide();
                      });
                } else {
                    this.$axios.post(getUrl('addRole'), {
                        name: value,
                        users: usersValue,
                    })
                      .then(({ data: { addedRole, users } }) => {
                          devlog.log('C_DATA', addedRole);
                          this.$store.dispatch('roles/addRole', addedRole);
                          this.$store.dispatch('auth/setUsersWithRolesAndPermissions', users);
                          this.showNotif('success', `Роль ${addedRole.name} успешно добавлена.`, 'center');
                          this.close(this.roleData);
                          this.$q.loading.hide();
                      })
                      .catch(({ response: { data: { errors } } }) => {
                          this.errorsData.errors = errors;
                          this.$q.loading.hide();
                      });
                }
            },
            close(data) {
                this.show = false;
                setDefaultData(data);
                this.$emit('update:roleName', '');
                this.roleData.name.readonly = false;
            },
        },
    };
</script>
