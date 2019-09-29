import Vue from 'vue';
import Vuex from 'vuex';

// import example from './module-example'
import auth from './auth';
import faxes from './faxes';
import category from './category';
import customer from './customer';
import transporter from './transporter';
import transport from './transport';

Vue.use(Vuex);

/*
 * If not building with SSR mode, you can
 * directly export the Store instantiation
 */

export default function (/* { ssrContext } */) {
    const Store = new Vuex.Store({
        modules: {
            auth,
            faxes,
            category,
            customer,
            transporter,
            transport,
        },

        // enable strict mode (adds overhead!)
        // for dev mode only
        strict: process.env.DEV,
    });

    return Store;
}
