<?php 

namespace Drupal\screw_trap;

use PhpOffice\PhpSpreadsheet\SpreadSheet;
use \PhpOffice\PhpSpreadsheet\Worksheet\WorkSheet;
use Drupal\screw_trap\ExcelHandlerBase;

class ExcelHandlerScrewTrapConsolidated extends ExcelHandlerBase {

    public static function SpreadSheetScrewTrap($response_data, &$spreadsheet) {
        $length = count($response_data->result);
       
        $spreadsheet->setActiveSheetIndex(0);
        $spreadsheet->getActiveSheet()->setTitle('ScrewTrap');

        $spreadsheet->getActiveSheet()            
            ->setCellValue('A1', 'GlobalID')
            ->getColumnDimension('A')
            ->setAutoSize(true);

        $spreadsheet->getActiveSheet()
            ->setCellValue('B1', 'Location')
            ->getColumnDimension('B')
            ->setAutoSize(true);

        $spreadsheet->getActiveSheet()
            ->setCellValue('C1', 'Date')
            ->getColumnDimension('C')
            ->setAutoSize(true);

        $spreadsheet->getActiveSheet()
            ->setCellValue('D1', 'Time')
            ->getColumnDimension('D')
            ->setAutoSize(true);

        $spreadsheet->getActiveSheet()
            ->setCellValue('E1', 'Initials')
            ->getColumnDimension('D')
            ->setAutoSize(true);
        
        $spreadsheet->getActiveSheet()
            ->setCellValue('F1', 'Fishing')
            ->getColumnDimension('F')
            ->setAutoSize(true);

        $spreadsheet->getActiveSheet()  
            ->setCellValue('G1', 'Rotating')
            ->getColumnDimension('G')
            ->setAutoSize(true);
        
        $spreadsheet->getActiveSheet()
            ->setCellValue('H1', 'AirTemp')
            ->getColumnDimension('H')
            ->setAutoSize(true);
        
        $spreadsheet->getActiveSheet()
            ->setCellValue('I1', 'WaterTemp')
            ->getColumnDimension('I')
            ->setAutoSize(true);

        $spreadsheet->getActiveSheet()
            ->setCellValue('J1', 'StaffGage')
            ->getColumnDimension('J')
            ->setAutoSize(true);

        $spreadsheet->getActiveSheet()
            ->setCellValue('K1', 'EfficiencyTest')
            ->getColumnDimension('K')
            ->setAutoSize(true);

        $spreadsheet->getActiveSheet()
            ->setCellValue('L1', 'CloudPercentage')
            ->getColumnDimension('L')
            ->setAutoSize(true);

        $spreadsheet->getActiveSheet()
            ->setCellValue('M1', 'SecondsPerOneFinalRotation')
            ->getColumnDimension('M')
            ->setAutoSize(true);

        $spreadsheet->getActiveSheet()
            ->setCellValue('N1', 'DebrisSizeDiameter')
            ->getColumnDimension('N')
            ->setAutoSize(true);

        $spreadsheet->getActiveSheet()
            ->setCellValue('O1', 'WaterClarity')
            ->getColumnDimension('O')
            ->setAutoSize(true);

        $spreadsheet->getActiveSheet()
            ->setCellValue('P1', 'PitTagFile')
            ->getColumnDimension('P')
            ->setAutoSize(true);

        $spreadsheet->getActiveSheet()
            ->setCellValue('Q1', 'PitTagVial1')
            ->getColumnDimension('Q')
            ->setAutoSize(true);

        $spreadsheet->getActiveSheet()
            ->setCellValue('R1', 'PitTagVial2')   
            ->getColumnDimension('R')
            ->setAutoSize(true);
            
        $spreadsheet->getActiveSheet()
            ->setCellValue('S1', 'GeneralComments')
            ->getColumnDimension('S')
            ->setAutoSize(true);
        
        $spreadsheet->getActiveSheet()
            ->setCellValue('T1', 'Lat')
            ->getColumnDimension('T')
            ->setAutoSize(true);
        
        $spreadsheet->getActiveSheet()
            ->setCellValue('U1', 'Long')
            ->getColumnDimension('U')
            ->setAutoSize(true);

        $spreadsheet->getActiveSheet()
            ->setCellValue('V1', 'EfficiencyTestNo')
            ->getColumnDimension('V')
            ->setAutoSize(true);
        
        $spreadsheet->getActiveSheet()
            ->setCellValue('W1', 'FishCount')
            ->getColumnDimension('W')
            ->setAutoSize(true);
        
        $spreadsheet->getActiveSheet()
            ->setCellValue('X1', 'BulkFishCount')
            ->getColumnDimension('X')
            ->setAutoSize(true);
    
        $spreadsheet->getActiveSheet()
            ->setCellValue('Y1', 'NumberTagged')
            ->getColumnDimension('Y')
            ->setAutoSize(true);
        
        $spreadsheet->getActiveSheet()
            ->setCellValue('Z1', 'MortalityPercentage')
            ->getColumnDimension('Z')
            ->setAutoSize(true);

        $spreadsheet->getActiveSheet()
            ->setCellValue('AA1', 'EfficiencyRelease')
            ->getColumnDimension('AA')
            ->setAutoSize(true);
        
        $spreadsheet->getActiveSheet()
            ->setCellValue('AB1', 'EfficiencyRecaps')
            ->getColumnDimension('AB')
            ->setAutoSize(true);
        
        $spreadsheet->getActiveSheet()
            ->setCellValue('AC1', 'AvgLength')
            ->getColumnDimension('AC')
            ->setAutoSize(true);
        
        $spreadsheet->getActiveSheet()
            ->setCellValue('AD1', 'MinMaxRatio')
            ->getColumnDimension('AD')
            ->setAutoSize(true);
        
        $spreadsheet->getActiveSheet()
            ->setCellValue('AE1', 'AvgWeight')
            ->getColumnDimension('AE')
            ->setAutoSize(true);
        
        $spreadsheet->getActiveSheet()
            ->setCellValue('AF1', 'Mortality')
            ->getColumnDimension('AF')
            ->setAutoSize(true);

        for($i=0; $i <= $length; ++$i) {
            $j= $i+2;
           self::addCellValue('A' .$j, $response_data->result[$i]->GlobalID, $spreadsheet);
           self::addCellValue('B' .$j, $response_data->result[$i]->Location, $spreadsheet);
           self::addCellValue('C' .$j, $response_data->result[$i]->Date, $spreadsheet);
           self::addCellValue('D' .$j, $response_data->result[$i]->Time, $spreadsheet);
           self::addCellValue('E' .$j, $response_data->result[$i]->Initials, $spreadsheet);
           self::addCellValue('F' .$j, $response_data->result[$i]->Fishing, $spreadsheet);
           self::addCellValue('G' .$j, $response_data->result[$i]->Rotating, $spreadsheet);
           self::addCellValue('H' .$j, $response_data->result[$i]->AirTemp, $spreadsheet);
           self::addCellValue('I' .$j, $response_data->result[$i]->WaterTemp, $spreadsheet);
           self::addCellValue('J' .$j, $response_data->result[$i]->StaffGage, $spreadsheet);
           self::addCellValue('K' .$j, $response_data->result[$i]->EfficiencyTest, $spreadsheet);
           self::addCellValue('L' .$j, $response_data->result[$i]->CloudPercentage, $spreadsheet);
           self::addCellValue('M' .$j, $response_data->result[$i]->SecondsPerOneFinalRotation, $spreadsheet);
           self::addCellValue('N' .$j, $response_data->result[$i]->DebrisSizeDiameter, $spreadsheet);
           self::addCellValue('O' .$j, $response_data->result[$i]->WaterClarity, $spreadsheet);
           self::addCellValue('P' .$j, $response_data->result[$i]->PitTagFile, $spreadsheet);
           self::addCellValue('Q' .$j, $response_data->result[$i]->PitTagVial1, $spreadsheet);
           self::addCellValue('R' .$j, $response_data->result[$i]->PitTagVial2, $spreadsheet);                
           self::addCellValue('S' .$j, $response_data->result[$i]->GeneralComments, $spreadsheet);
           self::addCellValue('T' .$j, $response_data->result[$i]->Lat, $spreadsheet);
           self::addCellValue('U' .$j, $response_data->result[$i]->Long, $spreadsheet);
           self::addCellValue('V' .$j, $response_data->result[$i]->EfficiencyTestNo, $spreadsheet);                
           self::addCellValue('W' .$j, $response_data->result[$i]->FishCount, $spreadsheet);
           self::addCellValue('X' .$j, $response_data->result[$i]->BulkCountSteelhead, $spreadsheet);
           self::addCellValue('Y' .$j, $response_data->result[$i]->NumberTagged, $spreadsheet);
           self::addCellValue('Z' .$j, $response_data->result[$i]->MortalityPercentage, $spreadsheet);
           self::addCellValue('AA' .$j, $response_data->result[$i]->EfficiencyRelease, $spreadsheet);
           self::addCellValue('AB' .$j, $response_data->result[$i]->EfficiencyRecaps, $spreadsheet);
           self::addCellValue('AC' .$j, $response_data->result[$i]->AvgLength, $spreadsheet);
           self::addCellValue('AD' .$j, $response_data->result[$i]->MinMaxRatio, $spreadsheet);
           self::addCellValue('AE' .$j, $response_data->result[$i]->AvgWeight, $spreadsheet);
           self::addCellValue('AF' .$j, $response_data->result[$i]->Mortality, $spreadsheet);
                
        }

    }

