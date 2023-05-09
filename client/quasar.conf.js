// Configuration for your app
// https://quasar.dev/quasar-cli/quasar-conf-js
const webpack = require('webpack');

module.exports = function ctxFunc(ctx) {
    return {
        boot: [
            'i18n',
            'axios',
        ],

        css: [
            'app.sass',
        ],

        extras: [
            'roboto-font', // optional, you are not bound to it
            'material-icons', // optional, you are not bound to it
        ],

        framework: {
            lang: 'ru', // Quasar language

            // Quasar plugins
            plugins: [
                'Loading',
                'LocalStorage',
                'Dialog',
                'Notify',
            ],
            config: {
                screen: {
                    bodyClasses: true,
                },
            },
        },

        build: {
            scopeHoisting: true,
            extendWebpack(cfg) {
                cfg.plugins.push(
                  new webpack.ProvidePlugin({
                      devlog: 'src/tools/devlog',
                  }),
                );
            },
            env: {
                API: ctx.dev
                  ? 'https://server007cargo.net.ua/'
                  : 'https://server007cargo.net.ua/',
            },
        },

        devServer: {
            open: false, // opens browser window automatically
        },

        ssr: {
            pwa: false,
        },
    };
};
