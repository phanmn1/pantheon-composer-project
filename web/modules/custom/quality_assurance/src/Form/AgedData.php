<?php

namespace Drupal\quality_assurance\Form;

use Drupal\stardb_queries\DataAccess; 
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;


class AgedData extends FormBase {

    protected $DataAccess;
    protected $count;

	public function __construct() {
        $this->DataAccess = new DataAccess(); 
        $this->count = 0; 
    }

    public function buildForm(array $form, FormStateInterface $form_state) {

        $uid = \Drupal::currentUser()->id();
        $user = \Drupal\user\Entity\User::load($uid);


        $form['#attached']['drupalSettings']['baseUrl'] =  \Drupal::config('apiurl')->get('value'); 
        $form['#attached']['library'][] = 'quality_assurance/datatables_library';
        $form['#attached']['library'][] = 'quality_assurance/agedData';
        $form['#attached']['library'][] = 'quality_assurance/calendar_library';
        $form['#attached']['drupalSettings']['username'] = $user->getUsername();

        //$body = $this->DataAccess->GetPagedData()->getBody(); 
        //$value = json_decode($body);

        $form['default_selects']['top'] = [
            '#type' => 'fieldset', 
            '#attributes' => 
                ['class' => 'default-selects']               
            ];

        $form['default_selects']['bottom'] = [
            '#type' => 'fieldset', 
            '#attributes' => 
                ['class' => 'default-selects']               
            ];


        $form['default_selects']['top']['facility'] = [
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

        $value = $this->DataAccess->GetTrapSelectLists()->getBody(); 
        $body = json_decode($value);
        //var_dump($body);



        

        $options['All'] = 'All'; 
        foreach($body -> SpeciesCodes as $key => $value) {
            $options[$value -> SppCode] = $value -> SppCode;  
        }

        $form['default_selects']['top']['Species'] = [
            '#type' => 'textfield', 
            '#title' => $this->t('Species code: '), 
            '#attributes' => [
                'maxlength' => 20, 
                'size' => 20
                ]
            
            //'#options' => $options,          
        ];

        $time = REQUEST_TIME; 
        $result = db_query("select  date_format(date_sub(CURDATE(), INTERVAL 7 DAY), '%m/%d/%Y') as LastDate");
        $row = $result->fetchAssoc();

    

        $form['default_selects']['top']['from'] = [
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


        $form['default_selects']['top']['to'] = [
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

        $form['default_selects']['bottom']['dna'] = [
            '#type' => 'textfield', 
            '#title' => 'DNA Sample ID: ', 
            '#attributes' => [
                'maxlength' => 20, 
                'size' => 20
            ]
            
        ]; 

        $form['default_selects']['bottom']['pittag'] = [
            '#type' => 'textfield',
            '#title' => 'PITTAG: ',
            '#attributes' => [
                'maxlength' => 20, 
                'size' => 20
            ]
        ]; 

        $form['default_selects']['bottom']['jvpittag'] = [
            '#type' => 'textfield',
            '#title' => 'JVPITTAG: ',
            '#attributes' => [
                'maxlength' => 20, 
                'size' => 20
            ]
        ];

        $form['default_selects']['bottom']['submit'] = [ 
            '#type' => 'submit', 
            '#value' => $this->t('Go'), 
            // '#submit' => ['::CallTableForm'],
            '#attributes' => [
            //call the first time that the class table wrapper is found so pick array 0
              //'onclick' => "document.getElementById('table-wrapper').style.visibility='visible'; return false;",
              'class' => ['btn-margin-wrapper']
              ]
          ]; 
      
          $form['default_selects']['bottom']['reset'] = [
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
        //     "#markup" => '<div><sup>*</sup>Editable rows</div>'
            
        //   ];
        
       
        $header = [
            
            // "Edit" => t(""),
            "SppCode" => t("SppCode"),
            "PassDate" => t("PassDate"),
            "PassTime" => t("PassTime"),
            "DNASample" => t("<span id='editable-column'><sup>*</sup>DNA Sample</span>"),
            "Pittag" => t("<span id='editable-column'><sup>*</sup>PitTag</span>"),
            "JvPitTag" => t("<span id='editable-column'><sup>*</sup>Juvenile Pittag</span>"), 
            "Forklgth" => t("Fork Length"), 
            "Mehlgth" => t("Meh Length"),
            "Weight" => t("Weight"), 
            "Status" => t("Status"),            
            "Sex" => t("Sex"),
            "Age" => t("<span id='editable-column'><sup>*</sup>Age</span>"), 
            "Scalesmpl" => t("<span id='editable-column'><sup>*</sup>Scale Sample</span>"),            
            "Comments" => t("Comments"),
            "ID" => t("ID"),
            "AdClip" =>t("Adipose Clip"),           
            "CarcassNo" => t("CarcassNo"),
            "Origin" => t("Origin"),
            "PohLgth" => t("Poh Length"),            
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
            //"Archive" => t("Archive"),
            "HPID" => t("HPID"), 
            "SampleSourceID" => t("SampleSourceID"), 
            "QC_ModifiedBy" => t("QC_ModifiedBy"),
            //"QC_Comments" => t("<div id='editable-column'><sup>*</sup>QC_Comments</div>")
            
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
        return 'aged_data_form';
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