<template>
  <div
    data-vue-component-name="Menu"
  >
    <IconBtn color="secondary">
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

    <AddEntryOnStorehouse :show-dialog.sync="showAddEntryOnStorehouseDialog" />
    <AddTransporter :show-dialog.sync="showTransporterDialog" />
    <AddCategory :show-dialog.sync="showCategoryDialog" />
    <DialogAddFax :show-dialog.sync="showFaxDialog" />
    <DialogAddCustomer :show-dialog.sync="showCustomerDialog" />
    <DialogAddCode :show-dialog.sync="showCodeDialog" />
    <AddThings />
  </div>
</template>

<script>
    export default {
        name: 'Menu',
        components: {
            List: () => import('src/components/Elements/List/List.vue'),
            AddEntryOnStorehouse: () => import('src/components/Dialogs/AddEntryOnStorehouse.vue'),
            AddTransporter: () => import('src/components/Dialogs/AddTransporter.vue'),
            AddCategory: () => import('src/components/Dialogs/AddCategory.vue'),
            DialogAddFax: () => import('src/components/Dialogs/DialogAddFax.vue'),
            DialogAddCustomer: () => import('src/components/Dialogs/DialogAddCustomer.vue'),
            DialogAddCode: () => import('src/components/Dialogs/DialogAddCode.vue'),
            AddThings: () => import('src/components/Dialogs/AddThings.vue'),
            ItemSection: () => import('src/components/Elements/List/ItemSection.vue'),
            // ItemLabel: () => import('src/components/Elements/List/ItemLabel.vue'),
            ListItem: () => import('src/components/Elements/List/ListItem.vue'),
            IconBtn: () => import('src/components/Buttons/IconBtn.vue'),
        },
        // props: {
        //     items: {
        //         type: Array,
        //         default: () => [],
        //     },
        // },
        data() {
            return {
                listItems: [
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
                ],
                showAddEntryOnStorehouseDialog: false,
                showTransporterDialog: false,
                showCategoryDialog: false,
                showFaxDialog: false,
                showCustomerDialog: false,
                showCodeDialog: false,
            };
        },
        methods: {
            onClick(value) {
                this[value] = true;
            },
        },
    };
</script>
