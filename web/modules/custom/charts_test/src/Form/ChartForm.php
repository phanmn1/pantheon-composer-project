<?php

namespace Drupal\charts_test\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\charts\Services\ChartsSettingsService;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Component\Uuid\Php;
use GuzzleHttp\Client;
use Drupal\stardb_queries\DataAccess; 
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;

class ChartForm extends FormBase {

    protected $chartSettings;
    protected $messenger;
    protected $uuidService;
    protected $httpClient;
    protected $urlPrefix; 
    protected $dataAccess;
    
    /**
     * Construct.
     *
     * @param \Drupal\charts\Services\ChartsSettingsService $chartSettings
     *   Service ChartsSettingsService.
     */

    public function __construct(ChartsSettingsService $chartSettings, 
                                MessengerInterface $messenger, 
                                Php $uuidService,
                                Client $http_client) {
        
        $this->chartSettings = $chartSettings->getChartsSettings();
        $this->messenger = $messenger;
        $this->uuidService = $uuidService;
        $this->httpClient = $http_client;
        $this->urlPrefix = \Drupal::config('apiurl')->get('value'); 
        $this->dataAccess = new DataAccess(); 
    }

    public function getFormId() {
        return 'star_chart_form'; 
    }

    public function concatText(array $notes) {
        return implode("<br><br>", $notes);
      }

    public function buildForm(array $form, FormStateInterface $form_state){

        $form_state->setCached(FALSE);

        $form['#attached']['library'][] = 'charts_test/charts_css';
        $form['#attached']['library'][] = 'core/drupal.dialog.ajax';

        $body =  json_decode($this->dataAccess->GetSelectLists()->getBody());

       

        $form['select-lists'] = [
            '#type' => 'fieldset',
            '#attributes' => [
                'class' => ['select-lists']
                ]
            ];
       	

        foreach ($body->facilityList as $record) {
            $FacilityRows[$record->FacilityID] = $record->FacilityName;  
        }

        
        

        $form['select-lists']['facility'] = [
            '#type' => 'select', 
            '#title' => $this->t('Facility: '), 
            '#options' => $FacilityRows,
            '#default_value' => '1'
        ];

        $form['select-lists']['species'] = [
            '#type' => 'select', 
            '#title' => $this->t('Species: '), 
            '#options' =>[
                '0' => $this->t('Chinook'), 
                '3' => $this->t('Steelhead'), 
                '1' => $this->t('Coho')
            ], 
            '#default_value' => '0'
        ];

        $form['select-lists']['year'] = [
            '#type' => 'select', 
            '#title' => $this->t('Years: '), 
            '#options' => $this->buildYearList(), 
            '#default_value' => '2018'
        ];

        $form['select-lists']['submit'] = [ 
            '#type' => 'button', 
            '#value' => $this->t('Go'), 
            '#ajax' => [
                'callback' => '::Graph', 
                'wrapper' => 'chart-wrapper', 
                'event' => 'click'
            ],
            '#attributes' => [
            //call the first time that the class table wrapper is found so pick array 0
              //'onclick' => "document.getElementById('table-wrapper').style.visibility='visible'; return false;",
              'class' => ['btn-margin-wrapper']
              ]
          ]; 
      
          $form['select-lists']['reset'] = [
            '#type' => 'button', 
            '#value' => $this->t('Reset'), 
            //'#submit' => ['::CallPassageForm'],
            '#attributes' => [
            //call the first time that the class table wrapper is found so pick array 0
              //'onclick' => "document.getElementById('table-wrapper').style.visibility='hidden'; return false;",
              'class' => ['btn-margin-wrapper']
              ]
      
          ];


          //$this->graph($form, $form_state); 

          $form['chart_wrapper'] = [
              '#type' => 'container', 
              '#attributes' => ['id' => 'chart-wrapper']
          ];

        
        return $form; 
    }

    

