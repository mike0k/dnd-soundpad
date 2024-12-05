const USocket = require('./Socket');
const URoom = require('./Room');

exports.connect = (app, express) => {
    const io = USocket.io();
    io.on('connection', socket => {
        console.log('New client connected: ' + socket.id);
        onConnect(socket);
    });
};

const onConnect = socket => {
    socket.on('disconnect', () => {
        console.log('Client disconnected');
    });

    onRoom(socket);
};

const onRoom = socket => {
    socket.on('room.create', data => {
        URoom.create(socket);
    });

    socket.on('room.leave', data => {
        URoom.leave(socket);
    });

    socket.on('room.join', data => {
        URoom.join(socket);
    });
};

