<?php 

namespace Drupal\streamnet_upload;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;

class HttpHandlerBase {

    //protected $apikey; 

    public function __construct(){
        $this->apikey = 'DD7A0A8C-9A52-4C08-959E-AE56ECA83271';
        $this->urlPrefix = \Drupal::config('apiurl')->get('value'); 
        $this->sn_prefix = \Drupal::config('sn_api')->get('value'); 
        $this->debug = true;
    }

    /*
    * Post data to Streamnet API using their JSON formatting method
    */
    public function post($item, $tableID, $id_value) {
        try {
            $fixed_array = array_map(function($value) { return $value === " " ? "" : $value; }, $item);

            $data['table_id'] = $tableID;
            $data['record_values'] = $fixed_array;

            
            $jsonEncodedSerializedEntity = json_encode($data);
            // var_dump($jsonEncodedSerializedEntity);
            // die;
                  
            if($data['record_values'][$id_value] != null){
                $request = \Drupal::httpClient()->post($this->sn_prefix.'/api/v1/ca', [
                    'body' => $jsonEncodedSerializedEntity, 
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'XApiKey' => $this->apikey,
                        //'XValidate' => $this->debug
                    ], 
                    'stream' => true
                ]);              
               return 1;
            }
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $response = new AjaxResponse(); 
            $content = $e->getResponse()->getBody()->getContents();  
            $decodedJson = json_decode($content, true);
            //var_dump($decodedJson);
            
            foreach($decodedJson['error']['record_values'] as $key => $value){
                $html .=  $key. ' : ' .$value. '<br>';
            } 

            foreach($data['record_values'] as $key => $value) {
                $headers[] = $key;
                $rows[] = $value;
            }

            $table = '<table><tr>';
            foreach($headers as $header) {
                $table .= '<th>'.$header.'</th>';
            }

            $table .= '</tr><tr>';

            foreach($rows as $row){
                $table .= '<td>'.$row.'</td>';
            }

            
            $table .= '</tr></table>';

            
            $html .= '<div>Record Values : '.$jsonEncodedSerializedEntity.'</div><br>';            

              
            $response->addCommand(new OpenModalDialogCommand(
                'Validation Errors', 
                $html, ['width' => '800'])
            ); 

            //var_dump($decodedJson);
    
            return $response;       
        }

        
    }

    public function delete($guid, $tableID) {
        try {
            $delete = \Drupal::httpClient()->delete($this->sn_prefix.'/api/v1/ca/'.$guid, [
                'headers' => [
                    'XApiKey' => $this->apikey,
                    'table_id' => $tableID,
                    //'XValidate' => $this->debug
                    ]
                ]);
                return 1;
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $response = new AjaxResponse(); 
            $content = $e->getResponse()->getBody()->getContents();   
            $decodedJson = json_decode($content, true);
            //var_dump($decodedJson);
            foreach($decodedJson['error']['record_values'] as $key => $value){
                $html .=  $key. ' : ' .$value. '<br>';
            }   
              
            $response->addCommand(new OpenModalDialogCommand(
                'Validation Errors', 
                $html, ['width' => '800'])
            ); 
    
            return $response;       
        }
    }

    public function get($tableID, $agency) {
        try {
            $request = \Drupal::httpClient()->get($this->sn_prefix.'/api/v1/ca?table_id='.$tableID.'&agency='.$agency, [
                'headers' => [
                  'XApiKey' => $this->apikey, 
                  ]
                ]);
        
                return json_decode($request->getBody()); 
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $response = new AjaxResponse(); 
            $content = $e->getResponse()->getBody()->getContents();   
            $decodedJson = json_decode($content, true);
            foreach($decodedJson['error']['record_values'] as $key => $value){
                $html .=  $key. ' : ' .$value. '<br>';
            }   
              
            $response->addCommand(new OpenModalDialogCommand(
                'Validation Errors', 
                $html, ['width' => '800'])
            ); 
    
            return $response;   
        }
    }
    
    


}