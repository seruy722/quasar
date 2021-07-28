<template>
  <div
    v-show="list.length"
    data-vue-component-name="CountCategories"
  >
    <div class="text-center text-bold text-uppercase q-mt-lg">
      {{ title }}
    </div>
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
          <ItemLabel>{{ numberFormat(item.place) }}</ItemLabel>
        </ItemSection>
        <ItemSection>
          <ItemLabel>{{ numberFormat(item.kg) }}</ItemLabel>
        </ItemSection>
        <ItemSection
          v-if="item.for_kg !== undefined"
          class="cursor-pointer"
        >
          <ItemLabel class="text-red text-bold">
            {{ numberFormat(item.for_kg) }}
          </ItemLabel>
          <PopupEdit
            v-if="item.name"
            v-model:value="item.for_kg"
            type="number"
            :title="item.name"
            @add-to-save="addToSaveArray(item)"
          />
        </ItemSection>
        <ItemSection v-if="item.sum !== undefined">
          <ItemLabel class="text-bold">
            {{ numberFormat(item.sum) }}
          </ItemLabel>
        </ItemSection>
      </ListItem>
      <!--      FOOTER-->
      <ListItem>
        <ItemSection>
          <ItemLabel>{{ footer.name }}</ItemLabel>
        </ItemSection>
        <ItemSection>
          <ItemLabel class="text-bold">
            <Badge>{{ numberFormat(footer.place) }}</Badge>
          </ItemLabel>
        </ItemSection>
        <ItemSection>
          <ItemLabel class="text-bold">
            <Badge>{{ numberFormat(footer.kg) }}</Badge>
          </ItemLabel>
        </ItemSection>
        <ItemSection v-if="footer.for_kg > -1">
          <ItemLabel />
        </ItemSection>
        <ItemSection v-if="footer.sum">
          <ItemLabel class="text-bold">
            <Badge>{{ numberFormat(footer.sum) }}</Badge>
          </ItemLabel>
        </ItemSection>
      </ListItem>
    </List>

    <BaseBtn
      v-show="arrayToSave.length"
      label="Сохранить"
      color="positive"
      @click-base-btn="saveCategoriesPrice(arrayToSave)"
    />
  </div>
</template>

<script>
import { setCategoriesStoreHouseData } from 'src/utils/FrequentlyCalledFunctions';
import { getUrl } from 'src/tools/url';
import { numberFormat } from 'src/utils';
import showNotif from 'src/mixins/showNotif';
import List from 'src/components/Elements/List/List.vue';
import ItemSection from 'src/components/Elements/List/ItemSection.vue';
import ItemLabel from 'src/components/Elements/List/ItemLabel.vue';
import ListItem from 'src/components/Elements/List/ListItem.vue';
import Badge from 'src/components/Elements/Badge.vue';
import PopupEdit from 'src/components/PopupEdit.vue';
import BaseBtn from 'src/components/Buttons/BaseBtn.vue';

export default {
  name: 'CountCategories',
  components: {
    List,
    ItemSection,
    ItemLabel,
    ListItem,
    Badge,
    PopupEdit,
    BaseBtn,
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
      footer: {},
    };
  },
  watch: {
    list: {
      handler: function setList(val) {
        if (!_.isEmpty(val)) {
          const faxId = _.get(_.first(val), 'fax_id');
          devlog.log('faxId', faxId);
          if (faxId === 0) {
            this.setLocalList(val, null);
          } else {
            this.getTransporterPriceData(faxId);
          }
        } else {
          this.localList = [];
        }
      },
      immediate: true,
    },
  },
  methods: {
    numberFormat,
    saveCategoriesPrice(array) {
      this.$q.loading.show();
      this.$axios.post(getUrl('saveCategoriesPrice'), array)
        .then(({ data: { transporterPriceData } }) => {
          devlog.log('PDATA', transporterPriceData);
          this.arrayToSave = [];
          this.setLocalList(this.list, transporterPriceData);
          this.$q.loading.hide();
          this.showNotif('success', 'Категории успешно обновлены');
        })
        .catch((errors) => {
          this.showNotif('success', 'Ошибка обновления категорий. Перегрузите пожалйста страницу.');
          devlog.log('errors', errors);
        });
    },
    setLocalList(list, transporterPriceData) {
      const {
        categoriesList,
        footer,
      } = setCategoriesStoreHouseData(list, transporterPriceData);
      this.localList = categoriesList;
      this.footer = footer;
    },
    addToSaveArray(item) {
      devlog.log('addToSaveArray', item);
      devlog.log('addToSaveArray', item.for_kg);
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
          this.setLocalList(this.list, transporterPriceData);
        })
        .catch((errors) => {
          devlog.log('errors', errors);
        });
    },
  },
};
</script>
