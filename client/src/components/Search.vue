<template>
    <q-input
        dense
        debounce="500"
        clearable
        clear-icon="close"
        :value="value"
        :placeholder="$t('search')"
        data-vue-component-name="Search"
        @input="$emit('input', $event)"
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
</template>

<script>
    export default {
        name: 'Search',
        props: {
            value: {
                type: String,
                default: '',
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
