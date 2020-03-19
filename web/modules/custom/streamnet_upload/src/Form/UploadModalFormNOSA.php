<?php 

namespace Drupal\streamnet_upload\Form;

use Drupal\streamnet_upload\Form\UploadModalFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\streamnet_upload\ExcelHandler;
use Drupal\streamnet_upload\NOSAHttpHandler;
use Drupal\streamnet_upload\Utilities;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;

class UploadModalFormNOSA extends UploadModalFormBase {
    public function __construct(){
        //NOSA Table
        $this->tableID = '4EF09E86-2AA8-4C98-A983-A272C2C2C7E3';
        $this->id_value = 'CommonName';
        $this->http_handler = new NOSAHttpHandler();
        $this->directory = 'public://CAXFiles';
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

        //REMEMBER TO CHANGE PATHS FOR NOSA AND ESCAPEMENT
        //$absolute_path = 'public://CAXFiles';
        $file = file_save_upload('file_upload', $validators, $this->directory, 0, FILE_EXISTS_REPLACE);
        if($file) {
            
            $inputFileName = $file->getFilename();   
               

            $serializedData = ExcelHandler::getSheetData($this->directory, $inputFileName);
            
            //Returns and associative array 
            $distinctPopIDs = ExcelHandler::getPopulationIDs($serializedData); 

            
            //GET CAX data to query and get ID's to Delete by 
            $caxData = $this->getNOSAData($serializedData);
           
            $this->deleteAllNOSA($distinctPopIDs, $caxData, $this->tableID);
            
            //Log Into Database
            $this->logExcelUploadFile($distinctPopIDs, $inputFileName);
              
            return $this->exportNOSA($serializedData);
        }// End if 
    }

    public function exportNOSA($serializedData) {
        foreach($serializedData as $item) {
            //Catch and Display Errors if response returns anything else
            $item['ID'] = '';
            $result = $this->http_handler->post($item, $this->tableID, $this->id_value);
            if($result !== 1 && !is_null($result)) {
                return $result;
            } 
        }// End foreach

        $ajaxResponse = new AjaxResponse();
        $build['myelement'] = [
            '#theme' => 'response_message', 
            '#title' => '',
            '#attributes' => [
                'class' => 'external-container'
            ]
        ];

            
        $ajaxResponse->addCommand(new OpenModalDialogCommand(
            'Messages', 
            $build, ['width' => '500'])
        );
        return $ajaxResponse; 
    }

    public function logExcelUploadFile($populationList, $inputFileName) {
        $caxheader = 'https://cax.streamnet.org/?popid=';
        
        //Prep HTML output to to put into SQL server
        foreach($populationList as $key => $value) {
            $array[] =  '<a href="'.$caxheader.$key.'&view=data" target = _blank>'.$value.'</a>';  
        }

        $serialized_array['populations'] = count($array) > 1 ? implode(', ', $array) : $array[0];
        $serialized_array['username'] = Utilities::getCurrentUser();
        $serialized_array['dateupdated'] = \Drupal::service('date.formatter')->format(\Drupal::time()->getCurrentTime(), 'custom', 'm-d-y');
        $serialized_array['filename'] = $inputFileName; 

        //Enode to json value
        $json_encode = json_encode($serialized_array);
        $this->http_handler->log($json_encode);
    }

    public function deleteAllNOSA($distinctPopIDs, $caxData, $tableID) {
        foreach($distinctPopIDs as $key => $value) {
            $popids[] = $key;
        }

        foreach($caxData->records as $item) {
            if(in_array($item->popid, $popids)){
                $this->http_handler->delete($item->id, $tableID);
            }
        }
    }

    public function getNOSAData($output) {
        $agency = $output[0]['SubmitAgency'];
        return $this->http_handler->get($this->tableID, $agency);
    }

}