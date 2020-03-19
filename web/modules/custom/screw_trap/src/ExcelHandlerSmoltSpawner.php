<?php 

namespace Drupal\screw_trap; 

use PhpOffice\PhpSpreadsheet\SpreadSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\WorkSheet;
use Drupal\screw_trap\ExcelHandlerBase;

class ExcelHandlerSmoltSpawner extends ExcelHandlerBase {

    public static function CreateSheet($json){
        $spreadsheet = new SpreadSheet();
        $spreadsheet->setActiveSheetIndex(0);
        $spreadsheet->getActiveSheet()->setTitle('Smolt per Redd Spawner');


        /****************************************************
                            HEADERS
        ******************************************************/
        $spreadsheet->getActiveSheet()
                    ->setCellValue('A1', 'Year')
                    ->getColumnDimension('A')
                    ->setAutoSize(true);

        $spreadsheet->getActiveSheet()
                    ->setCellValue('B1', 'Smolt number at trap')
                    ->getColumnDimension('B')
                    ->setAutoSize(true);

        $spreadsheet->getActiveSheet()
                    ->setCellValue('C1', 'Age1')
                    ->getColumnDimension('C')
                    ->setAutoSize(true);

        $spreadsheet->getActiveSheet()
                    ->setCellValue('D1', 'Age2')
                    ->getColumnDimension('D')
                    ->setAutoSize(true);

        $spreadsheet->getActiveSheet()
                    ->setCellValue('E1', 'Age3')
                    ->getColumnDimension('E')
                    ->setAutoSize(true);

        $spreadsheet->getActiveSheet()
                    ->setCellValue('F1', 'Year Class')
                    ->getColumnDimension('F')
                    ->setAutoSize(true);

        $spreadsheet->getActiveSheet()
                    ->setCellValue('G1', 'Juvenile Per Year Class')
                    ->getColumnDimension('G')
                    ->setAutoSize(true);

        $spreadsheet->getActiveSheet()
                    ->setCellValue('H1', 'Redds')
                    ->getColumnDimension('H')
                    ->setAutoSize(true);

        $spreadsheet->getActiveSheet()
                    ->setCellValue('I1', 'Spawner')
                    ->getColumnDimension('I')
                    ->setAutoSize(true);

        $spreadsheet->getActiveSheet()
                    ->setCellValue('J1', 'Smolt per Redd')
                    ->getColumnDimension('J')
                    ->setAutoSize(true);

        $spreadsheet->getActiveSheet()
                    ->setCellValue('K1', 'Smolt per spawner')
                    ->getColumnDimension('K')
                    ->setAutoSize(true);

        
        /*****************************************************************
                            RESPONSE DATA
        ******************************************************************/
        $length = count($json);
        for($i=0; $i <= $length; ++$i){
            $j=$i+2;
            self::addCellValue('A'.$j, $json[$i]->Year, $spreadsheet);
            self::addCellValue('B'.$j, $json[$i]->SmoltNumberAtTrap, $spreadsheet);
            self::addCellValue('C'.$j, $json[$i]->Age1, $spreadsheet);
            self::addCellValue('D'.$j, $json[$i]->Age2, $spreadsheet);
            self::addCellValue('E'.$j, $json[$i]->Age3, $spreadsheet);
            self::addCellValue('F'.$j, $json[$i]->YearClass, $spreadsheet);
            self::addCellValue('G'.$j, $json[$i]->JvPerYearClass, $spreadsheet); 
            self::addCellValue('H'.$j, $json[$i]->Redds, $spreadsheet); 
            self::addCellValue('I'.$j, $json[$i]->Spawner, $spreadsheet); 
            self::addCellValue('J'.$j, $json[$i]->SmoltPerRedd, $spreadsheet); 
            self::addCellValue('K'.$j, $json[$i]->SmoltPerSpawner, $spreadsheet);
        }

        return $spreadsheet;
    
    
    }
   

}

