<?php

namespace Drupal\quality_assurance\Form;


use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url; 
use Drupal\Component\Serialization\Json;
use Drupal\quality_assurance\Controller\LogReportsController; 
use Drupal\Core\Datetime\Element\Datetime; 
// use Drupal\Core\Link;
// use Drupal\Core\Ajax\AjaxResponse;
// use Drupal\Core\Ajax;


 class TrapSamples extends FormBase {

  public function buildForm(array $form, FormStateInterface $form_state) {
    //$form['#attached']['library'][] = 'core/drupal.dialog.ajax'; 
    $uid = \Drupal::currentUser()->id();
    $user = \Drupal\user\Entity\User::load($uid);
    $name = $user->getUsername();

    $urlPrefix = \Drupal::config('apiurl')->get('value'); 
    

    $form['#attached']['library'][] = 'quality_assurance/qc_form';
    $form['#attached']['library'][] = 'quality_assurance/calendar_library';
    $form['#attached']['library'][] = 'quality_assurance/datatables_library';
    $form['#attached']['drupalSettings']['username'] = $name;
    $form['#attached']['drupalSettings']['baseUrl'] = $urlPrefix;

    $form['title'] =[
      '#markup' => '<h1>QC Form - Trap Samples</h1>'
                    
                    
    ];
   
    
    $form['default_select'] = [
      '#type' => 'fieldset', 
      '#attributes' => 
        ['class' => 'default-selects']
    ];  
 
   

    $response = \Drupal::httpClient()->get($urlPrefix.'/api/TrapSamples/GetSelectLists');
    $body = json_decode($response->getBody()); 

    
   
   

    

   


    $form['default_select']['facility'] = [
      '#type' => 'select',
      '#title' => $this->t('Facility code: '),
      //'#options' => $facilityList,
      '#options' => [
        'pb' => $this->t('pb - Prosser Denil'),
        'ro' => $this->t('ro - Roza')
      ],
      '#attributes' => [
         'id' => 'facility'
         ]
      

    ]; 


    $options['All'] = 'All'; 
    foreach($body -> SpeciesCodes as $key => $value) {
      $options[$value -> SppCode] = $value -> SppCode;  
    }

    

    $form['default_select']['Species'] = [
      '#type' => 'select', 
      '#title' => $this->t('Species code: '), 
      '#options' => $options,          
    ];

    $time = REQUEST_TIME; 
    $result = db_query("select  date_format(date_sub(CURDATE(), INTERVAL 7 DAY), '%m/%d/%Y') as LastDate");
    $row = $result->fetchAssoc();

  

    $form['default_select']['from'] = [
      '#type' => 'textfield', 
      '#title' => $this->t('From Date: '), 
      '#default_value' => $row['LastDate'],
      '#attributes' => [
        'id'=> 'qa-demo-from', 
        'class' => ['datepicker-class-link'],
        'maxlength' => 20, 
        'size' => 20
        ]
    ];


    $form['default_select']['to'] = [
      '#type' => 'textfield', 
      '#title' => $this->t('To Date: '), 
      '#default_value' => date("m/d/Y", $time),
      '#attributes' => [
        'id'=> 'qa-demo-to', 
        'class' => ['datepicker-class-link'],
        'maxlength' => 20, 
        'size' => 20
        ]
    ]; 

   

    $form['default_select']['submit'] = [ 
      '#type' => 'submit', 
      '#value' => $this->t('Go'), 
      '#submit' => [
        //'callback' => '::CallTableForm', 
        ],
      '#attributes' => [
      //call the first time that the class table wrapper is found so pick array 0
        'class' => ['btn-margin-wrapper']
        ]
    ]; 

    $form['default_select']['reset'] = [
      '#type' => 'submit', 
      '#value' => $this->t('Reset'), 
      //'#submit' => ['::ResetForm'],
      '#attributes' => [
      //call the first time that the class table wrapper is found so pick array 0
        'class' => ['btn-margin-wrapper']
        ]

    ];

    /*
    $form['default_select']['advanced'] = [
      '#type' => 'link', 
      '#title' => t('Advanced options'),
      '#url' => '#',
      //'#submit' => ['::ResetForm'],
      '#attributes' => [
      //call the first time that the class table wrapper is found so pick array 0
        'class' => ['btn-margin-wrapper'],
        'id' => 'advanced-filter'
        ]

    ]; 
   */
    
    $form['default_select']['advanced'] = [
      '#markup' => '<a href="#" class="advanced-padding btn-margin-wrapper button btn" id="advanced-filter" data-toggle="modal" data-target="#myModal">Advanced options</a>'
    ]; 

    
    $form["note"] = [
      "#markup" => '<div class="note"><sup>*</sup>Note: For edits before 2010, 
      please contact staff (<a href="mailto:stem@yakamafish-nsn.gov">Michelle Steg-Geltner</a>).</div>'
    ];


    
    $header = [
      '' => t(''),
      'ID' => t('ID'), 
      'Edit' => t('Edit'),
      'LadCode' => ('LadCode'),
      'PassDate' => t('PassDate'), 
      'PassTime' => t('PassTime'),
      'SppCode' => t('SppCode'),
      'CarcassNo' => t('CarcassNo'),
      'PitTag' => t('PitTag'), 
      'JvPittag' => t('JvPittag'),
      'Status' => t('Status'), 
      'Origin' => t('Origin'),
      'Age' => t('Age'),
      'Sex' => t('Sex'),
      'Forklen' => t('Forklen'), 
      'Pohlen' => t('Pohlen'),
      'Mehlen' => t('Mehlen'),
      'Weight' => t('Weight'), 
      'Scalesmpl' => t('Scalesmpl'),
      'DNASample' => t('DNASample'), 
      'Injection' => t('Injection'), 
      'Comments' => t('Comments'), 
      'CWT' => t('CWT'),
      //'CWTSnout' => t('CWTSnout'), 
      'ElastomerColor' => t('ElastomerColor'), 
      'OCTSNT' => t('OCTSNT'), 
      'BodyLocation' => t('BodyLocation'), 
      'Mort' => t('Mort'), 
      'Channel' => t('Channel'), 
      'Release' => t('Release'), 
      'GonadStudy' => t('GonadStudy'), 
      'AdClip' => t('AdClip'), 
      'Ventral' => t('Ventral'), 
      'RTChannel' => t('RTChannel'), 
      'RTCode' => t('RTCode'), 
      'Brightness' => t('Brightness'), 
      'Tube' => t('Tube'), 
      'Drax' => t('Drax'), 
      'Operator' => t('Operator'), 
      'QC_Comments' => t('QC_Comments'),
      'HPID' => t('HPID'), 
      'SampleSourceID' => t('SampleSourceID')
    ];

    $form['Display Header'] = [
      '#markup' => '<div id="filter-output"></div>'
    ]; 

    $form['table'] = [
      '#type' => 'table', 
      '#header' => $header,
      '#attributes' => [
        'id' => 'qc-log-table',
        'class' => ['display'], 
        'cellspacing' => 0, 
        //'width' => '100%'
        //'max-width' => '100'
      ],	
    ];
    

    // $form['popup'] = [
    //   $value
    // ];



   
    return $form; 

   
  }




  public function getFormId() {
    return 'modal_demo_form_QC';
  }

  
  // public function ResetForm(array &$form, FormStateInterface $form_state) {
  // }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    $formValues = $form_state->getValues();

    //$dtime = Datetime::createFromFormat("d/m G:i", $formValues['from']);
    $startDate = strtotime($formValues['from']); 
    $endDate = strtotime($formValues['to']); 

    //var_dump($startTime);
    if( $startDate > $endDate) {
      $form_state->setErrorByName('from', $this->t('From date must not be bigger than to date.'));
    }
    


    //$startDate = new format($formValues['from']); 
    //$form_state->getValue(['default_select', 'to']); 
    // $startDate = new Date($formValues['from']); 
    // $endDate = new Date($formValues['to']); 
    // if($startDate > $endDate){
    //   $form_state->setErrorByName('from', $this->t('From date must not be bigger than to date.'));
    // }
    //var_dump($formValues['from']);
    //$form_state->setErrorByName('title', $this->t('The title must be at least 5 characters long.'));
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    
    //drupal_set_message('Added new entries');
    $form_state->setRebuild();
  }

  public function ajaxSubmitForm(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse(); 
    $response->addCommand(new CloseModalDialogCommand()); 
    return $response; 
  }





 }