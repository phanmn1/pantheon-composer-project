<?php

namespace Drupal\stardb_queries\Form;

// use Drupal\stardb_queries\Controller\QueryControllers;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\stardb_queries\DataAccess;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
//include Drupal\Core\Classes\PHPExcel; 



class QueryForm extends FormBase {
	
	protected $DataAccess;


	public function __construct() {
		$this->DataAccess = new DataAccess(); 
	}

	public function getFormId() {
		return 'query_form';
  }

  public function facilityNotes($facilitycode, $year) {
	$body = $this->DataAccess->GetFacilityNotes($facilitycode, $year)->getBody(); 
	$body = json_decode($body); 

	return $body; 
  }

  public function concatText(array $notes) {
	return implode("<br><br>", $notes);
  }
  
  public function buildForm(array $form, FormStateInterface $form_state) {

   
	$form['#attached']['library'][] = 'core/drupal.dialog.ajax';
	$form_state->setCached(FALSE);	  
	
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

	  
	  
	  $form['title']['title'] =[
		'#markup' => '<h1>Adult Passage Counts</h1>'
	  ];
	  
	  $form['select-lists']['Facility'] = [ 
		'#type' => 'select', 
		'#title' => $this->t('Facility'), 
		'#options' => $this->buildFacilityList(),
		'#attributes' => array ( 
			'id' => 'facility_list'), 
		'#attached' => [
			'library' => [
				'stardb_queries/AdultPassgeCSS',
			], 
		] 
		];
			  	  
	  $form['select-lists']['year'] = [
		'#type' => 'select', 
		'#title' => $this->t('Year'), 
		'#options' => $this->buildYearList(), 
		'#attributes' => array ( 
			'id' => 'year-list'), 
		'#attached' => [
			'library' => [
				'stardb_queries/AdultPassgeCSS',
			], 
		] 
		
		
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
	   
	   //------------------------------------------------------------------------------------------------------------
	  

    $form['actions'] = [
      '#type' => 'actions',
    ];

    // Add a submit button that handles the submission of the form.
    
	
	

	
	//---------------------------------------------------------------------------------------------------------------

	  
	  /*
	   $sqlQuery = 'SELECT distinct tblSpecies.SppCode as SpeciesCode, tblSpecies.SpeciesNm as SpeciesName
					FROM tblSamples INNER JOIN tblSpecies ON tblSamples.SppCode = tblSpecies.SppCode';
	  */

	  $form['buttons']['submit'] = [
		'#type' => 'button',
		'#value' => 'Go',
		'#ajax' => [
			'callback' => '::TableCallback', 
			'wrapper' => 'table-wrapper',
			'event' => 'click'
					
		],
		'#attributes' => array('onclick' => "document.getElementById('edit-export').style.visibility='visible'; return false;")
		

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
			
			$form['buttons']['graph'] = [
				'#type' => 'button', 
				'#value' => t('Show graph'), 
				'#ajax' => [
					 'callback' => '::ModalWindow', 
					 'wrapper' => 'table-wrapper',
					 'event' => 'click'
			 		],			
			 ];

			
	   
	   
	 $form['buttons']['export'] = [
		'#type' => 'submit', 
		'#value' => 'Export to csv', 
		'#attributes' => array('id' => 'edit-export'),
	   ];
 
	   
	   
	   
	   $form['table_wrapper'] = [
		'#type'=> 'container',
        '#attributes' => 
			['id' => 'table-wrapper'],		
	   ];
	   
	   $form['scrolltop'] = [
			'#markup' => '<a href="#" class="scrollup">Scroll</a>',
		];
	 
	return $form;
	
	}
	

	public function ModalWindow(array &$form, FormStateInterface $form_state){

		$Years = $form_state->getValue('year');
	  $FacilityID = $form_state->getValue('Facility');
		$Sort = $form_state->getValue('sort');	 

		$params['year'] = $Years;
		$params['facility'] = $FacilityID;

		//type 1 assume it's basic query form
		$params['type'] = 1; 
		
		
		$response = new AjaxResponse(); 
		$modal_form = \Drupal::formBuilder()->getForm('Drupal\charts_test\Form\ChartModalForm', $params); 
		$response->addCommand(new OpenModalDialogCommand(
				'Adult Passage Graphs',
				$modal_form,
				['width' => '1000', 'height' => '600']
		));

		return $response; 

 }
  
  public function TableCallback (array &$form, FormStateInterface $form_state) {

	$current_path = \Drupal::service('path.current')->getPath();
	//$current_path = 'aaa';

	preg_match('/adult_passage_counts/',$current_path, $matches); 
	//var_dump($matches[0] == 'adult_passage_counts' );
	  
	  $Years = $form_state->getValue('year');
	  $FacilityID = $form_state->getValue('Facility');
	  $Sort = $form_state->getValue('sort');	 

	 $notes = $this->facilityNotes($FacilityID, $Years);
	 $body =  $this->DataAccess->GetFishCountsData($Years,$FacilityID,$Sort)->getBody();
	 $value =  json_decode($body); 	  
	 $footnote = null; 
	 
	 

	//  $form['table_wrapper']['footnotes'] = [
	// 	'#markup' => '<p>*Notes: '.$notes[0].'</p>',
	// ];

	 if(!empty($notes)){
		$form['table_wrapper']['footnotes'] = [
			'#markup' => '<div class="bold-footnotes"><p>'.$this->concatText($notes).'</p></div><br>',
		];
	 }
	  
	 $form['table_wrapper']['description'] = [
		'#markup'=> '<p>Record Count: '.count($value->data).'</p>', 		
		]; 
		

	  $form['table_wrapper']['scroll_wrapper'] = [
	    '#markup' => '<div class="scroll-wrapper">'
	 ];	

	 if($matches[0] == 'adult_passage_counts') {
		$form ['table_wrapper']['table'] = [
			'#type' => 'table', 
			'#header' => $this->format($value->header, '', 'right-border', 'header'),
			'#rows' => $this->formatTable($value->data, '', 'right-border', 'header'), 
			'#sticky' => true, 
			'#attributes' => array('id' => 'results-db'),				
			'#attached' => [
				'library' => [
					'stardb_queries/AdultPassgeCSS',
					'stardb_queries/Sticky'
				], 
			], 
			'#footer' => $footerRows = $this->formatTable($value->totals, 'title', 'title-right-border', 'footer'),
			
		  ];
	 } else {
	 
	  $form ['table_wrapper']['table'] = [
		'#type' => 'table', 
		'#header' => $this->format($value->header, '', 'right-border', 'header'),
		'#rows' => $this->formatTable($value->data, '', 'right-border', 'header'), 
		'#sticky' => true, 
		'#attributes' => array('id' => 'results-db'),				
		'#attached' => [
			'library' => [
				'stardb_queries/AdultPassgeCSS',
			], 
		], 
		'#footer' => $footerRows = $this->formatTable($value->totals, 'title', 'title-right-border', 'footer'),
		
	  ];
	 }


	  $form['table_wrapper']['scroll_wrapper_end'] = [
	    '#markup' => '</div>'
	 ];	
	  
	  $form['table_wrapper']['notes'] = [
		'#markup'=>'<br><p>*Counts for the most recent days may be partial counts</p>', 
		
		
		]; 

	
	  
	  
	  
	 
	  return $form['table_wrapper']; 
  }

 
  
  public function ResetTable (array &$form, FormStateInterface $form_state) {
	  
	 $form ['table_wrapper']['submit'] = [
	  ]; 
	  
	  return $form['table_wrapper']; 
  }
  
  
  public function submitForm(array &$form, FormStateInterface $form_state) {
      $form_state->setRebuild(FALSE);
	  //ob_end_clean();
	  $Years = $form_state->getValue('year');
	  $FacilityID = $form_state->getValue('Facility');
	  $Sort = $form_state->getValue('sort');

	  
	  $body =  $this->DataAccess->GetFishCountsData($Years,$FacilityID,$Sort)->getBody();
	  $value =  json_decode($body); 


	//   $header = $this->format($value->header, '', '', 'header');
	//   $TableRows = $this->formatTable($value->data, '', '', 'header'); 
	  
	  $notes = $this->facilityNotes($FacilityID, $Years);

	  if(!empty($notes)) {
		foreach($notes as $note) {
			$display[] = array($note); 
			$display[] = array(); 
			}
		}


	  $header = array('Facility Name', 
					  'Date', 
					  'Adult Spring Chinook',
					  'Adult Summer Chinook',
					  'Adult Fall Chinook',
					  'Total Adult Chinook',
					  'Jack Spring Chinook',
					  'Jack Summer Chinook',					  
					  'Jack Fall Chinook',
					  'Total Jack Chinook',
					  'Unmarked Steelhead',
					  'Marked Steelhead', 
					  'Total Steelhead',
					  'Sockeye','Coho', 
					  'Jack Coho',
					  'Lamprey',  
					  'Bull Trout'
					  );
					  
	  foreach ($value->data as $record) {
				$TableRows[] =   array($record->FacilityName,
									   $record->Date, 
									   $this->zeroData($record->Spring_Chinook),
									   $this->zeroData($record->Summer_Chinook), 
									   $this->zeroData($record->Fall_Chinook), 
									   $this->zeroData($record->Total_Chinook), 
									   $this->zeroData($record->Jack_Spring_Chinook), 
									   $this->zeroData($record->Jack_Summer_Chinook), 
									   $this->zeroData($record->Jack_Fall_Chinook), 
									   $this->zeroData($record->Total_Jack_Chinook),
									   $this->zeroData($record->Wild_Steelhead), 
									   $this->zeroData($record->Hatch_Steelhead), 									  
									   $this->zeroData($record->Total_Steelhead), 									   
									   $this->zeroData($record->Sockeye), 
									   $this->zeroData($record->Coho),
									   $this->zeroData($record->Jack_Coho),
									   $this->zeroData($record->Lamprey), 
									   $this->zeroData($record->Bull_Trout));
				
	  } 
	
	  // var_dump(get_defined_vars()); 
	// Prevent Devel from messing us up.
	
		//$value = $controllers -> getAdultPassageTotals($FacilityID, $Years);
		foreach ($value->totals as $record) {
			$footerRows [] = array(		'Totals',
									   '', 
									   $this->zeroData($record->Spring_Chinook),
									   $this->zeroData($record->Summer_Chinook), 
									   $this->zeroData($record->Fall_Chinook), 
									   $this->zeroData($record->Total_Chinook), 
									   $this->zeroData($record->Jack_Spring_Chinook), 
									   $this->zeroData($record->Jack_Summer_Chinook), 
									   $this->zeroData($record->Jack_Fall_Chinook), 
									   $this->zeroData($record->Total_Jack_Chinook),
									   $this->zeroData($record->Wild_Steelhead), 
									   $this->zeroData($record->Hatch_Steelhead), 									  
									   $this->zeroData($record->Total_Steelhead), 									   
									   $this->zeroData($record->Sockeye), 
									   $this->zeroData($record->Coho),
									   $this->zeroData($record->Jack_Coho),
									   $this->zeroData($record->Lamprey), 
									   $this->zeroData($record->Bull_Trout));
		}
	    
		$GLOBALS['devel_shutdown'] = TRUE;
	  
	  // Set the headers to indicate this is a CSV file.
		
		//header('Pragma: no-cache');
		
		header('Content-Disposition: attachment; filename=untitled.csv');
		header('Content-type: text/csv; charset=UTF-8');
		
	  
	   // force download  
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");
        header('Expires: 0');
		
		
		$output = fopen('php://output', 'w'); 

		if(!empty($notes)) {
		foreach($display as $displayRow) {
			fputcsv($output, $displayRow);
		}

		}

		//Line break whitespace so people don't complain
		fputcsv($output, []); 
					  
		fputcsv ($output, $header); 
		
		foreach ($TableRows as $row) {
			fputcsv($output, (array) $row);
		}
		
		foreach($footerRows as $footerRow) {
			fputcsv($output, $footerRow);
		}

		
		
		//strip_tags($output); 
		
		//ob_end_clean();   
		
		
		
		fclose($output); 
		exit(); 
	
	
}


	private function zeroData(&$data) {
		if($data == 0) {
			$data = '-'; 
		}
		return $data; 
	}


	private function buildFacilityList() {
		$body =  $this->DataAccess->GetSelectLists()->getBody();
		$body =  json_decode($body); 
		foreach ($body -> facilityList as $record) {
				$FacilityRows[$record->FacilityID] =  $record->FacilityName; 
				//$FacilityAttributes[$record->FacilityName] = $record->FacilityID;
		}
		return $FacilityRows; 
	}



	private function buildYearList() {
		$body =  $this->DataAccess->GetSelectLists()->getBody();
		$body =  json_decode($body); 
		foreach ($body -> yearlist as $record) {
				$YearRows[$record] = $record; 				
		}

		return $YearRows; 

	}


	private function format(&$record, string $titleClass, string $borderClass, string $type) {
			
		
		//If footer header type
		$data = array (
					//array('data' => $record-> Total, 'colspan' => 2, 'class' => 'title-right-border'),
					array('data' => $record -> Spring_Chinook, 'class' => $titleClass),
					array('data' => $record -> Summer_Chinook, 'class' => $titleClass), 
					array('data' => $record -> Fall_Chinook, 'class' => $titleClass), 
					array('data' => $record -> Total_Chinook, 'class' => $borderClass), 
					array('data' => $record -> Jack_Spring_Chinook, 'class' => $titleClass),
					array('data' => $record -> Jack_Summer_Chinook, 'class' => $titleClass),
					array('data' => $record -> Jack_Fall_Chinook, 'class' => $titleClass),
					array('data' => $record -> Total_Jack_Chinook, 'class' => $borderClass),
					array('data' => $record -> Wild_Steelhead, 'class' => $titleClass),
					array('data' => $record -> Hatch_Steelhead, 'class' => $titleClass), 
					array('data' => $record -> Total_Steelhead, 'class' => $borderClass), 
					array('data' => $record -> Sockeye, 'class' => $borderClass), 
					array('data' => $record -> Coho, 'class' => $borderClass),
					array('data' => $record -> Jack_Coho,'class' =>$borderClass),
					array('data' => $record -> Lamprey, 'class' => $borderClass),
					array('data' => $record -> Bull_Trout, 'class' => $borderClass)
				); 

		
		

		if($type == 'header') {
			array_unshift($data, array('data' => $record->Date, 'class' => 'right-border'));
			array_unshift($data, array('data' => $record->FacilityName));
			
		} else if ($type == 'footer') {
			//array('data' => 'Totals', 'colspan' => 2, 'class' => 'title-right-border'),
			if($record->Total == null) {
				array_unshift($data, array('data' => 'Totals', 'colspan' => 2, 'class' => $borderClass));
			} else {
				array_unshift($data, array('data' => $record->Total, 'colspan' => 2, 'class' => $borderClass));
			}
			
			
			
		}
	
		return $data; 
	  }


	  private function formatTable(&$value, string $titleClass, string $borderClass, string $type) {
		foreach ($value as $record) {
			//Make this a function??? 
			// foreach($record as $item) {
			// 	$item = $this->zeroData($item);
			// 	//var_dump($item);
			// }

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