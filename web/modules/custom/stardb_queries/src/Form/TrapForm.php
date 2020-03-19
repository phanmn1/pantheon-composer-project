<?php

namespace Drupal\stardb_queries\Form;

// use Drupal\stardb_queries\Controller\QueryControllers;
use Drupal\stardb_queries\DataAccess;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax;
use Drupal\Core\Ajax\OpenModalDialogCommand;
//include Drupal\Core\Classes\PHPExcel; 



class TrapForm extends FormBase {

	protected $DataAccess;


	public function __construct() {
		$this->DataAccess = new DataAccess(); 
	}
	
	
	public function getFormId() {
		return 'trap_form';
  }
  
   public function buildForm(array $form, FormStateInterface $form_state) {
	   $form['#attached']['library'][] = 'core/drupal.dialog.ajax';
	   $form_state->setCached(FALSE); 
	   $FacilityRows = array(); 
	   
	   $body =  $this->DataAccess->GetSelectLists()->getBody();
	   $value =  json_decode($body); 	
	   
	   foreach ($value->facilityList as $record) {
		  $FacilityRows[$record->FacilityID] = $record->FacilityName; 		  
	   }
	   
      
	   foreach ($value->species as $record) {
		   $SpeciesRows[$record->Species] = $record->Species; 
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

	  $form['checkboxes'] = [
		'#type' => 'fieldset', 
		'#attributes' => [
			'class' => ['checkboxes']
			]
		];

	   
	   
	   $form['title']['title'] =[
		'#markup' => '<h1>Trap Samples by Facility</h1>'
	  ];
	  
	    $form['select-lists']['Facility'] = [ 
			'#type' => 'select', 
			'#title' => $this->t('Facility'), 
			'#options' => $FacilityRows,
			//'#empty_option' => $this->t('All'),
			'#attributes' => array ( 
				'id' => 'facility_list'), 
			'#attached' => [
				'library' => [
					'stardb_queries/AdultPassgeCSS',
				], 
			] 
			//'#options_attributes' => array (
			//$rows => array('id' => array($attributes)) 
				//	)
		]; 

		
		
		$form['select-lists']['species'] = [
			'#type' => 'select', 
			'#title' => $this->t('Species'), 
			'#options' => $SpeciesRows, 
			'#attributes' => array(
				'id' => 'species_list'), 
			'#attached' => [
				'library'=> [
					'stardb_queries/AdultPassgeCSS',
				]
			]
		]; 
		
		$time = REQUEST_TIME;
		$result = db_query("select  date_format(date_sub(CURDATE(), INTERVAL 7 DAY), '%m/%d/%Y') as LastDate");
		
		$row = $result->fetchAssoc(); 
		
		
		
		//var_dump(get_defined_vars()); 
		
	    $form['select-lists']['from'] = [
			'#type' => 'textfield',
			'#title' => $this->t('From Date: '), 
			'#default_value' => $row['LastDate'], 
			'#attributes' => array(
			'id' => 'from-date'),
			
			
			 
			];
			
		
	
		$form['select-lists']['to'] = [
		  '#type' => 'textfield',
		  '#title' => $this->t('To Date: '),
		  //'#default_value' => '2020-02-15',
		  '#attributes' => array(
				'id' => 'to-date'), 
		  '#default_value' => date("m/d/Y", $time)
		  
		];
/*-----------------------------------------------------------------------------------------------
		
		 $form['student_type'] = array(
    '#type' => 'radios',
    '#options' => array(
      'high_school'   => t('High School'),
      'undergraduate' => t('Undergraduate'),
      'graduate'      => t('Graduate'),
    ),
    '#title' => t('What type of student are you?')
  );

  // High school information.
  $form['high_school']['tests_taken'] = array(
    '#type' => 'checkboxes',
    '#options' => array($this->t('SAT'), $this->t('ACT')),
    '#title' => t('What standardized tests did you take?'),
    // This #states rule says that this checkboxes array will be visible only
    // when $form['student_type'] is set to t('High School').
    // It uses the jQuery selector :input[name=student_type] to choose the
    // element which triggers the behavior, and then defines the "High School"
    // value as the one that triggers visibility.
    '#states' => array(
      'visible' => array(   // action to take.
        ':input[name="student_type"]' => array('value' => 'high_school'),
      ),
    ),
  );

//---------------------------------------------------------------------------------------------------  */
		 $form['select-lists']['show'] = [
			'#type' => 'select',
			'#title' => t('Show detailed columns'),
			'#options' => array(
			  'show'   => t('Show'), 
			  'hide' => t('Hide')
			),			 
			'#default_value' => 'show' ,
			'#attributes' => array( 
				'id' => 'show-details')
			
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
		'#attributes' => array('onclick' => "document.getElementById('edit-export-trap').style.visibility='visible'; return false;")
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
								   'onclick' => "document.getElementById('edit-export-trap').style.visibility='hidden'; return false;"),
			
		  );
		  
		
		
		
		
		$selection = array(
		  'FacilityName' => $this->t('FacilityName'),
          'LadCode' => $this->t('LadCode'),
		  'PassDate' => $this->t('PassDate'),
          'PassTime' => $this->t('PassTime'),
		  'SppCode' => $this->t('SppCode'),
		  'CarcassNo' => $this->t('CarcassNo'), 
		  'PitTag' => $this->t('PitTag'),
		  'JvPitTag' => $this->t('JvPitTag'),
		  'Status' => $this->t('Status'),
		  'Origin' => $this->t('Origin'),
		  'Age' => $this->t('Age'),
		  'Sex' => $this->t('Sex'),
		  'Forklgth' => $this->t('Forklgth'),
		  'Pohlgth' => $this->t('Pohlgth'),
		  'Mehlgth' => $this->t('Mehlgth'),
		  'Weight' => $this->t('Weight'),
		  'Scalesmpl' => $this->t('Scalesmpl'), 
		  'DNASample' => $this->t('DNASample'),
		  'Injection' => $this->t('Injection'),
		  'Comments' => $this->t('Comments'),
		  'CWT' => $this->t('CWT'), 
		  'CWTSnout' => $this->t('CWTSnout'),
		  'ElastomerColor' => $this->t('ElastomerColor'),
		  'OCTSNT' => $this->t('OCTSNT'),
		  'BodyLocation' => $this->t('BodyLocation'),
		  'Mort' => $this->t('Mort'),
		  'Channel' => $this->t('Channel'),
		  'Release' => $this->t('Release'),
		  'GonadStudy' => $this->t('GonadStudy'),
		  'AdClip' => $this->t('AdClip'), 
		  'VentralClip' => $this->t('VentralClip'),
		  'RTChannel' => $this->t('RTChannel'),
		  'RTCode' => $this->t('RTCode'),
		  'Brightness'=> $this->t('Brightness'),
		  'Tube' => $this->t('Tube'),
		  'Drax' => $this->t('Drax'),
		  'Operator' => $this->t('Operator')
		);
		
		$default_columns = array('FacilityName', 'LadCode', 'PassDate', 'PassTime', 'SppCode',
								'CarcassNo', 'PitTag', 'JvPitTag', 'Status', 'Origin', 'Age', 
								'Sex', 'Forklgth', 'Pohlgth', 'Mehlgth', 'Weight');

		# the drupal checkboxes form field definition
		$form['checkboxes']['columns'] = [
		  '#title' => t('Show Columns'),
		  '#type' => 'checkboxes',
		  '#options' => $selection,
		  '#default_value' => $default_columns, 
		  '#states' => array(
				  'invisible' => array(   // action to take.
					':input[name="show"]' => array('value' => 'hide'),
				  ),
				),
		  
			
		  
		];
		
		  $form['buttons']['export'] = [
		'#type' => 'submit', 
		//'#type' => 'button', 
		'#value' => 'Export to csv', 
		/*'#ajax' => [
			'callback' => '::ExportToCSV', 
			'wrapper' => 'table-wrapper' 
			], */
		//'#submit' => ['::ExportToCSV'],
		'#attributes' => array('id' => 'edit-export-trap'),
	   ];
		
		//$form['columns']['convert(varchar, passdate, 101) as PassDate']['#access'] = FALSE; 
		$form['columns']['FacilityName']['#disabled'] = TRUE; 
		$form['columns']['LadCode']['#disabled'] = TRUE; 
		$form['columns']['PassDate']['#disabled'] = TRUE;
		$form['columns']['SppCode']['#disabled'] = TRUE;	
		$form['columns']['PassTime']['#disabled'] = TRUE; 
		
		
		
		//$form['columns']['SppCode'];
		
		
		
		
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
   
   public function TableCallback (array &$form, FormStateInterface $form_state ) {
	   
	  $CheckedColumns = $form_state->getValue('columns');
	  $FacilityID = $form_state->getValue('Facility');
	  $Species = $form_state->getValue('species'); 
	  $StartDate = $form_state->getValue('from'); 
	  $EndDate = $form_state->getValue('to'); 
	  $Sort = $form_state->getValue('sort'); 

	  

	   //var_dump($recordCount);
	   //$RecordCount = $controllers->getTrapCount ($FacilityID, $Species, $StartDate, $EndDate); 
	   //$controllers = new QueryControllers; 
	   //var_dump($CheckedColumns);
	   $value = json_decode($this->DataAccess->getTrapSamples( $CheckedColumns, $FacilityID, $Species, $StartDate, $EndDate, $Sort)->getBody());
	   $notes = json_decode($this->DataAccess->GetFacilityNotesTraps($FacilityID, $Species, $StartDate, $EndDate)->getBody()); 

	   //var_dump($notes);
	   $first = True; 
	  
	   $cols = ''; 
	  
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
	
	
	
	   
	   
	   foreach ($RecordCount as $Record) {
		   $Count = $Record->CountOfRows; 
	   }
	  
	   if($Count < 20000) {
	  
	   
	   
	   //create the header array that puts single quotes in each column name
	   foreach($CheckedColumns as $item){
		   if($item != '0') {
				$header [] = $item;
		   }
			// if($item != '0' and $first) {
			// 	//$cols = 'FacilityName, LadCode, PassDate, PassTime, SppCode, '.$item; 
				
			// 	$header [] = $item; 
			// 	$first = false;
			// } else if ($item != '0' and $first != true) {
			// 	//$cols = $cols . ', ' .$item; 	
			// 	$header[] = $item;			
			// }
			// }  else {
			// 	$header[] = $item;
			// }
		   }

		   
	   //}
	   
	   //$header= $header."'"; 
	   
	   //var_dump(get_defined_vars());
	    // $sqlQuery = "exec dbo.spGetTrapSamples '". $cols . "' ,
		//			".$FacilityID." ,'" .$Species. "' ,'".$StartDate. "' , '".$EndDate."'";
	   
	//    $rows = $controllers-> getTrapSamples($cols, $FacilityID, $Species, $StartDate, $EndDate, $Sort); 

	  // $value = json_decode($this->DataAccess->getTrapSamples($cols, $FacilityID, $Species, $StartDate, $EndDate, $Sort)->getBody());
	   //$recordCount = Count($record); 
	   
	   //db_set_active('HistoricalDB');	  
	  //$rows = db_query($sqlQuery); 
	// db_set_active('default');
	  
	  $first=true; 
	  /*
	  foreach ($header as $colname) {
		  if($first) {
			$columns = '$record->'.$colname; 
			$first=false; 
		  } else {
		  $columns = $columns.',$record->'.$colname; 
		  }
	  }
	  */
	  
	  
	   $content = array(); 
	   foreach($value as $record) { 
			$content = null; 
			//$TableRows[] = array($record->LadCode);
			foreach($header as $columnnm) {
				if($columnnm == "PassDate") {
					 $date = date_create($record->$columnnm);
					 //var_dump($date);	
					 $content[] = date_format($date, "m/d/y");				 
					 
					// $record->$columnmn = $date->format($date, 'm/d/y');
					
				} else if ($columnnm == "PassTime" && $record->$columnnm != null) {
					$time = date_create($record->$columnnm);
					//var_dump($record->$columnnm);
					$content[] = date_format($time, "h:m A"); 
				// } 
				} else {
					$content[] = $record->$columnnm; 
				}
				//   if($columnnm == 'PassDate') {
				// 	  $content [] = $record->Date; 
					  
				//   } else {
				  
				  //}
			} 
			
			$TableRows[] = $content; 
	  }
	  
	   //var_dump(get_defined_vars());
	   /*
	   $form ['table_wrapper']['table'] = [
		'#type' => 'table', 
		'#header' => $header, 
		'#rows' => $rows, 
		'#attributes' => array('id' => 'results-db-details'),
		]; 
		*/


		if(!empty($notes)){
			$form['table_wrapper']['footnotes'] = [
				'#markup' => '<div class="bold-footnotes"><p>'.implode("<br><br>", $notes).'</b></p></div><br>',
			];
		 }

		$form['table_wrapper']['count'] = [
		'#markup'=>'<p>Record Count: '.count($TableRows).'</p>', 
		
		
		]; 

		
		$form['table_wrapper']['scroll_wrapper'] = [
	    '#markup' => '<div class="scroll-wrapper">'
	 ];	
		
		$form ['table_wrapper']['table'] = [
		'#type' => 'table', 
		'#header' => $header, 
		'#rows' => $TableRows, 
		'#sticky' => true, 
		'#attributes' => array('id' => 'results-db-details'),
		]; 

		
		 $form['table_wrapper']['scroll_wrapper_end'] = [
	    '#markup' => '</div>'
	 ];	
		
		$form['table_wrapper']['notes'] = [
		'#markup'=>'<br><p>*Counts for the most recent days may be partial counts</p>', 
				
		]; 
		
		/*
		$form['table_wrapper']['table'] = [
		'#type' => 'date',
      '#title' => $this->t('To Date: '),
      //'#default_value' => '2020-02-15',
      '#attributes' => array(
			'id' => 'to-date'), 
			];
		*/
		
	   } else {
		   
		   $form['table_wrapper']['description'] = [
				'#markup'=>'<p id="set-this-red">Record count exceeds 20,000 records. Please re-run query.</p>', 
				//'#attributes' => array('id' => 'set-this-red')
		
		
		]; 
	   }
		
		return $form['table_wrapper'];
	  
   }
   
   public function ResetTable (array &$form, FormStateInterface $form_state) {

	 $form ['table_wrapper']['submit'] = [
	 
		
	  ]; 
	  
	  return $form['table_wrapper']; 
  }
   
   public function submitForm(array &$form, FormStateInterface $form_state) {
	  $CheckedColumns = $form_state->getValue('columns');
	  $FacilityID = $form_state->getValue('Facility');
	  $Species = $form_state->getValue('species'); 
	  $StartDate = $form_state->getValue('from'); 
	  $EndDate = $form_state->getValue('to'); 
	  $Sort = $form_state->getValue('sort'); 
	  
	  /*
	  if($StartDate > $EndDate) {
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
	  */
	  
	  
	   $first = True; 
	   
	   $cols = ''; 
	   
	   //create the header array that puts single quotes in each column name
	   foreach($CheckedColumns as $item){
		if($item != '0') {
			 $header [] = $item;
		}
	}
	   //$header= $header."'"; 
	   
	   //var_dump(get_defined_vars());
	    // $sqlQuery = "exec dbo.spGetTrapSamples '". $cols . "' ,
		//			".$FacilityID." ,'" .$Species. "' ,'".$StartDate. "' , '".$EndDate."'";
	   
	   $rows = json_decode($this->DataAccess->getTrapSamples( $CheckedColumns, $FacilityID, $Species, $StartDate, $EndDate, $Sort)->getBody());
	   //db_set_active('HistoricalDB');	  
	   //$rows = db_query($sqlQuery); 
	   //db_set_active('default');
	  
	  $first=true; 
	  /*
	  foreach ($header as $colname) {
		  if($first) {
			$columns = '$record->'.$colname; 
			$first=false; 
		  } else {
		  $columns = $columns.',$record->'.$colname; 
		  }
	  }
	  */
	  
	  
	   $content = array(); 
	   foreach($rows as $record) { 
		$content = null; 
		//$TableRows[] = array($record->LadCode);
		foreach($header as $columnnm) {
			if($columnnm == "PassDate") {
				 $date = date_create($record->$columnnm);
				 //var_dump($date);	
				 $content[] = date_format($date, "m/d/y");				 
				 
				// $record->$columnmn = $date->format($date, 'm/d/y');
				
			} else if ($columnnm == "PassTime" && $record->$columnnm != null) {
				$time = date_create($record->$columnnm);
				//var_dump($record->$columnnm);
				$content[] = date_format($time, "h:m A"); 
			// } 

			} else {
				$content[] = $record->$columnnm; 
			}
			//   if($columnnm == 'PassDate') {
			// 	  $content [] = $record->Date; 
				  
			//   } else {
			  
			  //}
		} 
		
		$TableRows[] = $content; 
  }
	  
	  $GLOBALS['devel_shutdown'] = TRUE;
	  $notes = json_decode($this->DataAccess->GetFacilityNotesTraps($FacilityID, $Species, $StartDate, $EndDate)->getBody()); 
	  
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
			foreach($notes as $note) {
				$display[] = array($note); 
				$display[] = array();
				}
			}

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
		
		//strip_tags($output); 
		
		//ob_end_clean();   
		
		
		fclose($output); 
		exit(); 
   }
   
   
  
}