const http = require('http')
    .Server();
const io = require('socket.io')(http);
const Redis = require('ioredis');

const redis = new Redis();
redis.subscribe('transfer-create');
redis.on('message', (channel, message) => {
    console.log('Message', message);
    console.log('Channel', channel);
    message = JSON.parse(message);
    io.emit(channel, message.event, message.data);
});

http.listen(8080, () => {
    console.log('Server listening on Port: 3000');
});

console.log('sdfsdfsd0');