    public static function SpreadSheetFishLog($response_data, &$spreadsheet){

        $length = count($response_data->ScrewTrapLogDto);
        $workSheet = new WorkSheet($spreadsheet, 'ScrewTrapFishLog');
        $spreadsheet->addSheet($workSheet);
        
        $spreadsheet->setActiveSheetIndex(1);

        $spreadsheet->getActiveSheet()
            ->setCellValue('A1', 'Species')
            ->getColumnDimension('A')
            ->setAutoSize(true);

        $spreadsheet->getActiveSheet()
            ->setCellValue('B1', 'Length')
            ->getColumnDimension('B')
            ->setAutoSize(true);

        $spreadsheet->getActiveSheet()
            ->setCellValue('C1', 'Weight')
            ->getColumnDimension('C')
            ->setAutoSize(true);

        $spreadsheet->getActiveSheet()
            ->setCellValue('D1', 'Recap')
            ->getColumnDimension('D')
            ->setAutoSize(true);

        $spreadsheet->getActiveSheet()
            ->setCellValue('E1', 'Mortality')
            ->getColumnDimension('E')
            ->setAutoSize(true);
        
        $spreadsheet->getActiveSheet()
            ->setCellValue('F1', 'PITTagNo')
            ->getColumnDimension('F')
            ->setAutoSize(true);

        $spreadsheet->getActiveSheet()
            ->setCellValue('G1', 'ConditionFactor')
            ->getColumnDimension('G')
            ->setAutoSize(true);
        
        $spreadsheet->getActiveSheet()
            ->setCellValue('H1', 'IndividualFishComments')
            ->getColumnDimension('H')
            ->setAutoSize(true);

        $spreadsheet->getActiveSheet()
            ->setCellValue('I1', 'DNAVialNo')
            ->getColumnDimension('I')
            ->setAutoSize(true);

        $spreadsheet->getActiveSheet()
            ->setCellValue('J1', 'ScaleCardNo')
            ->getColumnDimension('J')
            ->setAutoSize(true);
        
        $spreadsheet->getActiveSheet()
            ->setCellValue('K1', 'ParentGlobalID')
            ->getColumnDimension('k')
            ->setAutoSize(true);

        
        for($i=0; $i<=$length; ++$i){
            $j=$i+2;
            self::addCellValue('A'.$j, $response_data->ScrewTrapLogDto[$i]->Species, $spreadsheet);
            self::addCellValue('B'.$j, $response_data->ScrewTrapLogDto[$i]->Length, $spreadsheet);
            self::addCellValue('C'.$j, $response_data->ScrewTrapLogDto[$i]->Weight, $spreadsheet);
            self::addCellValue('D'.$j, $response_data->ScrewTrapLogDto[$i]->Recap, $spreadsheet); 
            self::addCellValue('E'.$j, $response_data->ScrewTrapLogDto[$i]->Mortality, $spreadsheet);
            self::addCellValue('F'.$j, $response_data->ScrewTrapLogDto[$i]->PITTagNo, $spreadsheet);
            self::addCellValue('G'.$j, $response_data->ScrewTrapLogDto[$i]->ConditionFactor, $spreadsheet);
            self::addCellValue('G'.$j, $response_data->ScrewTrapLogDto[$i]->IndividualFishComments, $spreadsheet);
            self::addCellValue('I'.$j, $response_data->ScrewTrapLogDto[$i]->DNAVialNo, $spreadsheet);
            self::addCellValue('J'.$j, $response_data->ScrewTrapLogDto[$i]->ScaleCardNo, $spreadsheet);
            self::addCellValue('K'.$j, $response_data->ScrewTrapLogDto[$i]->ParentGlobalID, $spreadsheet);
        }

        $spreadsheet->setActiveSheetIndex(0);

    }
}