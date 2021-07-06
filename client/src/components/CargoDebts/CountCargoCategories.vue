<template>
  <div
    v-show="list.length"
    data-vue-component-name="CountCategories"
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
        <q-item-section
v-if="item.for_kg !== undefined"
class="cursor-pointer"
>
          <q-item-label class="text-red text-bold">
{{ item.for_kg | numberFormatFilter }}
</q-item-label>
        </q-item-section>
        <q-item-section v-if="item.sum !== undefined">
          <q-item-label class="text-bold">
{{ item.sum | numberFormatFilter }}
          </q-item-label>
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
        <q-item-section v-if="footer.for_kg > -1">
          <q-item-label />
        </q-item-section>
        <q-item-section>
          <q-item-label class="text-bold">
            <q-badge>{{ footer.sum | numberFormatFilter }}</q-badge>
          </q-item-label>
        </q-item-section>
      </q-item>
    </q-list>
  </div>
</template>

<script>
    export default {
        name: 'CountCategories',
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
            async setLocalList(list, transporterPriceData) {
                const { setCargoCategoriesData } = await import('src/utils/FrequentlyCalledFunctions');
                const { categoriesList, footer } = setCargoCategoriesData(list, transporterPriceData);
                this.localList = categoriesList;
                this.footer = footer;
            },
        },
    };
</script>
