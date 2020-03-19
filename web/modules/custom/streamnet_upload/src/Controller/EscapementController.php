<?php 

namespace Drupal\streamnet_upload\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\streamnet_upload\EscapementHttpHandler;


class EscapementController extends ControllerBase {

    public function getContent(){
        $http_handler = new EscapementHttpHandler();
        $build['esc_container'] = [
            '#theme' => 'esc_layout', 
            '#title' => 'Trends',
            '#data' => $http_handler->getLog()
        ];

        return $build;
    }

    public function modalTrigger() {
        //$result = is_dir()
        $response = new AjaxResponse();
        $modal_form = \Drupal::formBuilder()->getForm('Drupal\streamnet_upload\Form\UploadModalFormEscapement');
        $response->addCommand(new OpenModalDialogCommand(
            'Upload Dataset',
            $modal_form, ['width' => '800'])
        );

        return $response;
        
    }

    
}

