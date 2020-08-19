<template>
  <q-dialog
    v-model="show"
    data-vue-component-name="DialogShowImageGallery"
  >
    <q-card style="width: 100%;">
      <q-card-section>
        <q-carousel
          v-model="slider"
          swipeable
          animated
          infinite
        >
          <q-carousel-slide
            v-for="(file, index) in files"
            :key="index"
            :name="index + 1"
            :img-src="`${fileUrl()}${file.path}`"
            style="background-size: contain;background-repeat: no-repeat;"
          />
        </q-carousel>
      </q-card-section>
    </q-card>
  </q-dialog>
</template>

<script>
    export default {
        name: 'DialogShowImageGallery',
        props: {
            showDialog: {
                type: Boolean,
                default: false,
            },
            slide: {
                type: Number,
                default: 1,
            },
            files: {
                type: Array,
                default: () => ([]),
            },
        },
        computed: {
            show: {
                get: function get() {
                    return this.showDialog;
                },
                set: function set(val) {
                    this.$emit('update:showDialog', val);
                },
            },
            slider: {
                get: function get() {
                    return this.slide;
                },
                set: function set(val) {
                    this.$emit('update:slide', val);
                },
            },
        },
        methods: {
            fileUrl() {
                return process.env.DEV ? 'http://sp.com.ua/storage/' : 'http://servercargo007.net.ua/storage/app/public/';
            },
        },
    };
</script>
