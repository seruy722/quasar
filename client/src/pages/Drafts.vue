<template>
  <div
    data-vue-component-name="Drafts"
    class="q-pa-md"
  >
    <div>
      <div>Кода без информации о клиенте</div>
      <q-btn unelevated rounded color="primary" label="Export" @click="getCodesWithoutInfo" />
    </div>

    <div>
      <div>Загрузка карго на сервер</div>
      <UploadFileToServer :uploadData="uploadData" />
      <Dialog :dialog.sync="dialogUploadCargoData">

        <q-separator />

        <q-card-actions align="right">
          <OutlineBtn
            :btnData="btnData"
            @clickOutlineBtn="dialogUploadCargoData.value = false"
          />
        </q-card-actions>
      </Dialog>
    </div>

    <div>
      <div>Загрузка долгов на сервер</div>
      <Dialog :dialog.sync="dialogUploadDebtsData">
        <template v-slot:body>
          <q-card-section>
            <UploadFileToServer :uploadData="uploadDebtsData" />
          </q-card-section>

          <q-separator />

          <q-card-actions align="right">
            <OutlineBtn
              :btnData="btnData"
              @clickOutlineBtn="dialogUploadDebtsData.value = false"
            />
          </q-card-actions>
        </template>
      </Dialog>
    </div>

    <div>
      <div>Загрузка склада на сервер</div>
      <Dialog :dialog.sync="dialogUploadSkladData">
        <template v-slot:body>
          <q-card-section>
            <UploadFileToServer :uploadData="uploadSkladData" />
          </q-card-section>

          <q-separator />

          <q-card-actions align="right">
            <OutlineBtn
              :btnData="btnData"
              @clickOutlineBtn="dialogUploadSkladData.value = false"
            />
          </q-card-actions>
        </template>
      </Dialog>
    </div>

    <div>
      <div>Добавление клиентов</div>
      <AddCode />
    </div>

    <div>
      Загрузка данных факсов
      <input type="file" multiple @change="upFiles">

      <div>
        <q-input v-model.trim="search" type="text" label="Search" />
        <q-btn label="Test" @click="searchData" />
        <div class="q-pa-md">
          <q-table
            title="Treats"
            :data="fields"
            :columns="columns"
            row-key="name"
          />
        </div>
      </div>
    </div>

    <div>
      Клиенты которые получают бренд
      <q-btn label="GET" @click="getBrandClients" />
    </div>

    <div>
      Загрузка факсов
      <input type="file" multiple @change="upFaxDataFiles">
    </div>

    <div>
      Загрузка кодов
      <input type="file" multiple @change="upCodes">
    </div>

  </div>
</template>

