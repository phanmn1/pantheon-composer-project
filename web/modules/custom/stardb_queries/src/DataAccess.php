<?php

namespace Drupal\stardb_queries;

class DataAccess implements DataAccessInterface {

    private $url; 

    public function __construct() {
        $this->url = \Drupal::config('apiurl')->get('value'); 
    }


    public function GetFishCountsData(int $year, int $facility, int $sort) {
        $response = \Drupal::httpClient()->get($this->url.'/api/FishCounts/GetFishCounts?facilityid='.$facility.'&year='.$year.'&sort='.$sort);
        return $response; 
        //Returns database to data from rest endpoint. 
    }

    public function GetSelectLists() {
        $response = \Drupal::httpClient()->get($this->url.'/api/FishCounts/GetSelectLists');
        return $response; 
    }

    public function getChinookDetails(int $facility, string $ladder, string $startdate, string $enddate, int $sort ) {
        $response = \Drupal::httpClient()->get($this->url.'/api/FishCounts/GetChinookDetails?facilityid='.$facility.                                                                                        
                                            '&ladder='.$ladder.
                                            '&startDate='.$startdate.
                                            '&endDate='.$enddate.
                                            '&sort='.$sort); 
                                            
                                            
        return $response; 
    }


    public function GetTrapSelectLists() {
        $response = \Drupal::httpClient()->get($this->url.'/api/TrapSamples/GetSelectLists');
        return $response; 
    }


    public function getCohoDetails(int $facility, string $ladder, string $startdate, string $enddate, int $sort ) {
        $response = \Drupal::httpClient()->get($this->url.'/api/FishCounts/GetCohoDetails?facilityid='.$facility.                                                                                        
                                            '&ladder='.$ladder.
                                            '&startDate='.$startdate.
                                            '&endDate='.$enddate.
                                            '&sort='.$sort); 
                                            
                                            
        return $response; 
    }


    public function getSockeyeDetails(int $facility, string $ladder, string $startdate, string $enddate, int $sort ) {
        $response = \Drupal::httpClient()->get($this->url.'/api/FishCounts/GetSockeyeDetails?facilityid='.$facility.                                                                                        
                                            '&ladder='.$ladder.
                                            '&startDate='.$startdate.
                                            '&endDate='.$enddate.
                                            '&sort='.$sort); 
                                            
                                            
        return $response; 
    }

    public function getSteelheadDetails(int $facility, string $ladder, string $startdate, string $enddate, int $sort ) {
        $response = \Drupal::httpClient()->get($this->url.'/api/FishCounts/GetSteelheadDetails?facilityid='.$facility.                                                                                        
                                            '&ladder='.$ladder.
                                            '&startDate='.$startdate.
                                            '&endDate='.$enddate.
                                            '&sort='.$sort); 
                                            
                                            
        return $response; 
    }

    public function OtherFishDetails(int $facility, string $ladder, string $startdate, string $enddate, int $sort ) {
        $response = \Drupal::httpClient()->get($this->url.'/api/FishCounts/OtherFishDetails?facilityid='.$facility.                                                                                        
                                            '&ladder='.$ladder.
                                            '&startDate='.$startdate.
                                            '&endDate='.$enddate.
                                            '&sort='.$sort); 
                                            
                                            
        return $response; 
    }

    public function getSpeciesList(int $speciesNM) {
        $response = \Drupal::httpClient()->get($this->url.'/api/FishCounts/GetSpeciesList?SpeciesName='.$speciesNM);  
        return $response;
    }

    public function getTrapSamples($cols, string $facility, string $species, string $startDate, string $endDate, int $sort) {

       $string = $this->url.'/api/FishCounts/GetTrapSamples?facility='.$facility.
       '&species='.$species.
       '&startDate='.$startDate.
       '&endDate='.$endDate.
       '&sort='.$sort;

       
       foreach($cols as $item){
           $array[] = $item; 
       }

    

        $response = \Drupal::httpClient()->get($this->url.'/api/FishCounts/GetTrapSamples?facility='.$facility.
                                                                                                        '&species='.$species.
                                                                                                        '&startDate='.$startDate.
                                                                                                        '&endDate='.$endDate.
                                                                                                        '&sort='.$sort);
                                                // 'body' => $serialized_entity, 
                                                // 'headers' => ['Content-Type' => 'application/json']
                                            // ]);
                                            
                                                
                                                
        
         
        return $response;
    }

    public function GetPagedData() {
        $response = \Drupal::httpClient()->get($this->url.'/api/TrapSamples/GetPagedData');

        return $response; 
    }

    public function GetFacilityNotes($facilitycode, $year) {
        // CODE GOES HERE 
        $response = \Drupal::httpClient()->get($this->url.'/api/FishCounts/FacilityNotesCounts?facilityCode='.$facilitycode.'&year='.$year);

        return $response; 
    }

    public function GetFacilityNotesDetails(string $facilitycode, int $species, string $startdate, string $enddate) {
        $response = \Drupal::httpClient()->get($this->url.'/api/FishCounts/FacilityNotesDetails?facilityCode='.$facilitycode.'&species='
                                                                                                             .$species.'&startdate='
                                                                                                             .$startdate.'&enddate='
                                                                                                             .$enddate);

        return $response; 

    }

    public function GetFacilityNotesTraps(string $facilitycode, string $species, string $startdate, string $enddate) {
        $response = \Drupal::httpClient()->get($this->url.'/api/FishCounts/FacilityNotesTraps?facilityCode='.$facilitycode.'&species='
                                                                                                            .$species.'&startdate='
                                                                                                            .$startdate.'&enddate='
                                                                                                            .$enddate);

        return $response; 
    }




}