<template>
  <div data-vue-component-name="CountCategories">
    <div class="text-center text-bold text-uppercase q-mt-lg">{{ title }}</div>
    <List
      dense
      bordered
      separator
    >
      <ListItem
        v-for="({name, kg, place}, index) in localList"
        :key="index"
      >
        <ItemSection>
          <ItemLabel>{{ name }}</ItemLabel>
        </ItemSection>
        <ItemSection>
          <ItemLabel v-if="name">{{ place | numberFormatFilter }}</ItemLabel>
          <ItemLabel v-else>
            <Badge>{{ place | numberFormatFilter }}</Badge>
          </ItemLabel>
        </ItemSection>
        <ItemSection>
          <ItemLabel v-if="name">{{ kg | numberFormatFilter }}</ItemLabel>
          <ItemLabel v-else>
            <Badge>{{ kg | numberFormatFilter }}</Badge>
          </ItemLabel>
        </ItemSection>
      </ListItem>
    </List>
  </div>
</template>

<script>
    import { setCategoriesStoreHouseData } from 'src/utils/FrequentlyCalledFunctions';

    export default {
        name: 'CountCategories',
        components: {
            List: () => import('src/components/Elements/List/List.vue'),
            ItemSection: () => import('src/components/Elements/List/ItemSection.vue'),
            ItemLabel: () => import('src/components/Elements/List/ItemLabel.vue'),
            ListItem: () => import('src/components/Elements/List/ListItem.vue'),
            Badge: () => import('src/components/Elements/Badge.vue'),
        },
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
            };
        },
        watch: {
            list: {
                handler: function setList(val) {
                    this.localList = setCategoriesStoreHouseData(val);
                },
                immediate: true,
            },
        },
    };
</script>
