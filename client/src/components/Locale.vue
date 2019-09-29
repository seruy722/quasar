<template>
    <div
        data-vue-component-name="Locale"
    >
        <q-btn
            flat
            :icon="icon"
            style="font-size: 1.1em;"
        >
            <q-menu
                transition-show="rotate"
                transition-hide="rotate"
                auto-close
            >
                <q-list
                    style="min-width: 150px"
                    bordered
                    separator
                >
                    <q-item
                        v-for="(item, i) in langs"
                        :key="i"
                        v-ripple
                        :active="item.active"
                        clickable
                        active-class="bg-teal-1"
                        @click="setLocale(item)"
                    >
                        <q-item-section avatar>
                            <q-icon :name="item.icon"/>
                        </q-item-section>
                        <q-item-section>{{ item.label }}</q-item-section>
                    </q-item>
                </q-list>
            </q-menu>
        </q-btn>
    </div>
</template>

<script>
    import { getLSKey } from 'src/tools/lsKeys';

    export default {
        name: 'Locale',
        data() {
            return {
                icon: '',
                langs: [
                    {
                        label: 'Русский',
                        value: 'ru',
                        icon: 'img:statics/icons/flag/ru.png',
                        active: true,
                    },
                    {
                        label: 'Turkish',
                        value: 'tr',
                        icon: 'img:statics/icons/flag/tr.png',
                        active: false,
                    },
                    {
                        label: 'English',
                        value: 'en',
                        icon: 'img:statics/icons/flag/en.png',
                        active: false,
                    },
                ],
            };
        },
        created() {
            // this.$q.localStorage.remove(getLSKey('lang'));
            // this.$q.cookies.remove(getLSKey('lang'));
            const langFromLs = this.$q.cookies.get(getLSKey('lang'));
            if (!_.isEmpty(langFromLs)) {
                this.setLocale(langFromLs);
            } else {
                const findActiveLang = _.find(this.langs, { active: true });
                this.setLocale(findActiveLang);
            }
            this.icon = `img:statics/icons/flag/${langFromLs ? langFromLs.value : this.$i18n.locale}.png`;
        },
        methods: {
            setLocale(item) {
                this.setActive(this.langs, item.value);
                this.$i18n.locale = item.value;
                this.icon = item.icon;
                this.$q.cookies.set(getLSKey('lang'), item);
            },
            setActive(arr, lang) {
                _.forEach(arr, (item) => {
                    item.active = item.value === lang;
                });
            },
        },
    };
</script>
