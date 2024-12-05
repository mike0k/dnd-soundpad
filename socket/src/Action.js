const USocket = require('./Socket');


exports.send = (socket, data) => {
    const io = USocket.io();

    switch (data.action) {
        case 'lobby.update':
            //socket.broadcast.emit('to.client', res.data);
            io.in('lobby').emit('to.client', data);
            break;

        default:
            socket.emit('to.client', data);
    }
};
