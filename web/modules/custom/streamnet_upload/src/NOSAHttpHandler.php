<?php 

namespace Drupal\streamnet_upload; 

use Drupal\streamnet_upload\HttpHandlerBase;

class NOSAHttpHandler extends HttpHandlerBase {
    //Maybe put into a class that inherits above code? 
    public function log($json_encode){
        try {
            $request = \Drupal::httpClient()->post($this->urlPrefix.'/api/CAX/Data/TableLog',[
                'body' => $json_encode,
                'headers' => [
                  'Content-Type' => 'application/json'
                ]
              ]);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            var_dump($e->getMessage());
        }
    }

    public function getLog() {
        try{
            $response = \Drupal::httpClient()->get($this->urlPrefix.'/api/CAX/Data/TableLog')->getBody();
            return  json_decode($response);
        } catch (\GuzzleHttp\Exception\RequestException $e){
            var_dump($e->getMessage());
        }
    }
}