<?php 

namespace Drupal\streamnet_upload;

use Drupal\streamnet_upload\ExcelHandlerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class ExcelHandler implements ExcelHandlerInterface {
    
    public static function getSheetData($realPath, $inputFileName, $sheetName = null) {
    
        $absolute_path = \Drupal::service('file_system')->realpath($realPath);

        $inputFilePath = $absolute_path.'\\'.$inputFileName;
        $inputFileType = IOFactory::identify($inputFilePath);
        $reader = IOFactory::createReader($inputFileType); 
        
        
        if($sheetName != null){
            $reader->setLoadSheetsOnly($sheetName); 
        }

        //Load spreadsheet
        $sheetData = $reader->load($inputFilePath)->getActiveSheet()->toArray(null, true, true, true); 
        $output = []; 
        $headers = $sheetData[1]; 
        $length = count($sheetData);
        for($x = 2; $x < $length+1; ++$x) {
            $output[] = $sheetData[$x]; 
        }

        array_walk(
            $output,
            function (&$row) use ($headers) {
                $row = array_combine($headers, $row);
                }
            );
   


        return $output;
  
    }

    //Take an array and return the distinct Population id's in an  
    //associative array of $array['PopID'] = PopulationName
    public static function getPopulationIDs(array $output) {
        $currentID = (int) $output[0]['PopId'];
        $array[(int) $output[0]['PopId'] ] = $output[0]['CommonPopName'];
      

        foreach($output as $item) {
            if(!($item['PopId']  == $currentID) && $item['PopId'] != null){
                $array[ (int) $item['PopId']] =  $item['CommonPopName']; 
                $currentID = $item['PopId'];
            }
        }
      
        return $array;
    }

    // public function submitSheetData() {
    //     try {
    //         foreach($this->serializedEntity as $item){

    //             //Set up JSON format to submit to Streamnet
    //             $data['table_id'] = $this->tableID;
    //             $data['record_values'] = $item;

    //             $this->postData($data);
    //             return true;
    //         }
    //         // $ajaxResponse = new AjaxResponse();
    //         //     $build['myelement'] = [
    //         //         '#theme' => 'res_layout', 
    //         //         '#title' => '',
    //         //         '#attributes' => [
    //         //             'class' => 'external-container'
    //         //         ]
    //         //     ];               
    //         //     $ajaxResponse->addCommand(new OpenModalDialogCommand(
    //         //         'Messages', 
    //         //         $build, ['width' => '500'])
    //         //     );
    //         // return $ajaxResponse;   
    //         //return true;
    //     } catch (Exception $e) {
    //         //Some error occurred 
    //         return $e;

    //         // $response = new AjaxResponse(); 
    //         // $content = $e->getResponse()->getBody()->getContents();   
    //         // $decodedJson = json_decode($content, true);
    //         // foreach($decodedJson['error']['record_values'] as $key => $value){
    //         //     $html .=  $key. ' : ' .$value. '<br>';
    //         // }       
    //         // $response->addCommand(new OpenModalDialogCommand(
    //         //     'Validation Errors', 
    //         //     $html, ['width' => '800'])
    //         // );           
    //     }
    // }

    // public function postData(array $data) {
    //     try {
    //         $jsonEncodedSerializedEntity = json_encode($data);
    //         if($data['record_values']) {
    //             $request = \Drupal::httpClient()->post($this->sn_prefix.'/api/v1/ca', [
    //                 'body' => $jsonEncodedSerializedEntity, 
    //                 'headers' => [
    //                     'Content-Type' => 'application/json',
    //                     'XApiKey' => $this->apikey,
    //                     'XValidate' => true
    //                 ], 
    //                 'stream' => true
    //             ]);
    //         }
    //     } catch (\GuzzleHttp\Exception\RequestException $e) {
    //         throw new Exception('Request Exception Error');
    //     }
    // }
}