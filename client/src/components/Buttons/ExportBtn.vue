<template>
  <q-btn
      round
      color="positive"
      icon="explicit"
      :loading="loading"
      data-vue-component-name="ExportBtn"
      @click="exportToExcel"
  >
    <q-tooltip
        anchor="bottom middle"
        self="top middle"
        :offset="[10, 10]"
    >
      Выгрузка в excel
    </q-tooltip>
  </q-btn>
</template>

<script>
import { getUrl } from 'src/tools/url';
import exportRequest from 'src/utils/ExportRequest';
import { ref } from 'vue';

export default {
  name: 'ExportBtn',
  props: {
    url: {
      type: String,
      require: true,
    },
    title: {
      type: String,
      default: 'export',
    },
    ids: {
      type: Array,
      default: () => ([]),
    },
  },
  setup(props) {
    const loading = ref(false);
    return {
      loading,
      exportToExcel() {
        loading.value = true;
        exportRequest(getUrl(props.url), {
          ids: props.ids,
        }, `${props.title}.xlsx`)
            .then(() => {
              loading.value = false;
            })
            .catch(() => {
              loading.value = false;
            });
      },
    };
  },
};
</script>
