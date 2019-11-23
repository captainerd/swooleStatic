Class that provides the following for the swoole PHP extension:

Handle the static files-directory.
Use gzip compression when that is possible (if the client/browser accepts)

The httpd.php starts a websocket server along with the web, at both ports 80 and SSL 443.

Useage examples:

  //SendResponse(String, Mime-type, Status-code)

   $httpSrv->SendResponse(json_encode($resp), 'json', 200);  OR $httpSrv->SendResponse('not found', 'txt', 404);
-

  //making a directory public for static-serving
   $httpSrv->handleRequest('./htdocs');
