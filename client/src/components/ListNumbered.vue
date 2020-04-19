<template>
  <q-list
    data-vue-component-name="ListNumbered"
    separator
    dense
  >
    <q-item
      v-for="(elem, i) in values"
      :key="i"
    >
      <q-item-section v-if="type==='phones'">
        {{ `${i + 1}. ` }}
        {{ elem | phoneNumberFilter }}
      </q-item-section>
      <q-item-section v-else>
        {{ `${i + 1}. ` }}
        {{ elem }}
      </q-item-section>
    </q-item>
  </q-list>
</template>

<script>
    export default {
        name: 'ListNumbered',
        props: {
            values: {
                type: Array,
                default: () => [],
            },
            type: {
                type: String,
                default: '',
            },
        },
    };
</script>
