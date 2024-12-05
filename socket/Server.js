const express = require('express');
const http = require('http');
const USocket = require('./src/Socket');

const port = 8080;
const app = express();
const server = http.createServer(app);
USocket.init(server);

const UConsumer = require('./src/Consumer');
UConsumer.connect(app, express);

server.listen(port, () => console.log(`Listening on port ${port}`));
