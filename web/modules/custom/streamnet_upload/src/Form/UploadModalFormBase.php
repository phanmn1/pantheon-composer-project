<?php 

namespace Drupal\streamnet_upload\Form;

use Drupal\Core\Form\FormBase; 
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;

class UploadModalFormBase extends FormBase {
    
    
    public function getFormID() {
        return 'sn_upload_form';
    }

    

    public function buildForm(array $form, FormStateInterface $form_state) {
        $uploadLocation = 'public://CAXFiles';
        $form['#attached']['library'][] = 'core/drupal.dialog.ajax';

        $form['status_messages'] = [
            '#type' => 'status_messages', 
            '#weight' => -10
        ];
        
        $form['file_upload'] = [
            '#type' => 'file',
            '#title' => t('Upload and Import Dataset'),
        ];

        $form['submit'] = [
            '#type' => 'submit',
            '#value' => t('Submit'),
            '#description' => 'Press submit to upload dataset to streamnet',
            '#attributes' => [
                'class' => ['use-ajax']
            ], 
            '#ajax' => [
                'callback' => [$this, 'submitModalFormAjax'],
                'event' => 'click'
            ]
        ];

        return $form;

    }

    public function Close() {
        $response = new AjaxResponse();
        $response->addCommand(new CloseModalDialogCommand); 
        return $response; 
    }

    public function submitForm(array &$form, FormStateInterface $form_state) { }


}