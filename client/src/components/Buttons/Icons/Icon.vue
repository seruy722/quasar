<template>
    <q-icon
        data-vue-component-name="Icon"
        :size="size"
        :name="name"
        @click.stop="$emit('icon-click')"
    >
        <q-tooltip>{{ $t(tooltip) }}</q-tooltip>
    </q-icon>
</template>

<script>
    export default {
        name: 'Icon',
        props: {
            name: {
                type: String,
                default: 'done_outline',
            },
            size: {
                type: String,
                default: 'xs',
            },
            tooltip: {
                type: String,
                default: 'reveal',
            },
        },
    };
</script>
