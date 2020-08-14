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
          <div class="q-pa-md">
            <div class="row justify-center q-gutter-sm">
              <q-intersection
                v-for="(file, ind) in com.files"
                :key="ind"
                once
                transition="scale"
                class="example-item cursor-pointer"
              >
                <q-card @click="viewImageGallery(com.files, ind + 1)" class="q-ma-sm">
                  <img :src="`${fileUrl()}${file.path}`">
                </q-card>
              </q-intersection>
            </div>
          </div>
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
                  <q-avatar>
                    <img :src="file.url" style="max-width: 100%">
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
    <q-dialog v-model="showImageGallery">
      <q-card style="width: 100%;">
        <q-card-section>
          <q-carousel
            v-model="slide"
            swipeable
            animated
            :arrows="filesGallery.length > 1"
            infinite
          >
            <q-carousel-slide
              v-for="(file, index) in filesGallery"
              :key="index"
              :name="index + 1"
              :img-src="`${fileUrl()}${file.path}`"
              style="background-size: contain;background-repeat: no-repeat;"
            />
          </q-carousel>
        </q-card-section>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
    import showNotif from 'src/mixins/showNotif';

    export default {
        name: 'Task',
        components: {
            PullRefresh: () => import('src/components/PullRefresh.vue'),
        },
        mixins: [showNotif],
        data() {
            return {
                showImageGallery: false,
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
                    this.$axios.post(getUrl('storeTaskComment'), formData)
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
                this.slide = slide;
                this.filesGallery = files;
                this.showImageGallery = true;
            },
            fileUrl() {
                return process.env.DEV ? 'http://sp.com.ua/storage/' : 'http://servercargo007.net.ua/storage/app/public/';
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
        },
    };
</script>

<style lang="sass" scoped>
  .example-item
    height: 150px
    width: 150px
    overflow: hidden
  .q-avatar
    margin: 0 5px
</style>
