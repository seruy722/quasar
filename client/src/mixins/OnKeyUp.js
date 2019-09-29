// Миксин обработки нажатия клавиши enter на инпуте

export default {
    methods: {
        data() {
            return {
                onKeyUpFunc: () => {},
            };
        },
        keyUp({ code }) {
            if (code === 'Enter' || code === 'NumpadEnter') {
                this.onKeyUpFunc();
            }
        },
    },
};
