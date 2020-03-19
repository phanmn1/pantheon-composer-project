<?php

namespace Drupal\stardb_queries\Form;


use Drupal\stardb_queries\DataAccess; 
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\select_or_other\Element\Select; 
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax;
use Drupal\Core\Ajax\OpenModalDialogCommand;

//include Drupal\Core\Classes\PHPExcel; 



class PassageDetails extends FormBase {

	protected $DataAccess;


	public function __construct() {
		$this->DataAccess = new DataAccess(); 
	}
	
	
	public function getFormId() {
		return 'adult_passage_form';
  	}
  
  public function buildForm(array $form, FormStateInterface $form_state) {
	  
	 $form['#attached']['library'][] = 'core/drupal.dialog.ajax';
	 $form_state->setCached(FALSE);
		//$def = array('year' => 2007, 'month' => 2, 'day' => 15); 
	 
	 $FacilityRows = array();
	  //$SpeciesRows = array(); 
	  
	//   $controllers = new QueryControllers; 
	//   $value = $controllers -> getfacilities ();

	$body =  $this->DataAccess->GetSelectLists()->getBody();
	$value =  json_decode($body); 	  
	
	  
	  foreach ($value->facilityList as $record) {
		  $FacilityRows[$record->FacilityID] = $record->FacilityName;  
	  }

	  $form['title'] = [
		'#type' => 'fieldset',
		'#attributes' => [
			'class' => ['title']
			]
		];


	  $form['select-lists'] = [
		'#type' => 'fieldset',
		'#attributes' => [
			'class' => ['select-lists']
			]
		];

	  
	  $form['buttons'] = [
		  '#type' => 'fieldset', 
		  '#attributes' => [
			  'class' => ['buttons']
		  	]
		  ];

	  $form['table'] = [
		'#type' => 'fieldset', 
		'#attributes' => [
			'class' => ['table']
			]
		];

	   
	  
	  
	   $form['select-lists']['Facility'] = [ 
		'#type' => 'select', 
		'#title' => $this->t('Facility'), 
		'#options' => $FacilityRows,
		
		//'#empty_option' => $this->t('All'),
		/*
		'#attributes' => array ( 
			'id' => 'facility_list'), 
		
		'#attached' => [
			'library' => [
				'stardb_queries/AdultPassgeCSS',
			],
         		
		], */
			
		//'#options_attributes' => array (
		//$rows => array('id' => array($attributes)) 
			//	)
		];
		
		//Ladder($form[], $form_state);

		$form['title']['title'] =[
			'#markup' => '<h1>Adult Passage Count Details</h1>'
		  ];

		//  $controllers = new QueryControllers;
		//  $FacilityID = $form_state->getValue('Facility');
		//  $value = $controllers->defaultLadder();
	
		 $rows['All'] = 'All';  
		 foreach ($value->defaultLadder as $record) {
		 	//we don't want to see the lamprey sides 
		 	if($record->LadCode != 'vr1' || $record->LadCode != 'vr2') {
		  		$rows[$record->LadCode] = $record->LadName;  
		  	}
		}

		 $form['select-lists']['ladder'] = [
		  '#type' => 'select', 
		  '#title' => $this->t('Ladder'),
		  '#options' => $rows,
		  '#states' => array (
				'visible' => array(
					':input[name="Facility"]' => array('value' => '1'),
					),
				),

		];
	  

		
		
		$form['select-lists']['species'] = [
		'#type' => 'select', 
		'#title' => $this->t('Species'), 
		'#options' => [
			'0' => $this->t('Chinook'),
			'1' => $this->t('Coho'), 
			'2' => $this->t('Sockeye'), 
			'3' => $this->t('Steelhead'), 
			'4' => $this->t('Other species')
			], 
		'#attributes' => array(
			'id' => 'species_list_details'), 
		'#attached' => [
			'library' => [
				'stardb_queries/AdultPassgeCSS',
			]
		  ]
		];
		
		$time = REQUEST_TIME;
		$result = db_query("select  date_format(date_sub(CURDATE(), INTERVAL 7 DAY), '%m/%d/%Y') as LastDate");
		
		$row = $result->fetchAssoc(); 
		
	$form['select-lists']['from'] = [
      '#type' => 'textfield',
      '#title' => $this->t('From Date: '), 
	  '#default_value' => $row['LastDate'], 
	  '#attributes' => array(
			'id' => 'from-date'),
	  '#attached' => [
			'library' => [
				'stardb_queries/AdultPassgeCSS',
					],
			],

    ];
	
	$form['select-lists']['to'] = [
      '#type' => 'textfield',
      '#title' => $this->t('To Date: '),
      '#default_value' => date("m/d/Y", $time),
      '#attributes' => array(
			'id' => 'to-date'), 
	  
    ];
	
	$form['select-lists']['sort'] = [
	   '#type' => 'select', 
	   '#title' => $this -> t('Date Sort By'), 
	   '#options' =>  
				['2' => $this->t('Ascending'),
				 '1' => $this->t('Descending') 
				 
				], 
	   '#attributes' => array('id' => 'list-sort'), 
	   '#attached' => [
			'library' => [ 
				'stardb_queries/AdultPassgeCSS',
				],
			]
		];
		
	
	$form['buttons']['submit'] = [
		'#type' => 'button',
		'#value' => 'Go',
		'#ajax' => [
			'callback' => '::TableCallback', 
		     'wrapper' => 'table-wrapper',
			 'event' => 'click'			
			],
			'#attributes' => array('onclick' => "document.getElementById('edit-export').style.visibility='visible'; return false;"),
		];
		
		$form['buttons']['clear'] = array(			
			'#type' => 'button',
			'#value' => t('Reset'),	
			
			'#ajax' => [
				'callback' => '::ResetTable', 
				'wrapper' => 'table-wrapper',
				'progress' => array('type' => 'none'),	
				'event' => 'click'
			], 
			'#attributes' => array('onclick' => 'this.form.reset(); return false;', 
								   'onclick' => "document.getElementById('edit-export').style.visibility='hidden'; return false;"),
			
			);
			
			// $form['buttons']['graph'] = [
			// 	'#type' => 'button', 
			// 	'#value' => t('Show graph'), 
			// 	'#ajax' => [
			// 		 'callback' => '::ModalWindow', 
			// 		 'wrapper' => 'table-wrapper',
			// 		 'event' => 'click'
			//  		],			
			//  ];
		  
		$form['buttons']['export'] = [
			'#type' => 'submit', 
			//'#type' => 'button', 
			'#value' => 'Export to csv', 
			/*'#ajax' => [
				'callback' => '::ExportToCSV', 
				'wrapper' => 'table-wrapper' 
				], */
			//'#submit' => ['::ExportToCSV'],
			//'#attributes' => array('id' => 'export-button'),
		 ];
		
	
		 $form['table_wrapper'] = [
		'#type'=> 'container',
        '#attributes' => 
			['id' => 'table-wrapper'],		
	   ];
	   
	   
		$form['scrolltop'] = [
			'#markup' => '<a href="#" class="scrollup">Scroll</a>',
		];
		
		//var_dump(get_defined_vars());
		//$Ladder =     $form_state->getValue('ladder');
		
		return $form;
		
	 
	}
	
