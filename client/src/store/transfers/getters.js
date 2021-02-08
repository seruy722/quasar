import { fullDate } from 'src/utils/formatDate';
import { setMethodLabel, setStatusLabel } from 'src/utils/FrequentlyCalledFunctions';

export const getTransfersClient = (state) => state.transfersClient;
export const getTransfers = (state) => _.map(setMethodLabel(setStatusLabel(state.transfers)), (item) => _.assign({}, item, {
  created_at: fullDate(item.created_at),
  issued_by: fullDate(item.issued_by),
}));
