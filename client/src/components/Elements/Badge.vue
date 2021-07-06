<template>
  <q-badge
    :color="color"
    :floating="floating"
    transparent
    data-vue-component-name="Badge"
  >
    <slot />
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
            floating: {
                type: Boolean,
                default: false,
            },
        },
    };
</script>
