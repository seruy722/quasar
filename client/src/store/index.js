import Vue from 'vue';
import Vuex from 'vuex';

import auth from './auth';
import faxes from './faxes';
import category from './category';
import transporter from './transporter';
import transport from './transport';
import storehouse from './storehouse';
import cities from './cities';
import thingsList from './things-list';
import shopsList from './shop-list';
import transfers from './transfers';
import codes from './codes';
import roles from './roles';
import permissions from './permissions';
import codesPrices from './codes-prices';
import deliveryMethods from './delivery-methods-list';
import cargoDebts from './cargo-debts';
import settings from './settings';
import tasks from './tasks';
import documents from './documents';

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
      transporter,
      transport,
      storehouse,
      cities,
      thingsList,
      shopsList,
      transfers,
      codes,
      roles,
      permissions,
      codesPrices,
      deliveryMethods,
      cargoDebts,
      settings,
      tasks,
      documents,
    },

    // enable strict mode (adds overhead!)
    // for dev mode only
    strict: process.env.DEV,
  });

  return Store;
}
