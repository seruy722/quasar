<template>
    <div
        data-vue-component-name="Search"
    >
        <q-input
            v-model="filter.value"
            dense
            debounce="500"
            clearable
            clear-icon="close"
            :placeholder="$t('search')"
            @focus="focusInput"
            @blur="blurInput"
        >
            <template v-slot:append>
                <q-icon
                    name="search"
                    :color="iconColor"
                />
            </template>
        </q-input>
    </div>
</template>

<script>
    export default {
        name: 'Search',
        props: {
            filter: {
                type: Object,
                default: () => ({}),
            },
        },
        data() {
            return {
                iconColor: '',
            };
        },
        methods: {
            focusInput() {
                this.iconColor = 'primary';
            },
            blurInput() {
                this.iconColor = '';
            },
        },
    };
</script>
