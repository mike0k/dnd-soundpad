const USocket = require('./Socket');

exports.create = (socketId) => {
    const io = USocket.io();
    const socket = io.sockets.connected[socketId];

    if(typeof socket !== 'undefined'){
        const roomId = 'test';
        //socket.join(roomId);
        console.log(roomId);
    }

    io.to(socketId).emit('to.client', {
        cb: 'user.create',
        data: null
    });
};

exports.leave = (socketId) => {
    const io = USocket.io();
    io.to(socketId).emit('to.client', {
        cb: 'user.leave',
        data: null
    });
};

exports.join = (socketId) => {
    const io = USocket.io();
    io.to(socketId).emit('to.client', {
        cb: 'user.join',
        data: null
    });
};


