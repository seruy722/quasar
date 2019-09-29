<template>
    <div
        data-vue-component-name="UploadFileDialog"
    >
        <slot name="button">
            <RoundBtn :roundBtnData="{label: dialog.labelBtn}" @roundBtnClick="dialog.value = true" />
        </slot>
        <q-dialog v-model="dialog.value" :persistent="dialog.persistent">
            <q-card :style="`min-width: ${dialog.minWidth || '250px'}`">
                <q-card-section class="row justify-between bg-primary text-white">
                    <div class="text-h5">{{ $t(dialog.title) }}</div>
                </q-card-section>

                <q-separator />

                <slot name="body"></slot>
            </q-card>
        </q-dialog>
    </div>
</template>

<script>
    export default {
        name: 'UploadFileDialog',
        components: {
            RoundBtn: () => import('src/components/Buttons/RoundBtn.vue'),
        },
        props: {
            dialog: {
                type: Object,
                default: () => ({}),
            },
        },
    };
</script>
