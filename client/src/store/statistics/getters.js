export const getStatistics = (state) => {
  const arr = [];
  _.forEach(state.statistics.month, (item) => {
    const currentMonth = new Date(item.date).getMonth() + 1;
    const currentYear = new Date(item.date).getFullYear();
    const obj = _.assign({}, item);
    obj.date = new Date(item.date).toISOString().slice(0, 7);
    obj.data = _.orderBy(_.filter(state.statistics.statistics, (elem) => currentMonth === new Date(elem.created_at).getMonth() + 1 && currentYear === new Date(elem.created_at).getFullYear()), (elem) => new Date(elem.created_at), 'desc');
    arr.push(obj);
  });
  return arr;
};
