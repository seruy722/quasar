import { fullDate } from "src/utils/formatDate";
import { combineCargoData } from "src/utils/FrequentlyCalledFunctions";

// export const getCargo = (state) => _.map(combineCargoData(state.cargo), (item) => _.assign({}, item, { created_at: fullDate(item.created_at) }));
export const getCargo = (state) =>
  _.map(
    _.orderBy(
      combineCargoData(state.cargo),
      (item) => new Date(item.created_at),
      "desc"
    ),
    (item) =>
      _.assign({}, item, {
        created_at: fullDate(item.created_at),
        chang: item.created_at,
      })
  );
export const getCurrentCodeClientId = (state) => state.currentCodeClientId;
export const getDebts = (state) =>
  _.map(
    _.orderBy(state.debts, (item) => new Date(item.created_at), "desc"),
    (item) =>
      _.assign({}, item, {
        created_at: fullDate(item.created_at),
        chang: item.created_at,
      })
  );
export const getCargoForSearch = (state) => state.cargoForSearch;
export const getDebtsForSearch = (state) => state.debtsForSearch;
export const getGeneralData = (state) => state.generalData;
