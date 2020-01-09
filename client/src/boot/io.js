import io from 'socket.io-client';

// "async" is optional
export default ({ Vue }) => {
  Vue.prototype.$io = io;
};
