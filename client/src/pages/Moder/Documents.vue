<template>
  <q-page
    data-vue-component-name="Documents"
  >
    <PullRefresh @refresh="refresh">
      <Table
        :table-properties="cargoTableProperties"
        :table-data="documents"
        :table-reactive-properties="cargoTableReactiveProperties"
        title="Документы"
        grid
      >
        <template v-slot:top-buttons>
          <MenuBtn :list="menuList">
            <q-menu
              transition-show="scale"
              transition-hide="scale"
            >
              <q-list separator style="min-width: 100px">
                <q-item
                  v-close-popup
                  clickable
                  @click="showDialogAddDocuments = true"
                >
                  <q-item-section avatar>
                    <q-icon name="add" color="positive" />
                  </q-item-section>
                  <q-item-section>Добавить</q-item-section>
                </q-item>
                <!--                <q-item-->
                <!--                  v-show="cargoTableReactiveProperties.selected.length === 1"-->
                <!--                  v-close-popup-->
                <!--                  clickable-->
                <!--                  @click="update"-->
                <!--                >-->
                <!--                  <q-item-section avatar>-->
                <!--                    <q-icon name="edit" color="teal" />-->
                <!--                  </q-item-section>-->
                <!--                  <q-item-section>Редактировать</q-item-section>-->
                <!--                </q-item>-->
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
                <!--                <q-item-->
                <!--                  v-close-popup-->
                <!--                  clickable-->
                <!--                  @click="exportFaxData(cargoTableReactiveProperties.selected)"-->
                <!--                >-->
                <!--                  <q-item-section avatar>-->
                <!--                    <q-icon name="explicit" color="positive" />-->
                <!--                  </q-item-section>-->
                <!--                  <q-item-section>Excel</q-item-section>-->
                <!--                </q-item>-->
                <!--                <q-item-->
                <!--                  v-show="cargoTableReactiveProperties.selected.length"-->
                <!--                  v-close-popup-->
                <!--                  clickable-->
                <!--                  @click="destroyEntry(cargoTableReactiveProperties.selected)"-->
                <!--                >-->
                <!--                  <q-item-section avatar>-->
                <!--                    <q-icon name="delete" color="negative" />-->
                <!--                  </q-item-section>-->
                <!--                  <q-item-section>Удалить</q-item-section>-->
                <!--                </q-item>-->
              </q-list>
            </q-menu>
          </MenuBtn>
        </template>
        <!--ОТОБРАЖЕНИЕ КОНТЕНТА НА МАЛЕНЬКИХ ЭКРАНАХ-->
        <template v-slot:inner-item="{props}">
          <div
            class="q-pa-xs col-xs-12 col-sm-6 col-md-4 col-lg-3 grid-style-transition"
            :style="props.selected ? 'transform: scale(0.95);' : ''"
          >
            <q-expansion-item
              v-model="expand"
              expand-separator
              class="shadow-1 overflow-hidden"
              :header-class="`${props.row.type ? 'bg-green' : 'bg-red'} text-white`"
              :style="`border-radius: 30px;border: 1px solid ${props.row.type ? 'lightgreen' : 'lightcoral'};`"
              expand-icon-class="text-white"
              @before-show="beforeShow"
            >
              <template v-slot:header>
                <q-item-section avatar>
                  <q-checkbox
                    v-model="props.selected"
                    dense
                  />
                </q-item-section>
                <q-item-section>
                  <q-item-label>
                    {{ props.row.date }}
                  </q-item-label>
                </q-item-section>
                <q-item-section>
                  <q-item-label>
                    {{ props.row.comments.length }}
                  </q-item-label>
                </q-item-section>
              </template>
              <q-chat-message
                v-for="(com, index) in props.row.comments"
                :key="index"
                :name="com.author_name"
                :text="[com.title]"
                :stamp="com.formatDate"
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
                <div
                  v-if="com.code_client_name"
                  class="q-mt-sm"
                >Клиент: {{ com.code_client_name }}
                </div>
              </q-chat-message>
            </q-expansion-item>
          </div>
        </template>
      </Table>
    </PullRefresh>
    <DialogAddDocuments
      :show-dialog.sync="showDialogAddDocuments"
    />
    <DialogShowImageGallery
      :show-dialog.sync="showDialogImageGallery"
      :files="filesGallery"
      :slide="slide"
    />
  </q-page>
</template>

