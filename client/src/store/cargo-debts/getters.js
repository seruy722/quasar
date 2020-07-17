import { toDate } from 'src/utils/formatDate';
import { combineCargoData } from 'src/utils/FrequentlyCalledFunctions';

// export const getCargo = (state) => _.orderBy(state.cargo, (item) => new Date(toDate(item.created_at)), 'desc');
export const getCargo = (state) => _.orderBy(combineCargoData(state.cargo), (item) => new Date(toDate(item.created_at)), 'desc');
export const getCurrentCodeClientId = (state) => state.currentCodeClientId;
