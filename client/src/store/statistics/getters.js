export const getStatistics = (state) => {
  const arr = [];
  _.forEach(state.statistics.month, (item) => {
    const currentMonth = new Date(item.date).getMonth() + 1;
    const obj = _.assign({}, item);
    obj.date = new Date(item.date).toISOString().slice(0, 7);
    obj.data = _.orderBy(_.filter(state.statistics.statistics, (elem) => currentMonth === new Date(elem.created_at).getMonth() + 1), (elem) => new Date(elem.created_at), 'desc');
    arr.push(obj);
  });
  return arr;
};