    private function SteelheadData(array &$form, FormStateInterface $form_state ) {
        
        //Get the current state of the form with selected data 

        $library = $this->chartSettings['library'];
            if (empty($library)) {
            $this->messenger->addError($this->t('You need to first configure Charts default settings'));
            return [];
        }
        
        $options = [
            'type' => $this->chartSettings['type'],
            'title' => $this->t('Adult Steelhead Counts'),
            'xaxis_title' => $this->t('Years'),
            'yaxis_title' => $this->t('Count'),
            'yaxis_min' => '',
            'yaxis_max' => '',
            'three_dimensional' => FALSE,
            'title_position' => 'out',
            'legend_position' => 'right',
            'data_labels'=> $this->chartSettings['data_labels'],
            // 'grouping'   => TRUE,
            'colors'   => $this->chartSettings['colors'],
            'min'   => $this->chartSettings['min'],
            'max'   => $this->chartSettings['max'],
            'tooltips' => true, 
            'height' => '500'
        ];

        $facility = $form_state->getValue('facility');
        $species = $form_state->getValue('species');
        $year = $form_state->getValue('year'); 
        $previousYear = (int) $year-1;

        $response = json_decode($this->httpClient->get($this->urlPrefix.'/api/FishCounts/GetSteelheadDetails?facilityid='
                                                                       .$facility
                                                                       .'&ladder=All&startdate=05/01/'
                                                                       .$previousYear.'&enddate=04/30/'
                                                                       .$year.'&sort=2')->getBody());
        $areaResponse = json_decode($this->httpClient->get($this->urlPrefix.'/api/FishCounts/GetChartData?FacilityID='
                                                                            .$facility.'&Species='
                                                                            .$species.'&Year='
                                                                            .$year)->getBody());

        $notes = json_decode($this->httpClient->get($this->urlPrefix.'/api/FishCounts/FacilityNotesDetails?facilitycode='.$facility
                                                                        .'&species='.$species
                                                                        .'&startDate=05/01/'.$previousYear
                                                                        .'&enddate=04/30/'.$year)->getBody());

                                                                       

        

        //$categories = []; 
        foreach($response->steelheadCount as $item) {
            $categories[] = $item->Date; 
            $wsth[] = $item->wsth;
            $hsth[] = $item->hsth;
            $usth[] = $item->usth;
        }

        // foreach($response->steelheadTotals as $item) {
        //    $totals[] = [
        //        $item->wsth, 
        //        $item->hsth, 
        //        $item->usth,
        //        $item->Total_Steelhead
        //    ];
        // }

        // $headers = ['Unmarked Steelhead', 'Marked Steelhead', 'Unknown Steelhead', 'Total Steelhead'];

       
       
        foreach($areaResponse as $item) {
            $areaAdPresentSteelhead[] = $item->adPresentSteelhead;
            $areaAdClippedSteelhead[] = $item->adClippedSteelhead;
            $areaAdUnknownSteelhead[] = $item->adUnknownSteelhead;
        }

        $seriesData[] = [
            'name' => '10yr avg Unmarked Steelhead',
            'color' => '#CD5C5C	',
            'type' => 'area',
            'data' =>  $areaAdPresentSteelhead,
        ];
    
        $seriesData[] = [
            'name' => '10yr avg Marked Steelhead',
            'color' => '#FFEBCD',            
            'type' => 'area',
            'data' =>  $areaAdClippedSteelhead,
            //$request = $this->httpClient->get($this->urlPrefix.'/api/FishCounts/TestGuzzleData')
        ];
    
        $seriesData[] = [
            'name' => '10yr avg Unknown Steelhead',
            'color' => '#EEE8AA',
            'type' => 'area',
            'data' =>  $areaAdUnknownSteelhead,
        ];
    
        $seriesData[] = [
            'name' => 'Unmarked Steelhead',
            'color' => '#0d233a',
            //'type' => $this->chartSettings['type'],
            'type' => 'line',
            'data' =>  $wsth,
            //$request = $this->httpClient->get($this->urlPrefix.'/api/FishCounts/TestGuzzleData')
        ];

        $seriesData[] = [
            'name' => 'Marked Steelhead',
            'color' => '#8bbc21',
            //'type' => $this->chartSettings['type'],
            'type' => 'line',
            'data' =>  $hsth,
            //$request = $this->httpClient->get($this->urlPrefix.'/api/FishCounts/TestGuzzleData')
        ];

        $seriesData[] = [
            'name' => 'Unknown Steelhead',
            'color' => '#910000',
            //'type' => $this->chartSettings['type'],
            'type' => 'line',
            'data' =>  $usth,
            //$request = $this->httpClient->get($this->urlPrefix.'/api/FishCounts/TestGuzzleData')
        ];

        if(!empty($notes)){
            $form['chart_wrapper']['footnotes'] = [
                '#markup' => '<div class="bold-footnotes"><p>'.$notes[0].'</p></div><br>',
            ];
        }

        $form['chart_wrapper']['chart'] = [
            '#theme' => 'charts_api_example',
            '#library' => (string) $library,
            '#categories' => $categories,
            '#seriesData' => $seriesData,
            '#options' => $options,
            '#id' => $chartId,
            '#override' => [],
        ];


        foreach($response->steelheadTotals as $item) {
            $totals['wsth'] = $item->wsth;
            $totals['hsth'] = $item->hsth;
            $totals['usth'] = $item->usth;  
            $totals['Total_Steelhead'] = $item->Total_Steelhead;   
         }
        
        $headers = ['Species', 'Total Count']; 
            
        $rows = [
                    ['Unmarked Steelhead', $totals['wsth']],
                    ['Marked Steelhead', $totals['hsth']],
                    ['Unknown Steelhead', $totals['usth']],
                    ['Total Steelhead', $totals['Total_Steelhead']]
                ]; 


        $form['chart_wrapper']['table'] = [
            '#type' => 'table',
            '#caption' => $this->t('Steelhead Totals ('.$previousYear.' - '.$year.')'),
            '#header' => $headers,
            '#rows' => $rows, 
            '#attributes' => ['class' => ['totals-table']]
        ];

         

        

    
    }

