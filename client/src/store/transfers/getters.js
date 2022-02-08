import { sortSuper } from 'src/utils/sort';

export const getTransfersClient = (state) => state.transfersClient;
export const getTransfers = (state) => sortSuper(state.transfers, 'created_at', true);
export const getSearchData = (state) => sortSuper(state.searchData, 'created_at', true);
export const getTransfersData = (state) => state.transfersData;
