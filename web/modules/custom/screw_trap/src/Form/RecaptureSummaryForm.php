<?php 

namespace Drupal\screw_trap\Form;

use Drupal\Core\Form\FormBase; 
use Drupal\Core\Form\FormStateInterface;

class RecaptureSummaryForm extends FormBase {
    public function buildForm(array $form, FormStateInterface $form_state){
        
        $urlPrefix = \Drupal::config('apiurl')->get('value'); 
        $form['#attached']['drupalSettings']['baseUrl'] =  \Drupal::config('apiurl')->get('value');
        $form['#attached']['library'][] = 'quality_assurance/datatables_library';
        $form["#attached"]['library'][] = 'screw_trap/screw_trap_recap_summary';

        $table = '<table id="example">
            <thead>
                <tr>
                    <th rowspan="2">Date</th>
                    <th colspan="4">O. mykiss</th>
                    <th>Chinook</th>
                    <th>Coho</th>
                    <th>Lamprey</th>
                </tr>
                <tr>
                    <th>Number Captured</th>
                    <th>Number Pit Tagged</th>
                    <th>Number Released Upstream</th>
                    <th>Number Recaptured</th>
                    <th>Number Captured</th>
                    <th>Number Captured</th>
                    <th>Number Captured</th>
                </tr>
            </thead>
         </table>
        ';

        $time = REQUEST_TIME; 
        $result = db_query("select  date_format(date_sub(CURDATE(), INTERVAL 365 DAY), '%m/%d/%Y') as LastDate");
        $row = $result->fetchAssoc();

        $client = \Drupal::httpClient();
        $request = $client->get($urlPrefix.'/api/ScrewTrap/SelectList');
        $body = json_decode($request->getBody());

        foreach($body as $location){
            $locations[$location] = $location;
        }

        $form['default_selects'] = [
            '#type' => 'fieldset',
            '#attributes' => [
                'class' => ['default-selects']
            ]
        ];

        $form['default_selects']['location'] = [
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
                '#type' => 'button',
                '#value' => $this->t('Export to CSV'),
                '#attributes' => [
                    'class' => ['btn-margin-wrapper']
                ]
            ];

        $form['table'] = [
            '#markup' => $table
        ];

        return $form; 
    }

    public function getFormId() {
        return 'screw_trap_recap';
    }

    public function submitForm(array &$form, FormStateInterface $form_state){
        $form_state->setRebuild(); 
    }

}