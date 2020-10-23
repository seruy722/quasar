<template>
  <Icon
    name="event"
    class="cursor-pointer"
    size="md"
    data-vue-component-name="Date"
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
</template>

<script>
export default {
  name: 'Date',
  components: {
    Icon: () => import('components/Elements/Icon.vue'),
  },
  props: {
    value: {
      type: String,
      default: '',
    },
    changeValue: {
      type: Boolean,
      default: false,
    },
  },
  computed: {
    valueData: {
      get: function get() {
        devlog.log('this.value', this.value);
        return this.value;
      },
      set: function set(val) {
        devlog.log('this.value_ЫУЕ', val);
        this.$emit('update:value', val);
        this.$emit('update:changeValue', true);
      },
    },
  },
};
</script>
