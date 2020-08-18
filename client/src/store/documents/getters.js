import { formatToDotDate } from 'src/utils/formatDate';

export const getDocuments = ((state) => {
  const { documents } = state;
  const allDates = _.uniq(_.map(_.map(documents, 'created_at'), (date) => formatToDotDate(date)));
  const res = [];
  _.forEach(allDates, (date) => {
    const comments = _.filter(documents, (comment) => date === formatToDotDate(comment.created_at));
    res.push({
      date,
      comments,
    });
  });

  return res;
});
