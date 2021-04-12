<template>
  <q-card data-vue-component-name="Login">
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
        label="Пароль"
        :dense="$q.screen.xs || $q.screen.sm"
        :errors="errorsData"
        @on-key-up="keyUp"
      />
    </q-card-section>

    <q-separator />

    <q-card-actions align="right">
      <BaseBtn
        label="Войти"
        @click-outline-btn="checkErrors(loginData, login)"
      />
    </q-card-actions>
  </q-card>
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
    BaseBtn: () => import('src/components/Buttons/BaseBtn.vue'),
  },
  mixins: [OnKeyUp, CheckErrorsMixin],
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
  destroyed() {
    this.$q.loading.hide();
  },
  methods: {
    onKeyUpFunc() {
      this.checkErrors(this.loginData, this.login);
    },
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
