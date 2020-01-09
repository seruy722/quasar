<template>
  <q-card
    data-vue-component-name="Card"
    @click="$emit('clickCard')"
  >
    <slot></slot>
  </q-card>
</template>

<script>
    export default {
        name: 'Card',
    };
</script>
