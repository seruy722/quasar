// Миксин обработки нажатия клавиши enter на инпуте

export default {
  methods: {
    keyUp({ code }) {
      if (code === 'Enter' || code === 'NumpadEnter') {
        devlog.log('onKeyUpFunc', this);
        if (_.isFunction(this.onKeyUpFunc)) {
          this.onKeyUpFunc();
        } else {
          devlog.log('onKeyUpFunc', this.onKeyUpFunc);
        }
      }
    },
  },
};
