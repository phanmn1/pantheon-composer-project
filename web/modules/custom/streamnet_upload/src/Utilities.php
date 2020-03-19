<?php 

namespace Drupal\streamnet_upload; 

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;

class Utilities {
    public static function getCurrentUser() {
        $uid = \Drupal::currentUser()->id();
        $user = \Drupal\user\Entity\User::load($uid);    
        return $user->getUsername();
    }

    public static function CreateDirectory($directory) {

        if (!file_prepare_directory($directory, FILE_MODIFY_PERMISSIONS | FILE_CREATE_DIRECTORY)) {
            //$this->messenger()->addMessage($this->t('Failed to create %directory.', ['%directory' => $directory]), 'error');
            $ajaxResponse = new AjaxResponse();
            $build = '<span>Failed to create '.$directory.'</span>';
            $ajaxResponse->addCommand(new OpenModalDialogCommand(
                'Messages', 
                $build, ['width' => '500'])
            );

            return $ajaxResponse; 
        }

        return null; 

    }

    

    //TO DO 
    /*
     *
     *   TODO: public static function createDirectory()
    */
}
