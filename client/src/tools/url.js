import _ from 'lodash';

const urls = {
    login: '/api/login',
    register: '/api/register',
    userModel: '/api/user',
    codes: '/api/codes',
    uploadCodes: '/api/upload-codes',
    uploadCargoTable: '/api/upload-cargo-table',
    uploadDebtsTable: '/api/upload-debts-table',
    uploadSkladData: '/api/upload-sklad-table',
    uploadFaxData: '/api/upload-fax-data-table',
    faxes: '/api/faxes',
    updateFaxes: '/api/update-faxes',
    deleteFax: '/api/delete-faxes',
    faxData: 'api/fax-data',
    updateFaxData: 'api/update-fax-data',
    updateAllPriceInFaxData: '/api/update-all-price-in-fax-data',
    exportFaxData: 'api/export-fax-data',
    categories: 'api/categories',
    updateTransporterPriceData: 'api/update-transporter-price-data',
    addFax: '/api/add-fax',
    codeList: '/api/codes-list',
    clientData: '/api/client-data',
    codeExist: '/api/check-code-exist/',
    validCustomerData: '/api/valid-customer-data',
    storeCode: '/api/store-code',
    storeCustomers: '/api/store-customers',
    transporters: '/api/transporters',
    transports: '/api/transports',
    axiosData: {
        baseURL: 'http://sp.com.ua',
        // headers: {
        //     Accept: 'application/json',
        // },
    },
    drafts: {
        codesWithoutInfo: '/api/export-codes-without-customers',
    },
};

export const getUrl = (value => _.get(urls, value));
