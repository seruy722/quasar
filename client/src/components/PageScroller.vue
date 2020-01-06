<template>
  <q-page-scroller
    :position="position"
    :offset="offset"
    :scroll-offset="scrollOffset"
    data-vue-component-name="PageScroller"
  >
    <slot></slot>
  </q-page-scroller>
</template>

<script>
    export default {
        name: 'PageScroller',
        props: {
            position: {
                type: String,
                default: 'bottom-right',
            },
            offset: {
                type: Array,
                default: () => [18, 18],
            },
            scrollOffset: {
                type: Number,
                default: 150,
            },
        },
    };
</script>
