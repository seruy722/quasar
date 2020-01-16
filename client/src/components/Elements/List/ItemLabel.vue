<template>
  <q-item-label
    data-vue-component-name="ItemLabel"
    :caption="caption"
    :lines="lines"
  >
    <slot></slot>
  </q-item-label>
</template>

<script>
    export default {
        name: 'ItemLabel',
        props: {
            caption: {
                type: Boolean,
                default: false,
            },
            lines: {
                type: Number,
                default: 1,
            },
        },
    };
</script>
