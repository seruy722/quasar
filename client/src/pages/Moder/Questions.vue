<template>
  <PullRefresh @refresh="refresh">
    <q-page
      data-vue-component-name="Questions"
    >
      <Table
        :table-properties="cargoTableProperties"
        :table-data="questions"
        :table-reactive-properties="cargoTableReactiveProperties"
        :title="$route.meta.title"
        grid
      >
        <template #top-buttons>
          <MenuBtn :list="menuList">
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
                  @click="showDialogAddQuestion = true"
                >
                  <q-item-section avatar>
                    <q-icon name="add" color="positive" />
                  </q-item-section>
                  <q-item-section>Добавить вопрос</q-item-section>
                </q-item>

                <q-item
                  v-show="cargoTableReactiveProperties.selected.length === 1"
                  v-close-popup
                  clickable
                  @click="addQuestionComment(cargoTableReactiveProperties.selected[0])"
                >
                  <q-item-section avatar>
                    <q-icon name="add" color="positive" />
                  </q-item-section>
                  <q-item-section>Добавить комментарий</q-item-section>
                </q-item>

                <q-item
                  v-show="cargoTableReactiveProperties.selected.length === 1"
                  v-close-popup
                  clickable
                  @click="update(cargoTableReactiveProperties.selected[0])"
                >
                  <q-item-section avatar>
                    <q-icon name="edit" color="teal" />
                  </q-item-section>
                  <q-item-section>Редактировать</q-item-section>
                </q-item>
                <q-item
                  v-close-popup
                  clickable
                  @click="refresh"
                >
                  <q-item-section avatar>
                    <q-icon name="sync" color="primary" />
                  </q-item-section>
                  <q-item-section>Обновить</q-item-section>
                </q-item>
                <q-item
                  v-show="cargoTableReactiveProperties.selected.length"
                  v-close-popup
                  clickable
                  @click="destroy(cargoTableReactiveProperties.selected)"
                >
                  <q-item-section avatar>
                    <q-icon name="delete" color="negative" />
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
              :header-class="`${props.row.status_id === 0 ? 'bg-red' : props.row.status_id === 1 ? 'bg-grey' : 'bg-green'} text-white`"
              :style="`border-radius: 30px;border: 1px solid ${props.row.status_id === 0 ? 'red' : props.row.status_id === 1 ? 'grey' : 'lightgreen'};`"
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
                    {{ props.row.responsible_name }}
                  </q-item-label>
                </q-item-section>
                <q-item-section>
                  <q-item-label>
                    {{ props.row.code_client_name }}
                  </q-item-label>
                </q-item-section>
              </template>
              <q-list separator bordered dense>
                <q-item>
                  <q-item-section>
                    <q-item-label>
                      Дата
                    </q-item-label>
                  </q-item-section>
                  <q-item-section side>
                    <q-item-label>
                      {{ props.row.formatDate }}
                    </q-item-label>
                  </q-item-section>
                </q-item>
                <q-item>
                  <q-item-section>
                    <q-item-label>
                      Описание
                    </q-item-label>
                  </q-item-section>
                  <q-item-section side>
                    <q-item-label lines="3">
                      {{ props.row.title }}
                    </q-item-label>
                  </q-item-section>
                </q-item>
                <q-item>
                  <q-item-section>
                    <q-item-label>
                      Ответсвенный
                    </q-item-label>
                  </q-item-section>
                  <q-item-section side>
                    <q-item-label>
                      {{ props.row.responsible_name }}
                    </q-item-label>
                  </q-item-section>
                </q-item>
                <q-item>
                  <q-item-section>
                    <q-item-label>
                      Клиент
                    </q-item-label>
                  </q-item-section>
                  <q-item-section side>
                    <q-item-label>
                      {{ props.row.code_client_name }}
                    </q-item-label>
                  </q-item-section>
                </q-item>
                <q-item>
                  <q-item-section>
                    <q-item-label>
                      Статус
                    </q-item-label>
                  </q-item-section>
                  <q-item-section side>
                    <q-item-label>
                      {{ props.row.status_id === 0 ? 'Новый' : props.row.status_id === 1 ? 'Отработано' : 'Выполнено' }}
                    </q-item-label>
                  </q-item-section>
                </q-item>
              </q-list>
              <q-chat-message
                v-for="(comment, i) in props.row.comments"
                :key="i"
                :text="[comment.title]"
                :stamp="comment.created_at | formatToDashDate"
                :sent="userId !== comment.author_id"
                bg-color="lightgrey"
              >
                <template #avatar>
                  <q-avatar
                    color="primary"
                    text-color="white"
                  >
                    {{ getAvatarText(comment.author_name) }}
                  </q-avatar>
                </template>
                <q-list
                  class="bg-white"
                  separator
                  bordered
                >
                  <q-item
                    v-for="(file, ind) in comment.files"
                    :key="ind"
                    clickable
                    class="cursor-pointer"
                    @click="viewImageGallery(comment.files, ind + 1)"
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
                  </q-item>
                </q-list>
              </q-chat-message>
            </q-expansion-item>
          </div>
        </template>
      </Table>
      <DialogQuestionsComment
        :show-dialog.sync="showDialogAddComment"
        :question-id.sync="questionId"
      />
      <DialogAddQuestion
        :show-dialog.sync="showDialogAddQuestion"
        :entry-data.sync="entryData"
      />
      <DialogShowImageGallery
        :show-dialog.sync="showDialogImageGallery"
        :files="filesGallery"
        :slide.sync="slide"
      />
    </q-page>
  </PullRefresh>
