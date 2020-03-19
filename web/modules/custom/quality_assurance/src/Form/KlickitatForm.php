<?php

namespace Drupal\quality_assurance\Form;


use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class KlickitatForm extends FormBase {
     public function buildForm(array $form, FormStateInterface $form_state) {

        $row = 1;
        if (($handle = fopen("book1.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $num = count($data);
            echo "<p> $num fields in line $row: <br /></p>\n";
            $row++;
            for ($c=0; $c < $num; $c++) {
                echo $data[$c] . "<br />\n";
            }
        }
    fclose($handle);
    }

         return $form; 
     }

      public function submitForm(array &$form, FormStateInterface $form_state) {
    
    drupal_set_message('test_trap_samples');
  }

    public function getFormId() {
        return 'klickitat_demo';
   }
}