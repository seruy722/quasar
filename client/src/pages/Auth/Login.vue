<template>
    <div
        v-if="!isToken()"
        data-vue-component-name="Login"
        class="q-pa-md row items-center justify-center q-gutter-md"
    >
        <q-card class="my-card">
            <q-card-section class="row justify-between bg-primary text-white">
                <div class="text-h5">{{ $t('entrance') }}</div>
                <Locale />
            </q-card-section>

            <q-separator />

            <q-card-section>
                <BaseInput
                    :inputData.sync="inputEmail"
                    :errors="errorsData"
                />

                <BaseInput
                    :inputData.sync="inputPassword"
                    :errors="errorsData"
                    @onKeyUp="keyUp"
                />
            </q-card-section>

            <q-separator />

            <q-card-actions align="left">
                <OutlineBtn
                    :btnData="btnData"
                    @clickOutlineBtn="login"
                />
            </q-card-actions>
        </q-card>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';
    import { getUrl } from 'src/tools/url';
    import { getLSKey } from 'src/tools/lsKeys';
    import OnKeyUp from 'src/mixins/OnKeyUp';

    export default {
        name: 'Login',
        components: {
            BaseInput: () => import('src/components/Elements/BaseInput.vue'),
            OutlineBtn: () => import('src/components/Buttons/OutlineBtn.vue'),
            Locale: () => import('src/components/Locale.vue'),
        },
        mixins: [OnKeyUp],
        data() {
            return {
                errorsData: {
                    errors: {},
                },
                inputEmail: {
                    type: 'email',
                    value: '',
                    field: 'email',
                    lang: 'email',
                    icon: 'person',
                    autofocus: true,
                    dark: false,
                },
                inputPassword: {
                    type: 'password',
                    value: '',
                    field: 'password',
                    lang: 'password',
                    icon: 'lock',
                    dark: false,
                },
                btnData: {
                    title: 'enter',
                    icon: 'lock',
                    dark: false,
                },
            };
        },
        computed: {
            ...mapGetters({
                isUserAuth: 'auth/isUserAuth',
                toPath: 'auth/getToPath',
            }),
        },
        beforeRouteEnter(to, from, next) {
            next((vm) => {
                // vm.$q.cookies.remove(getLSKey('authToken'));
                vm.$q.loading.show();
                const token = vm.$q.cookies.get(getLSKey('authToken'));
                devlog.log('tt', token);
                if (!vm.isUserAuth && token) {
                    vm.$store.dispatch('auth/getUserModel')
                      .then((response) => {
                          if (response) {
                              if (vm.toPath) {
                                  devlog.log('TOPATH', vm.toPath);
                                  vm.$router.push(vm.toPath);
                              } else {
                                  vm.$router.push({ name: 'warehouse' });
                              }
                              // vm.$router.push({ name: 'warehouse' });
                          }
                          vm.$q.loading.hide();
                      });
                } else {
                    vm.$router.push({ name: 'warehouse' });
                    vm.$q.loading.hide();
                }
            });
        },
        created() {
            this.onKeyUpFunc = this.login;
        },
        beforeDestroy() {
            this.$q.loading.hide();
        },
        methods: {
            async login() {
                this.errorsData.errors = {};
                this.$q.loading.show();
                await this.$axios.post(getUrl('login'), {
                    email: this.inputEmail.value,
                    password: this.inputPassword.value,
                })
                  .then(({ data }) => {
                      this.$store.dispatch('auth/setUser', _.get(data, 'user'));
                      this.$q.cookies.set(getLSKey('authToken'), _.get(data, 'access_token'));
                      this.$router.push({ name: 'warehouse' });
                      // devlog.log('LN', this.$q.cookies.get(getLSKey('authToken')));
                  })
                  .catch((errors) => {
                      devlog.log(errors);
                      this.$q.loading.hide();
                      this.errorsData.errors = _.get(errors, 'response.data.errors');
                  });
            },
            isToken() {
                return this.$q.cookies.get(getLSKey('authToken'));
            },
        },
    };
</script>

<style lang="stylus" scoped>
    .my-card
        width 100%
        height 100%
        max-width 600px
</style>
