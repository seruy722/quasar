<template>
  <q-item
    :clickable="clickable"
    :dense="dense"
    data-vue-component-name="ListItem"
    @click="$emit('list-item-click')"
  >
    <slot></slot>
  </q-item>
</template>

<script>
    export default {
        name: 'ListItem',
        props: {
            clickable: {
                type: Boolean,
                default: false,
            },
            dense: {
                type: Boolean,
                default: false,
            },
        },
    };
</script>
