<template>
  <q-page>
    <q-bar>
      <div class="text-weight-bold">
        Cargo 007
      </div>
      <q-space />
      <q-btn
        v-show="false"
        dense
        color="deep-orange"
        round
        icon="wifi_off"
        @click="closeUsersAccess"
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

<style>
</style>

<script>
    import accessFunc from 'src/tools/access';

    export default {
        name: 'PageIndex',
        components: {
            DialogInput: () => import('components/Dialogs/DialogInput.vue'),
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
        },
        watch: {
            userAccess(val) {
                this.setMenu(val);
            },
            dialogDialogInputKey(val) {
                if (val) {
                    this.closeUsersAccess();
                }
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
            closeUsersAccess() {
                this.showDialogInput = true;
                devlog.log('closeUsersAccess');
            },
        },
    };
</script>
