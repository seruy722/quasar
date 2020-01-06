<template>
  <q-tr
    :props="props"
    data-vue-component-name="TR"
    @click="$emit('trClick')"
  >
    <slot></slot>
  </q-tr>
</template>

<script>
    export default {
        name: 'TR',
        props: {
            props: {
                type: Object,
                default: () => ({}),
            },
        },
    };
</script>
