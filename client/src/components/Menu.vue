<template>
  <div
    data-vue-component-name="Menu"
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

    <DialogAddEntryOnStorehouse v-model:show-dialog="showAddEntryOnStorehouseDialog" />
    <DialogAddTransporter v-model:show-dialog="showTransporterDialog" />
    <DialogAddCategory v-model:show-dialog="showCategoryDialog" />
    <DialogAddFax v-model:show-dialog="showFaxDialog" />
    <DialogAddClient v-model:show-dialog="showCustomerDialog" />
    <DialogAddCode v-model:show-dialog="showCodeDialog" />
  </div>
</template>

<script>
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
        name: 'Menu',
        components: {
            DialogAddEntryOnStorehouse: () => import('src/components/Dialogs/DialogAddEntryOnStorehouse.vue'),
            DialogAddTransporter: () => import('src/components/Dialogs/DialogAddTransporter.vue'),
            DialogAddCategory: () => import('src/components/Dialogs/DialogAddCategory.vue'),
            DialogAddFax: () => import('src/components/Dialogs/DialogAddFax.vue'),
            DialogAddClient: () => import('src/components/Dialogs/DialogAddClient.vue'),
            DialogAddCode: () => import('src/components/Dialogs/DialogAddCode.vue'),
            IconBtn: () => import('src/components/Buttons/IconBtn.vue'),
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
