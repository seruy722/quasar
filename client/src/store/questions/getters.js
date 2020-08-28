import { fullDate } from 'src/utils/formatDate';

export const getQuestions = ((state) => _.map(_.orderBy(state.questions, (item) => new Date(item.created_at), 'desc'), (doc) => _.assign({}, doc, { formatDate: fullDate(doc.created_at) })));
