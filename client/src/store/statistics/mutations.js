export const SET_STATISTICS = (state, data) => {
  state.statistics = data;
};

export const ADD_STATISTIC = (state, data) => {
  state.statistics.statistics.unshift(data);
};
