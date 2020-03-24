<template>
  <div
    v-show="list.length"
    data-vue-component-name="CountCategories"
  >
    <div class="text-center text-bold text-uppercase q-mt-lg">{{ title }}</div>
    <List
      dense
      bordered
      separator
    >
      <ListItem
        v-for="(item, index) in localList"
        :key="index"
      >
        <ItemSection>
          <ItemLabel>{{ item.name }}</ItemLabel>
        </ItemSection>
        <ItemSection>
          <ItemLabel v-if="item.name">{{ item.place | numberFormatFilter }}</ItemLabel>
          <ItemLabel v-else>
            <Badge>{{ item.place | numberFormatFilter }}</Badge>
          </ItemLabel>
        </ItemSection>
        <ItemSection>
          <ItemLabel v-if="item.name">{{ item.kg | numberFormatFilter }}</ItemLabel>
          <ItemLabel v-else>
            <Badge>{{ item.kg | numberFormatFilter }}</Badge>
          </ItemLabel>
        </ItemSection>
        <ItemSection class="cursor-pointer">
          <ItemLabel v-if="item.name" class="text-red text-bold">{{ item.for_kg | numberFormatFilter }}</ItemLabel>
          <PopupEdit
            v-if="item.name"
            :value.sync="item.for_kg"
            type="number"
            :title="item.name"
            @addToSave="addToSaveArray(item)"
          />
        </ItemSection>
        <ItemSection>
          <ItemLabel class="text-bold" v-if="item.name">{{ item.sum | numberFormatFilter }}</ItemLabel>
          <ItemLabel v-else>
            <Badge>{{ item.sum | numberFormatFilter }}</Badge>
          </ItemLabel>
        </ItemSection>
      </ListItem>
    </List>

    <BaseBtn
      v-show="arrayToSave.length"
      label="Сохранить"
      color="positive"
      @clickBaseBtn="saveCategoriesPrice(arrayToSave)"
    />
  </div>
</template>

<script>
    import { setCategoriesStoreHouseData } from 'src/utils/FrequentlyCalledFunctions';
    import { getUrl } from 'src/tools/url';
    import showNotif from 'src/mixins/showNotif';

    export default {
        name: 'CountCategories',
        components: {
            List: () => import('src/components/Elements/List/List.vue'),
            ItemSection: () => import('src/components/Elements/List/ItemSection.vue'),
            ItemLabel: () => import('src/components/Elements/List/ItemLabel.vue'),
            ListItem: () => import('src/components/Elements/List/ListItem.vue'),
            Badge: () => import('src/components/Elements/Badge.vue'),
            PopupEdit: () => import('src/components/PopupEdit.vue'),
            BaseBtn: () => import('src/components/Buttons/BaseBtn.vue'),
        },
        mixins: [showNotif],
        props: {
            list: {
                type: Array,
                default: () => [],
            },
            title: {
                type: String,
                default: 'Категории',
            },
        },
        data() {
            return {
                arrayToSave: [],
                localList: [],
            };
        },
        watch: {
            list: {
                handler: function setList(val) {
                    if (!_.isEmpty(val)) {
                        this.getTransporterPriceData(_.get(_.first(val), 'fax_id'));
                    } else {
                        this.localList = [];
                    }
                },
                immediate: true,
            },
        },
        methods: {
            saveCategoriesPrice(array) {
                this.$q.loading.show();
                this.$axios.post(getUrl('saveCategoriesPrice'), array)
                  .then(({ data: { transporterPriceData } }) => {
                      devlog.log('PDATA', transporterPriceData);
                      this.arrayToSave = [];
                      this.localList = setCategoriesStoreHouseData(this.list, transporterPriceData);
                      this.$q.loading.hide();
                      this.showNotif('success', 'Категории успешно обновлены', 'center');
                  })
                  .catch((errors) => {
                      this.showNotif('success', 'Ошибка обновления категорий. Перегрузите пожалйста страницу.', 'center');
                      devlog.log('errors', errors);
                  });
            },
            addToSaveArray(item) {
                const obj = {
                    uid: item.uid,
                    category_id: item.category_id,
                    fax_id: item.fax_id,
                    category_price: item.for_kg,
                };
                const findIndex = _.findIndex(this.arrayToSave, { uid: item.uid });
                if (findIndex !== -1) {
                    this.arrayToSave.splice(findIndex, 1, obj);
                } else {
                    this.arrayToSave.push(obj);
                }
            },
            getTransporterPriceData(id) {
                this.$axios.get(`${getUrl('transporterPriceData')}/${id}`)
                  .then(({ data: { transporterPriceData } }) => {
                      devlog.log('PDATA', transporterPriceData);
                      this.localList = setCategoriesStoreHouseData(this.list, transporterPriceData);
                  })
                  .catch((errors) => {
                      devlog.log('errors', errors);
                  });
            },
        },
    };
</script>
