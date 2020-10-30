<template>
  <q-page
    v-show="!isUserAuth"
    data-vue-component-name="Login"
    class="q-pa-md row items-center justify-center q-gutter-md"
  >
    <q-card class="my-card" style="width: 100%;height: 100%;max-width: 600px;">
      <q-card-section class="row justify-between bg-primary text-white">
        <div class="text-h5">{{ $t('entrance') }}</div>
        <q-btn
          label="Регистрация"
          color="positive"
          :to="{name: 'register-client'}"
        />
      </q-card-section>

      <q-separator />

      <q-card-section>
        <BaseInput
          v-model.trim="loginData.email.value"
          type="email"
          field="email"
          icon="person"
          label="Email или номер телефона"
          :dense="$q.screen.xs || $q.screen.sm"
          :autofocus="true"
          :errors="errorsData"
          @on-key-up="keyUp"
        />

        <BaseInput
          v-model.trim="loginData.password.value"
          type="password"
          field="password"
          icon="person"
          :label="this.$t('password')"
          :dense="$q.screen.xs || $q.screen.sm"
          :errors="errorsData"
          @on-key-up="keyUp"
        />
      </q-card-section>

      <q-separator />

      <q-card-actions align="left">
        <OutlineBtn
          :label="this.$t('enter')"
          @click-outline-btn="checkErrors(loginData, login)"
        />
      </q-card-actions>
    </q-card>
  </q-page>
</template>

<script>
import { getUrl } from 'src/tools/url';
import { getLSKey } from 'src/tools/lsKeys';
import OnKeyUp from 'src/mixins/OnKeyUp';
import CheckErrorsMixin from 'src/mixins/CheckErrors';

export default {
  name: 'Login',
  components: {
    BaseInput: () => import('src/components/Elements/BaseInput.vue'),
    OutlineBtn: () => import('src/components/Buttons/OutlineBtn.vue'),
    // Locale: () => import('src/components/Locale.vue'),
  },
  mixins: [OnKeyUp, CheckErrorsMixin],
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
      errorsData: {
        errors: {},
      },
      loginData: {
        email: {
          rules: [
            {
              name: 'isLength',
              error: 'Поле может состоять от 2 до 200 символов',
              options: {
                min: 2,
                max: 200,
              },
            },
          ],
          require: true,
          requireError: 'Введите номер телефона:380501414144 или email: primer@ya.ru',
          value: '',
        },
        password: {
          rules: [
            {
              name: 'isLength',
              error: 'Минимальное количество символов 6.',
              options: {
                min: 6,
                max: 150,
              },
            },
          ],
          require: true,
          requireError: 'Поле обьязательное для заполнения.',
          value: '',
        },
      },
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
  created() {
    this.onKeyUpFunc = this.login;
  },
  destroyed() {
    this.$q.loading.hide();
  },
  methods: {
    async login() {
      devlog.log('LOgin');
      this.errorsData.errors = {};
      this.$q.loading.show();
      await this.$axios.post(getUrl('login'), {
        email: this.loginData.email.value,
        password: this.loginData.password.value,
      })
        .then(({ data }) => {
          this.$store.dispatch('auth/setUser', _.get(data, 'user'));
          this.$q.localStorage.set(getLSKey('authToken'), _.get(data, 'access_token'));
          this.$router.push({ name: 'index' })
            .catch((e) => {
              /* eslint-disable-next-line */
              console.error('transfersError3', e);
            });
        })
        .catch((errors) => {
          this.$q.loading.hide();
          const errorsData = _.get(errors, 'response.data.errors');
          if (!errorsData) {
            this.$q.notify({
              color: 'negative',
              textColor: 'white',
              icon: 'warning',
              message: 'Слишком много попыток. Вход можно будет повторить через 10 минут.',
              position: 'center',
            });
          } else {
            this.errorsData.errors = errorsData;
          }
        });
    },
    isToken() {
      return this.$q.localStorage.getItem(getLSKey('authToken'));
    },
  },
};
</script>
