<?php

namespace App\Http\Controllers;

use App\Models\Flyer;
use App\Repositories\FlyerRepository as FlyerRepository;
use Illuminate\Http\Request;
/* use League\Csv\Reader;
use League\Csv\Statement; */


class FlyersController extends Controller
{

    private $flyers;
    private $defaultPage = 1;
    private $defaultLimit = 100;
    private $fieldsToHide = [];

    public function __construct(Request $request, FlyerRepository $flyers){

        //Linking the repository
        $this->flyers = $flyers;

        //Exploding validated fields
        $fieldsArray = [];
        $fieldsList = $request->query('fields');
        if (!is_null($fieldsList)) {
            $fieldsArray = explode(',',$fieldsList);
        }
        
        //Determine if there are fields to hide
        if (count($fieldsArray) > 0) {
            $flyer = new Flyer();
            $flyerFillables = $flyer->getFillable();
            $this->fieldsToHide = array_diff($flyerFillables,$fieldsArray);
        }
        
    }
    
    public function getAll(Request $request){
        
        $flyers = [];

        //Parsing querystring parameters
        $page = $request->query('page', $this->defaultPage);
        $limit = $request->query('limit', $this->defaultLimit);
        $filters = $request->query('filter');
        
        //Ignoring wrong input values for $page and $limit
        $page = (!is_numeric($page) || $page <= 0) ? $this->defaultPage : $page;
        $limit = (!is_numeric($limit) || $limit <= 0) ? $this->defaultLimit : $limit;
        
        //Loading flyers data
        //$records = $this->getCsvFlyers();
        $records = $this->flyers->all();

        //Looping through the data
        foreach ($records as $flyerData) {
            $flyer = new Flyer([
                'id' => $flyerData["id"], 
                'title' => $flyerData["title"], 
                'start_date' => $flyerData["start_date"], 
                'end_date' => $flyerData["end_date"], 
                'is_published' => $flyerData["is_published"], 
                'retailer' => $flyerData["retailer"], 
                'category'=> $flyerData["category"]
            ]);
            if ($flyer->isActiveFlyer) {

                //Applying filters
                if (!is_null($filters)) {
                    $skipThisFlyer = false;
                    foreach ($filters as $filterKey => $filterValue) {
                        if( strtolower($flyer->$filterKey) != strtolower($filterValue) && !empty($filterValue)) {
                            $skipThisFlyer = true;
                        }
                    }

                    if($skipThisFlyer){
                        continue;
                    }
                }

                //Filtering fields
                if (count($this->fieldsToHide) > 0) {
                    foreach ($this->fieldsToHide as $fieldToHide) {
                        $flyer->makeHidden($fieldToHide)->toArray();
                    }
                }

                $flyers[] = $flyer;
            }
        }

        //Processing pagination on the active flyers array
        $paginationOffset = ($page * $limit) - $limit;
        $flyers = array_slice($flyers,$paginationOffset,$limit,true);

        //Building response
        if (count($flyers) > 0) {
            $response = response()->success(200, $flyers);
        }
        if (count($flyers) == 0) {
            $errors = [
                'message' => 'Not Found',
                'debug' => 'No results found' 
            ];
            $response = response()->error(404, $errors);
        }

        return $response;

    }

    public function getOne(Request $request, $id){
        
        //Loading flyers data 
        //$records = $this->getCsvFlyers();
        /* $records = $this->flyers->all(); */

        //Searching the desired id
        
        /* $flyerFound = false;

        foreach ($records as $flyerData) {
            
            if ($flyerData['id'] == $id) {
                
                $flyer = new Flyer([
                    'id' => $flyerData["id"], 
                    'title' => $flyerData["title"], 
                    'start_date' => $flyerData["start_date"], 
                    'end_date' => $flyerData["end_date"], 
                    'is_published' => $flyerData["is_published"], 
                    'retailer' => $flyerData["retailer"], 
                    'category'=> $flyerData["category"]
                ]);

                //filtering fields
                if (count($this->fieldsToHide) > 0) {
                    foreach ($this->fieldsToHide as $fieldToHide) {
                        $flyer->makeHidden($fieldToHide)->toArray();
                    }
                }
                
                $flyerFound = true;
                break;
            }
        }
        */
        //Preparing response
       /*  if ($flyerFound) { */
        $flyer = $this->flyers->find($id);

        if ($flyer !== false) {
            
            //filtering fields
            if (count($this->fieldsToHide) > 0) {
                foreach ($this->fieldsToHide as $fieldToHide) {
                    $flyer->makeHidden($fieldToHide)->toArray();
                }
            }
            $response = response()->success(200, $flyer);
        }

        if($flyer === false) {
            $error = [
                'message' => 'Not Found',
                'debug' => 'Resource '.$id.' not found' 
            ]; 
            $response =  response()->error(404, $error);
        }
            
        return $response;
    
    }

    /* private function getCsvFlyers() {
        $csv = Reader::createFromPath('flyers/flyers_data.csv', 'r');
        $csv->setHeaderOffset(0);
        $stmt = Statement::create();
        $records = $stmt->process($csv);
        return $records;
    } */
}
