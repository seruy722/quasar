<template>
  <div class="q-pa-md q-gutter-sm">
    <q-input
      ref="filter"
      filled
      v-model="filter"
      label="Search - only filters labels that have also '(*)'"
    >
      <template v-slot:append>
        <q-icon v-if="filter !== ''" name="clear" class="cursor-pointer" @click="resetFilter" />
      </template>
    </q-input>

    <q-tree
      :nodes="simple"
      node-key="label"
      :filter="filter"
      :filter-method="myFilterMethod"
    >
      <template #default-header="prop">
        <div class="row items-center">
          <!--          <q-icon :name="prop.node.icon || 'share'" color="orange" size="28px" class="q-mr-sm" />-->
          <div class="text-weight-bold text-primary">{{ prop.node.label || prop.node.category_name }}</div>
        </div>
      </template>

      <template #default-body="prop">
        <span v-else class="text-weight-light text-black">{{ prop.node.code_place }}</span>
      </template>
    </q-tree>
  </div>
</template>

<script>
export default {
  name: 'Tree',
  props: {
    enterData: {
      type: Object,
      require: true,
    },
  },
  data() {
    return {
      simple: [],
      filter: '',
    };
  },
  watch: {
    enterData(val) {
      this.simple = this.getNeedData(val);
    },
  },
  created() {
    devlog.log('created', this.enterData);
    this.simple = this.getNeedData(this.enterData);
  },
  methods: {
    myFilterMethod(node, filter) {
      devlog.log('NODE', node);
      const filt = filter.toLowerCase();
      return node.label && node.label.toLowerCase()
        .indexOf(filt) > -1 && node.label.toLowerCase();
    },
    resetFilter() {
      this.filter = '';
      this.$refs.filter.focus();
    },
    getNeedData(data) {
      const grouping = _.groupBy(data, 'fax_name');
      const arr = [];
      _.forEach(grouping, (item, index) => {
        arr.push({
          label: index,
          children: item,
        });
      });
      return arr;
    },
  }
};
</script>
