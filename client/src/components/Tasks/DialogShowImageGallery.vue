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
          <template #control>
            <q-carousel-control
              position="bottom-right"
              :offset="[3, 3]"
            >
              <q-btn
                push
                round
                dense
                color="white"
                icon="vertical_align_bottom"
                text-color="primary"
                @click="downloadFromIndex(slide, files)"
              />
            </q-carousel-control>
          </template>
        </q-carousel>
      </q-card-section>
    </q-card>
  </q-dialog>
</template>

<script>
import filesMixin from 'src/mixins/files';

export default {
  name: 'DialogShowImageGallery',
  mixins: [filesMixin],
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
  emits: ['update:slide', 'update:showDialog'],
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
};
</script>
