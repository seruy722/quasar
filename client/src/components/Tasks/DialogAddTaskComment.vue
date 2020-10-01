<template>
  <Dialog
    :dialog.sync="show"
    title="Клиент"
    :persistent="true"
    data-vue-component-name="DialogAddTaskComment"
  >
    <q-card style="min-width: 320px;width: 100%;max-width: 500px;">
      <q-card-section>
        <q-list
          separator
          bordered
        >
          <q-item
            v-for="(file, index) in files"
            :key="index"
            clickable
          >
            <q-item-section>
              <q-avatar
                v-if="extensions.includes(getFileExt(file))"
                rounded
                color="primary"
                text-color="white"
              >
                {{ getFileExt(file) }}
              </q-avatar>
              <q-img
                v-else
                :src="file.url"
                style="max-width: 100%"
              />
            </q-item-section>
            <q-item-section v-if="extensions.includes(getFileExt(file))">
              <q-item-label>
                {{ file.name }}
              </q-item-label>
            </q-item-section>
            <q-item-section side>
              <q-btn
                size="12px"
                flat
                dense
                round
                color="negative"
                icon="clear"
                @click="remove(index)"
              />
            </q-item-section>
          </q-item>
        </q-list>
      </q-card-section>
      <q-card-section v-show="!addFileToComment">
        <q-input
          v-model="text"
          placeholder="Сообщение"
          filled
          autogrow
          clearable
        />
      </q-card-section>
      <q-card-section v-show="!addFileToComment">
        <SearchSelect
          v-model="codeClientId"
          label="Клиент"
          clearable
          :dense="$q.screen.xs || $q.screen.sm"
          :options="clientCodes"
        />
      </q-card-section>
      <q-card-section v-show="!addFileToComment">
        <DateWithInputForCargo
          :value.sync="localDate"
        />
      </q-card-section>
      <q-card-section v-show="!isEdit">
        <q-file
          ref="input"
          label="Выберите файлы"
          outlined
          multiple
          clearable
          max-file-size="15728640"
          :value="files"
          @input="selectedFiles"
        />
      </q-card-section>
      <q-card-actions align="right">
        <BaseBtn
          :label="$t('close')"
          color="negative"
          icon="cancel"
          @click-base-btn="close"
        />
        <BaseBtn
          :label="$t('save')"
          color="positive"
          icon="save"
          @click-base-btn="save(files)"
        />
      </q-card-actions>
    </q-card>
  </Dialog>
</template>

<script>
    import { getClientCodes } from 'src/utils/FrequentlyCalledFunctions';
    import showNotif from 'src/mixins/showNotif';

    export default {
        name: 'DialogAddTaskComment',
        components: {
            Dialog: () => import('src/components/Dialogs/Dialog.vue'),
            BaseBtn: () => import('src/components/Buttons/BaseBtn.vue'),
            SearchSelect: () => import('src/components/Elements/SearchSelect.vue'),
            DateWithInputForCargo: () => import('src/components/DateWithInputForCargo.vue'),
        },
        mixins: [showNotif],
        props: {
            showDialog: {
                type: Boolean,
                default: false,
            },
            addFileToComment: {
                type: Boolean,
                default: false,
            },
            commentId: {
                type: Number,
                default: 0,
            },
            editData: {
                type: Object,
                default: () => ({}),
            },
        },
        data() {
            return {
                showDialogChooseDate: false,
                dialogChooseDateData: null,
                files: [],
                extensions: ['xlsx', 'txt', 'doc', 'docx', 'pdf'],
                codeClientId: null,
                text: null,
                localDate: null,
            };
        },
        computed: {
            show: {
                get: function get() {
                    return this.showDialog;
                },
                set: function set(val) {
                    this.$emit('update:showDialog', val);
                },
            },
            clientCodes() {
                return this.$store.getters['codes/getCodes'];
            },
            isEdit() {
                return !_.isEmpty(this.editData);
            },
        },
        watch: {
            editData(val) {
                this.text = val.title;
                this.localDate = val.created_at;
                this.codeClientId = val.code_client_id;
            },
        },
        created() {
            getClientCodes(this.$store);
        },
        methods: {
            selectedFiles(value) {
                this.files = _.map(value, (file) => {
                    file.url = URL.createObjectURL(file);
                    return file;
                });
                if (_.isNull(value)) {
                    this.files = [];
                }
            },
            getFileExt({ name }) {
                if (name) {
                    return name.slice(name.lastIndexOf('.') + 1);
                }
                return name;
            },
            remove(index) {
                this.files.splice(index, 1);
            },
            close() {
                this.files = [];
                this.show = false;
                if (this.addFileToComment) {
                    this.$emit('update:addFileToComment', false);
                }
            },
            async save(files) {
                const formData = new FormData();
                _.forEach(files, (file, index) => {
                    formData.set(`img${index}`, file);
                });
                if (this.codeClientId) {
                    formData.set('code_client_id', this.codeClientId);
                }
                if (this.text) {
                    formData.set('title', _.startCase(this.text));
                }
                if (this.localDate) {
                    const { addTime } = await import('src/utils/formatDate');
                    formData.set('created_at', addTime(this.localDate)
                      .toISOString());
                }
                formData.set('task_id', this.$route.params.id);

                if (this.text || files.length > 0) {
                    this.$q.loading.show();
                    const { getUrl } = await import('src/tools/url');
                    if (this.addFileToComment) {
                        formData.set('comment_id', this.commentId);
                        this.$axios.post(getUrl('addFileToComment'), formData)
                          .then(({ data: { comment } }) => {
                              devlog.log('comment', comment);
                              this.$store.dispatch('tasks/updateTaskComment', comment);
                              this.files = [];
                              this.$emit('update:addFileToComment', false);
                              this.show = false;
                              this.$q.loading.hide();
                              this.showNotif('success', 'Файл успешно добавлен', 'center');
                          })
                          .catch(() => {
                              this.$q.loading.hide();
                              this.showNotif('warning', 'Файлы из google диска не загружаются!', 'center');
                              devlog.error('storeComment');
                          });
                    } else if (this.isEdit) {
                        formData.set('comment_id', this.commentId);
                        this.$axios.post(getUrl('updateCommentData'), formData)
                          .then(({ data: { comment } }) => {
                              devlog.log('comment', comment);
                              this.$store.dispatch('tasks/updateTaskComment', comment);
                              this.files = [];
                              this.$emit('update:addFileToComment', false);
                              this.show = false;
                              this.$q.loading.hide();
                              this.showNotif('success', 'Файл успешно добавлен', 'center');
                          })
                          .catch(() => {
                              this.$q.loading.hide();
                              this.showNotif('warning', 'Файлы из google диска не загружаются!', 'center');
                              devlog.error('storeComment');
                          });
                    } else {
                        this.$axios.post(getUrl('storeComment'), formData)
                          .then(({ data: { comment } }) => {
                              devlog.log('comment', comment);
                              this.$store.dispatch('tasks/addTaskComment', comment);
                              this.text = '';
                              this.files = [];
                              this.localDate = null;
                              this.codeClientId = null;
                              this.$q.loading.hide();
                              this.showNotif('success', 'Данные успешно сохранены', 'center');
                          })
                          .catch(() => {
                              this.$q.loading.hide();
                              this.showNotif('warning', 'Файлы из google диска не загружаются!', 'center');
                              devlog.error('storeComment');
                          });
                    }
                } else {
                    this.showNotif('warning', 'Введите сообщение или выберите файл!', 'center');
                }
            },
        },
    };
</script>
