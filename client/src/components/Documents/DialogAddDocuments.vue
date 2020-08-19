<template>
  <Dialog
    :dialog.sync="show"
    title="Клиент"
    :persistent="true"
    data-vue-component-name="DialogAddDocuments"
  >
    <q-card style="min-width: 320px;width: 100%;max-width: 500px;">
      <q-card-section>
        <q-list
          v-for="(file, index) in files"
          :key="index"
          separator
          bordered
        >
          <q-item
            v-if="extensions.includes(getFileExt(file))"
            clickable
          >
            <q-item-section>
              <q-avatar
                rounded
                color="primary"
                text-color="white"
              >
                {{ getFileExt(file) }}
              </q-avatar>
            </q-item-section>
            <q-item-section>
              <q-item-label>
                {{ file.name }}
                <q-tooltip>{{ file.name }}</q-tooltip>
              </q-item-label>
            </q-item-section>
            <q-item-section side>
              <q-btn
                class="gt-xs"
                size="12px"
                flat
                dense
                round
                icon="clear"
                @click="remove(index)"
              />
            </q-item-section>
          </q-item>
          <q-item v-else>
            <q-item-section>
              <q-img
                :src="file.url"
                style="max-width: 100%"
              />
            </q-item-section>
            <q-item-section>
              <q-item-label lines="8">
                {{ file.name }}
                <q-tooltip>{{ file.name }}</q-tooltip>
              </q-item-label>
            </q-item-section>
            <q-item-section side>
              <q-btn
                class="gt-xs"
                size="12px"
                flat
                dense
                round
                icon="clear"
                @click="remove(index)"
              />
            </q-item-section>
          </q-item>
        </q-list>
      </q-card-section>
      <q-card-section>
        <q-input
          v-model="text"
          placeholder="Сообщение"
          filled
          autogrow
          clearable
        />
      </q-card-section>
      <q-card-section>
        <SearchSelect
          v-model="codeClientId"
          label="Клиент"
          clearable
          :dense="$q.screen.xs || $q.screen.sm"
          :options="clientCodes"
        />
      </q-card-section>
      <q-card-section>
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
          @clickBaseBtn="close"
        />
        <BaseBtn
          :label="$t('save')"
          color="positive"
          icon="save"
          @clickBaseBtn="save(files)"
        />
      </q-card-actions>
    </q-card>
  </Dialog>
</template>

<script>
    import { getClientCodes } from 'src/utils/FrequentlyCalledFunctions';
    import showNotif from 'src/mixins/showNotif';

    export default {
        name: 'DialogAddDocuments',
        components: {
            Dialog: () => import('src/components/Dialogs/Dialog.vue'),
            BaseBtn: () => import('src/components/Buttons/BaseBtn.vue'),
            SearchSelect: () => import('src/components/Elements/SearchSelect.vue'),
        },
        mixins: [showNotif],
        props: {
            showDialog: {
                type: Boolean,
                default: false,
            },
        },
        data() {
            return {
                files: [],
                extensions: ['xlsx', 'txt', 'doc', 'docx', 'pdf'],
                codeClientId: null,
                text: null,
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
            },
            async save(files) {
                const formData = new FormData();
                _.forEach(files, (file, index) => {
                    formData.set(`img${index}`, file);
                });
                formData.set('title', _.startCase(this.text));
                if (this.codeClientId) {
                    formData.set('code_client_id', this.codeClientId);
                }

                if (this.text || files.length > 0) {
                    this.$q.loading.show();
                    const { getUrl } = await import('src/tools/url');
                    this.$axios.post(getUrl('storeComment'), formData)
                      .then(({ data: { comment } }) => {
                          devlog.log('comment', comment);
                          this.$store.dispatch('documents/addDocument', comment);
                          this.text = '';
                          this.files = [];
                          this.codeClientId = null;
                          this.$q.loading.hide();
                          this.showNotif('success', 'Данные успешно сохранены', 'center');
                      })
                      .catch(() => {
                          this.$q.loading.hide();
                          devlog.error('storeComment');
                      });
                } else {
                    this.showNotif('warning', 'Введите сообщение или выберите файл!', 'center');
                }
            },
        },
    };
</script>
