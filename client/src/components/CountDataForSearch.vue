<template>
  <div
    v-show="list.length"
    data-vue-component-name="CountDataForSearch"
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
          <q-item-label>{{ numberFormat(item.place) }}</q-item-label>
        </q-item-section>
        <q-item-section>
          <q-item-label>{{ numberFormat(item.kg) }}</q-item-label>
        </q-item-section>
        <q-item-section
          v-if="item.for_kg !== undefined"
          class="cursor-pointer"
        >
          <q-item-label class="text-red text-bold">
            {{ numberFormat(item.for_kg) }}
          </q-item-label>
        </q-item-section>
        <q-item-section v-if="item.sum !== undefined">
          <q-item-label class="text-bold">
            {{ numberFormat(item.sum) }}
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
            <q-badge>{{ numberFormat(footer.place) }}</q-badge>
          </q-item-label>
        </q-item-section>
        <q-item-section>
          <q-item-label class="text-bold">
            <q-badge>{{ numberFormat(footer.kg) }}</q-badge>
          </q-item-label>
        </q-item-section>
        <q-item-section v-if="footer.for_kg > -1">
          <q-item-label />
        </q-item-section>
        <q-item-section>
          <q-item-label class="text-bold">
            <q-badge>{{ numberFormat(footer.sum) }}</q-badge>
          </q-item-label>
        </q-item-section>
      </q-item>
    </q-list>
  </div>
</template>

<script>
import { numberFormat } from 'src/utils';

export default {
  name: 'CountDataForSearch',
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
    numberFormat,
    async setLocalList(list, transporterPriceData) {
      const { setCargoCategoriesData } = await import('src/utils/FrequentlyCalledFunctions');
      const {
        categoriesList,
        footer
      } = setCargoCategoriesData(list, transporterPriceData);
      this.localList = categoriesList;
      this.footer = footer;
    },
  },
};
</script>
