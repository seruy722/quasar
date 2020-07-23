<template>
  <div
    v-show="list.length"
    data-vue-component-name="GeneralClientCargoData"
  >
    <div class="text-center text-bold text-uppercase q-mt-lg">
      {{ title }}
    </div>
    <q-list
      dense
      bordered
      separator
    >
      <q-item>
        <q-item-section>
          <q-item-label class="text-bold">
            Сумма
          </q-item-label>
        </q-item-section>
        <q-item-section>
          <q-item-label class="text-bold">
            Скидки
          </q-item-label>
        </q-item-section>
      </q-item>

      <q-item
        v-for="(item, index) in localList"
        :key="index"
      >
        <q-item-section>
          <q-item-label>{{ item.sum | numberFormatFilter }}</q-item-label>
        </q-item-section>
        <q-item-section>
          <q-item-label>{{ item.commission | numberFormatFilter }}</q-item-label>
        </q-item-section>
      </q-item>
    </q-list>
  </div>
</template>

<script>
    export default {
        name: 'GeneralClientCargoData',
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
                    if (!_.isEmpty(val)) {
                        this.setLocalList(val);
                    } else {
                        this.localList = [];
                    }
                },
                immediate: true,
            },
        },
        methods: {
            setLocalList(list) {
                this.localList = [
                    {
                        sum: _.sumBy(list, 'sum'),
                        commission: _.sumBy(list, 'sale'),
                    },
                ];
            },
        },
    };
</script>
