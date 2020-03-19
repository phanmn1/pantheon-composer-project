<?php 

namespace Drupal\screw_trap\Form;

use Drupal\Core\Form\FormBase; 
use Drupal\Core\Form\FormStateInterface;
use Drupal\screw_trap\ExcelHandlerJvAbundance;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class SteelheadJuvenileAbundanceReadOnly extends FormBase {
    public function buildForm(array $form, FormStateInterface $form_state){
        $urlPrefix = \Drupal::config('apiurl')->get('value'); 

        $form['#attached']['drupalSettings']['baseUrl'] =  \Drupal::config('apiurl')->get('value');
        $form['#attached']['library'][] = 'quality_assurance/datatables_library';
        $form['#attached']['library'][] = 'screw_trap/screw_trap_jv_readOnly';
        
        $client = \Drupal::httpClient();
        $request = $client->get($urlPrefix.'/api/ScrewTrap/SelectList');
        $body = json_decode($request->getBody());

        foreach($body as $location){
            $locations[$location] = $location;
        }

        $notes_header = [
            //'ID' => t('ID'),
            //'TableType' => t('Table Type'),
            '' => t(''),
            'Notes' => t('Notes')
        ];

        //$locations[0] = 'Upper Toppenish';
        $form['title'] =[
            '#markup' => '<h1>Steelhead Juvenile Abundance Estimates</h1>'
        ];
        
        $header = [
            'Year' => t('Year'),
            'NumberCaptured' => t('Number Captured'),
            'NumberCapturedAdjusted' => t("Number Captured Adjusted"),
            'NumberPitTagged' => t('Number Pit Tagged'),
            'SeasonLength' => t('Season (days)'),
            'DaysNotOperating' => t('Days Not Operating'),
            'PercentOfSeasonNotOperated' => t('% of season not operated'),
            'NumberReleased' => t('Number Released'),
            'NumberRecaptured' => t('Number Recaptured'),
            'PooledEfficiency' => t('Pooled Efficiency'),
            'PooledEstimate' => t('Pooled Estimate'),
            'OutmigrantAbundanceEstimate' => t("Outmigrant Abundance Estimate"),
            'EstimatedStandardError' => t("Estimated Standard Error"),
            'CV' => t('CV'),
            'CI' => t('95% CI + or -'),
            'Production' => t('Production')
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
            '#submit' => ['::ExportToCSV'],
            '#attributes' => [
                'class' => ['btn-margin-wrapper']
            ]
            ];

        $form['table'] = [
            '#type' => 'table',
            '#header' => $header,
            '#attributes' => [
                'id' => 'jv-abundance-table',
                'class' => ['display'], 
                'width' => '100%'
            ]
        ];

       

        $form['notes_title'] = [
            '#markup' => '<h2 class="notes-title">Notes</h2>'
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

    public function getFormId() {
        return 'screw_trap_abundance_estimate';
    }

    public function ExportToCSV(array &$form, FormStateInterface $form_state) {
        $formValues = $form_state->getValues();
        $filename = 'JvAbundanceEstimate.xlsx';

        $urlPrefix = \Drupal::config('apiurl')->get('value'); 
        $location = $formValues['location'];

        try {
            $response = \Drupal::httpClient()->get($urlPrefix.'/api/RecapSummary/JvAbundanceEstimate?location='.$location);

            $json = json_decode($response->getBody());

            $spreadsheet = ExcelHandlerJvAbundance::CreateSheet($json);

            $writer = new Xlsx($spreadsheet);
            $writer->save("sites/default/files/tmp/JvAbundanceEstimate.xlsx");
            $file = 'sites/default/files/tmp/JvAbundanceEstimate.xlsx';
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

    public function submitForm(array &$form, FormStateInterface $form_state){
        $form_state->setRebuild(); 
    }


}

