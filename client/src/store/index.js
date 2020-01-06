import Vue from 'vue';
import Vuex from 'vuex';

import auth from './auth';
import faxes from './faxes';
import category from './category';
import clientCodes from './client-codes';
import transporter from './transporter';
import transport from './transport';
import storehouse from './storehouse';
import cities from './cities';
import thingsList from './things-list';
import shopsList from './shop-list';
import transfers from './transfers';

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
      clientCodes,
      transporter,
      transport,
      storehouse,
      cities,
      thingsList,
      shopsList,
      transfers,
    },

    // enable strict mode (adds overhead!)
    // for dev mode only
    strict: process.env.DEV,
  });

  return Store;
}
