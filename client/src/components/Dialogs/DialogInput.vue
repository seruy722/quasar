<template>
  <DialogComponent
    :dialog="show"
    title="Удаление доступов"
    :persistent="true"
    data-vue-component-name="DialogInput"
  >
    <q-card style="min-width: 320px;width: 100%;max-width: 500px;">
      <q-card-section class="bg-grey q-mb-sm">
        <q-input
          v-model="key"
          label="Введите ключ"
          autofocus
          :rules="[val => !!val || 'Поле обьязательное']"
        />
      </q-card-section>

      <Separator />

      <q-card-actions align="right">
        <BaseBtn
          label="Отмена"
          color="negative"
          :dense="$q.screen.xs || $q.screen.sm"
          @click-base-btn="show = false"
        />

        <BaseBtn
          label="OK"
          color="positive"
          :dense="$q.screen.xs || $q.screen.sm"
          @click-base-btn="save(key)"
        />
      </q-card-actions>
    </q-card>
  </DialogComponent>
</template>

<script>
import { defineAsyncComponent } from 'vue';

export default {
  name: 'DialogInput',
  components: {
    DialogComponent: defineAsyncComponent(() => import('src/components/Dialogs/DialogComponent.vue')),
    BaseBtn: defineAsyncComponent(() => import('src/components/Buttons/BaseBtn.vue')),
    Separator: defineAsyncComponent(() => import('src/components/Separator.vue')),
  },
  props: {
    showDialog: {
      type: Boolean,
      default: false,
    },
    keyData: {
      type: String,
      default: null,
    },
  },
  emits: ['update:keyData', 'update:showDialog'],
  data() {
    return {
      key: null,
    };
  },
  computed: {
    show: {
      get: function getShow() {
        return this.showDialog;
      },
      set: function setSHow(val) {
        this.$emit('update:showDialog', val);
      },
    },
  },
  methods: {
    close() {
      this.$emit('update:keyData', null);
      this.key = null;
      this.show = false;
    },
    save(val) {
      this.$emit('update:keyData', val);
      this.key = null;
      this.show = false;
    },
  },
};
</script>
