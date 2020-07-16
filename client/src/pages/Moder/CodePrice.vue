<template>
  <q-page
    data-vue-component-name="CodePrice"
    class="q-pa-md"
  >
    <PullRefresh @refresh="refresh">
      MONEY
      <IconBtn
        color="teal"
        icon="add_box"
        tooltip="Добавить"
        @iconBtnClick="addCodeWithCategory"
      />
      <IconBtn
        color="primary"
        icon="sync"
        tooltip="Обновить"
        @iconBtnClick="refresh"
      />
      <Search v-model="search" :style="`max-width:${$q.screen.xs ? '100%':'250px'};margin: 10px;`" />
      <div style="max-width: 800px">
        <List
          dense
          bordered
        >
          <q-expansion-item
            v-for="(item, index) in codesPricesData"
            :key="index"
            icon="explore"
            label="Роли"
            expand-separator
          >
            <template v-slot:header>
              <q-item-section>
                <ItemLabel>
                  <Badge>{{ index }}</Badge>
                </ItemLabel>
              </q-item-section>

              <q-item-section>
                <ItemLabel>
                  <IconBtn
                    flat
                    dense
                    icon="add_circle"
                    tooltip="Добавить"
                    @iconBtnClick="openDialogAddCodePrice(item[0].code_id)"
                  />
                </ItemLabel>
              </q-item-section>
            </template>
            <List
              dense
              bordered
              separator
              class="q-ml-md"
            >
              <ListItem class="text-bold text-center">
                <ItemSection>
                  <ItemLabel>Категория</ItemLabel>
                </ItemSection>

                <ItemSection>
                  <ItemLabel>За кг</ItemLabel>
                </ItemSection>

                <ItemSection>
                  <ItemLabel>За место</ItemLabel>
                </ItemSection>
                <ItemSection>
                  <ItemLabel>Обновлено</ItemLabel>
                </ItemSection>

                <ItemSection side>
                  Управление
                </ItemSection>
              </ListItem>

              <ListItem
                v-for="(elem, i) in item"
                :key="i"
                class="text-center"
              >
                <ItemSection>
                  <ItemLabel :lines="3">
                    <Badge color="positive">{{ elem.category_name }}</Badge>
                  </ItemLabel>
                </ItemSection>

                <ItemSection>
                  <ItemLabel>{{ elem.for_kg }}</ItemLabel>
                </ItemSection>

                <ItemSection>
                  <ItemLabel>{{ elem.for_place }}</ItemLabel>
                </ItemSection>

                <ItemSection>
                  <ItemLabel>{{ elem.updated_at }}</ItemLabel>
                </ItemSection>

                <ItemSection side>
                  <div class="row">
                    <IconBtn
                      flat
                      dense
                      icon="history"
                      tooltip="История"
                      @iconBtnClick="getCodePriceHistory(elem.id)"
                    />
                    <IconBtn
                      flat
                      dense
                      icon="edit"
                      color="teal"
                      tooltip="Редактировать"
                      @iconBtnClick="openDialogAddCodePriceForUpdate(elem)"
                    />
                    <IconBtn
                      flat
                      dense
                      icon="delete"
                      color="negative"
                      tooltip="Удалить"
                      @iconBtnClick="deleteCodePrice(elem)"
                    />
                  </div>
                </ItemSection>
              </ListItem>
            </List>
          </q-expansion-item>
        </List>
      </div>
      <DialogAddCodePrice
        :show-dialog.sync="showDialogAddCodePrice"
        :code-id.sync="codeId"
        :entry-data.sync="entryData"
      />
      <DialogAddNewCodePrice
        :show-dialog.sync="showDialogAddNewCodePrice"
      />

      <Dialog
        :dialog="dialogHistory"
        :persistent="true"
        :maximized="true"
        transition-show="slide-up"
        transition-hide="slide-down"
      >
        <Card style="max-width: 600px;">
          <q-bar>
            <q-space />
            <IconBtn
              flat
              dense
              icon="close"
              tooltip="Закрыть"
              @iconBtnClick="dialogHistory = false"
            />
          </q-bar>

          <CardSection class="q-pt-none">
            <CodePriceHistory :history-data="codePriceHistoryData" />
          </CardSection>
        </Card>
      </Dialog>
    </PullRefresh>
  </q-page>
