<template>
  <div
    data-vue-component-name="MenuCargo"
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

    <DialogAddCargoPaymentEntry
      v-model:show-dialog="showDialogAddCargoPaymentEntry"
    />
    <DialogAddCargoDebtEntry
      v-model:show-dialog="showDialogAddCargoDebtEntry"
    />
  </div>
</template>

<script>
import DialogAddCargoPaymentEntry from 'src/components/CargoDebts/Dialogs/DialogAddCargoPaymentEntry.vue';
import IconBtn from 'src/components/Buttons/IconBtn.vue';
import DialogAddCargoDebtEntry from 'src/components/CargoDebts/Dialogs/DialogAddCargoDebtEntry.vue';

const listItems = [
  {
    title: 'Оплата',
    click: 'showDialogAddCargoPaymentEntry',
  },
  {
    title: 'Долг',
    click: 'showDialogAddCargoDebtEntry',
  },
];
export default {
  name: 'MenuCargo',
  components: {
    DialogAddCargoPaymentEntry,
    IconBtn,
    DialogAddCargoDebtEntry,
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
      showDialogAddCargoPaymentEntry: false,
      showDialogAddCargoDebtEntry: false,
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
