<template>
  <PullRefresh @refresh="refresh">
    <q-page
      data-vue-component-name="Task"
    >
      <Table
        :table-properties="cargoTableProperties"
        :table-data="comments"
        :table-reactive-properties="cargoTableReactiveProperties"
        title="Задача"
        grid
      >
        <template #top-buttons>
          <MenuBtn>
            <q-menu
              transition-show="scale"
              transition-hide="scale"
            >
              <q-list
                separator
                style="min-width: 100px"
              >
                <q-item
                  v-close-popup
                  clickable
                  @click="showDialogAddTaskComment = true"
                >
                  <q-item-section avatar>
                    <q-icon
                      name="add"
                      color="positive"
                    />
                  </q-item-section>
                  <q-item-section>Добавить</q-item-section>
                </q-item>
                <q-item
                  v-show="cargoTableReactiveProperties.selected.length === 1"
                  v-close-popup
                  clickable
                  @click="editTaskComment(cargoTableReactiveProperties.selected[0])"
                >
                  <q-item-section avatar>
                    <q-icon
                      name="edit"
                      color="teal"
                    />
                  </q-item-section>
                  <q-item-section>Редактировать</q-item-section>
                </q-item>
                <q-item
                  v-show="cargoTableReactiveProperties.selected.length === 1"
                  v-close-popup
                  clickable
                  @click="addFileToComment(cargoTableReactiveProperties.selected[0])"
                >
                  <q-item-section avatar>
                    <q-icon
                      name="attach_file"
                      color="teal"
                    />
                  </q-item-section>
                  <q-item-section>Добавить файл</q-item-section>
                </q-item>
                <q-item
                  v-close-popup
                  clickable
                  @click="refresh"
                >
                  <q-item-section avatar>
                    <q-icon
                      name="sync"
                      color="primary"
                    />
                  </q-item-section>
                  <q-item-section>Обновить</q-item-section>
                </q-item>
                <q-item
                  v-show="cargoTableReactiveProperties.selected.length"
                  v-close-popup
                  clickable
                  @click="destroyComment(cargoTableReactiveProperties.selected)"
                >
                  <q-item-section avatar>
                    <q-icon
                      name="delete"
                      color="negative"
                    />
                  </q-item-section>
                  <q-item-section>Удалить</q-item-section>
                </q-item>
              </q-list>
            </q-menu>
          </MenuBtn>
        </template>
        <!--ОТОБРАЖЕНИЕ КОНТЕНТА НА МАЛЕНЬКИХ ЭКРАНАХ-->
        <template #inner-item="{props}">
          <div
            class="q-pa-xs col-xs-12 col-sm-6 col-md-4 col-lg-3 grid-style-transition"
            :style="props.selected ? 'transform: scale(0.95);' : ''"
          >
            <q-expansion-item
              expand-separator
              class="shadow-1 overflow-hidden"
              :header-class="`${props.row.type ? 'bg-green' : 'bg-red'} text-white`"
              :style="`border-radius: 30px;border: 1px solid ${props.row.type ? 'lightgreen' : 'lightcoral'};`"
              expand-icon-class="text-white"
            >
              <template #header>
                <q-item-section avatar>
                  <q-checkbox
                    v-model="props.selected"
                    dense
                  />
                </q-item-section>
                <q-item-section>
                  <q-item-label>
                    {{ props.row.formatDate.slice(0, 10) }}
                  </q-item-label>
                </q-item-section>
                <q-item-section>
                  <q-item-label>
                    {{ props.row.author_name }}
                  </q-item-label>
                </q-item-section>
                <q-item-section>
                  <q-item-label>
                    {{ props.row.code_client_name }}
                  </q-item-label>
                </q-item-section>
              </template>
              <q-chat-message
                :text="[props.row.title]"
                :stamp="props.row.formatDate"
                :sent="userId !== props.row.author_id"
                bg-color="lightgrey"
              >
                <q-list
                  class="bg-white"
                  separator
                  bordered
                >
                  <q-item
                    v-for="(file, ind) in props.row.files"
                    :key="ind"
                    clickable
                    class="cursor-pointer"
                    @click="viewImageGallery(props.row.files, ind + 1)"
                  >
                    <q-item-section avatar>
                      <q-avatar
                        v-if="extensions.includes(getFileExt(file))"
                        rounded
                        color="primary"
                        text-color="white"
                      >
                        {{ getFileExt(file) }}
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
                    <q-item-section side>
                      <q-btn
                        size="12px"
                        flat
                        dense
                        round
                        color="negative"
                        icon="clear"
                        @click.stop="remove(file.id, props.row.id, file.path)"
                      />
                    </q-item-section>
                  </q-item>
                </q-list>
                <div
                  v-if="props.row.code_client_name"
                  class="q-mt-sm"
                >
                  Клиент: {{ props.row.code_client_name }}
                </div>
              </q-chat-message>
            </q-expansion-item>
          </div>
        </template>
      </Table>
      <DialogShowImageGallery
        :show-dialog.sync="showDialogImageGallery"
        :files="filesGallery"
        :slide="slide"
      />
      <DialogAddTaskComment
        :show-dialog.sync="showDialogAddTaskComment"
        :add-file-to-comment.sync="addFileToCom"
        :comment-id="commentId"
        :edit-data.sync="entryData"
      />
    </q-page>
  </PullRefresh>