<script>
    import { getUrl } from 'src/tools/url';
    import showNotif from 'src/mixins/showNotif';

    export default {
        name: 'Drafts',
        components: {
            Dialog: () => import('src/components/Dialogs/Dialog.vue'),
            AddCode: () => import('src/components/Dialogs/AddCode.vue'),
            UploadFileToServer: () => import('src/components/Upload/UploadFileToServer.vue'),
            OutlineBtn: () => import('src/components/Buttons/OutlineBtn.vue'),
        },
        mixins: [showNotif],
        data() {
            return {
                columns: [
                    {
                        name: 'code',
                        label: 'Код',
                        align: 'left',
                        field: 'code',
                        sortable: true,
                    },
                    {
                        name: 'client',
                        align: 'center',
                        label: 'Клиент',
                        field: 'client',
                        sortable: true,
                    },
                    {
                        name: 'place',
                        label: 'Мест',
                        field: 'place',
                        sortable: true,
                    },
                    {
                        name: 'kg',
                        label: 'Kg',
                        field: 'kg',
                    },
                    {
                        name: 'category',
                        label: 'Категория',
                        field: 'category',
                    },
                    {
                        name: 'brand',
                        label: 'Бренд',
                        field: 'brand',
                    },
                    {
                        name: 'fax_name',
                        label: 'Факс',
                        field: 'fax_name',
                        sortable: true,
                    },
                ],
                fields: [],
                search: '',
                dialogUploadCargoData: {
                    title: 'Загрузка файлов',
                    value: false,
                    labelBtn: 'Загрузить файл',
                },
                dialogUploadDebtsData: {
                    title: 'Загрузка файлов',
                    value: false,
                    labelBtn: 'Загрузить файл',
                },
                dialogUploadSkladData: {
                    title: 'Загрузка файлов',
                    value: false,
                    labelBtn: 'Загрузить файл',
                },
                btnData: {
                    title: 'close',
                    icon: 'lock',
                    dark: false,
                },
                uploadData: {
                    url: getUrl('uploadCargoTable'),
                    accept: '.xlsx, .xls, .csv',
                },
                uploadDebtsData: {
                    url: getUrl('uploadDebtsTable'),
                    accept: '.xlsx, .xls, .csv',
                },
                uploadSkladData: {
                    url: getUrl('uploadSkladData'),
                    accept: '.xlsx, .xls, .csv',
                },
                uploadAllFaxes: {
                    url: getUrl('uploadSkladData'),
                    accept: '.xlsx, .xls, .csv',
                },
            };
        },
        methods: {
            getCodesWithoutInfo() {
                // this.$axios.get(getUrl('drafts.codesWithoutInfo'));
                this.$axios({
                    url: getUrl('drafts.codesWithoutInfo'),
                    method: 'GET',
                    responseType: 'blob', // important
                    // headers: {
                    //     'Content-Type': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    // },
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
            },
            upFiles({ target }) {
                devlog.log('dfdg', target.files);
                const formData = new FormData();
                _.forEach(target.files, (file, index) => {
                    formData.append(`file${index}`, file);
                });

                this.$axios.post('/api/upload-faxes', formData)
                  .then(({ data }) => {
                      target.value = '';
                      devlog.log('UPLOADED', data);
                      this.showNotif('success', 'Данные успешно добавлены.', 'center');
                  })
                  .catch(() => {
                      this.showNotif('error', 'Произошла ошибка.', 'center');
                  });
            },
            upFaxDataFiles({ target }) {
                const formData = new FormData();
                formData.append('upload', _.first(target.files));
                formData.append('fax_id', 1);

                this.$axios.post('/api/upload-fax-data-table', formData)
                  .then(({ data }) => {
                      target.value = '';
                      devlog.log('UPLOADED', data);
                      this.showNotif('success', 'Данные успешно добавлены.', 'center');
                  })
                  .catch(() => {
                      target.value = '';
                      this.showNotif('error', 'Произошла ошибка.', 'center');
                  });
            },
            upCodes({ target }) {
                const formData = new FormData();
                formData.append('upload', _.first(target.files));

                this.$axios.post('/api/upload-codes', formData)
                  .then(({ data }) => {
                      target.value = '';
                      devlog.log('UPLOADED', data);
                      this.showNotif('success', 'Данные успешно добавлены.', 'center');
                  })
                  .catch(() => {
                      target.value = '';
                      this.showNotif('error', 'Произошла ошибка.', 'center');
                  });
            },
            searchData() {
                this.$axios.post('/api/search-in-faxes', { search: this.search })
                  .then(({ data }) => {
                      devlog.log('SEARCH', data);
                      this.fields = data.searchData;
                  })
                  .catch(() => {

                  });
            },
            getBrandClients() {
                this.$axios({
                    url: getUrl('drafts.brandsCustomers'),
                    method: 'GET',
                    responseType: 'blob', // important
                    // headers: {
                    //     'Content-Type': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    // },
                })
                  .then((response) => {
                      devlog.log('RES_BLOB', response);
                      if (!window.navigator.msSaveOrOpenBlob) {
                          // BLOB NAVIGATOR
                          const url = window.URL.createObjectURL(new Blob([response.data]));
                          const link = document.createElement('a');
                          link.href = url;
                          link.setAttribute('download', 'brands.xlsx');
                          document.body.appendChild(link);
                          link.click();
                      } else {
                          // BLOB FOR EXPLORER 11
                          window.navigator.msSaveOrOpenBlob(new Blob([response.data]), 'brands.xlsx');
                      }
                  });
            },
        },
    };
</script>
