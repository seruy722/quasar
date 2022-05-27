<template>
  <RoundBtn
      v-show="values.length"
      color="teal"
      tooltip="Добавить в долги"
      icon="move_to_inbox"
      @round-btn-click="dialogRef.show()"
  />
  <q-dialog
      ref="dialogRef"
  >
    <q-card class="q-dialog-plugin">
      <q-card-section class="row justify-between bg-grey q-mb-sm">
        <span class="text-h6">Выберите дату</span>
        <div>
          <IconBtn
              dense
              icon="clear"
              tooltip="Закрыть"
              color="negative"
              @icon-btn-click="dialogRef.hide()"
          />
        </div>
      </q-card-section>

      <q-card-section>
        <DateComponent2 v-model:dateValue="value" />
      </q-card-section>

      <q-separator />

      <q-card-actions align="right">
        <BaseBtn
            label="Закрыть"
            color="negative"
            @click-base-btn="dialogRef.hide()"
        />

        <BaseBtn
            label="OK"
            color="positive"
            @click-base-btn="onOKClick(value, dialogRef, values)"
        />
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>

<script>
import RoundBtn from 'src/components/Buttons/RoundBtn.vue';
import DateComponent2 from 'src/components/DateComponent2.vue';
import BaseBtn from 'src/components/Buttons/BaseBtn.vue';
import IconBtn from 'src/components/Buttons/IconBtn.vue';
import { ref } from 'vue';
import { useDialogPluginComponent, useQuasar } from 'quasar';
import { getUrl } from 'src/tools/url';
import { axiosInstance } from 'boot/axios';

export default {
  components: {
    RoundBtn,
    DateComponent2,
    BaseBtn,
    IconBtn,
  },
  props: {
    values: {
      type: Array,
      default: () => ([]),
    },
  },
  emits: ['update:values'],
  setup(props, { emit }) {
    const $q = useQuasar();
    const value = ref(null);
    const { dialogRef } = useDialogPluginComponent();

    return {
      value,
      dialogRef,

      onOKClick(val, dialog, values) {
        $q.loading.show();
        axiosInstance.post(getUrl('addTransfersToDebts'), {
          ids: _.map(values, 'id'),
          date: val,
        })
            .then(() => {
              dialog.hide();
              $q.loading.hide();
              emit('update:values', []);
              $q.notify({ message: 'Данные успешно добавлены в таблицу долгов', color: 'positive', position: 'top-right' });
            })
            .catch(() => {
              $q.loading.hide();
              $q.notify({ message: 'Данные успешно добавлены в таблицу долгов', color: 'negative', position: 'top-right' });
              devlog.error('Ошибка запроса - addTransfersToDebts');
            });
      },
    };
  },
};
</script>
