<template>
  <q-pull-to-refresh
    :color="color"
    :icon="icon"
    data-vue-component-name="PullRefresh"
    @refresh="refresh"
  >
    <slot></slot>
  </q-pull-to-refresh>
</template>

<script>
    export default {
        name: 'PullRefresh',
        props: {
            color: {
                type: String,
                default: 'yellow-9',
            },
            icon: {
                type: String,
                default: 'lightbulb',
            },
        },
        methods: {
            refresh(done) {
                this.$emit('refresh', done);
            },
        },
    };
</script>
