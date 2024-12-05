const socketIo = require('socket.io');
let io = null;

exports.io = () => {
    return io;
};

exports.init = server => {
    io = socketIo(server);
};
