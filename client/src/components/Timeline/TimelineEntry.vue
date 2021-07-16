<template>
  <q-timeline-entry
    :heading="heading"
    :title="title"
    :subtitle="subtitle"
    :icon="icon"
    :tag="tag"
    data-vue-component-name="TimelineEntry"
  >
    <slot />
  </q-timeline-entry>
</template>

<script>
export default {
  name: 'TimelineEntry',
  props: {
    heading: {
      type: Boolean,
      default: false,
    },
    title: {
      type: String,
      default: '',
    },
    subtitle: {
      type: String,
      default: '',
    },
    icon: {
      type: String,
      default: 'lens',
    },
    tag: {
      type: String,
      default: 'h3',
    },
  },
};
</script>
