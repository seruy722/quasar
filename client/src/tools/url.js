import _ from 'lodash';

const prefix = 'api';

const urls = {
  login: `${prefix}/login`,
  register: `${prefix}/register`,
  userModel: `${prefix}/user`,
  codes: `${prefix}/codes`,
  uploadCodes: `${prefix}/upload-codes`,
  uploadCargoTable: `${prefix}/upload-cargo-table`,
  uploadDebtsTable: `${prefix}/upload-debts-table`,
  uploadSkladData: `${prefix}/upload-sklad-table`,
  uploadFaxData: `${prefix}/upload-fax-data-table`,
  faxes: `${prefix}/faxes`,
  updateFaxes: `${prefix}/update-faxes`,
  deleteFax: `${prefix}/delete-faxes`,
  faxData: `${prefix}/fax-data`,
  updateFaxData: `${prefix}/update-fax-data`,
  updateAllPriceInFaxData: `${prefix}/update-all-price-in-fax-data`,
  exportFaxData: `${prefix}/export-fax-data`,
  categories: `${prefix}/categories`,
  addCategory: `${prefix}/add-category`,
  updateTransporterPriceData: `${prefix}/update-transporter-price-data`,
  updateTransporterFaxesPrice: `${prefix}/update-transporter-faxes-price`,
  addFax: `${prefix}/add-fax`,
  codeList: `${prefix}/codes-list`,
  clientData: `${prefix}/client-data`,
  codeExist: `${prefix}/check-code-exist/`,
  validCustomerData: `${prefix}/valid-customer-data`,
  storeCode: `${prefix}/store-code`,
  storeCustomers: `${prefix}/store-customers`,
  transporters: `${prefix}/transporters`,
  addTransporter: `${prefix}/add-transporter`,
  transports: `${prefix}/transports`,
  storehouseData: `${prefix}/store-house-data`,
  addStorehouseData: `${prefix}/add-storehouse-data`,
  updateStorehouseData: `${prefix}/update-storehouse-data`,
  cities: `${prefix}/cities`,
  thingsList: `${prefix}/thingsList`,
  shopsList: `${prefix}/shopsList`,
  transfers: `${prefix}/transfers`,
  updateTransfers: `${prefix}/update-transfers`,
  storeTransfers: `${prefix}/store-transfers`,
  getNewTransfers: `${prefix}/get-new-transfers`,
  exportTransfers: `${prefix}/export-transfers`,
  axiosData: {
    baseURL: process.env.API,
    // 'http://sp.com.ua'
    // headers: {
    //     Accept: 'application/json',
    // },
  },
  drafts: {
    codesWithoutInfo: `${prefix}/export-codes-without-customers`,
    brandsCustomers: `${prefix}/export-brands-customers`,
  },
};

export const getUrl = (value) => _.get(urls, value);
