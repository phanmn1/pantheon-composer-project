<?php

namespace Drupal\quality_assurance\Form;


use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
// use Drupal\Core\Url; 
// use Drupal\Component\Serialization\Json;
// use Drupal\Core\Link;
// use Drupal\Core\Ajax\AjaxResponse;
// use Drupal\Core\Ajax;
// use Drupal\Core\Render\Element\Tableselect; 




  class AdultCounts extends FormBase {

  
public function buildForm(array $form, FormStateInterface $form_state) {
  
  $uid = \Drupal::currentUser()->id();
  $user = \Drupal\user\Entity\User::load($uid);
  $name = $user->getUsername();

  $urlPrefix = \Drupal::config('apiurl')->get('value'); 

    $form['#attached']['library'][] = 'quality_assurance/adult_counts';
    //$form['#attached']['library'][] = 'quality_assurance/datatables_editor_library';
    //$form['#attached']['library'][] = 'core/drupal.dialog.ajax';
    $form['#attached']['drupalSettings']['username'] = $name; 
    $form['#attached']['drupalSettings']['baseUrl'] =  \Drupal::config('apiurl')->get('value'); 
    $form['#attached']['library'][] = 'quality_assurance/datatables_library';
    $form['#attached']['library'][] = 'quality_assurance/calendar_library';
    //$form_state->setCached(TRUE);
    
    
    
    
    $form['title'] =[
      '#markup' => '<h1>QC Form - Adult Passage Video Counts</h1>'
    ];

    $form['default_selects'] = [
      '#type' => 'fieldset', 
      '#attributes' => [
        'class' => ['default-selects']
      ]
    ];

    //var_dump()

      $client = \Drupal::httpClient();
      $request = $client->get($urlPrefix.'/api/TrapSamples/GetSelectLists');
      $body = json_decode($request->getBody()); 
  

    foreach($body -> Facility as $key => $value) {
      $facilityList[$value -> FacilityID] =  $value -> FacilityName;  
    }
    

    $form['default_selects']['Facility'] = [
      '#type' => 'select',
      '#title' => $this->t('Facility: '),
      '#options' => $facilityList,
    
    ];

   

   
    $options['All'] = 'All'; 
    foreach($body -> SpeciesCodes as $key => $value) {
      $options[$value -> SppCode] = $value -> SppCode;  
    }

    // //Species List
    $form['default_selects']['Species'] = [
      '#type' => 'select', 
      '#title' => $this->t('Species: '), 
      '#options' => $options,
      
    ];

    $time = REQUEST_TIME; 
    $result = db_query("select  date_format(date_sub(CURDATE(), INTERVAL 7 DAY), '%m/%d/%Y') as LastDate");
    $row = $result->fetchAssoc();

    $form['default_selects']['from'] = [
      '#type' => 'textfield', 
      '#title' => $this->t('From Date: '), 
      '#default_value' => $row['LastDate'],
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

    $form["note"] = [
      "#markup" => '<div class="note"><sup>*</sup>Note: For edits before 2017, 
      please contact staff (<a href="mailto:stem@yakamafish-nsn.gov">Michelle Steg-Geltner</a>).</div>'
    ];



   

    $header = [
      '' => t(''),
      'ID' => t('ID'), 
      'Edit' => t('Edit'),
      'PassDate' => t('PassDate'), 
      'PassTime' => t('PassTime'), 
      'LadCode' => t('LadCode'), 
      'SppCode' => t('SppCode'), 
      'Viewer' => t('Viewer'), 
      'EstFKLength' => t('EstFKLength'), 
      'MarkID' => t('MarkID'), 
      //'numFish' => t('numFish'),
      'HPID' => t('HPID'), 
      'SampleSourceID' => t('SampleSourceID'),
      'QC_ModifiedBy' => t('QC_ModifiedBy'), 
      'QA Comment' => t('QC Comment'), 
      


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


 
  public function getFormId() {
    return 'demo_form';
  }

 

  /*
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $title = $form_state->getValue('title');
    if (strlen($title) < 5) {
      // Set an error for the form element with a key of "title".
      $form_state->setErrorByName('title', $this->t('The title must be at least 5 characters long.'));
    }
  } */

  /**
   * Implements a form submit handler.
   *
   * The submitForm method is the default method called for any submit elements.
   *
   * @param array $form
   *   The render array of the currently built form.
   * @param FormStateInterface $form_state
   *   Object describing the current state of the form.
   */
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
