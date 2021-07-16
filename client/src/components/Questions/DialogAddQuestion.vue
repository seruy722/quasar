<template>
  <Dialog
    v-model:dialog="show"
    title="Клиент"
    :persistent="true"
    data-vue-component-name="DialogAddQuestion"
  >
    <q-card style="min-width: 320px;width: 100%;max-width: 500px;">
      <q-card-section class="row justify-between bg-grey q-mb-sm">
        <span class="text-h6">{{ dialogTitle }}</span>
        <div>
          <IconBtn
            dense
            icon="clear"
            tooltip="Закрыть"
            @icon-btn-click="close(tasksData)"
          />
        </div>
      </q-card-section>

      <q-card-section>
        <div
          v-for="(item, index) in tasksData"
          :key="index"
        >
          <SearchSelect
            v-if="item.type==='searchSelect'"
            v-model.number="item.value"
            v-model:change-value="item.changeValue"
            :label="item.label"
            :dense="$q.screen.xs || $q.screen.sm"
            :field="item.field"
            :options="item.options"
            :func-load-data="item.funcLoadData"
            :errors="errorsData"
          />

          <BaseInput
            v-else
            v-model="item.value"
            v-model:change-value="item.changeValue"
            :label="item.label"
            :dense="$q.screen.xs || $q.screen.sm"
            :field="item.field"
            :mask="item.mask"
            :unmasked-value="item.unmaskedValue"
            :autofocus="item.autofocus"
            :errors="errorsData"
          />
        </div>
      </q-card-section>
      <Separator />
      <q-card-actions align="right">
        <BaseBtn
          label="Отмена"
          color="negative"
          :dense="$q.screen.xs || $q.screen.sm"
          @click-base-btn="close(tasksData)"
        />

        <BaseBtn
          label="Сохранить"
          color="positive"
          :dense="$q.screen.xs || $q.screen.sm"
          @click-base-btn="checkErrors(tasksData, saveData)"
        />
      </q-card-actions>
    </q-card>
  </Dialog>
</template>

<script>
import { getUrl } from 'src/tools/url';
import CheckErrorsMixin from 'src/mixins/CheckErrors';
import showNotif from 'src/mixins/showNotif';
import {
  setDefaultData,
  setChangeValue,
  getClientCodes,
} from 'src/utils/FrequentlyCalledFunctions';
import getFromSettings from 'src/tools/settings';
import Dialog from 'src/components/Dialogs/Dialog.vue';
import BaseInput from 'src/components/Elements/BaseInput.vue';
import SearchSelect from 'src/components/Elements/SearchSelect.vue';
import Separator from 'src/components/Separator.vue';
import IconBtn from 'src/components/Buttons/IconBtn.vue';
import BaseBtn from 'src/components/Buttons/BaseBtn.vue';

export default {
  name: 'DialogAddQuestion',
  components: {
    Dialog,
    BaseInput,
    SearchSelect,
    Separator,
    IconBtn,
    BaseBtn,
  },
  mixins: [showNotif, CheckErrorsMixin],
  props: {
    showDialog: {
      type: Boolean,
      default: false,
    },
    entryData: {
      type: Object,
      default: () => ({}),
    },
  },
  emits: ['update:entryData', 'update:showDialog'],
  data() {
    return {
      show: false,
      tasksData: {
        title: {
          type: 'text',
          label: 'Описание',
          field: 'title',
          require: true,
          requireError: 'Поле обьзательное для заполнения.',
          changeValue: false,
          autofocus: true,
          default: null,
          value: null,
        },
        responsible_id: {
          type: 'searchSelect',
          label: 'Ответственный',
          field: 'responsible_id',
          options: [],
          changeValue: false,
          default: null,
          value: null,
        },
        code_client_id: {
          type: 'searchSelect',
          label: 'Клиент',
          field: 'code_client_id',
          options: [],
          changeValue: false,
          default: null,
          value: null,
        },
        status_id: {
          type: 'searchSelect',
          label: 'Статус',
          field: 'status_id',
          options: getFromSettings('statusQuestion'),
          changeValue: false,
          default: 0,
          value: 0,
        },
      },
    };
  },
  computed: {
    dialogTitle() {
      return _.isEmpty(this.entryData) ? 'Новый вопрос' : 'Редактирование вопроса';
    },
    usersList() {
      return this.$store.getters['auth/getUsersList'];
    },
    clientCodes() {
      return this.$store.getters['codes/getCodes'];
    },
  },
  watch: {
    entryData(val) {
      devlog.log('entryData', val);
      if (!_.isEmpty(val)) {
        _.forEach(this.tasksData, (item, index) => {
          devlog.log('ITEM', item);
          if (_.get(this.entryData, index)) {
            _.set(item, 'value', _.get(this.entryData, index));
          }
        });
      }
    },
    showDialog(val) {
      this.show = val;
    },
    show(val) {
      this.$emit('update:showDialog', val);
    },
    usersList(val) {
      if (!_.isEmpty(val)) {
        this.tasksData.responsible_id.options = val;
      }
    },
    clientCodes(val) {
      this.tasksData.code_client_id.options = val;
    },
  },
  created() {
    if (_.isEmpty(this.usersList)) {
      this.$store.dispatch('auth/fetchUsersList');
    }
    this.tasksData.responsible_id.options = this.usersList;
    getClientCodes(this.$store);
  },
  methods: {
    saveData(values) {
      const createData = _.reduce(values, (result, { value }, index) => {
        result[index] = value;
        return result;
      }, {});
      if (_.isEmpty(this.entryData)) {
        // ДОБАВЛЕНИЕ ЗАПИСИ
        this.$q.loading.show();
        this.$axios.post(getUrl('storeQuestion'), createData)
          .then(({ data: { question } }) => {
            this.$q.loading.hide();
            devlog.log('fgsdfsdf', question);
            this.$store.dispatch('questions/addQuestion', question);
            this.close(this.tasksData);
            this.showNotif('success', 'Вопрос успешно добавлен.', 'center');
          })
          .catch((errors) => {
            this.errorsData.errors = _.get(errors, 'response.data.errors');
            this.$q.loading.hide();
          });
      } else {
        // ОБНОВЛЕНИЕ ЗАПИСИ
        const updateData = _.reduce(values, (result, {
          value,
          changeValue,
        }, index) => {
          if (changeValue) {
            result[index] = value;
          }
          return result;
        }, {});
        if (!_.isEmpty(updateData)) {
          updateData.id = this.entryData.id;
          devlog.log('VALUIR', values);

          this.$q.loading.show();
          this.$axios.post(getUrl('updateQuestion'), updateData)
            .then(({ data: { question } }) => {
              this.$q.loading.hide();
              this.$store.dispatch('questions/updateQuestion', question);
              setChangeValue(values);
              this.close(this.tasksData);
              this.showNotif('success', 'Данные успешно обновлены.', 'center');
            })
            .catch((errors) => {
              this.errorsData.errors = _.get(errors, 'response.data.errors');
              this.$q.loading.hide();
            });
        } else {
          this.showNotif('info', 'Нет измененных данных', 'center');
        }
      }
    },
    close(data) {
      this.show = false;
      setDefaultData(data);
      setChangeValue(data);
      this.$emit('update:entryData', {});
    },
  },
};
</script>