	public function ModalWindow(array &$form, FormStateInterface $form_state){
	
		 
		$params['facility'] = $form_state->getValue('Facility');
		$params['species'] = $form_state->getValue('species');
		$params['year'] = date('Y',strtotime($form_state ->getValue('from')));
		$params['startdate'] = $form_state ->getValue('from'); 
		$params['enddate'] = $form_state->getValue('to'); 
		$params['type'] = 2;

		

		
		$response = new AjaxResponse(); 
		$modal_form = \Drupal::formBuilder()->getForm('Drupal\charts_test\Form\ChartModalForm', $params); 
		$response->addCommand(new OpenModalDialogCommand(
				'Adult Passage Graph',
				$modal_form,
				['width' => '1000', 'height' => '600']
		));

		return $response; 
	}
  
  public function TableCallback (array &$form, FormStateInterface $form_state ) {

	$current_path = \Drupal::service('path.current')->getPath();
	//$current_path = 'aaa';

	preg_match('/passage_details/',$current_path, $matches); 
     
	 $FacilityID = $form_state->getValue('Facility');
	 $Ladder =    $form_state->getValue('ladder');
	 //$Ladders = $form_state['values']['ladder'];
	 $SpeciesNum = $form_state->getValue('species');
	 $StartDate =  $form_state ->getValue('from'); 
	 $EndDate =    $form_state->getValue('to'); 
	 $Sort =       $form_state->getValue('sort');
	 
	 $StartDateDate = strtotime($StartDate); 
	 $EndDateDate = strtotime($EndDate); 
	 
	 if($StartDateDate > $EndDateDate) {
		  $response = new AjaxResponse();
		  $title = 'Query Error'; 
		  $content = '<div class="test-popup-content"> Check Input: From date is greater than to date </div>';
		  $options = array(
			'dialogClass' => 'popup-dialog-class',
			'width' => '300',
			'height' => '300',

      );

      $response->addCommand(new OpenModalDialogCommand($title, $content, $options));
	  return $response; 
    }
	  
	// $Ladder = 'pb'; 
	// $values = $form_state->getValues(); 
	 //$Ladder = $values['ladder']; 
	 
	 //$controllers = new QueryControllers; 
	 //$result = $controllers->getSpeciesList ($SpeciesNum);
	//  $body =  $this->DataAccess->GetSelectLists()->getBody();
	//  $value =  json_decode($body);
	
	
	 $footnotes =  json_decode($this->DataAccess->GetFacilityNotesDetails( $FacilityID, $SpeciesNum, $StartDate, $EndDate)->getBody());
	 $result = json_decode($this->DataAccess->GetSpeciesList($SpeciesNum)->getBody());
	 
	 //$abc = $description['ConcatenatedREsult'];
	 $first = true; 

	 
	 
	 foreach($result as $value) {
		 if ($first) {
			$defx  = '<b>'.$value->SppCode.'</b> - '.$value->SppType;
			$first = false; 
		 } else {
			 $defx = $defx.', <b>'.$value->SppCode.'</b> - '.$value->SppType; 
		 }
	 }
	 
	 switch($SpeciesNum) {
		 case '0': //Chinook Code 
			$header = array('Facility Name', 'Ladder', array('data' =>'Date', 'class' => 'right-border'), 														 							
							'wasck', 
							'wjsck', 
							'hasck', 
							'hjsck',							 							
							'uasck',
							'ujsck', 
							array('data' => 'Total Spring Chinook', 'class' => 'right-border'), 
							'wasum', 
							'wjsum', 
							'hasum', 
							'hjsum',							
							 array('data' => 'Total Summer Chinook', 'class' => 'right-border'), 
							'wafck', 
							'wjfck', 
							'hafck', 
							'hjfck', 							
							'uafck',
						     array('data' => 'Total Fall Chinook', 'class' => 'right-border')); 
			 $body =  $this->DataAccess->getChinookDetails($FacilityID, $Ladder,  $StartDate, $EndDate,$Sort)->getBody();
			 $value = json_decode($body); 
			 //var_dump($value->chinookCount);

			foreach($value->chinookCount as $record) {
				
				
				
				
				  $this->zeroData($record->wasck);
				  $this->zeroData($record->wjsck);
				  $this->zeroData($record->hasck);
				  $this->zeroData($record->hjsck);
				  $this->zeroData($record->uasck);
				  $this->zeroData($record->ujsck);
				  $this->zeroData($record->Total_Spring_Chinook);
				  $this->zeroData($record->wasum);
				  $this->zeroData($record->wjsum);
				  $this->zeroData($record->hasum);
				  $this->zeroData($record->hjsum); 
				  $this->zeroData($record->Total_Summer_Chinook);
				  $this->zeroData($record->wafck);
				  $this->zeroData($record->wjfck);
				  $this->zeroData($record->hafck);
				  $this->zeroData($record->hjfck);
				  $this->zeroData($record->uafck);
				  $this->zeroData($record->Total_Fall_Chinook);
				
				
				
				
				$rows[] = array($record->FacilityName, 
						$record->Ladder, 
						array('data' => $record->Date, 'class' => 'right-border'), 																 																								 
						$record->wasck,
						$record->wjsck, 
						$record->hasck, 
						$record->hjsck, 														
						$record->uasck,
						$record->ujsck, 
						array('data' => $record->Total_Spring_Chinook, 'class' => 'right-border'),								 
						$record->wasum,								
						$record->wjsum,
						$record->hasum, 
						$record->hjsum,								 
						array('data' => $record->Total_Summer_Chinook, 'class' => 'right-border'),
						$record->wafck, 
						$record->wjfck, 
						$record->hafck, 
						$record->hjfck,
						$record->uafck,
						array('data' => $record->Total_Fall_Chinook, 'class' => 'right-border')
					);
						
			}
			 
			
			foreach ($value->chinookTotals as $record) {

				  $this->zeroData($record->wasck);
				  $this->zeroData($record->wjsck);
				  $this->zeroData($record->hasck);
				  $this->zeroData($record->hjsck);
				  $this->zeroData($record->uasck);
				  $this->zeroData($record->ujsck);
				  $this->zeroData($record->Total_Spring_Chinook);
				  $this->zeroData($record->wasum);
				  $this->zeroData($record->wjsum);
				  $this->zeroData($record->hasum);
				  $this->zeroData($record->hjsum); 
				  $this->zeroData($record->Total_Summer_Chinook);
				  $this->zeroData($record->wafck);
				  $this->zeroData($record->wjfck);
				  $this->zeroData($record->hafck);
				  $this->zeroData($record->hjfck);
				  $this->zeroData($record->uafck);
				  $this->zeroData($record->Total_Fall_Chinook);

				  
				  
				 
				
				$footerRows [] = array(
									   array('data' => 'Totals', 'colspan' => 3, 'class' => 'title-right-border'), 
									   array('data' => $record ->wasck, 'class' => 'title'), 
									   array('data' => $record ->wjsck, 'class' => 'title'), 
									   array('data' => $record ->hasck, 'class' => 'title'), 
									   array('data' => $record ->hjsck, 'class' => 'title'), 
									   array('data' => $record ->uasck, 'class' => 'title'), 
									   array('data' => $record ->ujsck, 'class' => 'title'), 
									   array('data' => $record ->Total_Spring_Chinook, 'class' => 'title-right-border'), 
									   array('data' => $record ->wasum, 'class' => 'title'), 
									   array('data' => $record ->wjsum, 'class' => 'title'), 
									   array('data' => $record ->hasum, 'class' => 'title'), 
									   array('data' => $record ->hjsum, 'class' => 'title'),
									   array('data' => $record ->Total_Summer_Chinook, 'class' => 'title-right-border'), 
									   array('data' => $record ->wafck, 'class' => 'title'), 
									   array('data' => $record ->wjfck, 'class' => 'title'), 
									   array('data' => $record ->hafck, 'class' => 'title'), 
									   array('data' => $record ->hjfck, 'class' => 'title'), 
									   array('data' => $record ->uafck, 'class' => 'title'), 
									   array('data' => $record ->Total_Fall_Chinook, 'class' => 'title-right-border')
									   );
			}
					
			break; 
		case '1': //coho
			$header = array('Facility Name', 'Ladder' ,array('data' => 'Date', 'class' => 'right-border'), 							
							'wacoh', 
							'wjcoh', 
							array('data' => 'Total Wild', 'class' => 'right-border'),
							'hacoh', 
							'hjcoh',
							array('data' => 'Total Hatchery', 'class' => 'right-border'),							
							'uacoh',
							'ujcoh', 
							array('data' => 'Total Unknown', 'class' => 'right-border')); 
			$body =  $this->DataAccess->getCohoDetails($FacilityID, $Ladder,  $StartDate, $EndDate,$Sort)->getBody();
			$value = json_decode($body);

			foreach($value->cohoCount as $record) {
				$this->zeroData($record ->wacoh);
				$this->zeroData($record ->wjcoh);
				$this->zeroData($record ->Total_Wild);
				$this->zeroData($record ->hacoh);
				$this->zeroData($record ->hjcoh);
				$this->zeroData($record ->Total_Hatchery);
				$this->zeroData($record ->uacoh);
				$this->zeroData($record ->ujcoh);
				$this->zeroData($record ->Total_Unknown);

				$rows[] = array($record->FacilityName,
							   $record->Ladder,
							   array('data' => $record->Date, 'class' => 'right-border'),							   
							   $record->wacoh, 
							   $record->wjcoh, 
							   array('data' => $record->Total_Wild, 'class' => 'right-border'),
							   $record->hacoh, 
							   $record->hjcoh,							   
							   array('data' => $record->Total_Hatchery, 'class' => 'right-border'), 							  
							   $record->uacoh,
							   $record->ujcoh, 
							   array('data' => $record->Total_Unknown, 'class' => 'right-border')
								); 

			} 
			
			foreach ($value->cohoTotals as $record) {


				$this->zeroData($record ->wacoh);
				$this->zeroData($record ->wjcoh);
				$this->zeroData($record ->Total_Wild);
				$this->zeroData($record ->hacoh);
				$this->zeroData($record ->Total_Hatchery);
				$this->zeroData($record ->uacoh);
				$this->zeroData($record ->ujcoh);
				$this->zeroData($record ->Total_Unknown);
				
				$footerRows [] = array(array('data' => 'Totals', 'colspan' => 3, 'class' => 'title-right-border'), 
									   array('data' => $record ->wacoh, 'class' =>'title'), 
									   array('data' => $record ->wjcoh, 'class' =>'title'), 
									   array('data' => $record ->Total_Wild, 'class' => 'title-right-border'), 
									   array('data' => $record ->hacoh, 'class' => 'title'), 
									   array('data' => $record ->hjcoh, 'class' => 'title'), 
									   array('data' => $record ->Total_Hatchery, 'class' => 'title-right-border'), 
									   array('data' => $record ->uacoh, 'class' => 'title'), 
									   array('data' => $record ->ujcoh, 'class' => 'title'), 
									   array('data' => $record ->Total_Unknown, 'class' => 'title-right-border')
										);
			}
			break; 
		case '2': //sockeye
			$header = array('Facility Name','Ladder', 'Date', 
							  'wsock', 'hsock', 'Total Sockeye'); 


			$body =  $this->DataAccess->getSockeyeDetails($FacilityID, $Ladder,  $StartDate, $EndDate,$Sort)->getBody();
			$value = json_decode($body);
			// $rows = $controllers -> getSockeyeCountDetails ($FacilityID, $Ladder , $StartDate, $EndDate, $Sort); 
			
			// $value2 = $controllers -> getSockeyeTotals ($FacilityID, $Ladder, $StartDate, $EndDate); 
			
			foreach($value->sockeyeCount as $record) {
				$this->zeroData($record ->wsock);
				$this->zeroData($record ->hsock);
				$this->zeroData($record ->Total_Sockeye);

				$rows[] = array($record->facilityName, 
								$record->Ladder, 
								$record->Date,							   
								$record->wsock, 
								$record->hsock,	
								$record->Total_Sockeye
				
			 ); 
			}
			
			
			
			foreach ($value->sockeyeTotals as $record) {

				$this->zeroData($record ->wsock);
				$this->zeroData($record ->hsock);
				$this->zeroData($record ->Total_Sockeye);
				
				$footerRows [] = array(array('data' => 'Totals', 'colspan' => 3, 'class' => 'title'), 
									   array('data' => $record ->wsock, 'class' =>'title'), 
									   array('data' => $record ->hsock, 'class' =>'title'), 
									   array('data' => $record ->Total_Sockeye, 'class' => 'title')
										);
			}
			
			break; 
		case '3': //steelhead
			$header = array('Facility Name', 'Ladder', 'Date', 
							'wsth', 'hsth', 'usth','Total Steelhead'); 
			
			$body =  $this->DataAccess->getSteelheadDetails($FacilityID, $Ladder,  $StartDate, $EndDate,$Sort)->getBody();
			$value = json_decode($body);

			foreach($value->steelheadCount as $record) {
				$this->zeroData($record ->wsth);
				$this->zeroData($record ->hsth);
				$this->zeroData($record ->usth);
				$this->zeroData($record ->Total_Steelhead);

				$rows[] = array($record->FacilityName, 
							$record->Ladder,
							$record->Date,
							$record->wsth,
							$record->hsth, 							  							  
							$record->usth, 
							$record->Total_Steelhead
							); 
			}
			
			foreach ($value->steelheadTotals as $record) {

				$this->zeroData($record ->wsth);
				$this->zeroData($record ->hsth);
				$this->zeroData($record ->usth);
				$this->zeroData($record ->Total_Steelhead);
								
				$footerRows [] = array(array('data' => 'Totals', 'colspan' => 3, 'class' => 'title'), 
									   array('data' => $record ->wsth, 'class' =>'title'), 
									   array('data' => $record ->hsth, 'class' =>'title'), 
									   array('data' => $record ->usth, 'class' =>'title'),
									   array('data' => $record ->Total_Steelhead, 'class' => 'title')
										);
			}
			
			break; 
		case '4': //other fish
		
			$header = array('Facility Name','Ladder', 'Date', 'brwntr','bulltr', 'lamp', 'raintr', 'smbass','Total Fish'); 
			
			$body =  $this->DataAccess->OtherFishDetails($FacilityID, $Ladder,  $StartDate, $EndDate,$Sort)->getBody();
			$value = json_decode($body);

			//var_dump($value);

			foreach($value->otherFishDetails as $record) {
				$this->zeroData($record ->brwntr);
				$this->zeroData($record ->bulltr);
				$this->zeroData($record ->lamp);
				$this->zeroData($record ->rbt);
				$this->zeroData($record ->smbass);
				$this->zeroData($record ->Total_Fish);

				$rows[] = array($record->facilityName,
				$record->Ladder,
				$record->Date, 
				$record->brwntr, 
				$record->bulltr,
				$record->lamp, 
				$record->rbt, 
				$record->smbass, 
				$record->Total_Fish
				);
			} 
			
			foreach ($value->otherFishTotals as $record) {

				$this->zeroData($record ->brwntr);
				$this->zeroData($record ->bulltr);
				$this->zeroData($record ->lamp);
				$this->zeroData($record ->rbt);
				$this->zeroData($record ->smbass);
				$this->zeroData($record ->Total_Fish);

				$footerRows [] = array(array('data' => 'Totals', 'colspan' => 3, 'class' => 'title'), 
									   array('data' => $record ->brwntr, 'class' =>'title'), 
									   array('data' => $record ->bulltr, 'class' =>'title'), 
									   array('data' => $record ->lamp, 'class' =>'title'),
									   array('data' => $record ->rbt, 'class' => 'title'), 
									   array('data' => $record ->smbass, 'class' => 'title'),
									   array('data' => $record ->Total_Fish, 'class' => 'title')
										);
			}
			
			break; 
			
	 }
	 //var_dump(get_defined_vars());
	 
	  $form['table_wrapper']['count'] = [
		'#markup'=>'<p>Record Count: '.count($rows).'</p>', 
				
	]; 
	 

	 $form['table_wrapper']['scroll_wrapper'] = [
	    '#markup' => '<div class="scroll-wrapper">'
	 ];	

	 

	 if(!empty($footnotes)){
		$form['table_wrapper']['footnotes'] = [
			'#markup' => '<div class="bold-footnotes"><p>'.implode("<br><br>", $footnotes).'</p></div><br>',
		];
	 }


	 if($matches[0] == 'passage_details') {

		$form ['table_wrapper']['table'] = [
			'#type' => 'table', 
			//'#title' => 'Table',
			'#header' => $header, 
			'#sticky' => true, 
			'#rows' => $rows, 
			'#attributes' => array('id' => 'results-db-details'),
			'#attached' => [
				'library' => [
					'stardb_queries/Sticky',
				], 
			], 
			//'#description' => 'Checkboxes, #type = checkboxes',
			'#footer' => $footerRows, 
			]; 
	 
	} else {
	 $form ['table_wrapper']['table'] = [
		'#type' => 'table', 
		//'#title' => 'Table',
		'#header' => $header, 
		'#sticky' => true, 
		'#rows' => $rows, 
		'#attributes' => array('id' => 'results-db-details'),
		//'#description' => 'Checkboxes, #type = checkboxes',
		'#footer' => $footerRows, 
		]; 
	}
	$form['table_wrapper']['scroll_wrapper_end'] = [
	    '#markup' => '</div>'
	 ];	



		
	$form['table_wrapper']['description'] = [
		'#markup'=>'<br><p><b><u>*Species Codes </u></b> <br>'.$defx.'</p>' 
		
		];
		
		$form['table_wrapper']['notes'] = [
		'#markup'=>'<p>*Counts for the most recent days may be partial counts</p>', 
		
		
		]; 

	
	

		
		
	 
	  //var_dump(get_defined_vars());
	  
	  return $form['table_wrapper']; 
  }
  
