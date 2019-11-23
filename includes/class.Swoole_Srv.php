<?php

class Swoole_Srv {
    private $fd;
    private $ser;
    private $local_uri;

    public function __construct($fd, $ser) {
        $this->fd = $fd;
        $this->ser = $ser;

    }
    
  public  function handleRequest($wwwDir) {
 
    $this->local_uri = $wwwDir.$this->ser->server['request_uri'];
      //lets see if its dir look for index.html, htm
      if ($this->ser->server['request_uri'] == "/") {
        $this->local_uri = $this->local_uri.'index.html';
      }
       if (is_dir($this->local_uri) && substr($this->local_uri,strlen($this->local_uri)-1,1) != "/") {
         $this->fd->header('Location', $this->ser->server['request_uri'].'/');
         $this->fd->status(301);
          return;
       }
      
       if (is_dir($this->local_uri) && file_exists($this->local_uri.'index.html')) {
        $this->local_uri = $this->local_uri.'index.html';
       }
       if (file_exists($this->local_uri) && !is_dir($this->local_uri)) { 
        $this->SendFile($this->local_uri);
       } else {
        $this->fd->status(404);
        $this->fd->end('Not found ');
       }
}

public function SendFile($filename) {

if (strpos(strtolower($this->ser->header['accept-encoding']),'gzip') != false) {
    $this->fd->header("Content-Encoding", "gzip");
    $this->fd->status(200);
    $dsend = gzencode(file_get_contents($filename));
} else {
   $dsend = file_get_contents($filename);
}
$this->fd->header("Content-Type", $this->swoole_mime_type($filename));
$this->fd->end($dsend);

}
public function SendResponse($response, $type, int $status) {
  $mimet = json_decode(file_get_contents('mimetypes.txt'), true);
    if (strpos(strtolower($this->ser->header['accept-encoding']),'gzip') != false) {
        $this->fd->header("Content-Encoding", "gzip");
        $response = gzencode($response);
    }  
  
    $this->fd->status($status);
    $this->fd->header("Content-Type", $mimet[$type]);
    $this->fd->end($response);
    
}

private function swoole_mime_type($filename) {
 
    $idx = explode( '.', $filename );
    $count_explode = count($idx);
    $idx = strtolower($idx[$count_explode-1]);

    $mimet = json_decode(file_get_contents('mimetypes.txt'), true);

    if (isset( $mimet[$idx] )) {
     return $mimet[$idx];
    } else {
     return 'application/octet-stream';
    }
 }
}


?>