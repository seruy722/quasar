<template>
  <div
    data-vue-component-name="MenuCodes"
  >
    <RoundBtn
      color="secondary"
      tooltip="Добавить"
      icon="add"
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
    </RoundBtn>

    <DialogAddEntryOnStorehouse v-model:show-dialog="showAddEntryOnStorehouseDialog" />
    <DialogAddTransporter v-model:show-dialog="showTransporterDialog" />
    <DialogAddCategory v-model:show-dialog="showCategoryDialog" />
    <DialogAddFax v-model:show-dialog="showFaxDialog" />
    <DialogAddClient v-model:show-dialog="showCustomerDialog" />
    <DialogAddCode v-model:show-dialog="showCodeDialog" />
  </div>
</template>

<script>
// import DialogAddEntryOnStorehouse from 'src/components/Dialogs/DialogAddEntryOnStorehouse.vue';
import DialogAddTransporter from 'src/components/Dialogs/DialogAddTransporter.vue';
import DialogAddCategory from 'src/components/Dialogs/DialogAddCategory.vue';
import DialogAddFax from 'src/components/Dialogs/DialogAddFax.vue';
import DialogAddClient from 'src/components/Dialogs/DialogAddClient.vue';
import DialogAddCode from 'src/components/Dialogs/DialogAddCode.vue';
import RoundBtn from 'src/components/Buttons/RoundBtn.vue';
import { defineAsyncComponent } from 'vue';

const listItems = [
  {
    title: 'Запись',
    click: 'showAddEntryOnStorehouseDialog',
  },
  {
    title: 'Код',
    click: 'showCodeDialog',
  },
  {
    title: 'Клиента',
    click: 'showCustomerDialog',
  },
  {
    title: 'Категорию',
    click: 'showCategoryDialog',
  },
  {
    title: 'Факс',
    click: 'showFaxDialog',
  },
  {
    title: 'Перевожчика',
    click: 'showTransporterDialog',
  },
];
export default {
  name: 'MenuCodes',
  components: {
    DialogAddEntryOnStorehouse: defineAsyncComponent(() => import('src/components/Dialogs/DialogAddEntryOnStorehouse.vue')),
    DialogAddTransporter,
    DialogAddCategory,
    DialogAddFax,
    DialogAddClient,
    DialogAddCode,
    RoundBtn,
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
      showAddEntryOnStorehouseDialog: false,
      showTransporterDialog: false,
      showCategoryDialog: false,
      showFaxDialog: false,
      showCustomerDialog: false,
      showCodeDialog: false,
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
