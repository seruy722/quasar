<template>
  <DialogComponent
      v-model:dialog="show"
      title="Код"
      :persistent="true"
      data-vue-component-name="DialogAddCode"
  >
    <Card style="min-width: 320px;width: 100%;max-width: 500px;">
      <CardSection class="row justify-between bg-grey q-mb-sm">
        <span class="text-h6">Новый код клиента</span>
        <div>
          <IconBtn
              dense
              icon="clear"
              tooltip="Закрыть"
              @icon-btn-click="close(codeData)"
          />
        </div>
      </CardSection>

      <CardSection>
        <BaseInput
            v-model.trim="codeData.code.value"
            :label="codeData.code.label"
            :dense="$q.screen.xs || $q.screen.sm"
            :field="codeData.code.field"
            :autofocus="codeData.code.autofocus"
            :errors="errorsData"
        />
      </CardSection>

      <Separator />

      <CardActions>
        <BaseBtn
            label="Отмена"
            color="negative"
            :dense="$q.screen.xs || $q.screen.sm"
            @click-base-btn="close(codeData)"
        />

        <BaseBtn
            label="Сохранить"
            color="positive"
            :loading="loading"
            :dense="$q.screen.xs || $q.screen.sm"
            @click-base-btn="checkErrors(codeData, saveData)"
        />
      </CardActions>
    </Card>
  </DialogComponent>

  <DialogAddClient
      v-model:show-dialog="showClientDialog"
      :code-id="codeId"
  />
</template>

<script>
import { getUrl } from 'src/tools/url';
import CheckErrorsFunc from 'src/utils/CheckErrors.js';
import {
  setDefaultData,
  setFormatedDate,
} from 'src/utils/FrequentlyCalledFunctions';
import {
  defineAsyncComponent,
  defineComponent,
  ref,
  computed,
  reactive,
} from 'vue';
import Card from 'src/components/Elements/Card/Card.vue';
import DialogComponent from 'src/components/Dialogs/DialogComponent.vue';
import BaseBtn from 'src/components/Buttons/BaseBtn.vue';
import BaseInput from 'src/components/Elements/BaseInput.vue';
import Separator from 'src/components/Separator.vue';
import CardActions from 'src/components/Elements/Card/CardActions.vue';
import CardSection from 'src/components/Elements/Card/CardSection.vue';
import IconBtn from 'src/components/Buttons/IconBtn.vue';
import { axiosInstance } from 'boot/axios';
import { useStore } from 'vuex';

export default defineComponent({
  name: 'DialogAddCode',
  components: {
    DialogComponent,
    BaseBtn,
    BaseInput,
    DialogAddClient: defineAsyncComponent(() => import('src/components/Dialogs/DialogAddClient.vue')),
    Separator,
    Card,
    CardActions,
    CardSection,
    IconBtn,
  },
  props: {
    showDialog: {
      type: Boolean,
      default: false,
    },
    newCodeData: {
      type: Object,
      default: () => ({}),
    },
  },
  emits: ['update:showDialog', 'update:newCodeData'],
  setup(props, { emit }) {
    const store = useStore();
    const { errorsData, checkErrors } = CheckErrorsFunc();
    const loading = ref(false);
    const showClientDialog = ref(false);
    const codeId = ref(0);
    const show = computed({
      get: function get() {
        return props.showDialog;
      },
      set: function set(val) {
        emit('update:showDialog', val);
      },
    });

    const codeData = reactive({
      code: {
        type: 'text',
        label: 'Код',
        field: 'code',
        autofocus: true,
        rules: [
          {
            name: 'isLength',
            error: 'Минимальное количество символов 2.',
            options: {
              min: 2,
              max: 255,
            },
          },
        ],
        require: true,
        requireError: 'Поле обьзательное для заполнения.',
        default: '',
        value: '',
      },
    });
    const close = ((data) => {
      show.value = false;
      setDefaultData(data);
    });

    function saveData({ code: { value } }) {
      loading.value = true;
      axiosInstance.post(getUrl('storeCode'), { code: _.startCase(value) })
          .then(({
                   data: {
                     code: addedCode,
                     codeWithCustomers,
                   },
                 }) => {
            if (!_.isEmpty(store.getters['codes/getCodes'])) {
              store.dispatch('codes/addCode', {
                code: addedCode,
                codeWithCustomers: _.first(setFormatedDate(codeWithCustomers, ['created_at'])),
              });
            } else {
              store.dispatch('codes/setCodes');
              emit('update:newCodeData', addedCode);
            }

            codeId.value = addedCode.value;
            showClientDialog.value = true;
            loading.value = false;
          })
          .catch(({ response: { data: { errors } } }) => {
            errorsData.errors = errors;
            loading.value = false;
          });
    }

    return {
      errorsData,
      checkErrors,
      loading,
      showClientDialog,
      codeId,
      show,
      codeData,
      close,
      saveData,
    };
  },
});
</script>
