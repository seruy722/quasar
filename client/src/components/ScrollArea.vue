<template>
    <q-scroll-area
        :thumb-style="thumbStyle"
        :content-style="contentStyle"
        :content-active-style="contentActiveStyle"
        :style="`height: ${clientWindowHeight}px;`"
        data-vue-component-name="ScrollArea"
    >
        <slot />
    </q-scroll-area>
</template>

<script>
    export default {
        name: 'ScrollArea',
        computed: {
            contentStyle() {
                return {
                    backgroundColor: 'rgba(0,0,0,0.02)',
                    color: '#555',
                };
            },

            contentActiveStyle() {
                return {
                    backgroundColor: '#eee',
                    color: 'black',
                };
            },

            thumbStyle() {
                return {
                    right: '2px',
                    borderRadius: '5px',
                    backgroundColor: '#027be3',
                    width: '5px',
                    opacity: 0.75,
                };
            },
            clientWindowHeight() {
                return document.documentElement.clientHeight - 195;
            },
        },
    };
</script>
