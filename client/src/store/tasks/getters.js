import { fullDate } from 'src/utils/formatDate';

export const getTasks = (({ tasks }) => _.map(_.orderBy(tasks, (item) => new Date(item.created_at), 'desc'), (item) => _.assign({}, item, { created_at: fullDate(item.created_at) })));
export const getTaskComments = ((state) => _.map(_.orderBy(state.taskComments, (item) => new Date(item.created_at), 'asc'), (item) => _.assign({}, item, { created_at: fullDate(item.created_at) })));
export const getTaskId = (({ taskComments }) => _.get(_.first(taskComments), 'task_id'));
