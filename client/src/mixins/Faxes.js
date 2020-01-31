import { countSumCollection, round, numberFormat } from 'src/utils/index';

export default {
  computed: {
    categories() {
      return this.$store.getters['category/getCategories'];
    },
    clientCodes() {
      return this.$store.getters['clientCodes/getCodes'];
    },
    thingsList() {
      return this.$store.getters['thingsList/getThingsList'];
    },
    shopsList() {
      return this.$store.getters['shopsList/getShopsList'];
    },
  },
  // created() {
  //   this.getCategories();
  //   this.getCustomerCode();
  //   this.getThingsList();
  //   this.getShopsList();
  // },
  methods: {
    addToUpdateArray(item, key) {
      const add = ((elem, value) => {
        const duplicateIndex = _.findIndex(this[value], { id: elem.id });
        if (duplicateIndex !== -1) {
          this[value].splice(duplicateIndex, 1, elem);
        } else {
          this[value].push(elem);
        }
      });

      if (_.has(item, 'arr')) {
        _.forEach(_.get(item, 'arr'), (elem) => {
          add(elem, key);
        });
      } else {
        add(item, key);
      }
    },
    prepareFaxTableData(data) {
      const newArr = [];
      _.forEach(data, (value) => {
        if (_.isObject(value)) {
          _.forEach(value, (item) => {
            _.forEach(item, (elem) => {
              _.forEach(elem, (obj) => {
                _.set(obj, 'edit', false);
                _.set(obj, 'dialog', false);
              });
              const obj2 = {};
              _.assign(obj2, _.first(elem), {
                arr: elem,
                kg: this.countSumObjects(elem, 'kg'),
                place: this.countSumObjects(elem, 'place'),
                edit: false,
                sum: 0,
              });
              // obj2.sum = this.roundNumber(obj2.for_kg * obj2.kg + obj2.for_place * obj2.place);
              newArr.push(obj2);
            });
          });
        }
      });
      // devlog.log('newArr', newArr);
      this.setFaxDataSum(newArr);
      return newArr;
    },
    setFaxDataSum(data) {
      _.forEach(data, (item) => {
        item.sum = this.roundNumber(item.for_kg * item.kg + item.for_place * item.place);
      });
    },
    countSumObjects(arr, key) {
      return countSumCollection(arr, key);
    },
    roundNumber(num, precision) {
      return round(num, precision);
    },
    sumObjectForCategories(arr) {
      return {
        name: '',
        kg: this.countSumObjects(arr, 'kg'),
        place: this.countSumObjects(arr, 'place'),
      };
    },
    getClientCode() {
      if (_.isEmpty(this.$store.getters['clientCodes/getCodes'])) {
        this.$store.dispatch('clientCodes/getCodes');
      }
    },
    // ФОРМИРОВАНИЕ ДАННЫХ ТАБЛИЦЫ КАТЕГОРИЙ
    categoriesPreparation(data, transporterPriceData) {
      const uniq = _.uniqBy(_.map(data, ({ category, category_id: categoryID, fax_id: faxID }) => ({
        name: category,
        id: categoryID,
        faxID,
      })), 'name');
      const arr = [];
      _.forEach(uniq, ({ name, id: categoryID, faxID }) => {
        const kg = this.countSumObjects(_.filter(this.faxDataList, { category_id: categoryID }), ({ kg: kilo }) => kilo);
        const forKg = _.get(_.find(transporterPriceData, { category_id: categoryID }), 'category_price');
        arr.push({
          name,
          place: this.countSumObjects(_.filter(this.faxDataList, { category_id: categoryID }), ({ place }) => place),
          kg,
          category_price: forKg || 0,
          sum: this.roundNumber(kg * forKg) || 0,
          category_id: categoryID,
          fax_id: faxID,
        });
      });
      _.orderBy(arr, 'place', 'desc');

      return arr;
    },
    // ВЫВОД КОНЕЧНЫХ СУМ ПО КАТЕГОРИЯМ, ФАКСУ И ИХ РАЗНИЦЕ
    categoriesSumData(arr) {
      const categoriesSumObj = {
        place: this.countSumObjects(arr, 'place'),
        kg: this.countSumObjects(arr, 'kg'),
        type: 'categoriesSumObj',
        sum: this.roundNumber(this.countSumObjects(arr, 'sum')),
      };
      const faxDataListSumObj = {
        type: 'faxDataListSumObj',
        sum: this.roundNumber(this.countSumObjects(this.faxDataList, 'sum')),
      };
      const differenceSumObj = {
        type: 'differenceSumObj',
        sum: this.roundNumber(faxDataListSumObj.sum - categoriesSumObj.sum),
      };
      arr.push(categoriesSumObj, faxDataListSumObj, differenceSumObj);

      return arr;
    },
    // ОБНОВЛЕНИЕ СУММ КАТЕГОРИЙ
    updateCategories(arr) {
      const faxCategories = _.slice(arr, 0, -3);
      _.forEach(faxCategories, (item) => {
        item.sum = this.roundNumber(item.kg * item.category_price);
      });

      return faxCategories;
    },
    getCategories() {
      if (_.isEmpty(this.categories)) {
        this.$store.dispatch('category/getCategories');
      }
    },
    getCustomerCode() {
      if (_.isEmpty(this.clientCodes)) {
        this.$store.dispatch('clientCodes/getCodes');
      }
    },
    getThingsList() {
      if (_.isEmpty(this.thingsList)) {
        this.$store.dispatch('thingsList/setThingsList');
      }
    },
    getShopsList() {
      if (_.isEmpty(this.shopsList)) {
        this.$store.dispatch('shopsList/setShopsList');
      }
    },
    formatNumber(num) {
      return numberFormat(num);
    },
  },
};
