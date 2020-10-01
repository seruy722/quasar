<template>
  <q-page
    data-vue-component-name="Access"
    class="q-pa-md"
  >
    <div class="text-bold text-center text-h5">Доступ пользователей к роутам</div>
    <div v-for="(item, index) in usersWithRolesAndPermissions" :key="index">
      <div style="border: 1px solid lightgray;margin: 10px 0;padding: 10px;">
        <div>USER_NAME: {{ item.name }}</div>
        <div v-for="(role, i) in item.roles" :key="i" class="q-ml-md">
          USER_ROLE: {{ role.name }}
          <IconBtn
            flat
            dense
            icon="delete"
            color="negative"
            tooltip="Удалить роль"
            @icon-btn-click="deleteRoleFromUser(item, role)"
          />
          <div v-for="(permission, ind) in role.permissions" :key="ind" class="q-ml-lg">
            ROLE_PERMISIION: {{ permission.name }}
            <IconBtn
              flat
              dense
              icon="delete"
              color="negative"
              tooltip="Удалить разрешение"
              @icon-btn-click="deletePermissionFromRoleOrUser(role, permission, 'role')"
            />
          </div>
        </div>
        <div v-for="(permission) in item.permissions" :key="permission.name">
          USER_PERMISIION: {{ permission.name }}
          <IconBtn
            flat
            dense
            icon="delete"
            color="negative"
            tooltip="Удалить разрешение"
            @icon-btn-click="deletePermissionFromRoleOrUser(item, permission, 'user')"
          />
        </div>
      </div>
    </div>
    <div style="max-width: 500px">
      <List
        dense
        bordered
      >
        <q-expansion-item
          icon="explore"
          label="Роли"
          header-class="text-primary"
        >
          <template v-slot:header>
            <q-item-section avatar>
              <q-icon name="accessibility_new" />
            </q-item-section>

            <q-item-section>
              Роли
            </q-item-section>

            <q-item-section>
              <div>
                <Badge color="info">{{ getRoles.length }}</Badge>
              </div>
            </q-item-section>

            <q-item-section>
              <q-item-label>
                <IconBtn
                  flat
                  dense
                  icon="add_circle"
                  tooltip="Добавить"
                  @icon-btn-click="viewDialogAddRole"
                />
              </q-item-label>
            </q-item-section>
          </template>
          <List
            dense
            bordered
            separator
          >
            <ListItem
              v-for="(item, index) in getRoles"
              :key="index"
            >
              <ItemSection>
                <ItemLabel>{{ item.name }}</ItemLabel>
              </ItemSection>

              <ItemSection side>
                <div class="row">
                  <IconBtn
                    flat
                    dense
                    icon="edit"
                    color="teal"
                    tooltip="Редактировать"
                    @icon-btn-click="viewDialogAddRole(item.name)"
                  />
                  <IconBtn
                    flat
                    dense
                    icon="delete"
                    color="negative"
                    tooltip="Удалить"
                    @icon-btn-click="deleteRole(item)"
                  />
                </div>
              </ItemSection>
            </ListItem>
          </List>
        </q-expansion-item>

        <q-separator />

        <q-expansion-item icon="lock" label="Разрешения" header-class="text-teal">
          <template v-slot:header>
            <q-item-section avatar>
              <q-icon name="lock" />
            </q-item-section>

            <q-item-section>
              Разрешения
            </q-item-section>

            <q-item-section>
              <div>
                <Badge color="info">{{ getPermissions.length }}</Badge>
              </div>
            </q-item-section>

            <q-item-section>
              <q-item-label>
                <IconBtn
                  flat
                  dense
                  icon="add_circle"
                  tooltip="Добавить"
                  @icon-btn-click="viewDialogAddPermission"
                />
              </q-item-label>
            </q-item-section>
          </template>

          <List
            dense
            bordered
            separator
          >
            <ListItem
              v-for="(item, index) in getPermissions"
              :key="index"
            >
              <ItemSection>
                <ItemLabel>{{ item.name }}</ItemLabel>
              </ItemSection>

              <ItemSection side>
                <div class="row">
                  <IconBtn
                    flat
                    dense
                    icon="edit"
                    color="teal"
                    tooltip="Редактировать"
                    @icon-btn-click="viewDialogAddPermission(item.name)"
                  />
                  <IconBtn
                    flat
                    dense
                    icon="delete"
                    color="negative"
                    tooltip="Удалить"
                    @icon-btn-click="deletePermission(item)"
                  />
                </div>
              </ItemSection>
            </ListItem>
          </List>
        </q-expansion-item>
      </List>
    </div>
    <DialogAddRole
      :show-dialog.sync="showDialogAddRole"
      :users="users"
      :role-name.sync="roleName"
    />
    <DialogAddPermission
      :show-dialog.sync="showDialogAddPermission"
      :users="users"
      :roles="rolesNames"
      :permission-name.sync="permissionName"
    />
  </q-page>
</template>

