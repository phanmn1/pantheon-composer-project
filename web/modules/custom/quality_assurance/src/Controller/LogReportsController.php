<?php 

/**
 * @file 
 * Controller for generating JSON response to js file
 * 
 */

namespace Drupal\quality_assurance\Controller;

use Drupal\Core\Controller\ControllerBase; 
use Symfony\Component\HttpFoundation\JsonResponse;

class LogReportsController extends ControllerBase {
    /**
     * Returns JSON data of form inputs 
     * 
     * @return  \Symfony\Component\HttpFoundation\JsonResponse
     *   A JSON response containing the sorted numbers.
     */

    

     public function testdata($data) {
         if(empty($data)) {
            //var_dump($data);
         } else {
             //var_dump($data); 
         }

         $samplejson = '{"menu": {
            "id": "file",
            "value": "File",
            "popup": {
              "menuitem": [
                {"value": "New", "onclick": "CreateNewDoc()"},
                {"value": "Open", "onclick": "OpenDoc()"},
                {"value": "Close", "onclick": "CloseDoc()"}
              ]
            }
          }}';

         return new JsonResponse($samplejson);
     }

     public function content() {

      //$form = \Drupal::formBuilder()->getForm('modal_demo_form_QC');
      //$build = [];


 
      // return [
      //   //'#type' => 'markup',
      //   '#theme' => 'my_template',
      //   '#test_var' => $this->t('Test Value'),
      // ];

      // $build['popup'] = [
      //   '#theme' => 'my_template', 
      //   '#test_var' => $this->t('Test Value')
      // ];

      // return $build; 

      $myform = \Drupal::formBuilder()->getForm('Drupal\quality_assurance\Form\TrapSamples');
      $advQuery = \Drupal::formBuilder()->getForm('Drupal\quality_assurance\Form\TrapSamplesAdvancedQuery');
      //var_dump($myform);
      // $myform['popup']['#value'] = 'From my controller';       
      // $build = [
      //   '#theme' => 'my_template', 
      //   '#test_var' => $this->t('Test Value')
      // ];

      //var_dump($advQuery);

      return array (
        '#theme' => 'custom_form',
        '#my_form' => $myform, 
        '#advQuery' => $advQuery, 
      );

      // return [
      //   '#theme' => 'my_template',
      //   '#test_var' => $this->t('Test Value'),
      //   '#other_var' => $this->t('Other Var')
      // ];
   
    }
}


