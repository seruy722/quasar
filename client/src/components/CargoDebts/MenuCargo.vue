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
      :show-dialog.sync="showDialogAddCargoPaymentEntry"
    />
  </div>
</template>

<script>
    const listItems = [
        {
            title: 'Оплата',
            click: 'showDialogAddCargoPaymentEntry',
        },
    ];
    export default {
        name: 'MenuCargo',
        components: {
            DialogAddCargoPaymentEntry: () => import('src/components/CargoDebts/Dialogs/DialogAddCargoPaymentEntry.vue'),
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
                showDialogAddCargoPaymentEntry: false,
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
