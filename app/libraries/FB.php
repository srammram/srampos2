<?php defined('BASEPATH') OR exit('No direct script access allowed');
/*
*  ==============================================================================
*  Author   : Tharani
*  Email    : info@srampos.com
*  Web      : http://srampos.com
*  ==============================================================================
*/

require_once APPPATH . 'third_party/Facebook/autoload.php';
require_once APPPATH . 'third_party/Facebook/FileUpload/FacebookFile.php';
class FB
{

    public function __construct() {

    }

    public function __get($var) {
        return get_instance()->$var;
    }

    public function post($data) {
        
        $AppId = $this->Settings->fb_app_id;
        $AppSecret = $this->Settings->fb_secret_token;
        $pageid = $this->Settings->fb_page_id;
        $page_accessToken = $this->Settings->fb_page_access_token;
        $fb = new Facebook\Facebook([
        'app_id' => $AppId,
        'app_secret' => $AppSecret,
        'default_graph_version' => 'v2.10',
        ]);
        //print_r( $fb->fileToUpload($data['picture']));exit;
        $params = array(
            "is_explicit_place"=>true,
            "caption" => strip_tags(html_entity_decode($data['description']) ),
            'source' =>  $fb->fileToUpload($data['picture']),
            'published' => true,
        );
        try {
            // Returns a `Facebook\FacebookResponse` object
            $response = $fb->post(
              '/'.$pageid.'/photos',
              $params,
              $page_accessToken
            );
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
          $error =  'Graph returned an error: ' . $e->getMessage();
          return json_encode(array('status'=>'error','msg'=>$error));
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
          $error =  'Facebook SDK returned an error: ' . $e->getMessage();
          return json_encode(array('status'=>'error','msg'=>$error));
        }
        $graphNode = $response->getGraphNode();
        $decodedResponse = $response->getDecodedBody();
        return json_encode(array('status'=>'success','data'=>$decodedResponse));
    }

}
