<template>
  <q-input
      v-model="date"
      readonly
  >
    <template #append>
      <q-icon
          name="event"
          class="cursor-pointer"
      >
        <q-popup-proxy
            ref="qDateProxy"
            cover
            transition-show="scale"
            transition-hide="scale"
        >
          <q-date
              v-model="date"
              mask="YYYY-MM-DD"
              @update:model-value="$refs.qDateProxy.hide()"
          >
            <div class="row items-center justify-end">
              <q-btn
                  v-close-popup
                  label="Закрыть"
                  color="primary"
                  flat
              />
            </div>
          </q-date>
        </q-popup-proxy>
      </q-icon>
    </template>
  </q-input>
</template>

<script>
import { computed } from 'vue';

export default {
  name: 'DateComponent2',
  props: {
    dateValue: {
      type: String,
      default: null,
    },
  },
  emits: ['update:dateValue'],
  setup(props, { emit }) {
    const dateNow = new Date().toISOString()
        .slice(0, 10);
    if (!props.dateValue) {
      emit('update:dateValue', dateNow);
    }

    const date = computed({
      get: () => props.dateValue,
      set: (val) => {
        emit('update:dateValue', val);
      },
    });
    return {
      date,
    };
  },
};
</script>
