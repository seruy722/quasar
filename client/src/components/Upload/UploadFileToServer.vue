<template>
    <div
        data-vue-component-name="UploadFileToServer"
        class="q-pa-xs"
    >
        <q-uploader
            ref="uploader"
            :factory="factoryFn"
            :accept="uploadData.accept"
            style="max-width: 300px; width: auto"
            @added="added"
            @removed="removed"
        >
            <template v-slot:header="scope">
                <div class="row no-wrap items-center q-pa-sm q-gutter-xs">
                    <q-btn v-if="scope.queuedFiles.length > 0" icon="clear_all" @click="scope.removeQueuedFiles" round
                           dense flat>
                        <q-tooltip>{{ $t('clear.two') }}</q-tooltip>
                    </q-btn>
                    <q-btn v-if="scope.uploadedFiles.length > 0" icon="done_all" @click="scope.removeUploadedFiles"
                           round dense flat>
                        <q-tooltip>{{ $t('uploadFile.remove') }}</q-tooltip>
                    </q-btn>
                    <q-spinner v-if="scope.isUploading" class="q-uploader__spinner" />
                    <div class="col">
                        <div class="q-uploader__title">{{ $t('uploadFile.two') }}</div>
                        <div class="q-uploader__subtitle">{{ scope.uploadSizeLabel }} / {{ scope.uploadProgressLabel
                            }}
                        </div>
                    </div>
                    <q-btn v-if="scope.canAddFiles" type="a" icon="add_box" round dense flat>
                        <q-uploader-add-trigger />
                        <q-tooltip>{{ $t('uploadFile.pick') }}</q-tooltip>
                    </q-btn>
                    <q-btn v-if="!uploadData.hideUploadButton && scope.canUpload" icon="cloud_upload"
                           @click="scope.upload" round dense flat>
                        <q-tooltip>{{ $t('uploadFile.two') }}</q-tooltip>
                    </q-btn>

                    <q-btn v-if="scope.isUploading" icon="clear" @click="scope.abort" round dense flat>
                        <q-tooltip>{{ $t('uploadFile.abort') }}</q-tooltip>
                    </q-btn>
                </div>
            </template>

            <template v-slot:list="scope">
                <q-list separator class="bg-separator">

                    <q-item v-for="file in scope.files" :key="file.name">
                        <q-item-section>
                            <q-item-label class="full-width ellipsis text-bold">
                                {{ file.name }}
                            </q-item-label>

                            <q-item-label caption>
                                {{ file.__sizeLabel }} / {{ `${progress} %` }}
                            </q-item-label>
                        </q-item-section>

                        <q-item-section
                            v-if="file.__img"
                            thumbnail
                            class="gt-xs"
                        >
                            <img :src="file.__img.src" alt="Image">
                        </q-item-section>

                        <q-item-section center side>
                            <IconBtn
                                :iconBtnData="iconBtnData"
                                @iconBtnClick="scope.removeFile(file)"
                            />
                        </q-item-section>
                    </q-item>

                </q-list>
            </template>
        </q-uploader>
    </div>
</template>

<script>
    export default {
        name: 'UploadFileToServer',
        components: {
            IconBtn: () => import('src/components/Buttons/IconBtn.vue'),
        },
        props: {
            uploadData: {
                type: Object,
                default: () => ({}),
            },
        },
        data() {
            return {
                progress: 0,
                iconBtnData: {
                    icon: 'close',
                    tooltip: this.$t('clear.one'),
                },
            };
        },
        watch: {
            'uploadData.faxID': function faxId(val) {
                if (val) {
                    this.$refs.uploader.upload();
                    devlog.log('UP', this.$refs.uploader);
                }
            },
        },
        methods: {
            async factoryFn([file]) {
                devlog.log('FF', file);
                const dataFile = new FormData();
                dataFile.append('upload', file);
                dataFile.append('fax_id', this.uploadData.faxID);
                const config = {
                    onUploadProgress: ((progressEvent) => {
                        const { loaded, total } = progressEvent;
                        this.progress = Math.round((loaded * 100) / total);
                    }),
                };

                await this.$axios.post(this.uploadData.url, dataFile, config)
                  .then(({ data }) => {
                      devlog.log(data);
                      // this.$refs.uploader.removeQueuedFiles(file);
                      this.uploadData.faxID = 0;
                      // this.$refs.uploader.canUpload = false;
                      devlog.log('PI', this.$refs.uploader);
                  })
                  .catch((errors) => {
                      devlog.log(errors);
                      this.$q.loading.hide();
                  });
            },
            added() {
                this.uploadData.canUpload = true;
            },
            removed() {
                this.uploadData.canUpload = false;
                devlog.log('REMOVED');
            },
        },
    };
</script>
