<template>
    <div
        data-vue-component-name="BaseBtn"
    >
        <q-btn
            :color="color"
            :label="$t(label)"
            @click="$emit('clickBaseBtn')"
        />
    </div>
</template>

<script>
    export default {
        name: 'BaseBtn',
        props: {
            color: {
                type: String,
                default: 'primary',
            },
            label: {
                type: String,
                default: 'Ok',
            },
        },
    };
</script>
