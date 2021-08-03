<template>
  <q-popup-edit
    v-model="modelData"
    data-vue-component-name="PopupEdit"
    :title="title"
    buttons
    :label-set="labelSet"
    :label-cancel="labelCancel"
    dense
    @show="$emit('show')"
    @hide="$emit('update:edit', false)"
    @update:model-value="$emit('add-to-save')"
  >
    <template #default="scope">
      <slot
        name="inner-default"
        :scope="scope"
      >
        <q-input
          v-if="type === 'number'"
          v-model.number="scope.value"
          :type="type"
          :mask="mask"
          dense
          autofocus
          @keyup.enter="scope.set"
        />
        <q-input
          v-else
          v-model.trim="scope.value"
          :type="type"
          :mask="mask"
          dense
          autofocus
          @keyup.enter="scope.set"
        />
      </slot>
    </template>
  </q-popup-edit>
</template>

<script>
export default {
  name: 'PopupEdit',
  props: {
    value: {
      type: [String, Number],
      default: '',
    },
    title: {
      type: String,
      default: 'edit',
    },
    rowProps: {
      type: Object,
      default: () => ({}),
    },
    labelSet: {
      type: String,
      default: 'Сохранить',
    },
    labelCancel: {
      type: String,
      default: 'Отмена',
    },
    type: {
      type: String,
      default: 'text',
    },
    mask: {
      type: String,
      default: '',
    },
    edit: {
      type: Boolean,
      default: false,
    },
  },
  emits: ['update:value', 'update:edit', 'add-to-save', 'show'],
  computed: {
    modelData: {
      get: function getData() {
        return this.value;
      },
      set: function setData(newValue) {
        this.$emit('update:value', newValue);
      },
    },
  },
};
</script>
