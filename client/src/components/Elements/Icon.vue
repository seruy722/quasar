<template>
  <q-icon
    data-vue-component-name="Icon"
    :color="color"
    :name="name"
    :size="size"
    @click="$emit('click-icon', $event)"
  >
    <slot></slot>
  </q-icon>
</template>

<script>
    export default {
        name: 'Icon',
        props: {
            color: {
                type: String,
                default: '',
            },
            name: {
                type: String,
                default: '',
            },
            size: {
                type: String,
                default: '',
            },
        },
    };
</script>
