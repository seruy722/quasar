<template>
    <div
        data-vue-component-name="BaseBtn"
    >
        <q-btn outline :label="$t(btnData.title)" :color="btnData.color || 'primary'"
               @click="$emit('clickOutlineBtn')"/>
    </div>
</template>

<script>
    export default {
        name: 'BaseBtn',
        props: {
            btnData: {
                type: Object,
                default: () => ({}),
            },
        },
    };
</script>
