<template>
    <q-dialog
        v-model="modelData"
        :persistent="persistent"
        data-vue-component-name="Dialog"
    >
        <q-card style="min-width: 250px; width: 100%; max-width: 1200px;">
            <q-card-section class="row justify-between bg-primary text-white items-center">
                <div class="text-h6">
                    {{ title }}
                </div>

                <div>
                    <IconBtn
                        icon="close"
                        color="white"
                        :dense="$q.screen.xs || $q.screen.sm"
                        :tooltip="$t('close')"
                        @iconBtnClick="modelData = false" />
                </div>
            </q-card-section>

            <slot name="body"></slot>

            <q-separator />

            <slot name="actions"></slot>
        </q-card>
    </q-dialog>
</template>

<script>
    export default {
        name: 'Dialog',
        components: {
            IconBtn: () => import('src/components/Buttons/IconBtn.vue'),
        },
        props: {
            dialog: {
                type: Boolean,
                default: false,
            },
            title: {
                type: String,
                default: 'Dialog',
            },
            persistent: {
                type: Boolean,
                default: false,
            },
        },
        computed: {
            modelData: {
                get: function getValue() {
                    return this.dialog;
                },
                set: function setValue(newValue) {
                    this.$emit('update:dialog', newValue);
                },
            },
        },
    };
</script>