</template>

<script>
    import showNotif from 'src/mixins/showNotif';
    import filesMixin from 'src/mixins/files';
    import CheckErrorsMixin from 'src/mixins/CheckErrors';

    export default {
        name: 'Questions',
        components: {
            Table: () => import('components/Elements/Table/Table.vue'),
            MenuBtn: () => import('components/Buttons/MenuBtn.vue'),
            PullRefresh: () => import('components/PullRefresh.vue'),
            DialogQuestionsComment: () => import('components/Questions/DialogQuestionsComment.vue'),
            DialogAddQuestion: () => import('components/Questions/DialogAddQuestion.vue'),
            DialogShowImageGallery: () => import('components/Tasks/DialogShowImageGallery.vue'),
        },
        mixins: [showNotif, CheckErrorsMixin, filesMixin],
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
                menuList: [],
                showDialogAddTask: false,
                entryData: {},
                files: [],
                showDialogAddComment: false,
                showDialogAddQuestion: false,
                showDialogImageGallery: false,
                filesGallery: [],
                slide: 1,
                extensions: ['xlsx', 'txt', 'doc', 'docx', 'pdf'],
                questionId: 0,
            };
        },
        computed: {
            usersList() {
                return this.$store.getters['auth/getUsersList'];
            },
            questions() {
                return this.$store.getters['questions/getQuestions'];
            },
            userId() {
                return _.get(this.$store.getters['auth/getUser'], 'id');
            },
        },
        created() {
            if (_.isEmpty(this.usersList)) {
                this.$store.dispatch('auth/fetchUsersList');
            }
            if (_.isEmpty(this.questions)) {
                this.$store.dispatch('questions/fetchQuestions');
            }
        },
        methods: {
            async refresh(done) {
                if (!done) {
                    this.$q.loading.show();
                }
                const { callFunction } = await import('src/utils/index');
                await this.$store.dispatch('questions/fetchQuestions')
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
            destroy(data) {
                const ids = _.map(data, 'id');
                this.showNotif('warning', _.size(ids) > 1 ? 'Удалить записи?' : 'Удалить запись?', 'center', [
                    {
                        label: 'Отмена',
                        color: 'white',
                        handler: () => {
                            this.cargoTableReactiveProperties.selected = [];
                        },
                    },
                    {
                        label: 'Удалить',
                        color: 'white',
                        handler: async () => {
                            const { getUrl } = await import('src/tools/url');
                            this.$axios.post(getUrl('deleteQuestions'), { ids })
                              .then(() => {
                                  this.cargoTableReactiveProperties.selected = [];
                                  this.$store.dispatch('questions/deleteQuestions', ids);
                                  this.$q.loading.hide();
                                  this.showNotif('success', `${_.size(ids > 1) ? 'Записи успешно удалены' : 'Запись успешно удалена'}`, 'center');
                              })
                              .catch((error) => {
                                  devlog.error('Ошибка', error);
                                  this.$q.loading.hide();
                              });
                        },
                    },
                ]);
            },
            viewEditDialog(val, event) {
                if (!_.includes(_.get(event, 'target.classList'), 'select_checkbox')) {
                    this.entryData = val;
                    this.cargoTableReactiveProperties.selected = [];
                    devlog.log('VAL', val);
                    setTimeout(() => {
                        val.selected = !val.selected;
                    }, 100);
                    this.showDialogAddTask = true;
                }
            },
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
            getAvatarText(text) {
                if (text) {
                    return _.upperCase(`${text.slice(0, 1)}${text.slice(text.length - 1)}`);
                }
                return text;
            },
            update(obj) {
                devlog.log('update', obj);
                this.entryData = _.cloneDeep(obj);
                this.showDialogAddQuestion = true;
                this.cargoTableReactiveProperties.selected = [];
                //     this.entryData = { row: _.first(this.cargoTableReactiveProperties.selected) };
                //     this.showDialogAddTask = true;
            },
            addQuestionComment(obj) {
                devlog.log('obj412', obj);
                this.questionId = obj.id;
                this.showDialogAddComment = true;
                this.cargoTableReactiveProperties.selected = [];
            },
        },
    };
</script>

<style lang="scss" scoped>
  .example-item {
    width: 100px;
    overflow: hidden;
  }

  .q-avatar {
    margin: 0 5px;
  }

</style>
