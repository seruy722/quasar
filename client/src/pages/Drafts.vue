<template>
  <div
      data-vue-component-name="DraftsComponent"
      class="q-pa-md"
  >
    <div>
      <div>Кода без информации о клиенте</div>
      <q-btn
          unelevated
          rounded
          color="primary"
          label="Export"
          @click="getCodesWithoutInfo"
      />
    </div>

    <div>
      <div>Загрузка карго на сервер</div>
      <UploadFileToServer :upload-data="uploadData" />
      <DialogComponent v-model:dialog="dialogUploadCargoData">
        <q-separator />

        <q-card-actions align="right">
          <OutlineBtn
              :btn-data="btnData"
              @click-outline-btn="dialogUploadCargoData.value = false"
          />
        </q-card-actions>
      </DialogComponent>
    </div>

    <div>
      <div>Загрузка долгов на сервер</div>
      <DialogComponent v-model:dialog="dialogUploadDebtsData">
        <template #body>
          <q-card-section>
            <UploadFileToServer :upload-data="uploadDebtsData" />
          </q-card-section>

          <q-separator />

          <q-card-actions align="right">
            <OutlineBtn
                :btn-data="btnData"
                @click-outline-btn="dialogUploadDebtsData.value = false"
            />
          </q-card-actions>
        </template>
      </DialogComponent>
    </div>

    <div>
      <div>Загрузка склада на сервер</div>
      <DialogComponent v-model:dialog="dialogUploadSkladData">
        <template #body>
          <q-card-section>
            <UploadFileToServer :upload-data="uploadSkladData" />
          </q-card-section>

          <q-separator />

          <q-card-actions align="right">
            <OutlineBtn
                :btn-data="btnData"
                @click-outline-btn="dialogUploadSkladData.value = false"
            />
          </q-card-actions>
        </template>
      </DialogComponent>
    </div>

    <div>
      <div>Добавление клиентов</div>
      <AddCode />
    </div>

    <div>
      Загрузка данных факсов
      <input
          type="file"
          multiple
          @change="upFiles"
      >

      <div>
        <q-input
            v-model.trim="search"
            type="text"
            label="Search"
        />
        <q-btn
            label="Test"
            @click="searchData"
        />
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

    <div style="border: 1px solid blue;">
      Клиенты которые получают бренд
      <q-btn
          label="GET"
          @click="getBrandClients"
      />
    </div>
    <div style="border: 1px solid blue;">
      Клиенты которые не получали брендовый товар больше месяца
      <q-btn
          label="GET"
          @click="exportCustomersWhoLeftBrand"
      />
    </div>
    <div style="border: 1px solid blue;">
      Клиенты которые не получали товар больше месяца
      <q-btn
          label="GET"
          @click="exportCustomersWhoLeft"
      />
    </div>

    <div style="border: 1px solid blue;">
      Клиенты в нотации которых написано оплачено
      <q-btn
          label="GET"
          @click="getEntriesWithPayNotation"
      />
    </div>

    <div style="border: 1px solid blue;">
      Оборот товара Одесса
      <div class="q-pa-md">
        <q-date
            v-model="date"
            default-view="Months"
            mask="MM-YYYY"
        />
      </div>
      <q-btn
          label="GET"
          @click="exportReportOdessaData(date)"
      />
    </div>

    <div>
      Загрузка факсов
      <input
          type="file"
          multiple
          @change="upFaxDataFiles"
      >
    </div>

    <div>
      Загрузка кодов
      <input
          type="file"
          multiple
          @change="upCodes"
      >
    </div>

    <div>
      <q-btn
          label="clearCache"
          @click="reg"
      />
    </div>
  </div>
</template>

<script>
import { getUrl } from 'src/tools/url';
import showNotif from 'src/mixins/showNotif';
import DialogComponent from 'src/components/Dialogs/DialogComponent.vue';
import AddCode from 'src/components/Dialogs/AddCode.vue';
import UploadFileToServer from 'src/components/Upload/UploadFileToServer.vue';
import OutlineBtn from 'src/components/Buttons/OutlineBtn.vue';

export default {
  name: 'DraftsComponent',
  components: {
    DialogComponent,
    AddCode,
    UploadFileToServer,
    OutlineBtn,
  },
  mixins: [showNotif],
  data() {
    return {
      date: null,
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
        method: 'get',
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
    getEntriesWithPayNotation() {
      this.$axios({
        url: '/api/get-entries-with-pay-notation',
        method: 'get',
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
              link.setAttribute('download', 'олаченные места.xlsx');
              document.body.appendChild(link);
              link.click();
            } else {
              // BLOB FOR EXPLORER 11
              window.navigator.msSaveOrOpenBlob(new Blob([response.data]), 'олаченные места.xlsx');
            }
          });
      // this.$axios.get('/api/get-entries-with-pay-notation')
      //   .then(() => {
      //   })
      //   .catch(() => {
      //
      //   });
    },
    getBrandClients() {
      this.$axios({
        url: getUrl('drafts.brandsCustomers'),
        method: 'get',
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
    exportCustomersWhoLeft() {
      this.$axios({
        url: getUrl('drafts.exportCustomersWhoLeft'),
        method: 'get',
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
    exportCustomersWhoLeftBrand() {
      this.$axios({
        url: getUrl('drafts.exportCustomersWhoLeftBrand'),
        method: 'get',
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
    exportReportOdessaData(date) {
      if (date) {
        this.$axios({
          url: getUrl('drafts.exportReportOdessaData'),
          method: 'POST',
          responseType: 'blob', // important
          data: {
            date,
          },
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
      } else {
        this.showNotif('warning', 'Выберите дату!', 'center');
      }
    },
    // reg() {
    //   /* eslint-disable */
    //   this.$axios.post(getUrl('subscribe'), {
    //     "contact_id": "61250d628a1c9c39227079dd‌‌‌‌‌‌‌‌‌‌‌‌‌‌‌‌",
    //     "name": "Serg‌‌‌‌‌‌‌‌‌‌‌‌‌‌‌‌‌",
    //     "full_name": "Serg‌‌‌‌‌‌‌‌‌‌‌‌‌‌‌‌‌‌",
    //     "last_name": null,
    //     "bot_id": "61250d62b89d3a49981d48eb‌‌‌‌‌‌‌‌‌‌‌‌‌‌‌‌‌‌‌‌",
    //     "phone": null,
    //     "bot_name": "cargo007‌‌‌‌‌‌‌‌‌‌‌‌‌‌‌‌‌‌‌‌‌‌",
    //     "channel": "telegram‌‌‌‌‌‌‌‌‌‌‌‌‌‌‌‌‌‌‌‌‌‌‌",
    //     "subscribe": false
    //   });
    // },
    clearCache() {
      this.$axios.get(getUrl('drafts.clearCache'));
    },
    reg() {
      // this.$axios.post(getUrl('register'), {
      //   name: 'dasha',
      //   email: 'dasha@ya.ru',
      //   password: 'pbJ767S3Ym',
      //   password_confirmation: 'pbJ767S3Ym',
      // });
    },
  },
};
</script>
