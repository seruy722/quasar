<template>
  <q-popup-edit
    v-model.trim="modelData"
    data-vue-component-name="PopupEdit"
    :title="title"
    buttons
    :label-set="labelSet"
    :label-cancel="labelCancel"
    dense
    @save="$emit('addToSave')"
    @show="$emit('update:edit', true)"
    @hide="$emit('update:edit', false)"
  >
    <slot :dd="value">
      <q-input
        v-model.trim="modelData"
        :type="type"
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
            rowProps: {
                type: Object,
                default: () => ({}),
            },
            labelSet: {
                type: String,
                default: 'save',
            },
            labelCancel: {
                type: String,
                default: 'cancel',
            },
            type: {
                type: String,
                default: 'text',
            },
            mask: {
                type: String,
                default: '',
            },
            edit: {
                type: Boolean,
                default: false,
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
