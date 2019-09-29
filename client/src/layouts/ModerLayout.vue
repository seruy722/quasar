<template>
    <q-layout
        view="hHh Lpr lff"
        class="shadow-2 rounded-borders"
    >
        <q-header elevated class="bg-primary">
            <q-toolbar>
                <IconBtn
                    @iconBtnClick="drawer = !drawer"
                    :iconBtnData="{tooltip: 'menu', color: 'white'}"
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
                        title: 'warehouse',
                        field: 'warehouse',
                        icon: 'store',
                    },
                    {
                        title: 'customers',
                        field: 'customers',
                        icon: 'people',
                    },
                    {
                        title: 'profile',
                        field: 'profile',
                        icon: 'person',
                    },
                    {
                        title: 'faxes',
                        field: 'faxes',
                        icon: 'person',
                    },
                    {
                        title: 'drafts',
                        field: 'drafts',
                        icon: 'drafts',
                    },
                    {
                        title: 'search',
                        field: 'search',
                        icon: 'search',
                    },
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
        },
        methods: {
            // menuDrawer() {
            //     this.drawer = !this.drawer;
            // },
            onClickDrawerMenu(item) {
                this.$router.push({ name: item.field });
            },
        },
    };
</script>

<style lang="stylus">
    .my-menu-link
        color white
        background #F2C037
</style>
