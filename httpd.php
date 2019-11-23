<?php
if (!extension_loaded("swoole")) {
die("Swoole Extensions isn't loaded");
}
require_once("includes/class.Swoole_Srv.php");

$serverIP = "127.0.0.1";
$serverPORT = 443;
$wwwDir = "./htdocs";

 
 
$server = new swoole_websocket_server($serverIP, $serverPORT, SWOOLE_PROCESS, SWOOLE_SOCK_TCP | SWOOLE_SSL);
$port1 = $server->listen("127.0.0.1", 80, SWOOLE_SOCK_TCP);
 
$server->set([
    'ssl_cert_file' => './ssl-cert/host.cert',
    'ssl_key_file' => './ssl-cert/host.key',
    'open_http2_protocol' => true, // Enable HTTP2 protocol
]);


$server->on('start', function($serv) {
    echo "\033[93m \n\n Server: start.Swoole version is [".SWOOLE_VERSION."]";
    echo "MasterPid={$serv->master_pid}|Manager_pid={$serv->manager_pid}";
    echo "\033[92m \n\nServer Started: \n\n\nListening on ports https|443 http|80\033 \n"; 
});


$server->on('request', function (swoole_http_request $ser, swoole_http_response $fd) {
global $wwwDir;

   //set up some vars as you used them
    $_GET = $ser->get; 
    $_POST = $ser->post;
    $_JSON = json_decode($ser->rawContent(),true);

    $httpSrv = new Swoole_Srv($fd,$ser);

    // http://localhost:port/api-test

  if ($ser->server['request_uri'] == "/api-test") {
 
   $resp['msg'] = 'Hello world'; 
   $resp['status'] = 'Ok'; 

   //Send response, SendResponse(String, Mime-type, Status-code)
   $httpSrv->SendResponse(json_encode($resp), 'json', 200);
    return;
   }  
   //Start handling static files (index.html)
   $httpSrv->handleRequest($wwwDir);



});

require_once("./includes/websockets.php");

$server->start();

?>
