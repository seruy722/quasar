<template>
  <q-btn
    fab
    :icon="icon"
    :color="color"
    data-vue-component-name="FabBtn"
  />
</template>

<script>
    export default {
        name: 'FabBtn',
        props: {
            icon: {
                type: String,
                default: 'add',
            },
            color: {
                type: String,
                default: 'primary',
            },
        },
    };
</script>
