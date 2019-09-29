<template>
    <q-popup-edit
        v-model.trim="modelData"
        data-vue-component-name="PopupEdit"
        :title="$t(popupSettings.title || 'edit')"
        buttons
        :label-set="$t(popupSettings.labelSet || 'save')"
        :label-cancel="$t(popupSettings.labelCancel || 'cancel')"
        dense
        @save="$emit('addToSave')"
    >
        <slot name="body">
            <q-input
                v-if="popupSettings.type === 'text'"
                v-model.trim="modelData"
                type="text"
                dense
                autofocus
            />
            <q-input
                v-else-if="popupSettings.type === 'number'"
                v-model.number="modelData"
                type="number"
                dense
                autofocus
            />
        </slot>
    </q-popup-edit>
</template>

<script>
    export default {
        name: 'PopupEdit',
        props: {
            value: {
                type: [String, Number],
                default: '',
            },
            popupSettings: {
                type: Object,
                default: () => ({}),
            },
        },
        computed: {
            modelData: {
                get: function getData() {
                    return this.value;
                },
                set: function setData(newValue) {
                    this.$emit('update:value', newValue);
                },
            },
        },
    };
</script>
