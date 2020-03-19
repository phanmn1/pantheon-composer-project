<?php 

namespace Drupal\screw_trap\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface; 

class SmoltPerReddSpawnerReadOnly extends FormBase {
    public function buildForm(array $form, FormStateInterface $form_state){
        $urlPrefix = \Drupal::config('apiurl')->get('value');

        $form['#attached']['drupalSettings']['baseUrl'] = \Drupal::config('apiurl')->get('value');
        $form['#attached']['library'][] = 'quality_assurance/datatables_library';
        $form['#attached']['library'][] = 'screw_trap/smolt_trap_per_spawner_readOnly';

        $client = \Drupal::httpClient();
        $request = $client->get($urlPrefix.'/api/ScrewTrap/SelectList');
        $body = json_decode($request->getBody());

        foreach($body as $location){
            $locations[$location] = $location;
        }

        $form['title'] =[
            '#markup' => '<h1>Steelhead Smolt Per Redd/Spawner</h1>'
        ];
        
        $header = [
            'Year' => t('Year'),
            'SmoltNumberAtTrap' => t('Smolt Number at Trap'),
            'Age1' => t('Age 1'),
            'Age2' => t('Age 2'), 
            'Age3' => t('Age 3'), 
            'YearClass' => t('Year Class'), 
            'JvPerYearClass' => t('Juveniles per Year Class'),
            'Redds' => t("Redds"),
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

    public function getFormId(){
        return 'steelhead_smolt_redd_spawner_readonly';
    }

    public function submitForm(array &$form, FormStateInterface $form_state){
        $form_state->setRebuild();
    }
}