    public function Graph(array &$form, FormStateInterface $form_state) {
        
        // Customize options here.

        $form_state->setRebuild(FALSE);
        $facility = $form_state->getValue('facility');
        $species = $form_state->getValue('species');
        $year = $form_state->getValue('year'); 

            switch($species) {
                //Specie Code 0 - Chinook
                case 0: 
                    $this->ChinookData($form, $form_state);
                    break; 
                case 3: 
                    $this->SteelheadData($form, $form_state); 
                    break; 
                case 1: 
                    $this->CohoData($form, $form_state); 
                    break; 
                default: 
                    $this->ChinookData($form, $form_state);

            }
            

            $chartId = 'chart-' . $this->uuidService->generate();


            return $form['chart_wrapper']; 
    
    }

    private function ChinookData(array &$form, FormStateInterface $form_state ){
        
        $library = $this->chartSettings['library'];
            if (empty($library)) {
            $this->messenger->addError($this->t('You need to first configure Charts default settings'));
            return [];
        }

        $options = [
            'type' => $this->chartSettings['type'],
            'title' => $this->t('Adult Chinook Counts'),
            'xaxis_title' => $this->t('Years'),
            'yaxis_title' => $this->t('Count'),
            'yaxis_min' => '',
            'yaxis_max' => '',
            'three_dimensional' => FALSE,
            'title_position' => 'out',
            'legend_position' => 'bottom',
            'data_labels'=> $this->chartSettings['data_labels'],
            // 'grouping'   => TRUE,
            'colors'   => $this->chartSettings['colors'],
            'min'   => $this->chartSettings['min'],
            'max'   => $this->chartSettings['max'],
            'tooltips' => true, 
            'height' => '500'
        ];

        $facility = $form_state->getValue('facility');
        $species = $form_state->getValue('species');
        $year = $form_state->getValue('year'); 
        $previousYear = (int) $year-1;

        $response = json_decode($this->httpClient->get($this->urlPrefix.'/api/FishCounts/GetConsolidatedChinook?FacilityID='
                                                                       .$facility
                                                                       .'&ladder=All&startdate=01/01/'
                                                                       .$year.'&enddate=12/31/'
                                                                       .$year.'&sort=2')->getBody());

        $areaResponse = json_decode($this->httpClient->get($this->urlPrefix.'/api/FishCounts/GetChartData?FacilityID='
                                                                            .$facility.'&Species='
                                                                            .$species.'&Year='
                                                                            .$year)->getBody());
        
        $notes = json_decode($this->httpClient->get($this->urlPrefix.'/api/FishCounts/FacilityNotesDetails?facilitycode='.$facility
                                                                            .'&species='.$species
                                                                            .'&startDate=05/01/'.$previousYear
                                                                            .'&enddate=04/30/'.$year)->getBody());


        foreach($response->chinookCount as $item) {
            $categories[] = $item->date; 
            $adPresentAdultChinook[] = $item->adPresentAdultChinook;
            $adPresentJackChinook[] = $item->adPresentJackChinook;
            $adClippedAdultChinook[] = $item->adClippedAdultChinook;
            $adClippedJackChinook[] = $item->adClippedJackChinook;
        }

        foreach($areaResponse as $item) {
            $areaAdPresentAdultChinook[] = $item->adPresentAdultChinook;
            $areaAdPresentJackChinook[] = $item->adPresentJackChinook;
            $areaAdClippedAdultChinook[] = $item->adClippedAdultChinook;
            $areaAdClippedJackChinook[] = $item->adClippedJackChinook;
        }

        // foreach($response->chinookTotals as $item) {
        //     $totals[] = [
        //         $item->wafck + $item->adPresentAdultChinook, 
        //         $item->wafck + $item->adPresentJackChinook, 
        //         $item->hafck + $item->adClippedAdultChinook,
        //         $item->hjfck + $item->adClippedJackChinook,
        //         $item->totals
        //     ];
        //  }
 
        // $headers = ['Ad Present Adult Chinook', 'Ad Present Jack Chinook', 'Ad Clipped Adult Chinook', 'Ad Clipped Jack Chinook', 'Total Chinook'];

         //Area list
         $seriesData[] = [
            'name' => '10yr avg Ad Present Adult Chinook', 
            'color' => '#CD5C5C', 
            'type' => 'area', 
            'data' => $areaAdPresentAdultChinook
        ];

        $seriesData[] = [
            'name' => '10yr avg Ad Present Jack Chinook', 
            'color' => '#DAF7A6', 
            'type' => 'area', 
            'data' => $areaAdPresentJackChinook
        ];

        $seriesData[] = [
            'name' => '10yr avg Ad Clipped Adult Chinook', 
            'color' => '#7DCEA0', 
            'type' => 'area',
            'data' => $areaAdClippedAdultChinook
        ];

        $seriesData[] = [
            'name' => '10yr avg Ad Clipped Jack Chinook', 
            'color' => '#A9CCE3', 
            'type' => 'area',
            'data' => $areaAdClippedJackChinook
        ];

        $seriesData[] = [
            'name' => 'Ad Present Adult Chinook',
            'color' => '#0d233a',
            //'type' => $this->chartSettings['type'],
            'type' => 'line',
            'data' =>  $adPresentAdultChinook,
            //$request = $this->httpClient->get($this->urlPrefix.'/api/FishCounts/TestGuzzleData')
        ];

        $seriesData[] = [
            'name' => 'Ad Present Jack Chinook',
            'color' => '#8bbc21',
            //'type' => $this->chartSettings['type'],
            'type' => 'line',
            'data' =>  $adPresentJackChinook,
            //$request = $this->httpClient->get($this->urlPrefix.'/api/FishCounts/TestGuzzleData')
        ];

        $seriesData[] = [
            'name' => 'Ad Clipped Adult Chinook',
            'color' => '#910000',
            //'type' => $this->chartSettings['type'],
            'type' => 'line',
            'data' =>  $adClippedAdultChinook,
            //$request = $this->httpClient->get($this->urlPrefix.'/api/FishCounts/TestGuzzleData')
        ];

        $seriesData[] = [
            'name' => 'Ad Clipped Jack Chinook', 
            'color' => '#1a0000', 
            'type' => 'line', 
            'data' => $adClippedJackChinook
        ];

        if(!empty($notes)){
            $form['chart_wrapper']['footnotes'] = [
                '#markup' => '<div class="bold-footnotes"><p>'.$notes[0].'</p></div><br>',
            ];
        }

        $form['chart_wrapper']['chart'] = [
            '#theme' => 'charts_api_example',
            '#library' => (string) $library,
            '#categories' => $categories,
            '#seriesData' => $seriesData,
            '#options' => $options,
            '#id' => $chartId,
            '#override' => [],
        ];


        foreach($response->chinookTotals as $item){
            $totals['adPresentAdultChinook'] = $item->adPresentAdultChinook; 
            $totals['adPresentJackChinook'] = $item->adPresentJackChinook;
            $totals['adClippedAdultChinook'] = $item->adClippedAdultChinook;
            $totals['adClippedJackChinook'] = $item->adClippedJackChinook; 
            $totals['Total'] = $item->totals;
         }
 
    
        $headers = ['Species', 'Total Counts'];

        $rows = [
                    ['Ad Present Adult Chinook', $totals['adPresentAdultChinook']],
                    ['Ad Presnet Jack Chinook', $totals['adPresentJackChinook']],
                    ['Ad Clipped Adult Chinook', $totals['adClippedAdultChinook']],
                    ['Ad Clipped Jack Chinook', $totals['adClippedJackChinook']],
                    ['Total Chinook', $totals['Total']]
                ];


        $form['chart_wrapper']['table'] = [
            '#type' => 'table',
            '#caption' => $this->t('Chinook Totals ('.$year.')'),
            '#header' => $headers,
            '#rows' => $rows, 
            '#attributes' => ['class' => ['totals-table']]
        ];



    }

