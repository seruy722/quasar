<template>
  <div data-vue-component-name="UploadExcelFileForCodesPlaces">
    <IconBtn
      color="accent"
      :icon="icon"
      :tooltip="tooltip"
      @icon-btn-click="$refs.input.click()"
    />

    <input
      v-show="false"
      ref="input"
      type="file"
      @change="upFilesCodesPlaces"
    >
  </div>
</template>

<script>
import IconBtn from 'src/components/Buttons/IconBtn.vue';
import showNotif from 'src/mixins/showNotif';

export default {
  name: 'UploadExcelFileForCodesPlaces',
  components: {
    IconBtn,
  },
  mixins: [showNotif],
  props: {
    url: {
      type: String,
      default: '/api/upload-codes-places',
    },
    icon: {
      type: String,
      default: 'file_download',
    },
    tooltip: {
      type: String,
      default: 'Выгрузить данные по кодам',
    },
  },
  methods: {
    exportCodesPlaces(ids) {
      if (!_.isEmpty(ids)) {
        this.$axios({
          url: '/api/export-codes-places-with-post',
          method: 'POST',
          responseType: 'blob', // important
          data: {
            ids,
          },
        })
          .then((response) => {
            devlog.log('RES_BLOB', response);
            if (!window.navigator.msSaveOrOpenBlob) {
              // BLOB NAVIGATOR
              const url = window.URL.createObjectURL(new Blob([response.data]));
              const link = document.createElement('a');
              link.href = url;
              link.setAttribute('download', 'codes.xlsx');
              document.body.appendChild(link);
              link.click();
            } else {
              // BLOB FOR EXPLORER 11
              window.navigator.msSaveOrOpenBlob(new Blob([response.data]), 'codes.xlsx');
            }
          });
      }
    },
    upFilesCodesPlaces({ target }) {
      devlog.log('dfdg', target.files);
      const formData = new FormData();
      _.forEach(target.files, (file, index) => {
        formData.append(`file${index}`, file);
      });

      this.$axios.post(this.url, formData)
        .then(({ data: { files } }) => {
          target.value = '';
          devlog.log('UPLOADED', files);
          this.exportCodesPlaces(files);
          this.showNotif('success', 'Данные успешно загружены.', 'center');
        })
        .catch(() => {
          this.showNotif('error', 'Произошла ошибка.', 'center');
        });
    },
  },
};
</script>
