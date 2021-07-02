<template>
  <div>
    <Dialog
      v-model:dialog="show"
      title="Разрешение"
      :persistent="true"
      data-vue-component-name="DialogAddPermission"
    >
      <Card style="min-width: 320px;width: 100%;max-width: 500px;">
        <CardSection class="row justify-between bg-grey q-mb-sm">
          <span class="text-h6">Новое разрешение</span>
          <div>
            <IconBtn
              dense
              icon="clear"
              tooltip="Закрыть"
              @icon-btn-click="close(permissionData)"
            />
          </div>
        </CardSection>

        <CardSection>
          <BaseInput
            v-model.trim="permissionData.name.value"
            :label="permissionData.name.label"
            :dense="$q.screen.xs || $q.screen.sm"
            :field="permissionData.name.field"
            :readonly="permissionData.name.readonly"
            :autofocus="permissionData.name.autofocus"
            :errors="errorsData"
          />
          <SearchSelect
            v-model.number="permissionData.users.value"
            :label="permissionData.users.label"
            :dense="$q.screen.xs || $q.screen.sm"
            :field="permissionData.users.field"
            :multiple="true"
            :options="users"
            :errors="errorsData"
          />
          <SearchSelect
            v-model.number="permissionData.roles.value"
            :label="permissionData.roles.label"
            :dense="$q.screen.xs || $q.screen.sm"
            :field="permissionData.roles.field"
            :multiple="true"
            :options="roles"
            :errors="errorsData"
          />
        </CardSection>

        <Separator />

        <CardActions>
          <BaseBtn
            label="Отмена"
            color="negative"
            :dense="$q.screen.xs || $q.screen.sm"
            @click-base-btn="close(permissionData)"
          />

          <BaseBtn
            label="Сохранить"
            color="positive"
            :dense="$q.screen.xs || $q.screen.sm"
            @click-base-btn="checkErrors(permissionData, saveData)"
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
  name: 'DialogAddPermission',
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
    roles: {
      type: Array,
      default: () => [],
    },
    permissionName: {
      type: String,
      default: '',
    },
  },
  emits: ['update:permissionName', 'update:showDialog'],
  data() {
    return {
      show: false,
      permissionData: {
        name: {
          type: 'text',
          label: 'Разрешение',
          field: 'name',
          autofocus: true,
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
          readonly: false,
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
        roles: {
          type: 'select',
          label: 'Роли',
          field: 'roles',
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
    permissionName(val) {
      devlog.log('RN', val);
      if (_.trim(val)) {
        this.permissionData.name.value = val;
        this.permissionData.name.readonly = true;
      }
    },
  },
  methods: {
    saveData({
               name: { value },
               users: { value: usersNames },
               roles: { value: rolesNames },
             }) {
      devlog.log('S_VAL', value);
      this.$q.loading.show();
      // UPDATE
      if (this.permissionName) {
        this.$axios.post(getUrl('assignPermissionToRoleAndUser'), {
          name: value,
          users: usersNames,
          roles: rolesNames,
        })
          .then(({ data: { users } }) => {
            this.$store.dispatch('auth/setUsersWithRolesAndPermissions', users);
            this.showNotif('success', `Разрешение ${value} успешно добавлено пользователям ${usersNames.join(',')} и ролям ${rolesNames.join(',')}.`, 'center');
            this.close(this.permissionData);
            this.$q.loading.hide();
          })
          .catch(({ response: { data: { errors } } }) => {
            this.errorsData.errors = errors;
            this.$q.loading.hide();
          });
      } else {
        this.$axios.post(getUrl('addPermission'), {
          name: value,
          users: usersNames,
          roles: rolesNames,
        })
          .then(({
                   data: {
                     addedPermission,
                     users,
                   },
                 }) => {
            devlog.log('C_DATA', addedPermission);
            this.$store.dispatch('permissions/addPermission', addedPermission);
            this.$store.dispatch('auth/setUsersWithRolesAndPermissions', users);
            this.showNotif('success', `Разрешение ${addedPermission.name} успешно добавлено.`, 'center');
            this.close(this.permissionData);
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
      this.$emit('update:permissionName', '');
      this.permissionData.name.readonly = false;
    },
  },
};
</script>
