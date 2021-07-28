<template>
  <div
    data-vue-component-name="DateWithInput"
  >
    <q-input
      v-model="valueData"
      filled
      dense
    >
      <template #append>
        <q-icon
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
              mask="YYYY-MM-DD"
              @input="() => $refs.qDateProxy.hide()"
            />
          </q-popup-proxy>
        </q-icon>
      </template>
    </q-input>
  </div>
</template>

<script>
export default {
  name: 'DateWithInput',
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
  emits: ['update:changeValue', 'update:value'],
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
