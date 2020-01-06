<template>
  <div
    data-vue-component-name="Badge"
  >
    <q-badge
      :color="badgeData.color || 'red'"
      floating
      transparent
      data-vue-component-name="Badge"
    >
      {{ badgeData.value }}
    </q-badge>
  </div>
</template>

<script>
    export default {
        name: 'Badge',
        props: {
            badgeData: {
                type: Object,
                default: () => ({}),
            },
        },
    };
</script>
