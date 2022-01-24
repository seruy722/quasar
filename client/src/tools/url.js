import { get } from 'lodash';

const prefix = 'api';

const urls = {
  login: `${prefix}/login`,
  register: `${prefix}/register`,
  userModel: `${prefix}/user`,
  usersWithRoles: `${prefix}/users-with-roles`,
  codes: `${prefix}/codes`,
  codesIds: `${prefix}/codes-by-ids`,
  codesAssistant: `${prefix}/codes-assistant`,
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
  searchClientData: `${prefix}/client-data`,
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
  transfersSearch: `${prefix}/transfers-search`,
  getNewAndChangedTransfers: `${prefix}/get-new-transfers`,
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
  allCargoData: `${prefix}/get-all-cargo-data`,
  updateCargoPaymentEntry: `${prefix}/update-cargo-payment-entry`,
  updateCargoDebtEntry: `${prefix}/update-cargo-debt-entry`,
  createCargoPaymentEntry: `${prefix}/create-cargo-payment-entry`,
  createCargoDebtEntry: `${prefix}/create-cargo-debt-entry`,
  deleteCargoEntry: `${prefix}/delete-cargo-entry`,
  generalCargoData: `${prefix}/general-cargo-data`,
  addTransfersToDebts: `${prefix}/add-transfers-to-debts`,
  createDebtPaymentEntry: `${prefix}/create-debt-payment-entry`,
  updateDebtPaymentEntry: `${prefix}/update-debt-payment-entry`,
  deleteDebtEntry: `${prefix}/delete-debt-entry`,
  createDebtEntry: `${prefix}/create-debt-entry`,
  updateDebtEntry: `${prefix}/update-debt-entry`,
  exportCargoData: `${prefix}/export-cargo-data`,
  exportDetailCargoData: `${prefix}/export-detail-cargo-data`,
  exportDebtsData: `${prefix}/export-debts-data`,
  exportG: `${prefix}/export-cargo`,
  exportGeneralDebtsData: `${prefix}/export-general-debts-data`,
  exportGeneralDataByClients: `${prefix}/export-general-data-by-clients`,
  cargoPayEntry: `${prefix}/cargo-pay-entry`,
  debtPayEntry: `${prefix}/debt-pay-entry`,
  cargoPaymentsAll: `${prefix}/cargo-payments-all`,
  debtsPaymentsAll: `${prefix}/debts-payments-all`,
  getTasks: `${prefix}/get-tasks`,
  storeTask: `${prefix}/store-task`,
  usersList: `${prefix}/users-list`,
  deleteTasks: `${prefix}/delete-tasks`,
  updateTask: `${prefix}/update-task`,
  storeComment: `${prefix}/store-comment`,
  getTaskComments: `${prefix}/get-task-comments`,
  getDocumentsComments: `${prefix}/get-documents-comments`,
  paymentArrears: `${prefix}/get-payment-arrears`,
  removeCommentFile: `${prefix}/remove-comment-file`,
  deleteComments: `${prefix}/delete-comments`,
  exportCustomersWhoLeft: `${prefix}/export-customers-who-left`,
  exportCustomersWhoLeftBrand: `${prefix}/export-customers-who-left-brand`,
  addFileToComment: `${prefix}/add-file-to-comment`,
  updateCommentData: `${prefix}/update-comment-data`,
  storeQuestion: `${prefix}/store-question`,
  getQuestions: `${prefix}/get-questions`,
  deleteQuestions: `${prefix}/delete-questions`,
  updateQuestion: `${prefix}/update-question`,
  addQuestionComment: `${prefix}/add-question-comment`,
  exportFaxDataOdessaKharkov: `${prefix}/export-fax-admin-data-odessa-kharkov`,
  exportFaxDataOdessa: `${prefix}/export-fax-admin-data-odessa`,
  closeUsersAccess: `${prefix}/close-users-access`,
  getFaxDataHistory: `${prefix}/fax-data-history`,
  exportReportOdessaData: `${prefix}/export-report-odessa-data`,
  setDeliveredFaxData: `${prefix}/set-delivered-fax-data`,
  getNotDeliveredCargo: `${prefix}/get-not-delivered-cargo`,
  setDeliveredCargo: `${prefix}/set-delivered-cargo`,
  getStorehousePeriodData: `${prefix}/get-storehouse-period-data`,
  allCargo: `${prefix}/all-cargo`,
  registerClient: `${prefix}/register-client`,
  registerClientCode: `${prefix}/register-client-code`,
  changePasswordCode: `${prefix}/change-password-code`,
  changePassword: `${prefix}/change-password`,
  registerClientCodeRegister: `${prefix}/register-client-register`,
  getClientData: `${prefix}/get-client-data`,
  getClientStorehouseData: `${prefix}/get-client-storehouse-data`,
  exportClientsGeneralDataOdessa: `${prefix}/export-clients-general-data-odessa`,
  transfersClient: `${prefix}/transfers-client`,
  storeTransfersClient: `${prefix}/store-transfers-client`,
  // addDevice: `${prefix}/add-device`,
  updatePlayerId: `${prefix}/update-player-id`,
  createNotification: `${prefix}/create-notification`,
  exportFaxModerMailData: `${prefix}/export-fax-moder-mail-data`,
  getExpenses: `${prefix}/get-expenses`,
  addExpense: `${prefix}/add-expense`,
  getStatistics: `${prefix}/get-statistics`,
  sendSms: `${prefix}/send-sms`,
  getArchiveSms: `${prefix}/get-archive-sms`,
  getSmsBalance: `${prefix}/sms-balance`,
  statisticsForCodes: `${prefix}/statistics-for-codes`,
  removeCodesComments: `${prefix}/remove-codes-comments`,
  addCodesComments: `${prefix}/add-codes-comments`,
  subscribe: `${prefix}/subscribe`,
  areas: `${prefix}/areas`,
  test: `${prefix}/test`,
  serverUrl: 'https://server007cargo.net.ua',
  getTransferCodeCommission: `${prefix}/get-transfer-code-commission`,
  axiosData: {
    baseURL: process.env.API,
  },
  drafts: {
    codesWithoutInfo: `${prefix}/export-codes-without-customers`,
    brandsCustomers: `${prefix}/export-brands-customers`,
  },
};

export const getUrl = (value) => get(urls, value);
