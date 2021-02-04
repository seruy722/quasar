import { fullDate } from 'src/utils/formatDate';

export const getTransfersClient = (state) => state.transfersClient;
export const getTransfers = (state) => _.map(state.transfers, (item) => _.assign({}, item, { created_at: fullDate(item.created_at), issued_by: fullDate(item.issued_by) }));
