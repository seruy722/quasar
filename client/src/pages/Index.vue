<template>
  <q-page>
    <q-bar>
      <div class="text-weight-bold">
        Cargo 007 - v{{ $q.version }}
      </div>
      <q-space />
      <q-btn
        v-if="!client"
        dense
        color="deep-orange"
        round
        icon="wifi_off"
        @click="showDialogInput = true"
      />
      <div>{{ userName }}</div>
    </q-bar>
    <div class="row justify-center q-gutter-sm">
      <q-intersection
        v-for="(item, index) in menu"
        :key="index"
        transition="scale"
        style="height: 150px;width: 150px;"
      >
        <q-card
          class="q-ma-sm text-center cursor-pointer"
          @click="onClickDrawerMenu(item)"
        >
          <q-icon
            :name="item.icon"
            size="70px"
          />
          <q-card-section>
            <div class="text-subtitle2">{{ item.title }}</div>
          </q-card-section>
        </q-card>
      </q-intersection>
    </div>
    <DialogInput
      :show-dialog.sync="showDialogInput"
      :key-data.sync="dialogDialogInputKey"
    />
  </q-page>
</template>

<script>
import accessFunc from 'src/tools/access';

export default {
  name: 'PageIndex',
  components: {
    DialogInput: () => import('src/components/Dialogs/DialogInput.vue'),
  },
  data() {
    return {
      menu: [],
      showDialogInput: false,
      dialogDialogInputKey: null,
    };
  },
  computed: {
    userName() {
      return _.get(this.$store.getters['auth/getUser'], 'name');
    },
    userAccess() {
      return _.get(this.$store.getters['auth/getUser'], 'access');
    },
    mainMenu() {
      return this.$store.getters['settings/getMenu'];
    },
    client() {
      return _.includes(_.get(this.userAccess, 'roles'), 'client');
    },
  },
  watch: {
    userAccess(val) {
      this.setMenu(val);
    },
    dialogDialogInputKey(val) {
      if (val) {
        this.closeUsersAccess(val);
      }
    },
  },
  created() {
    this.setMenu(this.userAccess);
    devlog.log('userAccess', _.includes(_.get(this.userAccess, 'roles'), 'client'));
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
            if (item.field !== 'index') {
              this.menu.push(item);
            }
          }
        });
      }
    },
    async closeUsersAccess(key) {
      const { getUrl } = await import('src/tools/url');
      if (key === 'ruin') {
        this.$axios.post(getUrl('closeUsersAccess'), { key })
          .then(({ data: { answer } }) => {
            if (answer) {
              this.dialogDialogInputKey = null;
              this.$q.localStorage.clear();
              this.$store.commit('auth/REMOVE_USER_DATA');
              this.$router.push({ name: 'login' });
            }
          });
        devlog.log('CLOSE');
      } else {
        this.dialogDialogInputKey = null;
        devlog.log('CLOSE_FALSE');
      }
    },
  },
};
</script>
