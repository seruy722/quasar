<template>
    <div
        data-vue-component-name="Date"
    >
        <Icon
            name="event"
            class="cursor-pointer"
        >
            <q-popup-proxy
                ref="qDateProxy"
                transition-show="scale"
                transition-hide="scale"
            >
                <q-date
                    v-model="valueData"
                    today-btn
                    mask="DD-MM-YYYY"
                    @input="() => $refs.qDateProxy.hide()"
                />
            </q-popup-proxy>
        </Icon>
    </div>
</template>

<script>
  export default {
    name: 'Date',
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
