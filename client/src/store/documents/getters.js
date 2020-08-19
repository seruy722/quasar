import { fullDate } from 'src/utils/formatDate';

export const getDocuments = (({ documents }) => _.map(documents, (doc) => _.assign({}, doc, { formatDate: fullDate(doc.created_at) })));
