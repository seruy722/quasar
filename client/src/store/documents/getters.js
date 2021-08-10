import { fullDate } from 'src/utils/formatDate';

export const getDocuments = (({ documents }) => _.map(_.orderBy(documents, (item) => new Date(item.created_at), 'desc'), (doc) => _.assign({}, doc, { formatDate: fullDate(doc.created_at) })));
