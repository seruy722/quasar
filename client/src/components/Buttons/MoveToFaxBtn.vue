<template>
  <q-btn
    flat
    round
    color="info"
    icon="move_to_inbox"
    tooltip="Переместить"
    @click="$emit('moveToFaxClick')"
  />
</template>

<script>
    export default {
        name: 'MoveToFaxBtn',
    };
</script>
