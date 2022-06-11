<template>
  <div>
    <DialogComponent
      :dialog="show"
      title="Код"
      :persistent="true"
      data-vue-component-name="DialogChooseDate"
    >
      <q-card style="min-width: 320px;width: 100%;max-width: 500px;">
        <q-card-section class="row justify-between bg-grey q-mb-sm">
          <span class="text-h6">Выберите дату для записей</span>
          <div>
            <IconBtn
              dense
              icon="clear"
              tooltip="Закрыть"
              @icon-btn-click="show = false"
            />
          </div>
        </q-card-section>

        <q-card-section>
          <DateWithInputForCargo
            v-model:value="localDate"
          />
        </q-card-section>

        <Separator />

        <q-card-actions align="right">
          <BaseBtn
            label="Отмена"
            color="negative"
            :dense="$q.screen.xs || $q.screen.sm"
            @click-base-btn="show = false"
          />

          <BaseBtn
            label="OK"
            color="positive"
            :dense="$q.screen.xs || $q.screen.sm"
            @click-base-btn="setDate(localDate)"
          />
        </q-card-actions>
      </q-card>
    </DialogComponent>
  </div>
</template>

<script>
import DateWithInputForCargo from 'src/components/DateWithInputForCargo.vue';
import DialogComponent from 'src/components/Dialogs/DialogComponent.vue';
import BaseBtn from 'src/components/Buttons/BaseBtn.vue';
import Separator from 'src/components/Separator.vue';
import IconBtn from 'src/components/Buttons/IconBtn.vue';

export default {
  name: 'DialogChooseDate',
  components: {
    DateWithInputForCargo,
    DialogComponent,
    BaseBtn,
    Separator,
    IconBtn,
  },
  props: {
    showDialog: {
      type: Boolean,
      default: false,
    },
    date: {
      type: String,
      default: new Date().toLocaleDateString()
        .split('.')
        .reverse()
        .join('-'),
    },
  },
  emits: ['set-date', 'update:date', 'update:showDialog'],
  data() {
    return {
      localDate: new Date().toLocaleDateString()
        .split('.')
        .reverse()
        .join('-'),
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
  watch: {
    showDialog(val) {
      if (val && this.date) {
        this.localDate = this.date;
      }
    },
  },
  methods: {
    setDate(date) {
      devlog.log('setDate');
      this.$emit('update:date', date);
      this.$emit('set-date');
    },
    dateToday() {
      return new Date().toLocaleDateString()
        .split('.')
        .reverse()
        .join('-');
    },
  },
};
</script>
