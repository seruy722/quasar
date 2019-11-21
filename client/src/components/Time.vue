<template>
    <div
        data-vue-component-name="Time"
    >
        <Icon
            name="access_time"
            class="cursor-pointer"
        >
            <q-popup-proxy
                ref="qTimeProxy"
                transition-show="scale"
                transition-hide="scale"
            >
                <q-time
                    v-model="valueData"
                    mask="YYYY-MM-DD HH:mm"
                    format24h
                    @input="() => $refs.qTimeProxy.hide()"
                />
            </q-popup-proxy>
        </Icon>
    </div>
</template>

<script>
  export default {
    name: 'Time',
    components: {
      Icon: () => import('src/components/Elements/Icon.vue'),
    },
    props: {
      value: {
        type: String,
        default: '',
      },
    },
    computed: {
      valueData: {
        get: function get() {
          return this.value;
        },
        set: function set(val) {
          this.$emit('update:value', val);
        },
      },
    },
  };
</script>