</template>

<script>
    import { getUrl } from 'src/tools/url';
    import showNotif from 'src/mixins/showNotif';
    import {
        setMethodLabel,
        setStatusLabel,
        setFormatedDate,
        prepareHistoryData,
    } from 'src/utils/FrequentlyCalledFunctions';
    import {
        isoDate,
        toDate,
        formatToMysql,
    } from 'src/utils/formatDate';
    import { max } from 'date-fns';
    import { callFunction } from 'src/utils/index';
    import { sortCollection } from 'src/utils/sort';

    export default {
        name: 'CodePrice',
        components: {
            List: () => import('src/components/Elements/List/List.vue'),
            ItemSection: () => import('src/components/Elements/List/ItemSection.vue'),
            ItemLabel: () => import('src/components/Elements/List/ItemLabel.vue'),
            ListItem: () => import('src/components/Elements/List/ListItem.vue'),
            Badge: () => import('src/components/Elements/Badge.vue'),
            IconBtn: () => import('src/components/Buttons/IconBtn.vue'),
            DialogAddCodePrice: () => import('src/components/Dialogs/DialogAddCodePrice.vue'),
            CodePriceHistory: () => import('src/components/History/CodePriceHistory.vue'),
            Dialog: () => import('src/components/Dialogs/Dialog.vue'),
            Card: () => import('src/components/Elements/Card/Card.vue'),
            // CardActions: () => import('src/components/Elements/Card/CardActions.vue'),
            CardSection: () => import('src/components/Elements/Card/CardSection.vue'),
            Search: () => import('src/components/Search.vue'),
            // BaseBtn: () => import('src/components/Buttons/BaseBtn.vue'),
            DialogAddNewCodePrice: () => import('src/components/Dialogs/DialogAddNewCodePrice.vue'),
            PullRefresh: () => import('src/components/PullRefresh.vue'),
        },
        mixins: [showNotif],
        data() {
            return {
                showDialogAddCodePrice: false,
                showDialogAddNewCodePrice: false,
                codeId: 0,
                entryData: {},
                codePriceHistoryData: {},
                dialogHistory: false,
                search: null,
                codesPricesData: [],
            };
        },
        computed: {
            codesPriceData() {
                return this.$store.getters['codesPrices/getCodesPrices'];
            },
        },
        watch: {
            codesPriceData(val) {
                this.codesPricesData = val;
            },
            search: {
                handler: function set(val) {
                    if (!val) {
                        this.codesPricesData = this.codesPriceData;
                    } else {
                        this.codesPricesData = _.reduce(this.codesPriceData, (result, item, index) => {
                            if (_.includes(_.toLower(index), _.toLower(val))) {
                                result[index] = item;
                            }
                            return result;
                        }, {});
                    }
                },
                immediate: true,
            },
        },
        created() {
            this.getCodesPrices();
        },
        methods: {
            addCodeWithCategory() {
                this.showDialogAddNewCodePrice = true;
            },
            getCodesPrices() {
                this.$q.loading.show();
                this.$axios.get(getUrl('getCodesPrices'))
                  .then(({ data: { codesPrice } }) => {
                      _.forEach(codesPrice, (price) => {
                          setFormatedDate(price, ['updated_at']);
                      });
                      this.$store.dispatch('codesPrices/setCodesPrices', codesPrice);
                      this.$q.loading.hide();
                  })
                  .catch((errors) => {
                      devlog.error('Ошибка запроса getClientsPrices', errors);
                      this.$q.loading.hide();
                  });
            },
            openDialogAddCodePrice(id) {
                this.showDialogAddCodePrice = true;
                this.codeId = id;
            },
            openDialogAddCodePriceForUpdate(data) {
                this.showDialogAddCodePrice = true;
                this.codeId = data.code_id;
                this.entryData = data;
            },
            deleteCodePrice(elem) {
                this.showNotif('warning', `Удалить категорию - ${elem.category_name}?`, 'center', [
                    {
                        label: 'Отмена',
                        color: 'white',
                        handler: () => {
                        },
                    },
                    {
                        label: 'Удалить',
                        color: 'white',
                        handler: () => {
                            this.$q.loading.show();
                            this.$axios.delete(`${getUrl('deleteCodePrice')}/${elem.id}`)
                              .then(({ data: { status } }) => {
                                  devlog.log('DEL_DATA', status);
                                  this.$store.dispatch('codesPrices/deleteCodePrice', elem);
                                  this.$q.loading.hide();
                              })
                              .catch((errors) => {
                                  devlog.error('Ошибка запроса deleteCodePrice', errors);
                                  this.$q.loading.hide();
                              });
                        },
                    },
                ]);
            },
            setAdditionalData(data) {
                return setMethodLabel(setStatusLabel(setFormatedDate(data, ['created_at', 'issued_by'])));
            },
            async getCodePriceHistory(id) {
                this.$q.loading.show();
                await this.$axios.get(`${getUrl('getCodePriceHistory')}/${id}`)
                  .then(({ data: { codePriceHistory } }) => {
                      if (!_.isEmpty(codePriceHistory)) {
                          this.$q.loading.hide();
                          this.dialogHistory = true;
                          const historyData = prepareHistoryData([
                              {
                                  label: 'Категория',
                                  name: 'category_name',
                              },
                              {
                                  label: 'За кг',
                                  name: 'for_kg',
                              },
                              {
                                  label: 'За место',
                                  name: 'for_place',
                              },
                              {
                                  label: 'Пользователь',
                                  name: 'user_name',
                              },
                          ], codePriceHistory);
                          devlog.log('historyData', historyData);
                          historyData.historyData = this.setAdditionalData(historyData.historyData);
                          this.codePriceHistoryData = historyData;
                      } else {
                          this.$q.loading.hide();
                          this.showNotif('info', 'По этому коду нет истории.', 'center');
                      }
                  })
                  .catch(() => {
                      devlog.error('Ошибка при получении данных истории - getCodePriceHistory');
                  });
            },
            async refresh(done) {
                const codesData = _.flattenDeep(_.values(this.codesPriceData));
                devlog.log(codesData);
                // callFunction(done);
                await this.$axios.post(getUrl('getNewCodesPrices'), {
                    created_at: isoDate(max(_.map(codesData, (item) => new Date(toDate(item.created_at))))),
                    updated_at: formatToMysql(max(_.map(codesData, (item) => new Date(item.updated_at)))),
                })
                  .then(({ data: { updatedAndNewCodesPricesData } }) => {
                      devlog.log('DTA', updatedAndNewCodesPricesData);
                      if (!_.isEmpty(updatedAndNewCodesPricesData)) {
                          const createdItems = [];
                          _.forEach(updatedAndNewCodesPricesData, (item) => {
                              const find = _.some(codesData, ['id', item.id]);
                              if (find) {
                                  this.$store.dispatch('codesPrices/updateCodePrice', this.setAdditionalData([item]));
                              } else {
                                  createdItems.push(item);
                              }
                          });

                          if (!_.isEmpty(createdItems)) {
                              _.forEach(sortCollection(createdItems, 'id'), (elem) => {
                                  this.$store.dispatch('codesPrices/addNewCodePrice', this.setAdditionalData([elem]));
                              });
                          }

                          this.showNotif('success', 'Данные успешно обновлены.', 'center');
                      } else {
                          this.showNotif('info', 'Данные актуальны.', 'center');
                      }
                      callFunction(done);
                  })
                  .catch(() => {
                      callFunction(done);
                  });
            },
        },
    };
</script>
