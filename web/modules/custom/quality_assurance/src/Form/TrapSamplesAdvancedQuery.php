<?php 

namespace Drupal\quality_assurance\Form; 

use Drupal\Core\Form\FormBase; 
use Drupal\Core\Form\FormStateInterface; 
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\Url; 

class TrapSamplesAdvancedQuery extends FormBase {
  public function buildForm(array $form, FormStateInterface $form_state) {
    
    $urlPrefix = \Drupal::config('apiurl')->get('value'); 
    
    $form['#attached']['library'][] = 'quality_assurance/trap_samples_modal';
    $form['#attached']['library'][] = 'quality_assurance/calendar_library';
    $form['#attached']['library'][] = 'core/drupal.dialog.ajax'; 
    $form['#attached']['drupalSettings']['baseUrl'] = $urlPrefix;

  	$form['left'] = [
  	  '#type' => 'fieldset', 
  	  '#attributes' => [
  	    'class' => ['left']
  	  ]
  	];

  	$form['right'] = [
  	  '#type' => 'fieldset', 
  	  '#attributes' => [
  	    'class' => ['right']
  	  ]
    ];

     
    
    $response = \Drupal::httpClient()->get($urlPrefix.'/api/TrapSamples/GetSelectLists');
    $body = json_decode($response->getBody()); 

    foreach($body -> LadderCode as $key => $value) {
      $facilityList[$value -> LadCode] = $value -> LadCode." - ". $value -> LadName;  
    }

  	 $form['left']['Facility'] = [
      '#type' => 'select',
      '#title' => $this->t('Facility codes: '),
      '#options' => $facilityList,    
    ];

   


   
    $options['All'] = 'All'; 
    foreach($body -> SpeciesCodes as $key => $value) {
      $options[$value -> SppCode] = $value -> SppCode;  
    }

    $form['right']['Species'] = [
      '#type' => 'select', 
      '#title' => $this->t('Species'), 
      '#options' => $options,
     
    ];

    $time = REQUEST_TIME; 
    $result = db_query("select  date_format(date_sub(CURDATE(), INTERVAL 7 DAY), '%m/%d/%Y') as LastDate");
    $row = $result->fetchAssoc();


     $form['left']['from'] = [
      '#type' => 'textfield', 
      '#title' => $this->t('From Date: '), 
      '#default_value' => $row['LastDate'],
      '#attributes' => [
        'id'=> 'qa-demo-from-modal',
        'class' => ['datepicker-class-link'],
      	'size' => 20, 
        'maxlenth' => 20
      	],        
    ];

    $form['right']['to'] = [
      '#type' => 'textfield', 
      '#title' => $this->t('To Date: '), 
      '#default_value' => date("m/d/Y", $time),
      '#attributes' => [
      	'id' => 'qa-demo-to-modal',
        'class' => ['datepicker-class-link'],
      	'size' => 20, 
        'maxlength' => 20
      	],
    ]; 

    $form['left']['PassTimeFrom'] = [
      '#type' => 'textfield', 
      '#title' => $this->t('From Time: '), 
      //'#default_value' => date("m/d/Y", $time),
      '#attributes' => [
      	'id' => 'qa-demo-timefrom-modal',
        'class' => ['timepicker-class-link'],
      	'size' => 20, 
        'maxlength' => 20
      	],
    ]; 

    $form['right']['PassTimeTo'] = [
      '#type' => 'textfield', 
      '#title' => $this->t('To Time: '), 
      //'#default_value' => date("m/d/Y", $time),
      '#attributes' => [
      	'id' => 'qa-demo-timeto-modal',
        'class' => ['timepicker-class-link'],
      	'size' => 20, 
        'maxlength' => 20
        ],
      ];


    $form['left']['forklen'] = [
      '#type' => 'textfield', 
      '#title' => $this->t('forklgth from: '),
      '#attributes' => [
        // 'class' => 'text-box-size',
        'size' => 20,
        'maxlength' => 20
      ]
    ];

    $form['right']['forklento'] = [
      '#type' => 'textfield', 
      '#title' => $this->t('forklgth to: '), 
      '#attributes' => [
        // 'class' => 'text-box-size',
        'size' => 20, 
        'maxlength' => 20
      ]
    ];

    $form['left']['pohlen'] = [
      '#type' => 'textfield', 
      '#title' => $this->t('pohlgth from: '), 
      '#attributes' => [
        // 'class' => 'text-box-size',
        'size' => 20, 
        'maxlength' => 20
      ]
    ];

    $form['right']['pohlen_to'] = [
      '#type' => 'textfield', 
      '#title' => $this->t('pohlgth to: '), 
      '#attributes' => [
        // 'class' => 'text-box-size',
        'size' => 20, 
        'maxlength' => 20
      ]
    ];

    $form['left']['weight_from'] = [
      '#type' => 'textfield', 
      '#title' => $this->t('weight from: '), 
      '#attributes' => [
        // 'class' => 'text-box-size',
        'size' => 20, 
        'maxlength' => 20
      ]
    ];

    $form['right']['weight_to'] = [
      '#type' => 'textfield', 
      '#title' => $this->t('weight to: '),
      '#attributes' => [
        // 'class' => 'text-box-size',
        'size' => 20, 
        'maxlength' => 20
      ]
    ];

    // $form['left']['age-from'] = [
    //   '#type' => 'textfield', 
    //   '#title' => $this->t('Age from: '), 
    //   '#attributes' => [
    //     // 'class' => 'text-box-size',
    //     'size' => 20, 
    //     'maxlength' => 20
    //   ]
    // ]; 

    // $form['right']['age-to'] = [
    //   '#type' => 'textfield', 
    //   '#title' => $this->t('Age to: '), 
    //   '#attributes' => [
    //     // 'class' => 'text-box-size',
    //     'size' => 20, 
    //     'maxlength' => 20
    //   ]
    // ]; 

    $form['left']['dnasample'] = [
      '#type' => 'textfield', 
      '#title' => $this->t('dnasample: '),
      '#attributes' => [
        // 'class' => 'text-box-size',
        'size' => 20, 
        'maxlength' => 20
      ]
    ]; 

    // $form['right']['fishid'] = [
    //   '#type' => 'textfield', 
    //   '#title' => $this->t('FishID: '), 
    //   '#attributes' => [
    //     // 'class' => 'text-box-size',
    //     'size' => 20, 
    //     'maxlength' => 20
    //   ]
    // ]; 

    // $form['left']['scaleloc'] = [
    //   '#type' => 'textfield', 
    //   '#title' => $this->t('scaleloc: '), 
    //   '#attributes' => [
    //     // 'class' => 'text-box-size',
    //     'size' => 20, 
    //     'maxlength' => 20
    //   ]
    // ];

    $form['right']['pittag'] = [
      '#type' => 'textfield', 
      '#title' => $this->t('Pittag: '), 
      '#attributes' => [
        // 'class' => 'text-box-size',
        'size' => 20, 
        'maxlength' => 20
      ]
    ]; 

    $form['right']['JvPittag'] = [
      '#type' => 'textfield', 
      '#title' => $this->t('JvPittag: '), 
      '#attributes' => [
        // 'class' => 'text-box-size',
        'size' => 20, 
        'maxlength' => 20
      ]
    ]; 

    /*
    $form['left']['Facility'] = [
      '#type' => 'select',
      '#title' => $this->t('Facility codes: '),
      '#options' => $facilityList,
      
    ]; */

    foreach($body -> Sex as $key => $value) {
      $sex[$value -> SexCode] = $value -> Description;
    }

    
    $form['left']['sex'] = [
      '#type' => 'select', 
      '#title' => $this->t('sex: '), 
      '#options' => $sex,      
      '#empty_option' => t('- None -')
    ]; 
    
    


    $form['left']['CWTSnout'] = [
      '#title' => $this->t('CWTSnout'), 
      //'#options' => ['CWTSnout'],
      '#type' => 'checkbox', 
      //
      '#attributes' => [
        // 'class' => 'text-box-size',
        // 'size' => 20, 
        // 'maxlength' => 20
      ]
    ]; 

    $form['left']['adclip'] = [
      '#title' => $this->t('Adclip'), 
      //'#options' => ['CWTSnout'],
      '#type' => 'checkbox', 
      //
      '#attributes' => [
        // 'class' => 'text-box-size',
        // 'size' => 20, 
        // 'maxlength' => 20
      ]
    ]; 

    

    foreach($body -> Status as $key => $value) {
      $status[$value -> Status] = $value -> Description;
    }

    $form['right']['status'] = [
      '#type' => 'select', 
      '#title' => $this->t('status: '), 
      '#options' => $status,     
      '#empty_option' => t('- None -')
      
    ]; 

    foreach($body -> Origin as $key => $value) {
      $origin[$value -> OriginCode] = $value -> Description; 
    }


    $form['right']['origin'] = [
      '#type' => 'select', 
      '#title' => $this->t('origin: '), 
      '#options' => $origin,
      /*[
        "--" => $this->t("--"), 
        "??" => $this->t("Undetermined"),
        "HC" => $this->t("Hatchery Control"),
        "SH" => $this->t("Supplementation Hatchery"),
        "WN" => $this->t("Wild/Natural")

      ] */
        
      '#empty_option' => t('- None -'),
      
    ];
    
    
    $form['comment'] = [
      '#type' => 'textfield', 
      '#title' => $this->t('comment: '), 
      '#attributes' => [
        // 'class' => 'text-box-size',
        'size' => 80, 
        'maxlength' => 80
      ]
    ]; 
   





    return $form; 
  }

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

  public function getFormId() {
    return 'modal_trap_samples_advanced'; 
  }


  public function submitForm(array &$form, FormStateInterface $form_state) {
    //drupal_set_message('Return Results');
    $form_state->setRedirect('quality_assurance.trap_samples_qc'); 
  }

  


}