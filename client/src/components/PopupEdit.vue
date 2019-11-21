<template>
    <q-popup-edit
        v-model.trim="modelData"
        data-vue-component-name="PopupEdit"
        :title="$t(title)"
        buttons
        :label-set="$t(labelSet)"
        :label-cancel="$t(labelCancel)"
        dense
        @save="$emit('addToSave')"
    >
        <slot name="body">
            <q-input
                v-model.trim="modelData"
                :type="inputType"
                :mask="mask"
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
            title: {
                type: String,
                default: 'edit',
            },
            labelSet: {
                type: String,
                default: 'save',
            },
            labelCancel: {
                type: String,
                default: 'cancel',
            },
            inputType: {
                type: String,
                default: 'text',
            },
            mask: {
                type: String,
                default: '',
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
            // isValid() {
            //     return this.modelData.length <= 3;
            // },
        },
    };
</script>
