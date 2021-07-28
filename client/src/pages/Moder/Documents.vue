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
                  @click="showDialogAddDocuments = true"
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
                <q-item-section>
                  <q-item-label>
                    {{ props.row.formatDate }}
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
    </PullRefresh>
    <DialogAddDocuments
      v-model:show-dialog="showDialogAddDocuments"
    />
    <DialogShowImageGallery
      v-model:show-dialog="showDialogImageGallery"
      v-model:slide="slide"
      :files="filesGallery"
    />
  </q-page>
</template>

<script>
import showNotif from 'src/mixins/showNotif';
import filesMixin from 'src/mixins/files';
import CheckErrorsMixin from 'src/mixins/CheckErrors';
import Table from 'src/components/Elements/Table/Table.vue';
import MenuBtn from 'src/components/Buttons/MenuBtn.vue';
import PullRefresh from 'src/components/PullRefresh.vue';
import DialogAddDocuments from 'src/components/Documents/DialogAddDocuments.vue';
import DialogShowImageGallery from 'src/components/Tasks/DialogShowImageGallery.vue';

export default {
  name: 'Documents',
  components: {
    Table,
    MenuBtn,
    PullRefresh,
    DialogAddDocuments,
    DialogShowImageGallery,
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
      showDialogAddDocuments: false,
      showDialogImageGallery: false,
      filesGallery: [],
      slide: 1,
      extensions: ['xlsx', 'txt', 'doc', 'docx', 'pdf'],
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
      this.$q.loading.show();
      this.$store.dispatch('documents/fetchDocuments')
        .then(() => {
          this.$q.loading.hide();
        })
        .catch(() => {
          this.$q.loading.hide();
        });
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
