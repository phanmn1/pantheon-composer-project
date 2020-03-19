<?php

namespace Drupal\quality_assurance\Form;


use Drupal\stardb_queries\DataAccess; 
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
// use Drupal\select_or_other\Element\Select; 
// use Drupal\Core\Ajax\AjaxResponse;
// use Drupal\Core\Ajax;
// use Drupal\Core\Ajax\OpenModalDialogCommand;

//include Drupal\Core\Classes\PHPExcel; 



class LampreyForm extends FormBase {
    
    protected $DataAccess;


	public function __construct() {
		$this->DataAccess = new DataAccess(); 
    }
    
    public function getFormId() {
        return 'lamprey_passage_form';
    }

    public function buildForm(array $form, FormStateInterface $form_state) {

      $form['#attached']['library'][] = 'quality_assurance/lamprey';
      $form['#attached']['drupalSettings']['baseUrl'] =  \Drupal::config('apiurl')->get('value'); 
      $form['#attached']['library'][] = 'quality_assurance/datatables_library';
      
      $form['title'] =[
          '#markup' => '<h1>Lamprey Data Entry Form</h1>'                                          
        ];
    
        $form['default_selects'] = [
          '#type' => 'fieldset', 
          '#attributes' => [
            'class' => ['default-selects']
          ]
        ];
      
          //var_dump()
      
            // $client = \Drupal::httpClient();
            // $request = $client->get($urlPrefix.'/api/TrapSamples/GetSelectLists');
            // $body = json_decode($request->getBody()); 
        
            $body =  $this->DataAccess->GetSelectLists()->getBody();
            $value =  json_decode($body); 	

          foreach($value->facilityList as $item) {
            $facilityList[$item -> FacilityID] =  $item -> FacilityName;  
          }
          
      
          $form['default_selects']['Facility'] = [
            '#type' => 'select',
            '#title' => $this->t('Facility: '),
            //'#options' => $facilityList,
            '#options' => [
              '1' => 'Prosser'
            ]
          ];
      
        
      
          $time = REQUEST_TIME; 
          $date = new \DateTime(); 
          $date->setDate(date("Y"),1,1); 
          $result = db_query("select  date_format(date_sub(CURDATE(), INTERVAL 7 DAY), '%m/%d/%Y') as LastDate");
          $row = $result->fetchAssoc();
      
          $form['default_selects']['from'] = [
            '#type' => 'textfield', 
            '#title' => $this->t('From Date: '), 
            '#default_value' => $date->format("m/d/Y"),
            //$row['LastDate'],
            '#attributes' => [
              'id'=> 'qa-demo-from', 
              'class' => ['datepicker-class-link'],
              'size' => 20, 
              'maxlength' => 20
            ], 
            '#attached' => [
              'library' => [
                'quality_assurance/calendar_library',
              ]
            ]
          ];
      
          $form['default_selects']['to'] = [
            '#type' => 'textfield', 
            '#title' => $this->t('To Date: '), 
            '#default_value' => date("m/d/Y", $time),
            '#attributes' => [
              'id'=> 'qa-demo-to', 
              'class' => ['datepicker-class-link'],
              'size' => 20, 
              'maxlength' => 20
            ],
            '#attached' => [
              'library' => [
                'quality_assurance/calendar_library',
              ]
            ]
          ]; 
      
         
       
          $form['default_selects']['submit'] = [ 
            '#type' => 'submit', 
            '#value' => $this->t('Go'), 
            // '#submit' => ['::CallTableForm'],
            '#attributes' => [
            //call the first time that the class table wrapper is found so pick array 0
              //'onclick' => "document.getElementById('table-wrapper').style.visibility='visible'; return false;",
              'class' => ['btn-margin-wrapper']
              ]
          ]; 
      
          $form['default_selects']['reset'] = [
            '#type' => 'button', 
            '#value' => $this->t('Reset'), 
            //'#submit' => ['::CallPassageForm'],
            '#attributes' => [
            //call the first time that the class table wrapper is found so pick array 0
              //'onclick' => "document.getElementById('table-wrapper').style.visibility='hidden'; return false;",
              'class' => ['btn-margin-wrapper']
              ]
      
          ];
      
        //   $form["note"] = [
        //     "#markup" => '<div class="note"><sup>*</sup>Note: For edits before 2017, 
        //     please contact staff (<a href="mailto:stem@yakamafish-nsn.gov">Michelle Steg-Geltner</a>).</div>'
        //   ];
      
      
      
         
      
          $header = [
            
            'PassDate' => t('Date'),          
            'Ladder' => t('Ladder'),   
            'Location' => t('Location'),
            'LadCode' => t('LadCode'),
            'NumofLamprey' => t('# of Lamprey'),
            'Comments' => t('Comments')       
            // 'HPID' => t('HPID'), 
            // 'SampleSourceID' => t('SampleSourceID'),
            // 'QC_ModifiedBy' => t('QC_ModifiedBy'), 

          ];
      
          $form['table'] = [
            '#type' => 'table', 
            '#header' => $header,
            '#attributes' => [
              'id' => 'qc-log-table',
              'class' => ['display'], 
              'cellspacing' => 0, 
              'width' => '100%'
            ],	
          ];
      
      
          return $form; 
    
    
    
    
    }



    public function submitForm(array &$form, FormStateInterface $form_state) {
        /*
         * This would normally be replaced by code that actually does something
         * with the title.
         */
        //$title = $form_state->getValue('title');
        //drupal_set_message(t('You specified a title of %title.', ['%title' => $title]));
        $form_state->setRebuild();
      }
      


}