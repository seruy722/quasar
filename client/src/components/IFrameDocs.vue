<template>
  <iframe
    :src="src"
    :width="width"
    :height="height"
    frameborder="0"
    data-vue-component-name="IFrameDocs"
  >
    This is an embedded <a target="_blank" href='http://office.com'>Microsoft
    Office</a> document, powered by <a target="_blank" href="http://office.com/webapps">Office
    Online</a>.
  </iframe>
</template>

<script>
export default {
  name: 'IFrameDocs',
  props: {
    src: {
      type: String,
      default: '',
    },
    width: {
      type: [String, Number],
      default: '100%',
    },
    height: {
      type: String,
      default: '623px',
    },
  },
};
</script>
