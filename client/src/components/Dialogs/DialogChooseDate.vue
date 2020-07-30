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
              @iconBtnClick="close"
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
            @clickBaseBtn="close"
          />

          <BaseBtn
            label="OK"
            color="positive"
            :dense="$q.screen.xs || $q.screen.sm"
            @clickBaseBtn="setDate(localDate)"
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
                show: false,
                localDate: new Date().toLocaleDateString()
                  .split('.')
                  .reverse()
                  .join('-'),
            };
        },
        watch: {
            showDialog(val) {
                this.show = val;
            },
            show(val) {
                this.$emit('update:showDialog', val);
            },
            date(val) {
                this.localDate = val;
            },
        },
        methods: {
            setDate(date) {
                this.$emit('update:date', date);
                this.close();
            },
            close() {
                this.show = false;
            },
        },
    };
</script>
