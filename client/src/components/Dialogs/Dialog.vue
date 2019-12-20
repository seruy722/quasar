<template>
    <q-dialog
        v-model="modelData"
        :persistent="persistent"
        data-vue-component-name="Dialog"
    >
      <slot></slot>
    </q-dialog>
</template>

<script>
    export default {
        name: 'Dialog',
        props: {
            dialog: {
                type: Boolean,
                default: false,
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
