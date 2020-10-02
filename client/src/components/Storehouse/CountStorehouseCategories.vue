<template>
  <div
    v-show="list.length"
    data-vue-component-name="CountStorehouseCategories"
  >
    <div class="text-center text-bold text-uppercase q-mt-lg">
      {{ title }}
    </div>
    <q-list
      dense
      bordered
      separator
    >
      <q-item
        v-for="(item, index) in localList"
        :key="index"
      >
        <q-item-section>
          <q-item-label>{{ item.name }}</q-item-label>
        </q-item-section>
        <q-item-section>
          <q-item-label>{{ item.place | numberFormatFilter }}</q-item-label>
        </q-item-section>
        <q-item-section>
          <q-item-label>{{ item.kg | numberFormatFilter }}</q-item-label>
        </q-item-section>
      </q-item>
      <!--      FOOTER-->
      <q-item>
        <q-item-section>
          <q-item-label>{{ footer.name }}</q-item-label>
        </q-item-section>
        <q-item-section>
          <q-item-label class="text-bold">
            <q-badge>{{ footer.place | numberFormatFilter }}</q-badge>
          </q-item-label>
        </q-item-section>
        <q-item-section>
          <q-item-label class="text-bold">
            <q-badge>{{ footer.kg | numberFormatFilter }}</q-badge>
          </q-item-label>
        </q-item-section>
      </q-item>
    </q-list>
  </div>
</template>

<script>
    import { setCategoriesStoreHouseData } from 'src/utils/FrequentlyCalledFunctions';

    export default {
        name: 'CountStorehouseCategories',
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
                localList: [],
                footer: {},
            };
        },
        watch: {
            list: {
                handler: function setList(val) {
                    if (!_.isEmpty(val)) {
                        this.setLocalList(val, null);
                    } else {
                        this.localList = [];
                    }
                },
                immediate: true,
            },
        },
        methods: {
            setLocalList(list, transporterPriceData) {
                const { categoriesList, footer } = setCategoriesStoreHouseData(list, transporterPriceData);
                this.localList = categoriesList;
                this.footer = footer;
            },
        },
    };
</script>
