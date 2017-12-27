let http = require('http');

let server = http.createServer(function (req, res) {
  res.writeHead(200);
  res.end('Hello Http');
});
server.listen(3000);