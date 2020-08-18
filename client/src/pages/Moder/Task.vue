<template>
  <q-page
    data-vue-component-name="Task"
    class="q-pa-md row justify-center"
  >
    <PullRefresh @refresh="refresh">
      <div style="width: 100%; max-width: 1000px">
        <q-chat-message
          v-for="(com, index) in comments"
          :key="index"
          :name="com.author_name"
          :text="[com.title]"
          :stamp="com.created_at"
          :sent="userId !== com.author_id"
          bg-color="lightgrey"
        >
          <template v-slot:avatar>
            <q-avatar
              :color="userId !== com.author_id ? 'primary': 'secondary'"
              text-color="white"
            >
              {{ com.author_name.slice(0,2).toUpperCase() }}
            </q-avatar>
          </template>
          <q-list
            class="bg-white"
            separator
            bordered
          >
            <q-item
              v-for="(file, ind) in com.files"
              :key="ind"
              clickable
              class="cursor-pointer"
              @click="viewImageGallery(com.files, ind + 1)"
            >
              <q-item-section avatar>
                <q-avatar v-if="file.ext === 'xlsx' || file.ext === 'txt'" rounded color="primary" text-color="white">{{
                  file.ext }}
                </q-avatar>
                <div
                  v-else
                  class="example-item"
                >
                  <q-img
                    :src="`${fileUrl()}${file.path}`"
                    style="max-width: 100%;"
                  />
                </div>
              </q-item-section>
              <q-item-section>
                <q-item-label>
                  {{ file.name }}
                </q-item-label>
              </q-item-section>
            </q-item>
          </q-list>
        </q-chat-message>
        <q-separator color="orange" size="3px" />
        <div class="q-pa-md" style="max-width: 500px;">
          <div class="q-gutter-md">
            <q-input
              v-model="text"
              placeholder="Сообщение"
              filled
              autogrow
              clearable
            />

            <q-file
              label="Выберите файлы"
              outlined
              multiple
              clearable
              style="max-width: 400px"
              :value="files"
              @input="selectedFiles"
            >
              <template v-slot:file="{ index, file }">
                <q-chip
                  class="full-width q-my-xs"
                  square
                  removable
                  @remove="cancelFile(index)"
                >
                  <q-avatar
                    v-if="file.ext === 'xlsx' || file.ext === 'txt'"
                    square
                    color="teal"
                    text-color="white"
                    icon="directions"
                  />
                  <q-avatar
                    v-else
                    square
                  >
                    <q-img
                      :src="file.url"
                      style="max-width: 100%"
                    />
                  </q-avatar>
                  <div class="ellipsis relative-position">
                    {{ file.name }}
                  </div>
                  <q-tooltip>
                    {{ file.name }}
                  </q-tooltip>
                </q-chip>
              </template>

              <template v-slot:after>
                <q-btn
                  color="primary"
                  dense
                  icon="send"
                  round
                  :disable="files.length === 0 && !text"
                  @click="saveData(files)"
                />
              </template>
            </q-file>
          </div>
        </div>
      </div>
    </PullRefresh>
    <DialogShowImageGallery
      :show-dialog.sync="showDialogImageGallery"
      :files="filesGallery"
      :slide="slide"
    />
  </q-page>
</template>

<script>
    import showNotif from 'src/mixins/showNotif';

    export default {
        name: 'Task',
        components: {
            PullRefresh: () => import('components/PullRefresh.vue'),
            DialogShowImageGallery: () => import('components/Tasks/DialogShowImageGallery.vue'),
            // IFrameDocs: () => import('components/IFrameDocs.vue'),
        },
        mixins: [showNotif],
        data() {
            return {
                showDialogImageGallery: false,
                filesGallery: [],
                slide: 1,
                text: '',
                files: [],
            };
        },
        computed: {
            userId() {
                return _.get(this.$store.getters['auth/getUser'], 'id');
            },
            comments() {
                return this.$store.getters['tasks/getTaskComments'];
            },
            taskId() {
                return this.$store.getters['tasks/getTaskId'];
            },
        },
        created() {
            devlog.log(this.taskId !== this.$route.params.id);
            if (_.isEmpty(this.comments) || this.taskId !== this.$route.params.id) {
                this.$store.dispatch('tasks/fetchTaskComments', this.$route.params.id);
            }
        },
        methods: {
            async saveData(files) {
                const formData = new FormData();
                _.forEach(files, (file, index) => {
                    formData.set(`img${index}`, file);
                });
                formData.set('title', _.startCase(this.text));
                formData.set('task_id', this.$route.params.id);

                if (this.text || files.length > 0) {
                    const { getUrl } = await import('src/tools/url');
                    this.$axios.post(getUrl('storeComment'), formData)
                      .then(({ data: { comment } }) => {
                          devlog.log('comment', comment);
                          this.$store.dispatch('tasks/addTaskComment', comment);
                          this.text = '';
                          this.files = [];
                      })
                      .catch(() => {
                          devlog.error('storeTaskComment');
                      });
                } else {
                    this.showNotif('warning', 'Введите сообщение или выберите файл!', 'center');
                }
            },
            selectedFiles(value) {
                this.files = _.map(value, (file) => {
                    file.url = URL.createObjectURL(file);
                    return file;
                });
                devlog.log('CVB', value);
                if (_.isNull(value)) {
                    this.files = [];
                }
            },
            cancelFile(index) {
                this.files.splice(index, 1);
            },
            viewImageGallery(files, slide) {
                devlog.log(this.$route, process);
                const file = files[slide - 1];
                if (file.ext === 'txt' || file.ext === 'xlsx') {
                    devlog.log(file.path);
                    const link = document.createElement('a');
                    link.href = `${this.fileUrl()}${file.path}`;
                    link.setAttribute('download', file.name);
                    link.setAttribute('target', '_blank');
                    document.body.appendChild(link);
                    link.click();
                } else {
                    this.slide = slide;
                    this.filesGallery = _.filter(files, ({ ext }) => ext !== 'xlsx' && ext !== 'txt');
                    this.showDialogImageGallery = true;
                }
            },
            async refresh(done) {
                if (!done) {
                    this.$q.loading.show();
                }
                const { callFunction } = await import('src/utils/index');
                await this.$store.dispatch('tasks/fetchTaskComments', this.$route.params.id)
                  .then(() => {
                      callFunction(done);
                      this.$q.loading.hide();
                      this.showNotif('success', 'Данные успешно обновлены.', 'center');
                  })
                  .catch(() => {
                      this.$q.loading.hide();
                      callFunction(done);
                  });
            },
            fileUrl() {
                return process.env.DEV ? 'http://sp.com.ua/storage/' : 'http://servercargo007.net.ua/storage/app/public/';
            },
        },
    };
</script>

<style lang="scss" scoped>
  .example-item {
    width: 150px;
    overflow: hidden;
  }

  .q-avatar {
    margin: 0 5px;
  }

</style>
