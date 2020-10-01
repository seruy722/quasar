<template>
  <q-btn
    flat
    round
    color="positive"
    icon="explicit"
    data-vue-component-name="ExportBtn"
    @click="$emit('export-btn-click')"
  >
    <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 10]">
      Excel
    </q-tooltip>
  </q-btn>
</template>

<script>
    export default {
        name: 'ExportBtn',
    };
</script>
