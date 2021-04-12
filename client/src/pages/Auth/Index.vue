<template>
  <q-page
    data-vue-component-name="Index"
    class="row items-center justify-center"
  >
    <div
      style="max-width: 320px;width: 100%;height: 400px;"
    >
      <q-tabs
        v-model="tab"
        class="bg-primary text-white shadow-2"
        align="justify"
        inline-label
      >
        <q-tab
          name="mails"
          label="Вход"
        />
        <q-tab
          name="alarms"
          label="Регистрация"
        />
<!--        <q-tab-->
<!--          name="movies"-->
<!--          label="Востановление пароля"-->
<!--        />-->
      </q-tabs>

      <q-tab-panels
        v-model="tab"
        animated
      >
        <q-tab-panel name="mails">
          <Login />
        </q-tab-panel>

        <q-tab-panel name="alarms">
          <RegisterClient />
        </q-tab-panel>

        <q-tab-panel name="movies">
          <PasswordRecovery />
        </q-tab-panel>
      </q-tab-panels>
    </div>
  </q-page>
</template>

<script>
import { getLSKey } from 'src/tools/lsKeys';

export default {
  name: 'Index',
  components: {
    Login: () => import('src/pages/Auth/Login.vue'),
    RegisterClient: () => import('src/pages/Auth/RegisterClient.vue'),
    PasswordRecovery: () => import('src/pages/Auth/PasswordRecovery.vue'),
  },
  beforeRouteEnter(to, from, next) {
    next((vm) => {
      vm.$q.loading.show();
      // vm.$q.localStorage.clear();
      const token = vm.$q.localStorage.getItem(getLSKey('authToken'));
      devlog.log('TOKEN_AUTH', token);
      if (!vm.isUserAuth && token) {
        devlog.log('tt', vm.isUserAuth);
        vm.$store.dispatch('auth/getUserModel')
          .then((response) => {
            devlog.log('response', response);
            devlog.log('TOPATH', vm.toPath);
            if (vm.toPath) {
              vm.$router.push(vm.toPath);
            } else {
              vm.$router.push({ name: 'index' })
                .catch((e) => {
                  /* eslint-disable-next-line */
                  console.error('transfersError', e);
                });
            }
            vm.$q.loading.hide();
          })
          .catch(() => {
            devlog.log('CATCH_LOGINO');
            vm.$q.loading.hide();
          });
      } else if (vm.isUserAuth) {
        devlog.log('vm.isUserAuth', vm.isUserAuth);
        devlog.log('TOPATH', vm.toPath);
        if (vm.toPath) {
          vm.$router.push(vm.toPath);
        } else {
          vm.$router.push({ name: 'index' })
            .catch((e) => {
              /* eslint-disable-next-line */
              console.error('transfersError2', e);
            });
        }
        vm.$q.loading.hide();
      } else {
        vm.$q.loading.hide();
      }
    });
  },
  data() {
    return {
      tab: 'mails',
    };
  },
  computed: {
    isUserAuth() {
      return this.$store.getters['auth/isUserAuth'];
    },
    toPath() {
      return this.$store.getters['auth/getToPath'];
    },
  },
};
</script>
