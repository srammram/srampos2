<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Archival {
    function start(){
          $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, site_url('archival_data'));
	    curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_VERBOSE, true);
	    $result = curl_exec($ch);
	    curl_close($ch);
    }
	
}