<?php

/**
 * @author Ananthan
 * @link URL Tutorial link
 */
 
use ElephantIO\Client;
use ElephantIO\Engine\SocketIO\Version2X;

require APPPATH . '/libraries/vendor/autoload.php';

class Socketemitter {

    var $socket_connection;
    var $socket_client;
    function __construct() {
        
	$CI =& get_instance();
	$CI->load->model('site');
        $settings = $CI->site->get_setting();
        define('SOCKET_PORT',$settings->socket_port);
        define('SOCKET_HOST',$settings->socket_host);
	if($CI->site->isSocketEnabled()){
	    $host = str_replace('http://','',SOCKET_HOST);
	    
	    $this->socket_connection = @fsockopen($host, SOCKET_PORT);
	   
	    if(is_resource($this->socket_connection)){
		    $this->socket_client = new Client(new Version2X(SOCKET_HOST.':'.SOCKET_PORT, [
		     'headers' => [
			 'X-My-Header: websocket rocks',
			 'Authorization: Bearer 12b3c4d5e6f7g8h9i'
		     ]
		    ]));
		    $this->socket_client->initialize();
	    }
	}
    }

    public function setEmit($event, $edata) {
	$CI =& get_instance();
        if($CI->site->isSocketEnabled()){
	    
	    if(is_resource($this->socket_connection)){
		
		$result = $this->socket_client->emit($event, $edata);
		if($result){		   
		    return true;   
		}
		return false;
	    }
	}
	return false;
    }

   

}
