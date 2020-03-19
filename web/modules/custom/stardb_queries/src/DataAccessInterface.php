<?php 

namespace Drupal\stardb_queries; 

interface DataAccessInterface {

    public function GetFishCountsData(int $year, int $facility, int $sort);
    
    public function GetSelectLists();

    public function getChinookDetails(int $facility, string $ladder, string $startdate, string $enddate, int $sort);

    public function getCohoDetails(int $facility, string $ladder, string $startdate, string $enddate, int $sort );

    public function getSockeyeDetails(int $facility, string $ladder, string $startdate, string $enddate, int $sort);

    public function getSteelheadDetails(int $facility, string $ladder, string $startdate, string $enddate, int $sort);

    public function OtherFishDetails(int $facility, string $ladder, string $startdate, string $enddate, int $sort);

    public function getSpeciesList(int $speciesNM);

    public function getTrapSamples($cols, string $facility, string $species, string $startDate, string $endDate, int $sort);

    public function GetFacilityNotes($facilitycode, $year);

    public function GetFacilityNotesDetails(string $facilitycode, int $species, string $startdate, string $enddate);

    public function GetFacilityNotesTraps(string $facilitycode, string $species, string $startdate, string $enddate); 
    

}



