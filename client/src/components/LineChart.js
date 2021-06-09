import { Line, mixins } from 'vue-chartjs';

const { reactiveProp } = mixins;

export default {
  extends: Line,
  mixins: [reactiveProp],
  props: ['options'],
  mounted() {
    // this.chartData создаётся внутри миксина.
    // Если вы хотите передать опции, создайте локальный объект options
    console.log('this.chartData', this.chartData);
    console.log('this.options', this.options);
    this.renderChart(this.chartData, this.options);
  },
};
