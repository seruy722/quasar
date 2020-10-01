<template>
  <q-btn
    flat
    round
    color="primary"
    icon="sync"
    data-vue-component-name="UpdateBtn"
    @click="$emit('update-btn-click')"
  >
    <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 10]">
      Обновить
    </q-tooltip>
  </q-btn>
</template>

<script>
    export default {
        name: 'UpdateBtn',
    };
</script>
