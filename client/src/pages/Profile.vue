<template>
    <div
        data-vue-component-name="Profile"
        class="q-pa-md row justify-center"
    >
        <q-card class="my-card">
            <img src="https://cdn.quasar.dev/img/mountains.jpg">

            <q-card-actions align="center">
                <q-btn flat>Action 1</q-btn>
            </q-card-actions>
        </q-card>

        <div style="max-width: 350px">
            <q-list bordered padding>
                <q-item-label header>User Controls</q-item-label>

                <q-item clickable v-ripple>
                    <q-item-section>
                        <q-item-label>Content filtering</q-item-label>
                        <q-item-label caption>
                            Set the content filtering level to restrict
                            apps that can be downloaded
                        </q-item-label>
                    </q-item-section>
                </q-item>

                <q-item clickable v-ripple>
                    <q-item-section>
                        <q-item-label>Password</q-item-label>
                        <q-item-label caption>
                            Require password for purchase or use
                            password to restrict purchase
                        </q-item-label>
                    </q-item-section>
                </q-item>

                <q-separator spaced />
                <q-item-label header>General</q-item-label>

                <q-item tag="label" v-ripple>
                    <q-item-section side top>
                        <q-checkbox v-model="check1" />
                    </q-item-section>

                    <q-item-section>
                        <q-item-label>Notifications</q-item-label>
                        <q-item-label caption>
                            Notify me about updates to apps or games that I downloaded
                        </q-item-label>
                    </q-item-section>
                </q-item>

                <q-item tag="label" v-ripple>
                    <q-item-section side top>
                        <q-checkbox v-model="check2" />
                    </q-item-section>

                    <q-item-section>
                        <q-item-label>Sound</q-item-label>
                        <q-item-label caption>
                            Auto-update apps at anytime. Data charges may apply
                        </q-item-label>
                    </q-item-section>
                </q-item>

                <q-item tag="label" v-ripple>
                    <q-item-section side top>
                        <q-checkbox v-model="check3" />
                    </q-item-section>

                    <q-item-section>
                        <q-item-label>Auto-add widgets</q-item-label>
                        <q-item-label caption>
                            Automatically add home screen widgets
                        </q-item-label>
                    </q-item-section>
                </q-item>

                <q-separator spaced />
                <q-item-label header>Notifications</q-item-label>

                <q-item tag="label" v-ripple>
                    <q-item-section>
                        <q-item-label>{{ $t('chooseLanguage') }}</q-item-label>
                    </q-item-section>
                    <q-item-section side>
                        <Locale />
                    </q-item-section>
                </q-item>

                <q-item tag="label" v-ripple>
                    <q-item-section>
                        <q-item-label>Battery too low</q-item-label>
                    </q-item-section>
                    <q-item-section side>
                        <q-toggle color="blue" v-model="notif1" val="battery" />
                    </q-item-section>
                </q-item>

                <q-item tag="label" v-ripple>
                    <q-item-section>
                        <q-item-label>Friend request</q-item-label>
                        <q-item-label caption>Allow notification</q-item-label>
                    </q-item-section>
                    <q-item-section side top>
                        <q-toggle color="green" v-model="notif2" val="friend" />
                    </q-item-section>
                </q-item>

                <q-item tag="label" v-ripple>
                    <q-item-section>
                        <q-item-label>Picture uploaded</q-item-label>
                        <q-item-label caption>Allow notification when uploading images</q-item-label>
                    </q-item-section>
                    <q-item-section side top>
                        <q-toggle color="red" v-model="notif3" val="picture" />
                    </q-item-section>
                </q-item>
            </q-list>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'Profile',
        components: {
            Locale: () => import('src/components/Locale.vue'),
        },
        data() {
            return {
                check1: true,
                check2: false,
                check3: false,

                notif1: true,
                notif2: true,
                notif3: false,

                volume: 6,
                brightness: 3,
                mic: 8,
            };
        },
    };
</script>

<style lang="stylus" scoped>
    .my-card
        width 100%
        max-width 350px
</style>
