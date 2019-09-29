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
            <Dialog :dialog.sync="dialogUploadCargoData">
                <template v-slot:body>
                        <q-card-section>
                            <UploadFileToServer :uploadData="uploadData" />
                        </q-card-section>

                        <q-separator />

                        <q-card-actions align="right">
                            <OutlineBtn
                                :btnData="btnData"
                                @clickOutlineBtn="dialogUploadCargoData.value = false"
                            />
                        </q-card-actions>
                </template>
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
            <AddCode/>
        </div>
    </div>
</template>

<script>
    import { getUrl } from 'src/tools/url';

    export default {
        name: 'Drafts',
        components: {
            Dialog: () => import('src/components/Dialogs/Dialog.vue'),
            AddCode: () => import('src/components/Dialogs/AddCode.vue'),
            UploadFileToServer: () => import('src/components/Upload/UploadFileToServer.vue'),
            OutlineBtn: () => import('src/components/Buttons/OutlineBtn.vue'),
        },
        data() {
            return {
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
        },
    };
</script>
