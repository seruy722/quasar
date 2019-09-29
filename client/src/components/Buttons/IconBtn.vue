<template>
    <div
        data-vue-component-name="IconBtn"
    >
        <q-btn
            flat
            round
            dense
            :color="iconBtnData.color || 'black'"
            :icon="iconBtnData.icon || 'menu'"
            :disable="iconBtnData.disable"
            @click="$emit('iconBtnClick')"
        >
            <q-tooltip>{{ $t(iconBtnData.tooltip) }}</q-tooltip>
        </q-btn>
    </div>
</template>

<script>
    export default {
        name: 'IconBtn',
        props: {
            iconBtnData: {
                type: Object,
                default: () => ({}),
            },
        },
    };
</script>
