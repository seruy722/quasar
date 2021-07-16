<template>
  <q-layout
    view="hHh LpR fFf"
    class="column justify-center align-center"
    data-vue-component-name="StartLayout"
  >
    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script>
export default {
  name: 'AuthLayout',
};
</script>
