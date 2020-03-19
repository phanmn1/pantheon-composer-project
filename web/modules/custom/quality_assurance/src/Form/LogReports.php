<?php 

namespace Drupal\quality_assurance\Form;

use Drupal\Core\Form\FormBase; 
use Drupal\Core\Form\FormStateInterface; 

class LogReports extends FormBase {

  

	public function buildForm(array $form, FormStateInterface $form_state) {

   
    
       
      global $base_url; 

      $form['#attached']['library'][] = 'quality_assurance/datatables_library';
      $form['#attached']['library'][] = 'quality_assurance/qc_log_table'; 
      $form['#attached']['library'][] = 'quality_assurance/calendar_library';
      $form['#attached']['drupalSettings']['baseUrl'] =  \Drupal::config('apiurl')->get('value'); 
      //$form['#attached']['library'][] = 'quality_assurance/calendar_library';
     // $form['#attached']['drupalSettings']['baseUrl'] = $base_url;


     $form['title'] =[
      '#markup' => '<h1>QC Log - Video and Samples</h1>'
      
    ];

      $form['default_select'] = [
        '#type' => 'fieldset', 
        '#attributes' => 
          ['class' => 'default-selects']
      ];

      $form['default_select']['facility'] = [
       '#type' => 'select',
       '#title' => $this->t('Facility'),
       '#options' => [
         '1' => $this->t('Prosser'),
         '2' => $this->t('Roza'), 
         '4' => $this->t('Cowiche'), 
         '6' => $this->t('Castile Falls')
       ],
       //'#empty_option' => $this->t('-select-'),
      //'#description' => $this->t('Title must be at least 5 characters in length.'),

     ]; 

    //  $key = $form_state->getValue('facility');
    //  var_dump($key);

    $time = REQUEST_TIME; 
    $result = db_query("select  date_format(date_sub(CURDATE(), INTERVAL 7 DAY), '%m/%d/%Y') as LastDate");
    $row = $result->fetchAssoc();


    $form['default_select']['filter'] = [
      '#type' => 'select',
      '#title' => $this->t('Filter by:'), 
      '#options' => [
         '1' => $this->t('Modified Date'),
         '2' => $this->t('Passage Date'),
       ],
    ];

     $form['default_select']['from'] = [
      '#type' => 'textfield', 
      '#title' => $this->t('From Date: '), 
      '#default_value' => $row['LastDate'],
      '#attributes' => [
        'id'=> 'qa-demo-from', 
        'class' => ['datepicker-class-link'],
        'maxlength' => 20, 
        'size' => 20
        ],
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
        ],
    ]; 

    

     $form['default_select']['submit'] = [ 
      '#type' => 'button', 
      '#value' => $this->t('Go'), 
     
      '#attributes' => [
        'class' => ['btn-margin-wrapper']
        ]
    ]; 

    $form['default_select']['reset'] = [ 
      '#type' => 'button', 
      '#value' => $this->t('Reset'), 
      '#submit' => ['::CallPassageForm'], 
      '#attributes' => [
      //call the first time that the class table wrapper is found so pick array 0
        'class' => ['btn-margin-wrapper']
        ]
	]; 
		// $form['tables'] = [
		// 	'#type' => 'markup',
		// 	'#markup' => '<div id="table-location"></div>'
    // ];
    
    $header = [
            
      // "Edit" => t(""),
      "SppCode" => t("SppCode"),
      "PassDate" => t("PassDate"),
      "PassTime" => t("PassTime"),
      "DNASample" => t("DNASample"),
      "Sex" => t("Sex"),
      "Age" => t("Age"), 
      "HPID" => t("HPID"), 
      "SampleSourceID" => t("SampleSourceID"), 
      "ModifiedDate" => t("Modified Date"),
      "QC_ModifiedBy" => t("QC_ModifiedBy"),
      "QC_Comments" => t("QC_Comments"),
      "PitTag" => t("PitTag"),
      "JvPitTag" => t("Juvenile Pittag"), 
      "Forklgth" => t("Fork Length"), 
      "Mehlgth" => t("Meh Length"),
      "Weight" => t("Weight"), 
      "Status" => t("Status"),                
      "Scalesmpl" => t("Scale Sample"),            
      "Comments" => t("Comments"),
      "ID" => t("ID"),
      "AdClip" =>t("Adipose Clip"),           
      "CarcassNo" => t("CarcassNo"),
      "Origin" => t("Origin"),
      "Pohlgth" => t("Poh Length"),            
      "Injection" => t("Injection"),
      "CWT" => t("CWT"),
      "ElastomerColor" => t("ElastomerColor"),
      "OCTSNT" => t("OCTSNT"),
      "BodyLocation" => t("BodyLocation"),
      "Mort" => t("Mort"), 
      "Channel" => t("Channel"),
      "Release" => t("Release"), 
      "GonadStudy" => t("GonadStudy"), 
      "VentralClip" => t("VentralClip"), 
      "RTChannel" => t("RTChannel"),
      "RTCode" => t("RTCode"), 
      "Brightness" => t("Brightness"),
      "Tube" => t("Tube"),
      "Drax" => t("Drax"), 
      "Operator" => t("Operator"),
      
      
      
  ];

    // $rows[] = [
    //   ["data" => "x"],
    //   ["data" => "x"],
    //   ["data" => "x"], 
    //   ["data" => "x"]
    // ];

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
			//'#markup' => '<div id="table-location"></div>'
   
    
    

    
		
    
    // $key = $form_state->getValue('facility');
    // var_dump($key);

    // $field_key = $form_state['values']['facility'];
    // $value = $form['facility']['#options'][$field_key];

		return $form; 
  }


	 public function submitForm(array &$form, FormStateInterface $form_state) {
    drupal_set_message($this->t('form data', array(
      '@faciliy' => $form_state->getValue('facility'), 
      '@from' => $form_state->getValue('from'), 
      '@to' => $form_state->getValue('to'),
    )));

    $form_state->setRebuild(); 

	}

	public function getFormId() {
		return 'qc_log_form';
	}
}