   public function ResetTable (array &$form, FormStateInterface $form_state) {

	//return empty form to reset query results 
	 $form ['table_wrapper']['submit'] = [

	  ]; 
	  
	  return $form['table_wrapper']; 
  }
  
	 
	public function submitForm(array &$form, FormStateInterface $form_state) {
	 $FacilityID = $form_state->getValue('Facility');
	 $Ladder =    $form_state->getValue('ladder');
	 //$Ladders = $form_state['values']['ladder'];
	 $SpeciesNum = $form_state->getValue('species');
	 $StartDate =  $form_state ->getValue('from'); 
	 $EndDate =    $form_state->getValue('to'); 
	 $Sort =       $form_state->getValue('sort');
	  
	// $Ladder = 'pb'; 
	// $values = $form_state->getValues(); 
	 //$Ladder = $values['ladder']; 
	 
	 $result = json_decode($this->DataAccess->getSpeciesList($SpeciesNum)->getBody()); 
	
	 
	 //$abc = $description['ConcatenatedREsult'];
	 $first = false; 
	 $Count = 0; 
	 $display[] = array(); 
	 $display[] = array('Species Codes*'); 
	 $defx = "";
	 //var_dump(json_decode($result));
	 
	 foreach($result as $value) {	 
		 if ($Count % 5 != 0) {			 
				$defx  = $defx. ', '.$value->SppCode.' - '.$value->SppType;
				//$first = false; 
				++$Count; 			 
		} else {	
			$display[] = array($defx); 
			$defx = $value->SppCode.' - '.$value->SppType; 
		    ++$Count; //add new line in formatting sppCodes 
			//$first = true;
		}

	 }
	 
	 $display[] = array($defx); 
	 //$defx = array($defx); 
	 
	 switch($SpeciesNum) {
		 case '0': //Chinook Code 
			$header = array('Facility Name', 'Ladder', 
							'Date', 														 							
							'wasck', 
							'wjsck', 
							'hasck', 
							'hjsck',							 							
							'uasck',
							'ujsck', 
							'Total Spring Chinook',  
							'wasum', 
							'wjsum', 
							'hasum', 
							'hjsum',							
							'Total Summer Chinook', 
							'wafck', 
							'wjfck', 
							'hafck', 
							'hjfck', 							
							'uafck',
							'Total Fall Chinook',); 
							
			$body =  $this->DataAccess->getChinookDetails($FacilityID, $Ladder,  $StartDate, $EndDate,$Sort)->getBody();
			$value = json_decode($body);

			// $rows = $controllers -> getChinookCountDetails ($FacilityID, $Ladder, $StartDate, $EndDate, $Sort, 2);
			// $value2 = $controllers -> getChinookTotals ($FacilityID, $Ladder, $StartDate, $EndDate); 
			
			
			foreach($value->chinookCount as $record) {
				$rows [] = array($record->FacilityName, 
								$record->Ladder, 
								$record->Date, 															 																								 
								$this->zeroData($record->wasck),
								$this->zeroData($record->wjsck), 
								$this->zeroData($record->hasck), 
								$this->zeroData($record->hjsck), 														
								$this->zeroData($record->uasck),
								$this->zeroData($record->ujsck), 
								$this->zeroData($record->Total_Spring_Chinook), 							 
								$this->zeroData($record->wasum),								
								$this->zeroData($record->wjsum),
								$this->zeroData($record->hasum), 
								$this->zeroData($record->hjsum),								 
								$this->zeroData($record->Total_Summer_Chinook),
								$this->zeroData($record->wafck), 
								$this->zeroData($record->wjfck), 
								$this->zeroData($record->hafck), 
								$this->zeroData($record->hjfck),
								$this->zeroData($record->uafck),
								$this->zeroData($record->Total_Fall_Chinook)				
								);
			}
			
			foreach ($value->chinookTotals as $record) {
				$footerRows [] = array('Totals', '', '', 
									   $this->zeroData($record ->wasck), 
									   $this->zeroData($record ->wjsck), 
									   $this->zeroData($record ->hasck), 
									   $this->zeroData($record ->hjsck), 
									   $this->zeroData($record ->uasck), 
									   $this->zeroData($record ->ujsck), 
									   $this->zeroData($record ->Total_Spring_Chinook), 
									   $this->zeroData($record ->wasum), 
									   $this->zeroData($record ->wjsum),  
									   $this->zeroData($record ->hasum), 
									   $this->zeroData($record ->hjsum), 
									   $this->zeroData($record ->Total_Summer_Chinook), 
									   $this->zeroData($record ->wafck), 
									   $this->zeroData($record ->wjfck), 
									   $this->zeroData($record ->hafck), 
									   $this->zeroData($record ->hjfck), 
									   $this->zeroData($record ->uafck), 
									   $this->zeroData($record ->Total_Fall_Chinook), 
									   );
								}
			break; 
		case '1': //coho
			$header = array('Facility Name', 'Ladder' ,
							'Date',  							
							'wacoh', 
							'wjcoh', 
							'Total Wild', 
							'hacoh', 
							'hjcoh',
							'Total Hatchery', 						
							'uacoh',
							'ujcoh', 
							'Total Unknown'); 
				$body =  $this->DataAccess->getCohoDetails($FacilityID, $Ladder,  $StartDate, $EndDate,$Sort)->getBody();
				$value = json_decode($body);

			foreach($value->cohoCount as $record) {
				$rows[] = array($record->FacilityName,
								$record->Ladder,
								$record->Date, 							   
								$this->zeroData($record->wacoh), 
								$this->zeroData($record->wjcoh), 
								$this->zeroData($record->Total_Wild), 
								$this->zeroData($record->hacoh), 
								$this->zeroData($record->hjcoh),							   
								$this->zeroData($record->Total_Hatchery), 							  
								$this->zeroData($record->uacoh),
								$this->zeroData($record->ujcoh), 
								$this->zeroData($record->Total_Unknown)
									); 
			}
			
			foreach ($value->cohoTotals as $record) {
				$footerRows [] = array('Totals', '', '',
									   $this->zeroData($record ->wacoh), 
									   $this->zeroData($record ->wjcoh), 
									   $this->zeroData($record ->Total_Wild), 
									   $this->zeroData($record ->hacoh),
									   $this->zeroData($record ->hjcoh), 
									   $this->zeroData($record ->Total_Hatchery), 
									   $this->zeroData($record ->uacoh), 
									   $this->zeroData($record ->ujcoh), 
									   $this->zeroData($record ->Total_Unknown)
										);
					}
			
			break; 
		case '2': //sockeye
			$header = array('Facility Name','Ladder', 'Date', 
							  'wsock', 'hsock', 'Total Sockeye'); 
			
			$body =  $this->DataAccess->getSockeyeDetails($FacilityID, $Ladder,  $StartDate, $EndDate,$Sort)->getBody();
			$value = json_decode($body);

			foreach($value->sockeyeCount as $record) {
				$rows[] = array($record->facilityName, 
								$record->Ladder, 
								$record->Date,							   
								$this->zeroData($record->wsock), 
								$this->zeroData($record->hsock),	
								$this->zeroData($record->Total_Sockeye)				
			 					); 
			}
			
			foreach ($value->sockeyeTotals as $record) {
				$footerRows [] = array('Totals', '', '',
									   $this->zeroData($record ->wsock), 
									   $this->zeroData($record ->hsock), 
									   $this->zeroData($record ->Total_Sockeye)
										);
					}
			
			break; 
		case '3': //steelhead
			$header = array('Facility Name', 'Ladder', 'Date', 
							'wsth', 'hsth', 'usth','Total Steelhead'); 
			
			$body =  $this->DataAccess->getSteelheadDetails($FacilityID, $Ladder,  $StartDate, $EndDate,$Sort)->getBody();
			$value = json_decode($body);


			foreach($value->steelheadCount as $record){
				$rows[] = array($record->FacilityName, 
								$record->Ladder,
								$record->Date,
								$record->wsth,
								$this->zeroData($record->hsth), 							  							  
								$this->zeroData($record->usth), 
								$this->zeroData($record->Total_Steelhead)
								); 
			}
			
			foreach ($value->steelheadTotals as $record) {
				$footerRows [] = array('Totals', '', '',
									   $this->zeroData($record ->wsth), 
									   $this->zeroData($record ->hsth),
									   $this->zeroData($record ->usth),
									   $this->zeroData($record ->Total_Steelhead)
										);
					}
			
			break; 
		case '4': //other fish
			$header = array('Facility Name','Ladder', 'Date', 'brwntr','bulltr', 'lamp', 'raintr', 'smbass','Total Fish'); 
			
			$body =  $this->DataAccess->OtherFishDetails($FacilityID, $Ladder,  $StartDate, $EndDate,$Sort)->getBody();
			$value = json_decode($body);

			foreach($value->otherFishDetails as $record) {
				$rows[] = array($record->facilityName,
							   $record->Ladder,
							   $record->Date, 
							   $this->zeroData($record->brwntr), 
							   $this->zeroData($record->bulltr),
							   $this->zeroData($record->lamp), 
							   $this->zeroData($record->raintr), 
							   $this->zeroData($record->smbass), 
							   $this->zeroData($record->Total_Fish)
							   );
			}
			
			foreach ($value->otherFishTotals as $record) {
				$footerRows [] = array('Totals', '', '',
									   $this->zeroData($record ->brwntr), 
									   $this->zeroData($record ->bulltr),
									   $this->zeroData($record ->lamp),
									   $this->zeroData($record ->raintr), 
									   $this->zeroData($record ->smbass), 
									   $this->zeroData($record ->Total_Fish)
										);
			}
			
			break; 
			
	 }
	 //var_dump(get_defined_vars());\
	 
	    $GLOBALS['devel_shutdown'] = TRUE;
	  
	  // Set the headers to indicate this is a CSV file.
		header('Content-type: text/csv; charset=UTF-8');
		header('Content-Disposition: attachment; filename=untitled.csv');
		header('Pragma: no-cache');
	  
	   // force download  
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");
	
	    header('Expires: 0');
		$output = fopen('php://output', 'w'); 

		$notes =  json_decode($this->DataAccess->GetFacilityNotesDetails( $FacilityID, $SpeciesNum, $StartDate, $EndDate)->getBody());

	  
		if(!empty($notes)) {
		   foreach($notes as $note) {
			   $footnotes[] = array($note); 
			   $footnotes[] = array();
			   }
		   }

		if(!empty($notes)) {
			foreach($footnotes as $displayRow) {
				fputcsv($output, $displayRow);
			}	
		}
	
		//Line break whitespace so people don't complain
		fputcsv($output, []); 
					  
		fputcsv ($output, $header); 


	
		foreach ($rows as $row) {
			fputcsv($output, $row);
		}
		
		foreach ($footerRows as $footer) {
			fputcsv($output, $footer);
		}
		
		
		foreach ($display as $displayrow) {
			fputcsv($output, $displayrow); 
		}
		
		
		
		
	
	
		//strip_tags($output); 
		
		//ob_end_clean();
		fclose($output); 
		exit(); //get rid of the html after data export. 
	 
	 
	 }

