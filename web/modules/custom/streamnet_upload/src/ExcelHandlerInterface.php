<?php 

namespace Drupal\streamnet_upload; 

interface ExcelHandlerInterface {
    public static function getSheetData($realPath, $inputFileName, $sheetName);
}