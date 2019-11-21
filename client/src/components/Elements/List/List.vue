<template>
    <q-list
        dense
        bordered
        padding
        separator
        data-vue-component-name="List"
    >
        <slot></slot>
    </q-list>
</template>

<script>
    export default {
        name: 'List',
        props: {
            dense: {
                type: Boolean,
                default: false,
            },
            bordered: {
                type: Boolean,
                default: false,
            },
            padding: {
                type: Boolean,
                default: false,
            },
            separator: {
                type: Boolean,
                default: false,
            },
        },
    };
</script>
