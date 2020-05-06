import { getUrl } from 'src/tools/url';
import {
  prepareHistoryData,
  setFormatedDate,
} from 'src/utils/FrequentlyCalledFunctions';

export default {
  data() {
    return {
      dialogHistory: false,
      storehouseHistoryData: {
        cols: {},
        historyData: [],
      },
    };
  },
  methods: {
    /**
     * Получение истории изменения записи на складе
     * @param id
     * @param cols
     * @return {Promise<void>}
     */
    async getStorehouseDataHistory(id, cols) {
      this.$q.loading.show();
      await this.$axios.get(`${getUrl('storehouseDataHistory')}/${id}`)
        .then(({ data: { storehouseDataHistory } }) => {
          if (!_.isEmpty(storehouseDataHistory)) {
            this.$q.loading.hide();
            this.dialogHistory = true;
            this.storehouseHistoryData = prepareHistoryData(cols, storehouseDataHistory);
            this.storehouseHistoryData.cols.user_name = 'Пользователь';
            this.storehouseHistoryData.cols.in_cargo = 'Загружен в карго';
            this.storehouseHistoryData.cols.code_place = 'Код';
            this.storehouseHistoryData.cols.fax_name = 'Перемещен в факс';
            this.storehouseHistoryData.historyData = setFormatedDate(this.storehouseHistoryData.historyData, ['created_at']);
            devlog.log('storehouseDataHistory', storehouseDataHistory);
          } else {
            this.$q.loading.hide();
            this.showNotif('info', 'По этой записи нет истории.', 'center');
          }
        })
        .catch(() => {
          this.$q.loading.hide();
          devlog.error('Ошибка при получении данных истории.');
        });
    },
  },
};
