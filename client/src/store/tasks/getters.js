import { fullDate } from 'src/utils/formatDate';

export const getTasks = ((state) => _.map(_.orderBy(state.tasks, (item) => new Date(item.created_at), 'desc'), (item) => _.assign({}, item, { created_at: fullDate(item.created_at) })));
