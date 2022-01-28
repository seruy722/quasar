// import { fullDate } from 'src/utils/formatDate';
// import { setMethodLabel, setStatusLabel } from 'src/utils/FrequentlyCalledFunctions';
import { sortSuper } from 'src/utils/sort';

export const getTransfersClient = (state) => state.transfersClient;
export const getTransfers = (state) => sortSuper(state.transfers, 'created_at', true);

// export const getTransfers = (state) => {
//   const dd = _.map(setMethodLabel(setStatusLabel(_.cloneDeep(state.transfers))), (item) => _.assign({}, item, {
//     created_at_date: fullDate(item.created_at),
//     issued_by_date: fullDate(item.issued_by),
//   }));
//   sortSuper(dd, 'created_at', true);
//   return dd;
// };

export const getTransfersData = (state) => state.transfersData;
