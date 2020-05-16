<template>
  <div
    data-vue-component-name="Menu"
  >
    <IconBtn color="secondary" tooltip="Добавить">
      <q-menu
        transition-show="flip-right"
        transition-hide="flip-left"
      >
        <List
          :dense="$q.screen.xs || $q.screen.sm"
          bordered
          padding
          separator
          class="q-px-sm"
        >
          <ListItem
            v-for="(item, index) in listItems"
            :key="index"
            v-ripple
            clickable
            @listItemClick="onClick(item.click)"
          >
            <ItemSection>{{ item.title }}</ItemSection>
          </ListItem>
        </List>
      </q-menu>
    </IconBtn>

    <DialogAddEntryOnStorehouse :show-dialog.sync="showAddEntryOnStorehouseDialog" />
    <DialogAddTransporter :show-dialog.sync="showTransporterDialog" />
    <DialogAddCategory :show-dialog.sync="showCategoryDialog" />
    <DialogAddFax :show-dialog.sync="showFaxDialog" />
    <DialogAddClient :show-dialog.sync="showCustomerDialog" />
    <DialogAddCode :show-dialog.sync="showCodeDialog" />
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
            List: () => import('src/components/Elements/List/List.vue'),
            DialogAddEntryOnStorehouse: () => import('src/components/Dialogs/DialogAddEntryOnStorehouse.vue'),
            DialogAddTransporter: () => import('src/components/Dialogs/DialogAddTransporter.vue'),
            DialogAddCategory: () => import('src/components/Dialogs/DialogAddCategory.vue'),
            DialogAddFax: () => import('src/components/Dialogs/DialogAddFax.vue'),
            DialogAddClient: () => import('src/components/Dialogs/DialogAddClient.vue'),
            DialogAddCode: () => import('src/components/Dialogs/DialogAddCode.vue'),
            ItemSection: () => import('src/components/Elements/List/ItemSection.vue'),
            ListItem: () => import('src/components/Elements/List/ListItem.vue'),
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
