<?php 

namespace Drupal\streamnet_upload;

use Drupal\streamnet_upload\HttpHandlerBase; 

class EscapementHttpHandler extends HttpHandlerBase {
        //TO DO: LOG FUNCTIONS
    public function log($locations, $species, $run, $inputFileName){
        //$log['location'] = count($locations) > 1 ? implode(', <br>', $locations) : $locations[0];
        $log['locations'] = $locations;
        $log['species'] = $species;
        $log['run'] = $run; 
        $log['type'] = 'Escapement';
        $log['username'] = Utilities::getCurrentUser();
        $log['dateupdated'] = \Drupal::service('date.formatter')->format(\Drupal::time()->getCurrentTime(), 'custom', 'm-d-y');
        $log['filename'] = $inputFileName;  


        $json_encode = json_encode($log);

        try {
            \Drupal::httpClient()->post($this->urlPrefix.'/api/ESC/Data/TableLog',[
                'body' => $json_encode,
                'headers' => [
                        'Content-Type' => 'application/json'                        
                        ]
                ]);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
                var_dump($e->getMessage());
        }

    }

    public function adHocQuery($tableID, $type, $value, $field, $agency = 'YIN'){
        $filter['type'] = $type; 
        $filter['value'] = $value;
        $filter['field'] = $field;
        $json_encode = json_encode($filter);
        try {
            $request = \Drupal::httpClient()->get($this->sn_prefix.'/api/v1/ca?table_id='.$tableID.'&filter=['.$json_encode.']', [
                'headers' => [
                    'XApiKey' => $this->apikey, 
                  ]
                ]);

            $json_decode = json_decode($request->getBody());
            return $json_decode->records; 

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            //
        }       
    }

    public function adHocQuerys($tableID, $filter) {
        $json_encode = json_encode($filter);
        try {
            $request = \Drupal::httpClient()->get($this->sn_prefix.'/api/v1/ca?table_id='.$tableID.'&filter='.$json_encode, [
                'headers' => [
                    'XApiKey' => $this->apikey,
                ]
            ]);

            $json_decode = json_decode($request->getBody());
            return $json_decode->records;

        } catch (\GuzzleHttp\Exception\RequestException $e){          
            var_dump($e->getMessage());
        }
    }

    public function getLog() {
       try {
          $response = \Drupal::httpClient()->get($this->urlPrefix.'/api/ESC/Data/TableLog')->getBody();
          return json_decode($response);
       } catch (\GuzzleHttp\Exception\RequestException $e){          
          var_dump($e->getMessage());
       }
    }
        
}