	 private function zeroData(string &$data) {
		if($data == 0) {
			$data = '-'; 
		}
		return $data; 
	}


	 private function format($record, string $class) {
			
		
		//If footer header type
		// $data = array (
		// 			//array('data' => $record-> Total, 'colspan' => 2, 'class' => 'title-right-border'),
		// 			array('data' => $record -> Spring_Chinook, 'class' => $titleClass),
		// 			array('data' => $record -> Summer_Chinook, 'class' => $titleClass), 
		// 			array('data' => $record -> Fall_Chinook, 'class' => $titleClass), 
		// 			array('data' => $record -> Total_Chinook, 'class' => $borderClass), 
		// 			array('data' => $record -> Jack_Spring_Chinook, 'class' => $titleClass),
		// 			array('data' => $record -> Jack_Summer_Chinook, 'class' => $titleClass),
		// 			array('data' => $record -> Jack_Fall_Chinook, 'class' => $titleClass),
		// 			array('data' => $record -> Total_Jack_Chinook, 'class' => $borderClass),
		// 			array('data' => $record -> Wild_Steelhead, 'class' => $titleClass),
		// 			array('data' => $record -> Hatch_Steelhead, 'class' => $titleClass), 
		// 			array('data' => $record -> Total_Steelhead, 'class' => $borderClass), 
		// 			array('data' => $record -> Sockeye, 'class' => $borderClass), 
		// 			array('data' => $record -> Coho, 'class' => $borderClass),
		// 			array('data' => $record -> Jack_Coho,'class' =>$borderClass),
		// 			array('data' => $record -> Lamprey, 'class' => $borderClass),
		// 			array('data' => $record -> Bull_Trout, 'class' => $borderClass)
		// 		); 

				
				return array('data' => $record, 'class' => $class); 

				// array(array('data' => 'Totals', 'colspan' => 3, 'class' => 'title-right-border'), 
				// array('data' => $record ->wasck, 'class' => 'title'), 
				// array('data' => $record ->wjsck, 'class' => 'title'), 
				// array('data' => $record ->hasck, 'class' => 'title'), 
				// array('data' => $record ->hjsck, 'class' => 'title'), 
				// array('data' => $record ->uasck, 'class' => 'title'), 
				// array('data' => $record ->ujsck, 'class' => 'title'), 
				// array('data' => $record ->Total_Spring_Chinook, 'class' => 'title-right-border'), 
				// array('data' => $record ->wasum, 'class' => 'title'), 
				// array('data' => $record ->wjsum, 'class' => 'title'), 
				// array('data' => $record ->hasum, 'class' => 'title'), 
				// array('data' => $record ->hjsum, 'class' => 'title'),
				// array('data' => $record ->Total_Summer_Chinook, 'class' => 'title-right-border'), 
				// array('data' => $record ->wafck, 'class' => 'title'), 
				// array('data' => $record ->wjfck, 'class' => 'title'), 
				// array('data' => $record ->hafck, 'class' => 'title'), 
				// array('data' => $record ->hjfck, 'class' => 'title'), 
				// array('data' => $record ->uafck, 'class' => 'title'), 
				// array('data' => $record ->Total_Fall_Chinook, 'class' => 'title-right-border')
				// );

		
		

		// if($type == 'header') {
		// 	array_unshift($data, array('data' => $record->Date, 'class' => 'right-border'));
		// 	array_unshift($data, array('data' => $record->FacilityName));
			
		// } else if ($type == 'footer') {
		// 	//array('data' => 'Totals', 'colspan' => 2, 'class' => 'title-right-border'),
		// 	if($record->Total == null) {
		// 		array_unshift($data, array('data' => 'Totals', 'colspan' => 2, 'class' => $borderClass));
		// 	} else {
		// 		array_unshift($data, array('data' => $record->Total, 'colspan' => 2, 'class' => $borderClass));
		// 	}
			
			
			
		// }
	
		// return $data; 
	  }


	  private function formatTable(&$value, string $titleClass, string $borderClass, string $type) {
		foreach ($value as $record) {
			//Make this a function??? 
			$this->zeroData($record->Spring_Chinook);
			$this->zeroData($record->Summer_Chinook);
			$this->zeroData($record->Fall_Chinook);
			$this->zeroData($record->Total_Chinook);
			$this->zeroData($record->Jack_Spring_Chinook);
			$this->zeroData($record->Jack_Summer_Chinook);	
			$this->zeroData($record->Jack_Fall_Chinook);
			$this->zeroData($record->Total_Jack_Chinook);
			$this->zeroData($record->Wild_Steelhead);
			$this->zeroData($record->Hatch_Steelhead);
			$this->zeroData($record->Total_Steelhead);
			$this->zeroData($record->Sockeye);
			$this->zeroData($record->Coho);
			$this->zeroData($record->Jack_Coho);
			$this->zeroData($record->Lamprey);	
			$this->zeroData($record->Bull_Trout);	
				
				
			$Rows[] =  $this->format($record, $titleClass, $borderClass, $type);
			//var_dump($Rows);
		}

		
		return $Rows; 
	}
}