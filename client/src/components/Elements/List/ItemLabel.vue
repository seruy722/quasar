<template>
    <q-item-label
        data-vue-component-name="ItemLabel"
        :caption="caption"
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
    },
  };
</script>
