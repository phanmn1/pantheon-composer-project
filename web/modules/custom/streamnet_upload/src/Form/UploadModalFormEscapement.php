<?php 

namespace Drupal\streamnet_upload\Form;

use Drupal\streamnet_upload\Form\UploadModalFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\streamnet_upload\ExcelHandler;
use Drupal\streamnet_upload\EscapementHttpHandler;
use Drupal\streamnet_upload\Utilities;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;

class UploadModalFormEscapement extends UploadModalFormBase {

    protected $tableID;
    protected $id_value;

    public function __construct(){
        //TrendEdits Table
        $this->locationMaster = 'FB0D82E4-116A-47CD-A48B-7619CE3EE69C';
        $this->superCode = '3D7A9003-FA48-4784-9C70-10A92C008806';
        $this->trendTable = 'CB99265A-57D0-4BCC-A1D6-853B58447C19';
        $this->EscTable = 'F11B5228-F716-487B-807D-0DD04B96EEB8';
        $this->id_value = 'TrendID';
        $this->directory = 'public://ESCFiles';
        $this->http_handler = new EscapementHttpHandler();

        $this->locations = [];
        $this->species = '';
        $this->run = '';
        
        //$this->apiKey = 'DD7A0A8C-9A52-4C08-959E-AE56ECA83271';
    }

    public function submitModalFormAjax(array &$form, FormStateInterface $form_state) {
        if(!is_dir($this->directory)){
            $new_dir = Utilities::CreateDirectory($this->directory);
            if($new_dir != null) {
                return $new_dir;
            } 
        }

        $validators = [];
        $destination = FALSE;

        $file = file_save_upload('file_upload', $validators, $this->directory, 0, FILE_EXISTS_REPLACE);

        if($file) {
            $inputFileName = $file->getFilename();
            
            

            $supercode = $this->uploadTable($inputFileName, 'SupercodeStreams', $this->superCode, 'SupercodeID');
            $trendTable = $this->uploadTable($inputFileName, 'Trendtable', $this->trendTable);
            
            //$this->deleteAllData($this->EscTable, 'YIN');
            $escTable = $this->uploadTable($inputFileName, 'EscTable', $this->EscTable);
            $this->http_handler->log($this->locations, $this->species, $this->run, $inputFileName);
            if($supercode != null) {
                return $supercode; 
            } 

            if($trendTable != null) {
                return $trendTable; 
            } 

            if($escTable != null) {
                return $escTable; 
            }

           

            

            

            $ajaxResponse = new AjaxResponse();
            $build['myelement'] = [
                '#theme' => 'response_message', 
                '#title' => 'Escapement',
                '#data' => [
                    'datatype' => 'Trends Data Upload',
                    'dataset' => 'Escapement'
                ],
                '#attributes' => [
                    'class' => 'external-container'
                ]
            ];

                
            $ajaxResponse->addCommand(new OpenModalDialogCommand(
                'Messages', 
                $build, ['width' => '500'])
            );
            return $ajaxResponse;   

        }// End if 
    } // End Submit Modal Ajax


    public function getLocationNames($trendTable){
        $this->species = $trendTable[0]['SpecieID'];
        $this->run = $trendTable[0]['RunID'];
        foreach($trendTable as $trend){
            if($trend['LocationID'] != null) {
                
                $location = $this->http_handler->adHocQuery($this->locationMaster, 'string', $trend['LocationID'], 'locationid');            
                $locations[] = $location[0]->name;
            }
        }

        return $locations; 
    }


    public function getIDArray($tableID, $agency, $id){
        $tableData = $this->http_handler->get($tableID, $agency);
        foreach($tableData as $data) {
            $array[] = $data[$id];
        }

        return $array; 
    }

    public function getMissingTrends($serializedData){
        //$missing_trendids[] 
        foreach($serializedData as $item){
            if($item['TrendID'] != null) {
                $result = $this->http_handler->adHocQuery($this->trendTable, 'string', $item['TrendID'], 'trendid');
                if($result[0] == null){
                    $missing_trends[] = $item; 
                }
            }
        }
        return $missing_trends; 
    }

    public function getMissingSuperCodes($serializedData){
        foreach($serializedData as $item){

            $filter[0]['type'] = 'string';
            $filter[0]['value'] = $item['SupercodeID'];
            $filter[0]['field'] = 'supercodeid';

            $filter[1]['type'] = 'string';
            $filter[1]['value'] = $item['LocationID']; 
            $filter[1]['field'] = 'locationid';

            $filter[2]['type'] = 'string';
            $filter[2]['value'] = $item['BegFt'];
            $filter[2]['field'] = 'begft';

            if($item['SupercodeID'] != null) {
                $result = $this->http_handler->adHocQuerys($this->superCode, $filter);
                if($result == null) {
                    $missing_supercodes[] = $item;
                }                
            }
        }

        return $missing_supercodes;
    }

    public function uploadTable($inputFileName, $sheetName, $tableID, $id_value = 'TrendID') {
        //$sheetname = 'Trendtable';
        $serializedData = ExcelHandler::getSheetData($this->directory,$inputFileName, $sheetName);
        

        if($sheetName == 'SupercodeStreams') {
            $serializedData = $this->getMissingSuperCodes($serializedData);
        }

        if($sheetName == 'Trendtable'){
            $this->locations = $this->getLocationNames($serializedData);
            $serializedData = $this->getMissingTrends($serializedData); 
        }

        if($sheetName == 'EscTable') {
            $this->deleteAllData($serializedData);
            if(is_object($delete)) {
                return $delete;
            } 
        }

        
        foreach($serializedData as $item) {
            //Catch and Display Errors if response returns anything else
            $result = $this->http_handler->post($item, $tableID, $id_value);
            if(is_object($result)) {
                return $result;
            } 
        }// End foreach
        return null; 
    }

    public function deleteAllData($serializedData){
        //Query streamnet trendEdits table to get all relevant data 
        //Loop through json result and pull out only the ID fields and store them into an array 
        //Loop through the json array and delete each one
        
        //Initialize trendid to -1 to jump start all the trend id's that will be passed into it
        $trendID = -1;
        foreach($serializedData as $item) {
            if($item['TrendID'] != $trendID && $item['TrendID'] != null) {
                $trendID = $item['TrendID'];
                $result = $this->http_handler->adHocQuery($this->EscTable, 'string', $item['TrendID'], 'trendid');
                if(is_object($result)){
                    return $result;
                } else {
                    foreach($result as $trend) {
                        $delete = $this->http_handler->delete($trend->id, $this->EscTable);
                        if(is_object($delete)){
                            return $delete;
                        }
                    }
                }
                
            }
        }
    }
}