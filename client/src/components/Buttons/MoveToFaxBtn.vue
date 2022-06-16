<template>
  <q-btn
      flat
      round
      color="info"
      icon="move_to_inbox"
      @click="$emit('move-to-fax-click')"
  >
    <q-tooltip
        anchor="bottom middle"
        self="top middle"
        :offset="[10, 10]"
    >
      Переместить
    </q-tooltip>
  </q-btn>
</template>

<script>
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'MoveToFaxBtn',
  emits: ['move-to-fax-click'],
});
</script>
