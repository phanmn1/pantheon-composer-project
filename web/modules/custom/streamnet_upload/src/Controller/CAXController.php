<?php 

namespace Drupal\streamnet_upload\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\streamnet_upload\NOSAHttpHandler;

class CAXController extends ControllerBase {
    
    public function getContent() {
        $http_handler = new NOSAHttpHandler();
        $build['cax_container'] = [
            '#theme' => 'cax_layout',
            '#title' => 'CAX',
            '#data' => $http_handler->getLog()
        ]; 

        return $build;
    }

    public function modalTrigger() {
        $response = new AjaxResponse();
        $modal_form = \Drupal::formBuilder()->getForm('Drupal\streamnet_upload\Form\UploadModalFormNOSA');
        $response->addCommand(new OpenModalDialogCommand(
            'Upload Dataset',
            $modal_form, ['width' => '800'])
        );

        return $response;
    }

    public function Close() {
        $response = new AjaxResponse();
        $response->addCommand(new CloseModalDialogCommand); 
        return $response; 
    }

}