</template>

<script>
import showNotif from 'src/mixins/showNotif';
import filesMixin from 'src/mixins/files';

export default {
  name: 'Task',
  components: {
    PullRefresh: () => import('src/components/PullRefresh.vue'),
    DialogShowImageGallery: () => import('src/components/Tasks/DialogShowImageGallery.vue'),
    MenuBtn: () => import('src/components/Buttons/MenuBtn.vue'),
    Table: () => import('src/components/Elements/Table/Table.vue'),
    DialogAddTaskComment: () => import('src/components/Tasks/DialogAddTaskComment.vue'),
  },
  mixins: [showNotif, filesMixin],
  data() {
    return {
      cargoTableProperties: {
        columns: [
          {
            name: 'author_name',
            label: 'Автор',
            align: 'center',
            field: 'author_name',
            sortable: true,
          },
          {
            name: 'title',
            label: 'Описание',
            align: 'center',
            field: 'title',
            sortable: true,
          },
          {
            name: 'code_client_name',
            label: 'Клиент',
            align: 'center',
            field: 'code_client_name',
            sortable: true,
          },
          {
            name: 'formatDate',
            label: 'Дата',
            field: 'formatDate',
            align: 'center',
            sortable: true,
          },
        ],
      },
      cargoTableReactiveProperties: {
        selected: [],
        visibleColumns: ['author_name', 'title', 'code_client_name', 'formatDate'],
        title: '',
      },
      showDialogImageGallery: false,
      showDialogAddTaskComment: false,
      filesGallery: [],
      slide: 1,
      text: '',
      files: [],
      extensions: ['xlsx', 'txt', 'doc', 'docx', 'pdf'],
      addFileToCom: false,
      commentId: 0,
      entryData: {},
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
    viewImageGallery(files, slide) {
      const file = files[slide - 1];
      if (_.includes(this.extensions, file.ext)) {
        this.downloadFromIndex(slide, files);
      } else {
        this.filesGallery = _.filter(files, ({ ext }) => !_.includes(this.extensions, ext));
        const indexFile = _.findIndex(this.filesGallery, { id: file.id });
        this.slide = indexFile + 1;
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
    async remove(fileId, commentId, filePath) {
      this.showNotif('warning', 'Удалить файл?', 'center', [
        {
          label: 'Отмена',
          color: 'white',
          handler: () => {
          },
        },
        {
          label: 'Удалить',
          color: 'white',
          handler: async () => {
            this.$q.loading.show();
            const { getUrl } = await import('src/tools/url');
            devlog.log(fileId, commentId);
            this.$axios.post(getUrl('removeCommentFile'), {
              fileId,
              commentId,
              filePath,
            })
              .then(({ data: { comment } }) => {
                this.$q.loading.hide();
                this.$store.dispatch('tasks/updateTaskComment', comment);
              })
              .catch(() => {
                this.$q.loading.hide();
              });
          },
        },
      ]);
    },
    async destroyComment(selected) {
      const ids = _.map(selected, 'id');
      devlog.log(ids);
      devlog.log(selected);
      this.$q.loading.show();
      const { getUrl } = await import('src/tools/url');
      this.$axios.post(getUrl('deleteComments'), { ids })
        .then(() => {
          this.$store.dispatch('tasks/deleteTaskComments', ids);
          this.$q.loading.hide();
        })
        .catch(() => {
          this.$q.loading.hide();
          devlog.error('deleteComments');
        });
    },
    addFileToComment(selected) {
      const { id: commentId } = selected;
      this.commentId = commentId;
      devlog.log(commentId);
      this.addFileToCom = true;
      this.showDialogAddTaskComment = true;
    },
    editTaskComment(data) {
      const { id: commentId } = data;
      this.commentId = commentId;
      this.showDialogAddTaskComment = true;
      this.entryData = data;
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
