<?php 

namespace Drupal\screw_trap;

use Drupal\screw_trap\ExcelHandlerInterface; 

class ExcelHandlerBase implements ExcelHandlerInterface {
    public static function addCellValue($cell, $title, &$spreadsheet) {
        $spreadsheet->getActiveSheet()->setCellValue($cell, $title);
    }
}