    private function CohoData(array &$form, FormStateInterface $form_state) {
        
        $library = $this->chartSettings['library'];
            if (empty($library)) {
            $this->messenger->addError($this->t('You need to first configure Charts default settings'));
            return [];
        }
        
        $options = [
            'type' => $this->chartSettings['type'],
            'title' => $this->t('Adult Coho Counts'),
            'xaxis_title' => $this->t('Years'),
            'yaxis_title' => $this->t('Count'),
            'yaxis_min' => '',
            'yaxis_max' => '',
            'height' => '',
            'three_dimensional' => FALSE,
            'title_position' => 'out',
            // 'legend_position' => [
            //     'layout' => 'horizontal',
            //     'align' => 'center',
            //     'verticalAlign' => 'bottom'
            // ],
            'legend_position' => 'bottom',
            'layout' => 'horizontal',
            'data_labels'=> $this->chartSettings['data_labels'],
            'height' => '500',
            'tooltips' => true, 

        ];

        

        $facility = $form_state->getValue('facility');
        $species = $form_state->getValue('species');
        $year = $form_state->getValue('year'); 
        $previousYear = (int) $year-1;

        $response = json_decode($this->httpClient->get($this->urlPrefix.'/api/FishCounts/GetCohoDetails?facilityid='
                                                                       .$facility
                                                                       .'&ladder=All&startdate=05/01/'
                                                                       .$previousYear.'&enddate=04/30/'
                                                                       .$year.'&sort=2')->getBody());

        $areaResponse = json_decode($this->httpClient->get($this->urlPrefix.'/api/FishCounts/GetChartData?FacilityID='
                                                                            .$facility.'&Species='
                                                                            .$species.'&Year='
                                                                            .$year)->getBody());


        foreach($response->cohoCount as $item) {
            $categories[] = $item->Date; 
            $adPresentAdultCoho[] = $item->wacoh;
            $adPresentJackCoho[] = $item->wjcoh;
            $adClippedAdultCoho[] = $item->hacoh;
            $adClippedJackCoho[] = $item->hjcoh;
            $adUnknownCoho[] = $item->uacoh + $item->ujcoh;
        }

        foreach($areaResponse as $item) {
            $areaAdPresentAdultCoho[] = $item->adPresentAdultCoho;
            $areaAdPresentJackCoho[] = $item->adPresentJackCoho;
            $areaAdClippedAdultCoho[] = $item->adClippedAdultCoho;
            $areaAdClippedJackCoho[] = $item->adClippedJackCoho;
            $areaAdUnknownCoho[] = $item->adUnknownCoho;
        }

        // foreach($response->cohoTotals as $item) {
        //      $totals[] = [
        //          $item->wacoh, 
        //          $item->wjcoh,
        //          $item->hacoh,
        //          $item->hjcoh,
        //          $item->uacoh + $item->ujcoh, 
        //          $item->Total_Hatchery + $item->Total_Wild + $item->Total_Unknown
        //      ];
        // }

        $headers = ['Ad Present Adult Coho', 'Ad Present Jack Coho', 'Ad Clipped Adult Coho', 'Ad Clipped Jack Coho','Ad Unknown Coho', 'Total Coho'];

        $seriesData[] = [
            'name' => '10yr avg Ad Present Adult Coho', 
            'color' => '#CD5C5C', 
            'type' => 'area', 
            'data' => $areaAdPresentAdultCoho
        ];

        $seriesData[] = [
            'name' => '10yr avg Ad Present Jack Coho', 
            'color' => '#DAF7A6', 
            'type' => 'area', 
            'data' =>  $areaAdPresentJackCoho
        ];

        $seriesData[] = [
            'name' => '10yr avg Ad Clipped Adult Coho', 
            'color' => '#7DCEA0', 
            'type' => 'area',
            'data' => $areaAdClippedAdultCoho
        ];

        $seriesData[] = [
            'name' => '10yr avg Ad Clipped Jack Coho', 
            'color' => '#A9CCE3', 
            'type' => 'area',
            'data' => $areaAdClippedJackCoho
        ];

        $seriesData[] = [
            'name' => '10yr avg Ad Unknown Coho', 
            'color' => '#7DCEA0', 
            'type' => 'area',
            'data' => $areaAdUnknownCoho
        ];

        $seriesData[] = [
            'name' => 'Ad Present Adult Coho',
            'color' => '#0d233a',
            //'type' => $this->chartSettings['type'],
            'type' => 'line',
            'data' =>  $adPresentAdultCoho,
            //$request = $this->httpClient->get($this->urlPrefix.'/api/FishCounts/TestGuzzleData')
        ];

        $seriesData[] = [
            'name' => 'Ad Present Jack Coho',
            'color' => '#8bbc21',
            //'type' => $this->chartSettings['type'],
            'type' => 'line',
            'data' =>  $adPresentJackCoho,
            //$request = $this->httpClient->get($this->urlPrefix.'/api/FishCounts/TestGuzzleData')
        ];

        $seriesData[] = [
            'name' => 'Ad Clipped Adult Coho',
            'color' => '#910000',
            //'type' => $this->chartSettings['type'],
            'type' => 'line',
            'data' =>  $adClippedAdultCoho,
            //$request = $this->httpClient->get($this->urlPrefix.'/api/FishCounts/TestGuzzleData')
        ];

        $seriesData[] = [
            'name' => 'Ad Clipped Jack Coho', 
            'color' => '#1a0000', 
            'type' => 'line', 
            'data' => $adClippedJackCoho
        ];

        $seriesData[] = [
            'name' => 'Ad Unknown Coho', 
            'color' => '#40ff00', 
            'type' => 'line', 
            'data' => $adUnknownCoho
        ];

        if(!empty($notes)){
            $form['chart_wrapper']['footnotes'] = [
                '#markup' => '<div class="bold-footnotes"><p>'.$notes[0].'</p></div><br>',
            ];
         }

        $form['chart_wrapper']['chart'] = [
            '#theme' => 'charts_api_example',
            '#library' => (string) $library,
            '#categories' => $categories,
            '#seriesData' => $seriesData,
            '#options' => $options,
            '#id' => $chartId,
            '#override' => []
        ];

        foreach($response->cohoTotals as $item) {
            $totals['wacoh'] = $item->wacoh; 
            $totals['wjcoh'] = $item->wjcoh;
            $totals['hacoh'] = $item->hacoh; 
            $totals['hjcoh'] = $item->hjcoh; 
            $totals['unknown'] = $item->uacoh + $item->ujcoh;
            $totals['Total'] = $item->Total_Hatchery + $item->Total_Wild + $item->Total_Unknown;  
        }

        //$headers = ['Ad Present Adult Coho', 'Ad Present Jack Coho', 'Ad Clipped Adult Coho', 'Ad Clipped Jack Coho','Ad Unknown Coho', 'Total Coho'];

        $headers = ['Species', 'Total Counts']; 

        $rows = [
                    ['Ad Present Adult Coho', $totals['wacoh']],
                    ['Ad Present Jack Coho', $totals['wjcoh']],
                    ['Ad Clipped Adult Coho', $totals['hacoh']],
                    ['Ad Clipped Jack Coho', $totals['hjcoh']], 
                    ['Ad Unknown Coho', $totals['unknown']], 
                    ['Total Coho', $totals['Total']]
                ];

        $form['chart_wrapper']['table'] = [
            '#type' => 'table',
            '#caption' => $this->t('Coho Totals ('.$previousYear.' - '.$year.')'),
            '#header' => $headers,
            '#rows' => $rows, 
            '#attributes' => ['class' => ['totals-table']]
        ];
    }


    private function buildYearList() {
		$body =  $this->dataAccess->GetSelectLists()->getBody();
		$body =  json_decode($body); 
		foreach ($body -> yearlist as $record) {
				$YearRows[$record] = $record; 				
		}

		return $YearRows; 

	}

    public function submitForm(array &$form, FormStateInterface $form_state) {}

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container) {
        return new static(
        $container->get('charts.settings'),
        $container->get('messenger'),
        $container->get('uuid'), 
        $container->get('http_client')
        );
    }

}