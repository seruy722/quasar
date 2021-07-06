<template>
  <q-card
    data-vue-component-name="Card"
    @click="$emit('click-card')"
  >
    <slot />
  </q-card>
</template>

<script>
    export default {
        name: 'Card',
    };
</script>
