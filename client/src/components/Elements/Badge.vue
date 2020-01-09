<template>
  <q-badge
    :color="color"
    transparent
    data-vue-component-name="Badge"
  >
    <slot></slot>
  </q-badge>
</template>

<script>
    export default {
        name: 'Badge',
        props: {
            color: {
                type: String,
                default: 'primary',
            },
        },
    };
</script>
