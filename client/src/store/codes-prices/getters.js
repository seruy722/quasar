export const getCodesPrices = ((state) => Object.entries(state.codesPrices)
  .map(([k, v]) => ({ data: v, code: k, id: k })));
