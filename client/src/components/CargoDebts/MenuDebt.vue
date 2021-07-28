<template>
  <div
    data-vue-component-name="MenuDebt"
  >
    <IconBtn
      color="secondary"
      tooltip="Добавить"
    >
      <q-menu
        transition-show="flip-right"
        transition-hide="flip-left"
      >
        <q-list
          :dense="$q.screen.xs || $q.screen.sm"
          bordered
          padding
          separator
          class="q-px-sm"
        >
          <q-item
            v-for="(item, index) in listItems"
            :key="index"
            v-ripple
            clickable
            @click="onClick(item.click)"
          >
            <q-item-section>{{ item.title }}</q-item-section>
          </q-item>
        </q-list>
      </q-menu>
    </IconBtn>

    <DialogAddDebtPaymentEntry
      v-model:show-dialog="showDialogAddDebtPaymentEntry"
    />
    <DialogAddDebEntry
      v-model:show-dialog="showDialogAddDebtEntry"
    />
  </div>
</template>

<script>
import DialogAddDebtPaymentEntry from 'src/components/CargoDebts/Dialogs/DialogAddDebtPaymentEntry.vue';
import IconBtn from 'src/components/Buttons/IconBtn.vue';
import DialogAddDebEntry from 'src/components/CargoDebts/Dialogs/DialogAddDebEntry.vue';

const listItems = [
  {
    title: 'Оплата',
    click: 'showDialogAddDebtPaymentEntry',
  },
  {
    title: 'Долг',
    click: 'showDialogAddDebtEntry',
  },
];
export default {
  name: 'MenuDebt',
  components: {
    DialogAddDebtPaymentEntry,
    IconBtn,
    DialogAddDebEntry,
  },
  props: {
    items: {
      type: Array,
      default: () => [],
    },
  },
  data() {
    return {
      listItems: [],
      showDialogAddDebtPaymentEntry: false,
      showDialogAddDebtEntry: false,
    };
  },
  watch: {
    items: {
      handler: function set(val) {
        if (!_.isEmpty(val)) {
          this.listItems = _.reduce(listItems, (result, item) => {
            if (val.includes(item.title)) {
              result.push(item);
            }
            return result;
          }, []);
        } else {
          this.listItems = listItems;
        }
      },
      immediate: true,
    },
  },
  methods: {
    onClick(value) {
      this[value] = true;
    },
  },
};
</script>
