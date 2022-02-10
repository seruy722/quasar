// Configuration for your app
// https://quasar.dev/quasar-cli/quasar-conf-js
const webpack = require('webpack');

module.exports = function ctxFunc(ctx) {
    return {
        // app boot file (/src/boot)
        // --> boot files are part of "main.js"
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
            // iconSet: 'ionicons-v4',
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
            // vueRouterMode: 'history',
            // vueCompiler: true,
            // gzip: true,
            // analyze: true,
            // extractCSS: false,
            extendWebpack(cfg) {
                cfg.plugins.push(
                  new webpack.ProvidePlugin({
                      devlog: 'src/tools/devlog',
                  }),
                );
            },
            env: {
                API: ctx.dev
                  ? 'http://sp.com.ua/'
                  : 'https://server007cargo.net.ua/',
            },
        },

        devServer: {
            // https: true,
            // port: 8080,
            open: false, // opens browser window automatically
        },

        ssr: {
            pwa: false,
        },
    };
};
