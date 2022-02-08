<template>
  <q-layout
    view="hHh Lpr lff"
    class="shadow-2 rounded-borders"
  >
    <q-header
      elevated
      class="bg-primary"
    >
      <q-toolbar>
        <IconBtn
          color="white"
          icon="menu"
          tooltip="Меню"
          @icon-btn-click="drawer = !drawer"
        />
        <q-toolbar-title>{{ pageTitle }}</q-toolbar-title>
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
              <q-icon
                name="account_circle"
                size="md"
              />
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
              {{ item.title }}
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
import IconBtn from 'components/Buttons/IconBtn.vue';
import { ref } from 'vue';

export default {
  name: 'ModerLayout',
  components: {
    IconBtn,
  },
  setup() {
    return {
      drawer: ref(false),
      miniState: ref(true),
      menu: ref([]),
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
    mainMenu() {
      return this.$store.getters['settings/getMenu'];
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
    onClickDrawerMenu({ field }) {
      if (this.$route.name !== field) {
        if (field === 'exit') {
          this.$q.localStorage.clear();
          this.$store.commit('auth/REMOVE_USER_DATA');
          this.$router.push({ name: 'login' });
        } else {
          this.$router.push({ name: field });
        }
      }
    },
    setMenu(userAccess) {
      if (userAccess) {
        _.forEach(this.mainMenu, (item) => {
          if (accessFunc(userAccess, item.access)) {
            this.menu.push(item);
          }
        });
      }
    },
  },
};
</script>

<style lang="scss">
.my-menu-link {
  color: white;
  background-color: orange;
}
</style>
