<script>
import { Line } from 'vue-chartjs';

export default {
  extends: Line,
  props: {
    chartdata: {
      type: Object,
      default: null,
    },
    options: {
      type: Object,
      default: null,
    },
  },
  // watch: {
  //   /* eslint-disable */
  //   chartdata() {
  //     devlog.log('WWWWW', this.$data._chart.update);
  //     this.$data._chart.update();
  //   },
  // },
  mounted() {
    this.setChart(this.chartdata, this.options);
  },
  methods: {
    setChart(chartdata, options) {
      if (!_.isEmpty(chartdata)) {
        this.renderChart(chartdata, options);
      }
    },
  },
};
</script>
