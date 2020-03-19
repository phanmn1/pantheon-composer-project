<?php 

namespace Drupal\screw_trap;

use PhpOffice\PhpSpreadsheet\SpreadSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\WorkSheet;
use Drupal\screw_trap\ExcelHandlerBase;

class ExcelHandlerJvAbundance extends ExcelHandlerBase {

    public static function CreateSheet($json) {
        $spreadsheet = new SpreadSheet();

        $spreadsheet->setActiveSheetIndex(0);
        $spreadsheet->getActiveSheet()->setTitle('Juvenile Abundance Estimate');

        //Use Self because we are not declaring ExcelHandlerJvAbundance as an object context
        //Might need to change later if you want to integrate/ register as drupal service for 
        //Dependency Injector to handle

        /****************************************************
                            HEADERS
        ******************************************************/
        $spreadsheet->getActiveSheet()
                    ->setCellValue('A1','Years')
                    ->getColumnDimension('A')
                    ->setAutoSize(true);
        
        
        $spreadsheet->getActiveSheet()
                    ->setCellValue('B1','Number Captured')
                    ->getColumnDimension('B')
                    ->setAutoSize(true);


        $spreadsheet->getActiveSheet()
                    ->setCellValue('C1','Number Captured Adjusted')
                    ->getColumnDimension('C')
                    ->setAutoSize(true);
                   
        $spreadsheet->getActiveSheet()
                    ->setCellValue('D1','Number PIT Tagged')
                    ->getColumnDimension('D')
                    ->setAutoSize(true);

        $spreadsheet->getActiveSheet()
                    ->setCellValue('E1','Season length (Days)')
                    ->getColumnDimension('E')
                    ->setAutoSize(true);
        
        $spreadsheet->getActiveSheet()
                    ->setCellValue('F1','Days Not Operating (Days)')
                    ->getColumnDimension('F')
                    ->setAutoSize(true);
        
        $spreadsheet->getActiveSheet()
                    ->setCellValue('G1','Percent of season not operated')
                    ->getColumnDimension('G')
                    ->setAutoSize(true);

        $spreadsheet->getActiveSheet()
                    ->setCellValue('H1','Number Released')
                    ->getColumnDimension('H')
                    ->setAutoSize(true);

        $spreadsheet->getActiveSheet()
                    ->setCellValue('I1','Pooled Efficiency')
                    ->getColumnDimension('I')
                    ->setAutoSize(true);

        $spreadsheet->getActiveSheet()
                    ->setCellValue('J1','Pooled (peterson) estimate')
                    ->getColumnDimension('J')
                    ->setAutoSize(true);

        $spreadsheet->getActiveSheet()
                    ->setCellValue('K1',"ESTIMATE (Darroch's stratefied weekly)(DARR 2.0.2)")
                    ->getColumnDimension('K')
                    ->setAutoSize(true);

        $spreadsheet->getActiveSheet()
                    ->setCellValue('L1','Estimated Standard Error')
                    ->getColumnDimension('L')
                    ->setAutoSize(true);

        $spreadsheet->getActiveSheet()
                    ->setCellValue('M1','CV')
                    ->getColumnDimension('M')
                    ->setAutoSize(true);

        $spreadsheet->getActiveSheet()
                    ->setCellValue('N1','95% CI + or - ')
                    ->getColumnDimension('N')
                    ->setAutoSize(true);

        $spreadsheet->getActiveSheet()
                    ->setCellValue('O1','Production (fish per mile)')
                    ->getColumnDimension('O')
                    ->setAutoSize(true);

        
        
        /*****************************************************************
                            RESPONSE DATA
        ******************************************************************/
        $length = count($json);
        for($i=0; $i <= $length; ++$i){
            $j=$i+2;
            self::addCellValue('A'.$j, $json[$i]->Year, $spreadsheet);
            self::addCellValue('B'.$j, $json[$i]->NumberCaptured, $spreadsheet);
            self::addCellValue('C'.$j, $json[$i]->NumberCapturedAdjusted, $spreadsheet);
            self::addCellValue('D'.$j, $json[$i]->NumberPitTagged, $spreadsheet);
            self::addCellValue('E'.$j, $json[$i]->SeasonLength, $spreadsheet);
            self::addCellValue('F'.$j, $json[$i]->DaysNotOperating, $spreadsheet);
            self::addCellValue('G'.$j, $json[$i]->PercentOfSeasonNotOperated, $spreadsheet);
            self::addCellValue('H'.$j, $json[$i]->NumberReleased, $spreadsheet);
            self::addCellValue('I'.$j, $json[$i]->NumberRecaptured, $spreadsheet);
            self::addCellValue('I'.$j, $json[$i]->PooledEfficiency, $spreadsheet);
            self::addCellValue('J'.$j, $json[$i]->PooledEstimate, $spreadsheet);
            self::addCellValue('K'.$j, $json[$i]->OutmigrantAbundanceEstimate, $spreadsheet);
            self::addCellValue('L'.$j, $json[$i]->EstimatedStandardError, $spreadsheet);
            self::addCellValue('M'.$j, $json[$i]->CV, $spreadsheet);
            self::addCellValue('N'.$j, $json[$i]->CI, $spreadsheet);
            self::addCellValue('O'.$j, $json[$i]->Production, $spreadsheet);                    
        }
        

        return $spreadsheet; 

    }

    

    


}