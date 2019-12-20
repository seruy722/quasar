<template>
  <q-card-actions :align="align" data-vue-component-name="CardActions">
    <slot></slot>
  </q-card-actions>
</template>

<script>
    export default {
        name: 'CardActions',
        props: {
            align: {
                type: String,
                default: 'right',
            },
            vertical: {
                type: Boolean,
                default: false,
            },
        },
    };
</script>
