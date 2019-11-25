<?php

class Swoole_Srv {
    private $response;
    private $request;
    private $local_uri;

    public function __construct($response, $request) {
        $this->response = $response;
        $this->request = $request;

    }
    
  public  function get_request() {  
    return $this->request;

  }
  public  function get_response() {  
    return $this->response;

  }
  public  function handle_Request($wwwDir) {
 
    $this->local_uri = $wwwDir.$this->request->server['request_uri'];
      //lets see if its dir look for index.html, htm
      if ($this->request->server['request_uri'] == "/") {

        if (file_exists($this->local_uri.'index.html')) $this->local_uri = $this->local_uri.'index.html';
        if (file_exists($this->local_uri.'index.htm')) $this->local_uri = $this->local_uri.'index.htm';
      }
       if (is_dir($this->local_uri) && substr($this->local_uri,strlen($this->local_uri)-1,1) != "/") {
         $this->response->header('Location', $this->request->server['request_uri'].'/');
         $this->response->status(301);
          return;
       }
      
       if (is_dir($this->local_uri) && file_exists($this->local_uri.'index.html')) {
        $this->local_uri = $this->local_uri.'index.html';
       }
       if (file_exists($this->local_uri) && !is_dir($this->local_uri)) { 
        $this->SendFile($this->local_uri);
       } else {
        $this->response->status(404);
        $this->response->end('Not found ');
       }
}

public function SendFile($filename) {
 
if (isset($this->request->header['accept-encoding']) && strpos(strtolower($this->request->header['accept-encoding']),'gzip') != false) {
    $this->response->header("Content-Encoding", "gzip");
    $this->response->status(200);
    $dsend = gzencode(file_get_contents($filename));
} else {
   $dsend = file_get_contents($filename);
}
$this->response->header("Content-Type", swoole_get_mime_type($filename));
$this->response->end($dsend);

}
public function send_response($response, $type, int $status = 0) {
$gziped = false;
$type = "res.".$type;
$type = swoole_get_mime_type('ext'.strtolower($type));
 
    if (isset($this->request->header['accept-encoding']) && strpos(strtolower($this->request->header['accept-encoding']),'gzip') != false) {
        $this->response->header("Content-Encoding", "gzip");
        $response = gzencode($response);
        $gziped = true;
    }  
    if ($gziped == false && isset($this->request->header['accept-encoding']) && strpos(strtolower($this->request->header['accept-encoding']),'deflate') != false) {
      $this->response->header("Content-Encoding", "deflate");
      $response = gzdeflate($response);
      $gziped = true;
  }  

    if ($status != 0) $this->response->status($status);
    $this->response->header("Content-Type", $type);
    $this->response->header("requestver", "ScoopScript");
    $this->response->end($response);
    
}

 
}


?>
