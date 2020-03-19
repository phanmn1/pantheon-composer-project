<?php 

namespace Drupal\jvpittag\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;

class JvPitTagController extends ControllerBase {
    protected $urlPrefix; 

    public function __construct() {
        $this->urlPrefix = \Drupal::config('apiurl')->get('value'); 
    }

    public function GetContent() {

        return [
            '#theme' => 'jvpittag_page',
            '#test_var' => $this->t('Test Value'),
          ];
    }

    public function GetUserName($uid){
        $account = \Drupal\user\Entity\User::load($uid);
        $user = $account->get('name')->value;
        $data['user'] = $user;
        return new JsonResponse($data);
    }

    public function GetBaseURL() {
        return new JsonResponse($this->urlPrefix);
    }
}