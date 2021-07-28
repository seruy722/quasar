<template>
  <div>
    <Dialog
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
    </Dialog>

    <DialogAddClient
      v-model:show-dialog="showClientDialog"
      :code-id="codeId"
    />
  </div>
</template>

<script>
import { getUrl } from 'src/tools/url';
import CheckErrorsMixin from 'src/mixins/CheckErrors';
import showNotif from 'src/mixins/showNotif';
import {
  setDefaultData,
  setFormatedDate,
} from 'src/utils/FrequentlyCalledFunctions';
import { defineAsyncComponent } from 'vue';
import Card from 'src/components/Elements/Card/Card.vue';
import Dialog from 'src/components/Dialogs/Dialog.vue';
import BaseBtn from 'src/components/Buttons/BaseBtn.vue';
import BaseInput from 'src/components/Elements/BaseInput.vue';
import Separator from 'src/components/Separator.vue';
import CardActions from 'src/components/Elements/Card/CardActions.vue';
import CardSection from 'src/components/Elements/Card/CardSection.vue';
import IconBtn from 'src/components/Buttons/IconBtn.vue';

export default {
  name: 'DialogAddCode',
  components: {
    Dialog,
    BaseBtn,
    BaseInput,
    DialogAddClient: defineAsyncComponent(() => import('src/components/Dialogs/DialogAddClient.vue')),
    Separator,
    Card,
    CardActions,
    CardSection,
    IconBtn,
  },
  mixins: [showNotif, CheckErrorsMixin],
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
  data() {
    return {
      loading: false,
      codeData: {
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
      },
      showClientDialog: false,
      codeId: 0,
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
  },
  methods: {
    saveData({ code: { value } }) {
      devlog.log('S_VAL', value);
      // this.$q.loading.show();
      this.loading = true;
      this.$axios.post(getUrl('storeCode'), { code: _.startCase(value) })
        .then(({
                 data: {
                   code: addedCode,
                   codeWithCustomers,
                 },
               }) => {
          devlog.log('C_DATA', addedCode);
          if (!_.isEmpty(this.$store.getters['codes/getCodes'])) {
            this.$store.dispatch('codes/addCode', {
              code: addedCode,
              codeWithCustomers: _.first(setFormatedDate(codeWithCustomers, ['created_at'])),
            });
          } else {
            this.$store.dispatch('codes/setCodes');
            this.$emit('update:newCodeData', addedCode);
          }

          this.codeId = addedCode.value;
          this.showClientDialog = true;
          // this.$q.loading.hide();
          this.loading = false;
        })
        .catch(({ response: { data: { errors } } }) => {
          this.errorsData.errors = errors;
          // this.$q.loading.hide();
          this.loading = false;
        });
    },
    close(data) {
      this.show = false;
      setDefaultData(data);
    },
  },
};
</script>