<script>
    import { getUrl } from 'src/tools/url';
    import showNotif from 'src/mixins/showNotif';

    export default {
        name: 'Access',
        components: {
            List: () => import('src/components/Elements/List/List.vue'),
            ItemSection: () => import('src/components/Elements/List/ItemSection.vue'),
            ItemLabel: () => import('src/components/Elements/List/ItemLabel.vue'),
            ListItem: () => import('src/components/Elements/List/ListItem.vue'),
            Badge: () => import('src/components/Elements/Badge.vue'),
            IconBtn: () => import('src/components/Buttons/IconBtn.vue'),
            DialogAddRole: () => import('src/components/Dialogs/DialogAddRole.vue'),
            DialogAddPermission: () => import('src/components/Dialogs/DialogAddPermission.vue'),
        },
        mixins: [showNotif],
        data() {
            return {
                showDialogAddRole: false,
                showDialogAddPermission: false,
                roles: [],
                permissions: [],
                roleName: '',
                permissionName: '',
            };
        },
        computed: {
            getRoles() {
                return this.$store.getters['roles/getRoles'];
            },
            getPermissions() {
                return this.$store.getters['permissions/getPermissions'];
            },
            users() {
                return _.map(this.usersWithRolesAndPermissions, ({ name }) => name);
            },
            rolesNames() {
                return _.map(this.getRoles, ({ name }) => name);
            },
            usersWithRolesAndPermissions() {
                return this.$store.getters['auth/getUsersWithRolesAndPermissions'];
            },
        },
        created() {
            this.$store.dispatch('roles/fetchRoles');
            this.$store.dispatch('permissions/fetchPermissions');
            this.getUsersWithRoles();
        },
        methods: {
            async getUsersWithRoles() {
                await this.$axios.get(getUrl('usersWithRoles'))
                  .then(({ data: { users } }) => {
                      devlog.log('DAERT', users);
                      this.$store.dispatch('auth/setUsersWithRolesAndPermissions', users);
                  })
                  .catch((errors) => {
                      devlog.error('Ошибка ', errors);
                  });
            },
            viewDialogAddRole(val) {
                devlog.log('VAL', val);
                if (val) {
                    this.roleName = val;
                }
                this.showDialogAddRole = true;
            },
            viewDialogAddPermission(val) {
                devlog.log('VAL', val);
                if (val) {
                    this.permissionName = val;
                }
                this.showDialogAddPermission = true;
            },
            deleteRole({ id, name }) {
                devlog.log('IDDDD', id);
                this.showNotif('warning', `Удалить роль - ${name} ?`, 'center', [
                    {
                        label: 'Отмена',
                        color: 'white',
                        handler: () => {
                        },
                    },
                    {
                        label: 'Удалить',
                        color: 'white',
                        handler: () => {
                            this.$q.loading.show();
                            this.$axios.get(`${getUrl('deleteRole')}/${id}`)
                              .then(({ data: { users } }) => {
                                  this.$store.dispatch('roles/deleteRole', id);
                                  this.$store.dispatch('auth/setUsersWithRolesAndPermissions', users);
                                  this.$q.loading.hide();
                                  this.showNotif('success', `Роль ${name} успешно удалена.`, 'center');
                              })
                              .catch((errors) => {
                                  this.$q.loading.hide();
                                  devlog.error('Ошибка ', errors);
                              });
                        },
                    },
                ]);
            },
            deleteRoleFromUser({ id: userId, name: userName }, { id: roleId, name: roleName }) {
                this.showNotif('warning', `Удалить роль - ${roleName} у пользователя ${userName}?`, 'center', [
                    {
                        label: 'Отмена',
                        color: 'white',
                        handler: () => {
                        },
                    },
                    {
                        label: 'Удалить',
                        color: 'white',
                        handler: () => {
                            this.$q.loading.show();
                            this.$axios.post(getUrl('deleteRoleFromUser'), {
                                userId,
                                roleId,
                            })
                              .then(({ data: { users } }) => {
                                  this.$store.dispatch('auth/setUsersWithRolesAndPermissions', users);
                                  this.$q.loading.hide();
                                  this.showNotif('success', `Роль ${roleName} успешно удалена у пользователя ${userName}.`, 'center');
                              })
                              .catch((errors) => {
                                  this.$q.loading.hide();
                                  devlog.error('Ошибка ', errors);
                              });
                        },
                    },
                ]);
            },
            deletePermission({ id, name }) {
                devlog.log('IDDDD', id);
                this.showNotif('warning', `Удалить разрешение - ${name} ?`, 'center', [
                    {
                        label: 'Отмена',
                        color: 'white',
                        handler: () => {
                        },
                    },
                    {
                        label: 'Удалить',
                        color: 'white',
                        handler: () => {
                            this.$q.loading.show();
                            this.$axios.get(`${getUrl('deletePermission')}/${id}`)
                              .then(({ data: { users } }) => {
                                  this.$store.dispatch('permissions/deletePermission', id);
                                  this.$store.dispatch('auth/setUsersWithRolesAndPermissions', users);
                                  this.$q.loading.hide();
                                  this.showNotif('success', `Разрешение ${name} успешно удалено.`, 'center');
                              })
                              .catch((errors) => {
                                  this.$q.loading.hide();
                                  devlog.error('Ошибка ', errors);
                              });
                        },
                    },
                ]);
            },
            deletePermissionFromRoleOrUser(fromObj, permission, key) {
                this.showNotif('warning', `Удалить разрешение - ${permission.name}?`, 'center', [
                    {
                        label: 'Отмена',
                        color: 'white',
                        handler: () => {
                        },
                    },
                    {
                        label: 'Удалить',
                        color: 'white',
                        handler: () => {
                            this.$q.loading.show();
                            this.$axios.post(getUrl('deletePermissionFromRoleOrUser'), {
                                name: fromObj.name,
                                permissionName: permission.name,
                                key,
                            })
                              .then(({ data: { users } }) => {
                                  this.$store.dispatch('auth/setUsersWithRolesAndPermissions', users);
                                  this.$q.loading.hide();
                                  this.showNotif('success', `Разрешение ${permission.name} успешно удалено.`, 'center');
                              })
                              .catch((errors) => {
                                  this.$q.loading.hide();
                                  devlog.error('Ошибка ', errors);
                              });
                        },
                    },
                ]);
            },
        },
    };
</script>
<style lang="stylus">
  .block
    border 1px solid lightgray

  .block_table
    padding 30px
    margin 10px
    border 1px solid lightgray
</style>
