import _ from 'lodash';

const prefix = 'api';

const urls = {
  login: `${prefix}/login`,
  register: `${prefix}/register`,
  userModel: `${prefix}/user`,
  usersWithRoles: `${prefix}/users-with-roles`,
  codes: `${prefix}/codes`,
  uploadCodes: `${prefix}/upload-codes`,
  uploadCargoTable: `${prefix}/upload-cargo-table`,
  uploadDebtsTable: `${prefix}/upload-debts-table`,
  uploadSkladData: `${prefix}/upload-sklad-table`,
  uploadFaxData: `${prefix}/upload-fax-data-table`,
  faxes: `${prefix}/faxes`,
  updateFaxes: `${prefix}/update-faxes`,
  updateFaxCombineData: `${prefix}/update-fax-combine-data`,
  deleteFax: `${prefix}/delete-faxes`,
  faxData: `${prefix}/fax-data`,
  moveFromStorehouseToFax: `${prefix}/move-from-storehouse-to-fax`,
  exportFaxModerData: `${prefix}/export-fax-moder-data`,
  exportFaxAdminData: `${prefix}/export-fax-admin-data`,
  updateFaxData: `${prefix}/update-fax-data`,
  updateAllPriceInFaxData: `${prefix}/update-all-price-in-fax-data`,
  exportFaxData: `${prefix}/export-fax-data`,
  categories: `${prefix}/categories`,
  saveCategoriesPrice: `${prefix}/save-categories-price`,
  addCategory: `${prefix}/add-category`,
  updateTransporterPriceData: `${prefix}/update-transporter-price-data`,
  updateTransporterFaxesPrice: `${prefix}/update-transporter-faxes-price`,
  addFax: `${prefix}/add-fax`,
  fax: `${prefix}/fax`,
  combineFaxes: `${prefix}/combine-faxes`,
  updatePricesInFax: `${prefix}/update-prices-in-fax`,
  uploadFaxToCargo: `${prefix}/upload-to-cargo`,
  faxHistory: `${prefix}/fax-history`,
  getNewFax: `${prefix}/get-new-fax`,
  transfersStoreFax: `${prefix}/set-transfers-storehouse-fax`,
  codeList: `${prefix}/codes-list`,
  clientData: `${prefix}/client-data`,
  codeExist: `${prefix}/check-code-exist`,
  exportCustomers: `${prefix}/export-customers`,
  // getNewCodes: `${prefix}/get-new-codes`,
  codeHistory: `${prefix}/get-code-history`,
  validCustomerData: `${prefix}/valid-customer-data`,
  storeCode: `${prefix}/store-code`,
  storeCustomers: `${prefix}/store-customers`,
  updateCustomer: `${prefix}/update-customer`,
  destroyCustomerEntry: `${prefix}/destroy-customer`,
  getCustomerHistory: `${prefix}/get-customer-history`,
  transporters: `${prefix}/transporters`,
  transporterPriceData: `${prefix}/transporter-price-data`,
  addTransporter: `${prefix}/add-transporter`,
  transports: `${prefix}/transports`,
  storehouseData: `${prefix}/store-house-data`,
  addStorehouseData: `${prefix}/add-storehouse-data`,
  updateStorehouseData: `${prefix}/update-storehouse-data`,
  destroyStorehouseData: `${prefix}/destroy-storehouse-data`,
  exportStorehouseData: `${prefix}/export-storehouse-data`,
  storehouseDataHistory: `${prefix}/storehouse-data-history`,
  getNewStorehouseData: `${prefix}/get-new-storehouseData`,
  cities: `${prefix}/cities`,
  thingsList: `${prefix}/thing-list`,
  shopsList: `${prefix}/shop-names`,
  deliveryMethodsList: `${prefix}/delivery-methods-list`,
  transfers: `${prefix}/transfers`,
  updateTransfers: `${prefix}/update-transfers`,
  storeTransfers: `${prefix}/store-transfers`,
  getNewTransfers: `${prefix}/get-new-transfers`,
  exportTransfers: `${prefix}/export-transfers`,
  transfersHistory: `${prefix}/transfers-history`,
  rolesList: `${prefix}/roles-list`,
  addRole: `${prefix}/add-role`,
  deleteRoleFromUser: `${prefix}/delete-role-from-user`,
  assignRoleToUsers: `${prefix}/assign-role-to-users`,
  deleteRole: `${prefix}/delete-role`,
  permissionList: `${prefix}/permission-list`,
  addPermission: `${prefix}/add-permission`,
  deletePermission: `${prefix}/delete-permission`,
  assignPermissionToRoleAndUser: `${prefix}/assign-permission-to-role-and-user`,
  deletePermissionFromRoleOrUser: `${prefix}/delete-permission-from-role-or-user`,
  getCodesPrices: `${prefix}/get-codes-prices`,
  addCodePrice: `${prefix}/add-code-price`,
  deleteCodePrice: `${prefix}/delete-code-price`,
  updateCodePrice: `${prefix}/update-code-price`,
  getCodePriceHistory: `${prefix}/get-code-price-history`,
  getNewCodesPrices: `${prefix}/get-new-codes-prices`,
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
