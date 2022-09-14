<template>
  <q-card
    data-vue-component-name="RegisterClient"
    flat
  >
    <q-card-section>
      <q-form
        v-show="!yesPhoneInDB"
        @submit="getCodeForRegister"
      >
        <q-input
          v-model="phone"
          hint="380501414144"
          type="tel"
          lazy-rules
          autofocus
          mask="+38 (###)-###-##-##"
          filled
          label="Номер телефона"
          :rules="[ val => val && val.length > 0 || 'Введите номер телефона - пример:380501414144', val => val && val.length === 19 || 'Некоректный номер телефона - пример:380501414144']"
        />
        <div>
          <q-btn
            label="Получить код"
            type="submit"
            color="primary"
          />
        </div>
      </q-form>
      <q-form
        v-show="yesPhoneInDB"
        class="q-gutter-md`"
        @submit="onSubmitRegister"
      >
        <q-input
          v-model="code"
          hint="123456"
          lazy-rules
          autofocus
          mask="##-##-##"
          filled
          label="Код из смс"
          :rules="[ val => val && val.length > 0 || 'Введите код', val => val && val.length === 8 || 'Код должен состоять из 6 цифр']"
        />
        <q-input
          v-model="password"
          type="password"
          lazy-rules
          filled
          label="Пароль"
          :rules="[ val => val && val.length > 0 || 'Введите пароль',val => val && val.length >= 6 || 'Пароль должен состоять из 6 символов']"
        />
        <q-input
          v-model="confirm"
          type="password"
          lazy-rules
          filled
          label="Повторите пароль"
          :rules="[ val => val && val.length > 0 || 'Повторите пароль', val => val && val.length >= 6 || 'Пароль должен состоять из 6 символов']"
        />
        <div>
          <q-btn
            label="Зарегистрироватся"
            type="submit"
            color="primary"
          />
        </div>
      </q-form>
    </q-card-section>
  </q-card>
</template>

<script>
import { getUrl } from 'src/tools/url.js';
import showNotif from 'src/mixins/showNotif';

export default {
  name: 'RegisterClient',
  mixins: [showNotif],
  emits: ['change-tab'],
  data() {
    return {
      phone: null,
      code: null,
      confirm: null,
      password: null,
      yesPhoneInDB: false,
    };
  },
  methods: {
    goToLogin() {
      this.$emit('change-tab', 'login');
    },
    getCodeForRegister() {
      const phone = parseInt(this.phone.replace(/[^\d]/g, ''), 10);
      const sendData = {
        phone,
        text: 'Code for register: ',
      };
      this.$axios.post(getUrl('registerClientCode'), sendData)
        .then(({
                 data: {
                   codeStatus,
                   message,
                 },
               }) => {
          if (codeStatus === 3) {
            this.yesPhoneInDB = true;
            this.showNotif('info', `На номер ${this.phone} отправлен код подтверждения`, 'center');
          } else if (codeStatus === 2) {
            this.showNotif('warning', message, 'center');
          } else if (codeStatus === 1) {
            this.showNotif('info', message, 'center');
            this.goToLogin();
          }
        })
        .catch(() => {
          this.showNotif('warning', 'Слишком много попыток на получение кода. Повторите попытку через 5 минут', 'center');
        });
    },
    onSubmitRegister() {
      this.$axios.post(getUrl('registerClientCodeRegister'), {
        phone: parseInt(this.phone.replace(/[^\d]/g, ''), 10),
        code: parseInt(this.code.replace(/[^\d]/g, ''), 10),
        password: this.password,
        password_confirmation: this.confirm,
      })
        .then(({ data: { register } }) => {
          if (register) {
            this.showNotif('success', 'Регистрация пройшла успешно', 'center');
            this.goToLogin();
          } else {
            this.showNotif('warning', 'Номера телефона нет в базе', 'center');
          }
        })
        .catch((errors) => {
          const errorsData = _.get(errors, 'response.data.errors');
          if (errorsData) {
            this.showNotif('error', 'Пароли не совпадают', 'center');
          } else {
            this.showNotif('warning', 'Слишком много попыток на получение кода. Повторите попытку через 5 минут', 'center');
          }
        });
    },
  },
};
</script>
