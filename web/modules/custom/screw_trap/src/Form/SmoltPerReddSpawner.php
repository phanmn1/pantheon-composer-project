<?php 

namespace Drupal\screw_trap\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface; 
use Drupal\screw_trap\ExcelHandlerSmoltSpawner;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class SmoltPerReddSpawner extends FormBase {
    public function buildForm(array $form, FormStateInterface $form_state){
        $urlPrefix = \Drupal::config('apiurl')->get('value');

        $form['#attached']['drupalSettings']['baseUrl'] = \Drupal::config('apiurl')->get('value');
        $form['#attached']['library'][] = 'quality_assurance/datatables_library';
        $form['#attached']['library'][] = 'screw_trap/screw_trap_smolt_per_spawner';

        $client = \Drupal::httpClient();
        $request = $client->get($urlPrefix.'/api/ScrewTrap/SelectList');
        $body = json_decode($request->getBody());

        foreach($body as $location){
            $locations[$location] = $location;
        }

        $form['title'] =[
            '#markup' => '<h1>Steelhead Smolt Per Redd/Spawner</h1>'
        ];

        $notes_header = [
            //'ID' => t('ID'),
            //'TableType' => t('Table Type'),
            '' => t(''),
            'Notes' => t('Notes')
        ];
        
        $header = [
            'Year' => t('Year'),
            'SmoltNumberAtTrap' => t('Smolt Number at Trap'),
            'Age1' => t('Age 1'),
            'Age2' => t('Age 2'), 
            'Age3' => t('Age 3'), 
            'YearClass' => t('Year Class'), 
            'JvPerYearClass' => t('Juveniles per Year Class'),
            'Redds' => t("<span class='editable-column'><sup>*</sup>Redds</span>"),
            'Spawner' => t('Spawner'),
            'SmoltPerRedd' => t('Smolt per Redd'), 
            'SmoltPerSpawner' => t('Smolt per spawner')
        ];

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

        $form['table'] = [
            '#type' => 'table',
            '#header' => $header,
            '#attributes' => [
                'id' => 'smolt-per-spawner',
                'class' => ['display'],
                'width' => '100%'
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

        $form['default_selects']['export'] = [
            '#type' => 'submit', 
            '#value' => $this->t('Export'), 
            '#submit' => ['::ExportToExcel'],
            '#attributes' => [
                'class' => ['btn-margin-wrapper']
            ]
        ];


        $form['notes_table'] = [
            '#type' => 'table',
            '#header' => $notes_header,
            '#attributes' => [
                'id' => 'notes-table',
                'class' => ['notes']
            ]
        ];

        

        return $form;

    }

    public function ExportToExcel(array &$form, FormStateInterface $form_state){
        $formValues = $form_state->getValues();
        $filename = 'SmoltPerSpawner.xlsx';

        $urlPrefix = \Drupal::config('apiurl')->get('value'); 
        $location = $formValues['location']; 

        try {
            $response = \Drupal::httpClient()->get($urlPrefix.'/api/RecapSummary/SmoltPerSpawner?location='.$location);

            $json = json_decode($response->getBody());

            $spreadsheet = ExcelHandlerSmoltSpawner::CreateSheet($json);

            $writer = new Xlsx($spreadsheet);
            $writer->save("sites/default/files/tmp/SmoltPerSpawner.xlsx");
            $file = "sites/default/files/tmp/SmoltPerSpawner.xlsx";
            $binary_response = new BinaryFileResponse($file);

            $binary_response->setContentDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $filename
            );
        
            $binary_response->send();
            exit;
        } catch (\GuzzleHttp\Exception\RequestException $e){
            var_dump($e->getMessage());
        } 

    }

    public function getFormId(){
        return 'steelhead_smolt_redd_spawner';
    }

    public function submitForm(array &$form, FormStateInterface $form_state){
        $form_state->setRebuild();
    }
}