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
              <q-icon name="account_circle" size="md"/>
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
    // import { getLSKey } from 'src/tools/lsKeys';

    export default {
        name: 'ModerLayout',
        components: {
            IconBtn: () => import('src/components/Buttons/IconBtn.vue'),
        },
        data() {
            return {
                drawer: false,
                miniState: true,
                menu: [
                    {
                        title: 'storehouse',
                        field: 'storehouse',
                        icon: 'store',
                    },
                    // {
                    //     title: 'customers',
                    //     field: 'customers',
                    //     icon: 'people',
                    // },
                    // {
                    //     title: 'profile',
                    //     field: 'profile',
                    //     icon: 'person',
                    // },
                    // {
                    //     title: 'faxes',
                    //     field: 'faxes',
                    //     icon: 'person',
                    // },
                    {
                        title: 'transfers',
                        field: 'transfers',
                        icon: 'import_export',
                    },
                    // {
                    //     title: 'drafts',
                    //     field: 'drafts',
                    //     icon: 'drafts',
                    // },
                    // {
                    //     title: 'search',
                    //     field: 'search',
                    //     icon: 'search',
                    // },
                    {
                        title: 'exit',
                        field: 'exit',
                        icon: 'exit_to_app',
                    },
                ],
            };
        },
        // beforeDestroy() {
        //     this.$q.localStorage.set(getLSKey('moderDrawerItem'), _.find(this.menu, { active: true }));
        // },
        computed: {
            pageTitle() {
                return this.$route.meta.title;
            },
            userName() {
                return _.get(this.$store.getters['auth/getUser'], 'name');
            },
        },
        methods: {
            // menuDrawer() {
            //     this.drawer = !this.drawer;
            // },
            onClickDrawerMenu({ field }) {
                if (this.$route.name !== field) {
                    if (field === 'exit') {
                        this.$q.localStorage.clear();
                        this.$router.push({ name: 'login' });
                    } else {
                        this.$router.push({ name: field });
                    }
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
