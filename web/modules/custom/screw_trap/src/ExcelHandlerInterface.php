<?php 

namespace Drupal\screw_trap;

// Declare interface 
interface ExcelHandlerInterface {
    public static function addCellValue($cell, $title, &$spreadsheet);
}