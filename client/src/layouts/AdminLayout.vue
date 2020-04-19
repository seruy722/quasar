<template>
  <q-layout
    view="hHh Lpr lff"
    class="shadow-2 rounded-borders"
  >
    <q-header elevated class="bg-primary">
      <q-toolbar>
        <IconBtn
          dense
          icon="menu"
          tooltip="Меню"
          color="white"
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
    // import { getLSKey } from 'src/tools/lsKeys';
    import accessFunc from 'src/tools/access';

    export default {
        name: 'ModerLayout',
        components: {
            IconBtn: () => import('src/components/Buttons/IconBtn.vue'),
        },
        data() {
            this.$_menu = [
                {
                    title: 'storehouse',
                    field: 'storehouse',
                    icon: 'store',
                    access: {
                        roles: ['admin'],
                        permissions: [],
                    },
                },
                {
                    title: 'customers',
                    field: 'customers',
                    icon: 'people',
                    access: {
                        roles: ['admin'],
                        permissions: [],
                    },
                },
                {
                    title: 'profile',
                    field: 'profile',
                    icon: 'person',
                    access: {
                        roles: ['admin'],
                        permissions: [],
                    },
                },
                {
                    title: 'admin-faxes',
                    field: 'admin-faxes',
                    icon: 'person',
                    access: {
                        roles: ['admin'],
                        permissions: [],
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
                        permissions: [],
                    },
                },
                {
                    title: 'exit',
                    field: 'exit',
                    icon: 'exit_to_app',
                    access: {
                        roles: ['admin'],
                        permissions: [],
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
            userAccess() {
                return _.get(this.$store.getters['auth/getUser'], 'access');
            },
        },
        watch: {
            userAccess(val) {
                this.setMenu(val);
            },
        },
        created() {
            this.setMenu(this.userAccess);
        },
        methods: {
            onClickDrawerMenu(item) {
                this.$router.push({ name: item.field });
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
    background #F2C037
</style>
