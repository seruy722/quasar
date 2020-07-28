<template>
  <q-layout
    view="hHh Lpr lff"
    class="shadow-2 rounded-borders"
  >
    <q-header elevated class="bg-primary">
      <q-toolbar>
        <IconBtn
          color="white"
          icon="menu"
          tooltip="Меню"
          @iconBtnClick="drawer = !drawer"
        />
        <q-toolbar-title>{{ $t(pageTitle) }}</q-toolbar-title>
      </q-toolbar>
    </q-header>

    <q-drawer
      v-model="drawer"
      show-if-above
      :width="200"
      :breakpoint="500"
      :mini="miniState"
      bordered
      content-class="bg-grey-3"
      mini-to-overlay
      @mouseover="miniState = false"
      @mouseout="miniState = true"
    >
      <q-scroll-area class="fit">
        <q-list padding>
          <q-item>
            <q-item-section avatar>
              <q-icon name="account_circle" size="md" />
            </q-item-section>

            <q-item-section class="text-bold">
              {{ userName }}
            </q-item-section>
          </q-item>

          <q-item
            v-for="(item, index) in menu"
            :key="index"
            v-ripple
            :active="$route.name === item.field"
            clickable
            active-class="my-menu-link"
            @click="onClickDrawerMenu(item)"
          >
            <q-item-section avatar>
              <q-icon :name="item.icon" />
            </q-item-section>

            <q-item-section>
              {{ $t(item.title) }}
            </q-item-section>
          </q-item>
        </q-list>
      </q-scroll-area>
    </q-drawer>

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script>
    import accessFunc from 'src/tools/access';

    export default {
        name: 'ModerLayout',
        components: {
            IconBtn: () => import('src/components/Buttons/IconBtn.vue'),
        },
        data() {
            this.$_menu = [
                {
                    title: 'Карго Долги',
                    field: 'cargo-debts',
                    icon: 'assignment',
                    access: {
                        roles: ['admin'],
                        permissions: [],
                    },
                },
                {
                    title: 'storehouse',
                    field: 'storehouse',
                    icon: 'store',
                    access: {
                        roles: ['admin', 'storehouse'],
                        permissions: ['view storehouse data'],
                    },
                },
                {
                    title: 'codes',
                    field: 'codes',
                    icon: 'people',
                    access: {
                        roles: ['admin', 'codes'],
                        permissions: ['view codes list'],
                    },
                },
                {
                    title: 'codes-prices',
                    field: 'codes-prices',
                    icon: 'money',
                    access: {
                        roles: ['admin', 'codes-prices'],
                        permissions: ['get-codes-prices'],
                    },
                },
                {
                    title: 'faxes',
                    field: 'faxes',
                    icon: 'library_books',
                    access: {
                        roles: ['admin', 'fax'],
                        permissions: ['view faxes list'],
                    },
                },
                {
                    title: 'transfers',
                    field: 'transfers',
                    icon: 'import_export',
                    access: {
                        roles: ['admin', 'transfers'],
                        permissions: ['view transfers list'],
                    },
                },
                {
                    title: 'drafts',
                    field: 'drafts',
                    icon: 'drafts',
                    access: {
                        roles: ['admin'],
                        permissions: [],
                    },
                },
                {
                    title: 'search',
                    field: 'search',
                    icon: 'search',
                    access: {
                        roles: ['admin'],
                        permissions: ['view search page'],
                    },
                },
                {
                    title: 'Доступы',
                    field: 'access',
                    icon: 'settings',
                    access: {
                        roles: ['admin'],
                        permissions: ['view access list'],
                    },
                },
                {
                    title: 'exit',
                    field: 'exit',
                    icon: 'exit_to_app',
                    access: {
                        roles: [],
                        permissions: ['exit app'],
                    },
                },
            ];
            return {
                drawer: false,
                miniState: true,
                menu: [],
            };
        },
        computed: {
            pageTitle() {
                return this.$route.meta.title;
            },
            userName() {
                return _.get(this.$store.getters['auth/getUser'], 'name');
            },
            userAccess() {
                return _.get(this.$store.getters['auth/getUser'], 'access');
            },
        },
        watch: {
            userAccess(val) {
                devlog.log('userAccessWATCH', val);
                this.setMenu(val);
            },
        },
        created() {
            devlog.log('userAccesscreated', this.userAccess);
            this.setMenu(this.userAccess);
        },
        methods: {
            onClickDrawerMenu({ field }) {
                if (this.$route.name !== field) {
                    if (field === 'exit') {
                        this.$q.localStorage.clear();
                        this.$store.dispatch('auth/logout');
                        this.$router.push({ name: 'login' });
                        /* eslint-disable-next-line */
                        globalThis.location.reload();
                        // location.reload();
                    } else {
                        devlog.log('ROUTYFGHDH', this.$route);
                        this.$router.push({ name: field });
                        // this.showNotif('error', 'У Вас нет доступа к этой странице.', 'center');
                    }
                }
            },
            setMenu(userAccess) {
                if (userAccess) {
                    _.forEach(this.$_menu, (item) => {
                        if (accessFunc(userAccess, item.access)) {
                            this.menu.push(item);
                        }
                    });
                }
            },
        },
    };
</script>

<style lang="stylus">
  .my-menu-link
    color white
    background $orange_bg
</style>
