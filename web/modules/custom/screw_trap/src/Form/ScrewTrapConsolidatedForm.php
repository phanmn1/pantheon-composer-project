<?php 

namespace Drupal\screw_trap\Form;

use Drupal\Core\Form\FormBase; 
use Drupal\Core\Form\FormStateInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\RedirectCommand;

use Drupal\screw_trap\ExcelHandlerScrewTrapConsolidated;

class ScrewTrapConsolidatedForm extends FormBase {

    public function buildForm(array $form, FormStateInterface $form_state){
        
        $urlPrefix = \Drupal::config('apiurl')->get('value'); 
        $gis_form_value = \Drupal::config('arcgisid')->get('value'); 
        $form['#attached']['library'][] = 'screw_trap/screw_trap_consolidated';
        $form['#attached']['drupalSettings']['baseUrl'] =  \Drupal::config('apiurl')->get('value');
        $form['#attached']['library'][] = 'quality_assurance/datatables_library';
        $form['#attached']['drupalSettings']['arcgisid'] = $gis_form_value;
        
        $header = [
             '' =>t(''),             
            //  'Location' => t('Site'),
             'Date' => t('Date'),
             'Time' => t('Time'),
             'Initials' => t('Initials'),
             'Fishing' => t('Fishing'),
             'Rotating' => t('Rotating'),
             'SecondsPerOneFinalRotation' => t('Seconds/ Rotation'),
             'StaffGage' => t('Staff Gage'), 
             'WaterTemp' => t('Water Temp (F)'),
             'AirTemp' => t('Air Temp (F)'),
             'DebrisSizeDiameter' => t('Debris Size Diameter'),
             'WaterClarity' => t('Water Clarity'),
             'CloudPercentage' =>t('Cloud %'),
             'GeneralComments' => t('General Comments'),
             'EfficiencyTest' => t('Efficiency Test'),
             'EfficiencyTestNo' =>t('Efficiency Test No'),
             'PitTagFile' => t('Pit Tag File'),
             'PitTagVial1' => t('Pit Tag Vial 1'),
             'PitTagVial2' => t('Pit Tag Vial 2'),
             'TotalFishCount' => t('Fish Count'),
             'NumberTagged' => t('Number Tagged'),
             'EfficiencyRecaps' => t('Efficiency Recaps'),
             'Mortality' => t('Mort'),
             'AvgLength' => t('Avg Length'),
             'AvgWeight' => t('Avg Weight'),
             'MortalityPercentage' => t('% Mort'),
             'MinMaxRatio' => t('Min Max Range')
            //  'MinLength' => t('Min Length'),
            //  'MaxLength' => t('Max Length')
                        
        ];

        $form['default_selects'] = [
            '#type' => 'fieldset',
            '#attributes' => [
                'class' => ['default-selects']
            ]
        ];

        $time = REQUEST_TIME; 
        $result = db_query("select  date_format(date_sub(CURDATE(), INTERVAL 30 DAY), '%m/%d/%Y') as LastDate");
        $row = $result->fetchAssoc();

        $client = \Drupal::httpClient();
        $request = $client->get($urlPrefix.'/api/ScrewTrap/SelectList');
        $body = json_decode($request->getBody());

        foreach($body as $location){
            $locations[$location] = $location;
        }

        $form['default_selects']['Locations'] = [
            '#type' => 'select',
            '#title' => $this->t('Location: '),
            '#options' => $locations,
            '#default_value' => 'Upper Toppenish'
          
        ];

        $form['default_selects']['from'] = [
            '#type' => 'textfield',
            '#title' => $this->t('From Date: '),
            '#default_value' => $row['LastDate'],
            '#attributes' => [
                'id' => 'screwtrap-from',
                'class' => ['datepicker-class-link'],
                'size' => 20, 
                'maxlength' => 20
            ], 
            '#attached' => [
                'library' => [
                    'quality_assurance/calendar_library'
                ]
            ]
        ];

        $form['default_selects']['to'] = [
            '#type' => 'textfield', 
            '#title' => $this->t('To Date: '), 
            '#default_value' => date("m/d/Y", $time),
            '#attributes' => [
              'id'=> 'screwtrap-to', 
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

        $form['default_selects']['order'] = [
            '#type' => 'select', 
            '#title' => $this->t('Order: '),
            '#options' => [
                1 => 'Ascending',
                2 => 'Descending'
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

        $form['default_selects']['export'] = [
            '#type' => 'submit',
            '#value' => $this->t('Export to CSV'),
            '#submit' => ['::ExportToCSV'],
            '#attributes' => [
                'class' => ['btn-margin-wrapper']
            ]
        ];

        $form['default_selects']['new_item'] = [
            '#type' => 'link',
            '#title' => $this->t('New Entry'),
            '#url' => Url::fromUri('https://survey123.arcgis.com/share/'.$gis_form_value),
            '#attributes' => [
                'class' => ['btn-margin-wrapper', 'button'],
                'target' => '_blank',
                'id' => 'new-entry'
            ]
        ];
        
        $form['table'] = [
            '#type' => 'table',
            '#header' => $header,
            '#attributes' => [
                'id' => 'screw-trap-table',
                'class' => ['display'], 
                'width' => '100%'
            ]
        ];

        return $form; 
    }

    public function getFormId() {
        return 'screw_trap_consolidated';
    }

    public function ExportToCSV(array &$form, FormStateInterface $form_state) {

        $formValues = $form_state->getValues();
        
        $spreadsheet = new SpreadSheet();
        $filename = 'ScrewTrap.xlsx';
        $urlPrefix = \Drupal::config('apiurl')->get('value'); 


        $return_value = $this->getResponse($urlPrefix, $formValues);



        ExcelHandlerScrewTrapConsolidated::SpreadSheetScrewTrap($return_value, $spreadsheet);
        ExcelHandlerScrewTrapConsolidated::SpreadSheetFishLog($return_value, $spreadsheet);
        
        $writer = new Xlsx($spreadsheet);
        $writer->save("sites/default/files/tmp/ScrewTrap.xlsx");
        $file = 'sites/default/files/tmp/ScrewTrap.xlsx';
        $response = new BinaryFileResponse($file);
        
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
          );

        $response->send();

        
        
        //phpspreadsheet excel found unreadable content
        exit;
        // $form_state->setRebuild(TRUE);
        //return $form;
        // $response = new AjaxResponse();
        // $currentURL = Url::fromRoute('<current>');
        // $response->addCommand(new RedirectCommand($currentURL->toString()));
        // return $response;
        
    }

    private function getResponse($urlPrefix, $formValues){
        $location = $formValues['Locations'];
        $fromDate = $formValues['from'];
        $toDate = $formValues['to'];

        try {
            $response = \Drupal::httpClient()->get($urlPrefix.'/api/ScrewTrap/ScrewTrapExport?Location='
                                                             .$location.'&FromDate='
                                                             .$fromDate.'&ToDate='
                                                             .$toDate);
            
            return json_decode($response->getBody());
        } catch (\GuzzleHttp\Exception\RequestException $e){
            var_dump($e->getMessage());
        } 
    
    }

    public function submitForm(array &$form, FormStateInterface $form_state){
        $form_state->setRebuild(); 
    }
}