<template>
  <q-btn
    flat
    round
    color="info"
    icon="move_to_inbox"
    @click="$emit('moveToFaxClick')"
  >
    <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 10]">
      Переместить
    </q-tooltip>
  </q-btn>
</template>

<script>
    export default {
        name: 'MoveToFaxBtn',
    };
</script>
