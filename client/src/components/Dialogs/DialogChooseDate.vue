<template>
  <div>
    <Dialog
      :dialog.sync="show"
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
            :value.sync="localDate"
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
    </Dialog>
  </div>
</template>

<script>
export default {
  name: 'DialogChooseDate',
  components: {
    Dialog: () => import('src/components/Dialogs/Dialog.vue'),
    BaseBtn: () => import('src/components/Buttons/BaseBtn.vue'),
    Separator: () => import('src/components/Separator.vue'),
    IconBtn: () => import('src/components/Buttons/IconBtn.vue'),
    DateWithInputForCargo: () => import('src/components/DateWithInputForCargo.vue'),
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
