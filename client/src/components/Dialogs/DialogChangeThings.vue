<template>
  <div
    data-vue-component-name="DialogChangeThings"
  >
    <Dialog
      :dialog.sync="thingsData.dialog"
      :persistent="true"
      title="Запись"
    >
      <Card style="min-width: 320px;width: 100%;max-width: 800px;">
        <CardSection class="row justify-between bg-grey q-mb-sm">
          <span class="text-h6">{{ title }}</span>
          <div>
            <IconBtn
              dense
              icon="add_box"
              @iconBtnClick="add"
            />

            <IconBtn
              dense
              color="info"
              icon="clear_all"
              @iconBtnClick="clear"
            />

            <IconBtn
              dense
              color="negative"
              icon="clear"
              @iconBtnClick="cancel(thingsData)"
            />
          </div>
        </CardSection>
        <CardSection>
          <List
            dense
            bordered
            padding
            separator
            class="q-px-sm"
          >
            <ListItem class="text-bold">
              <ItemSection>Название</ItemSection>
              <ItemSection>Количество</ItemSection>
              <ItemSection side>
                Действие
              </ItemSection>
            </ListItem>

            <ListItem
              v-for="(item, index) in localThings"
              :key="index"
            >
              <ItemSection>
                <SelectChips
                  v-model.trim="item.label"
                  dense
                  :options="thingsList"
                />
              </ItemSection>
              <ItemSection>
                <BaseInput
                  v-model.number="item.value"
                  type="number"
                />
              </ItemSection>
              <ItemSection side>
                <IconBtn
                  color="negative"
                  icon="delete"
                  :dense="$q.screen.xs || $q.screen.sm"
                  :tooltip="$t('delete')"
                  @iconBtnClick="deleteLocalThing(index)"
                />
              </ItemSection>
            </ListItem>
          </List>
        </CardSection>

        <Separator />
        <CardActions>
          <BaseBtn
            label="Cancel"
            color="negative"
            @clickBaseBtn="cancel(thingsData)"
          />
          <BaseBtn
            label="Save"
            @clickBaseBtn="save(localThings, thingsData)"
          />
        </CardActions>
      </Card>
    </Dialog>
  </div>
</template>

<script>
    export default {
        name: 'DialogChangeThings',
        components: {
            BaseInput: () => import('src/components/Elements/BaseInput.vue'),
            SelectChips: () => import('src/components/Elements/SelectChips.vue'),
            List: () => import('src/components/Elements/List/List.vue'),
            ItemSection: () => import('src/components/Elements/List/ItemSection.vue'),
            // ItemLabel: () => import('src/components/Elements/List/ItemLabel.vue'),
            ListItem: () => import('src/components/Elements/List/ListItem.vue'),
            IconBtn: () => import('src/components/Buttons/IconBtn.vue'),
            Dialog: () => import('src/components/Dialogs/Dialog.vue'),
            Card: () => import('src/components/Elements/Card/Card.vue'),
            CardActions: () => import('src/components/Elements/Card/CardActions.vue'),
            CardSection: () => import('src/components/Elements/Card/CardSection.vue'),
            BaseBtn: () => import('src/components/Buttons/BaseBtn.vue'),
            Separator: () => import('src/components/Separator.vue'),
        },
        props: {
            title: {
                type: String,
                default: '',
            },
            thingsData: {
                type: Object,
                default: () => ({}),
            },
        },
        data() {
            this.$_changeData = false;
            return {
                localThings: [],
            };
        },
        computed: {
            thingsList() {
                return this.$store.getters['thingsList/getThingsList'];
            },
        },
        watch: {
            'thingsData.things': {
                handler: function set(val) {
                    this.setLocalThings(val);
                },
                immediate: true,
            },
            'thingsData.dialog': {
                handler: function set(val) {
                    this.thingsData.edit = val;
                },
                immediate: true,
            },
            localThings: {
                handler: function get() {
                    this.$_changeData = true;
                },
                deep: true,
                immediate: false,
            },
        },
        methods: {
            setLocalThings(value) {
                if (_.isString(value) && _.trim(value)) {
                    try {
                        this.localThings = JSON.parse(value);
                    } catch (e) {
                        devlog.error(`Неправильный JSON формат строки - ${value}`);
                    }
                }
            },
            deleteLocalThing(index) {
                this.localThings.splice(index, 1);
            },
            cancel(data) {
                this.closeDialog(data);
                this.setLocalThings(data.things);
            },
            save(values, data) {
                if (this.$_changeData) {
                    if (_.isEmpty(values)) {
                        data.things = null;
                    } else {
                        // Удаление пустых обьектов
                        _.remove(values, (item) => !item.label && !item.value);
                        // Каждое слово с большой буквы
                        _.forEach(values, (item) => {
                            if (_.isString(item.label)) {
                                item.label = _.join(_.map(_.split(item.label, ' '), (elem) => _.upperFirst(elem)), ' ');
                            }
                        });
                        data.things = JSON.stringify(values);
                    }
                    this.$emit('addToSave');
                }
                this.closeDialog(data);
            },
            closeDialog(data) {
                data.dialog = false;
            },
            add() {
                this.localThings.unshift({
                    label: null,
                    value: 0,
                });
            },
            clear() {
                if (!_.isEmpty(this.localThings)) {
                    this.localThings = [];
                }
            },
        },
    };
</script>
