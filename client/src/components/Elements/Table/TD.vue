<template>
  <q-td
    :key="keyTd"
    :props="props"
    data-vue-component-name="TD"
  >
    <slot></slot>
  </q-td>
</template>

<script>
    export default {
        name: 'TD',
        props: {
            props: {
                type: Object,
                default: () => ({}),
            },
            keyTd: {
                type: [String, Number],
                default: '',
            },
        },
    };
</script>