<script>
    import showNotif from 'src/mixins/showNotif';
    import CheckErrorsMixin from 'src/mixins/CheckErrors';

    export default {
        name: 'Documents',
        components: {
            Table: () => import('components/Elements/Table/Table.vue'),
            MenuBtn: () => import('components/Buttons/MenuBtn.vue'),
            PullRefresh: () => import('components/PullRefresh.vue'),
            DialogAddDocuments: () => import('components/Documents/DialogAddDocuments.vue'),
            DialogShowImageGallery: () => import('components/Tasks/DialogShowImageGallery.vue'),
        },
        mixins: [showNotif, CheckErrorsMixin],
        data() {
            return {
                cargoTableProperties: {
                    columns: [
                        {
                            name: 'created_at',
                            label: 'Дата',
                            align: 'center',
                            field: 'created_at',
                            sortable: true,
                        },
                        {
                            name: 'author_name',
                            label: 'Автор',
                            align: 'center',
                            field: 'author_name',
                            sortable: true,
                        },
                        {
                            name: 'description',
                            label: 'Описание',
                            align: 'center',
                            field: 'description',
                            sortable: true,
                        },
                        {
                            name: 'section_name',
                            label: 'Раздел',
                            align: 'center',
                            field: 'section_name',
                            sortable: true,
                        },
                        {
                            name: 'status_name',
                            label: 'Статус',
                            field: 'status_name',
                            align: 'center',
                            sortable: true,
                        },
                        {
                            name: 'responsible_name',
                            label: 'Ответственный',
                            field: 'responsible_name',
                            align: 'center',
                            sortable: true,
                        },
                        {
                            name: 'date',
                            label: 'Дата',
                            field: 'date',
                            align: 'center',
                            sortable: true,
                        },
                    ],
                },
                cargoTableReactiveProperties: {
                    selected: [],
                    visibleColumns: ['created_at', 'author_name', 'description', 'section_name', 'status_name', 'responsible_name', 'date'],
                    title: '',
                },
                menuList: [],
                showDialogAddTask: false,
                entryData: {},
                files: [],
                showDialogAddDocuments: false,
                showDialogImageGallery: false,
                filesGallery: [],
                slide: 1,
                extensions: ['xlsx', 'txt', 'doc', 'docx', 'pdf'],
                expand: false,
            };
        },
        computed: {
            usersList() {
                return this.$store.getters['auth/getUsersList'];
            },
            documents() {
                return this.$store.getters['documents/getDocuments'];
            },
            userId() {
                return _.get(this.$store.getters['auth/getUser'], 'id');
            },
        },
        created() {
            if (_.isEmpty(this.usersList)) {
                this.$store.dispatch('auth/fetchUsersList');
            }
            if (_.isEmpty(this.documents)) {
                this.$store.dispatch('documents/fetchDocuments');
            }
        },
        methods: {
            async refresh(done) {
                if (!done) {
                    this.$q.loading.show();
                }
                const { callFunction } = await import('src/utils/index');
                await this.$store.dispatch('documents/fetchDocuments')
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
            // destroyEntry(data) {
            //     const ids = _.map(data, 'id');
            //     this.showNotif('warning', _.size(ids) > 1 ? 'Удалить записи?' : 'Удалить запись?', 'center', [
            //         {
            //             label: 'Отмена',
            //             color: 'white',
            //             handler: () => {
            //                 this.cargoTableReactiveProperties.selected = [];
            //             },
            //         },
            //         {
            //             label: 'Удалить',
            //             color: 'white',
            //             handler: async () => {
            //                 const { getUrl } = await import('src/tools/url');
            //                 this.$axios.post(getUrl('deleteTasks'), { ids })
            //                   .then(() => {
            //                       this.cargoTableReactiveProperties.selected = [];
            //                       this.$store.dispatch('tasks/deleteTasks', ids);
            //                       this.$q.loading.hide();
            //                       this.showNotif('success', `${_.size(ids > 1) ? 'Записи успешно удалены' : 'Запись успешно удалена'}`, 'center');
            //                   })
            //                   .catch((error) => {
            //                       devlog.error('Ошибка', error);
            //                       this.$q.loading.hide();
            //                   });
            //             },
            //         },
            //     ]);
            // },
            // viewEditDialog(val, event) {
            //     if (!_.includes(_.get(event, 'target.classList'), 'select_checkbox')) {
            //         this.entryData = val;
            //         this.cargoTableReactiveProperties.selected = [];
            //         devlog.log('VAL', val);
            //         setTimeout(() => {
            //             val.selected = !val.selected;
            //         }, 100);
            //         this.showDialogAddTask = true;
            //     }
            // },
            // update() {
            //     this.entryData = { row: _.first(this.cargoTableReactiveProperties.selected) };
            //     this.showDialogAddTask = true;
            // },
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
            viewImageGallery(files, slide) {
                devlog.log(this.$route, process);
                const file = files[slide - 1];
                if (_.includes(this.extensions, file.ext)) {
                    devlog.log(file.path);
                    const link = document.createElement('a');
                    link.href = `${this.fileUrl()}${file.path}`;
                    link.setAttribute('download', file.name);
                    link.setAttribute('target', '_blank');
                    document.body.appendChild(link);
                    link.click();
                } else {
                    this.slide = slide;
                    this.filesGallery = _.filter(files, ({ ext }) => !_.includes(this.extensions, ext));
                    this.showDialogImageGallery = true;
                }
            },
            fileUrl() {
                return process.env.DEV ? 'http://sp.com.ua/storage/' : 'http://servercargo007.net.ua/storage/app/public/';
            },
            getFileExt({ name }) {
                if (name) {
                    return name.slice(name.lastIndexOf('.') + 1);
                }
                return name;
            },
            beforeShow() {
                devlog.log('beforeShow');
                this.expand = true;
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
