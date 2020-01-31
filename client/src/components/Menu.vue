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

    <DialogAddEntryOnStorehouse :show-dialog.sync="showAddEntryOnStorehouseDialog" />
    <DialogAddTransporter :show-dialog.sync="showTransporterDialog" />
    <DialogAddCategory :show-dialog.sync="showCategoryDialog" />
    <DialogAddFax :show-dialog.sync="showFaxDialog" />
    <DialogAddClient :show-dialog.sync="showCustomerDialog" />
    <DialogAddCode :show-dialog.sync="showCodeDialog" />
    <DialogAddThings />
  </div>
</template>

<script>
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
            DialogAddThings: () => import('src/components/Dialogs/DialogAddThings.vue'),
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
