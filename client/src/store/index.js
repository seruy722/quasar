import { createStore } from 'vuex';
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
import questions from './questions';
import statistics from './statistics';

export default function initStore() {
  const Store = createStore({
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
      questions,
      statistics,
    },

    strict: process.env.DEBUGGING,
  });

  return Store;
}
