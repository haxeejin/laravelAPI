<?php

namespace App\Http\Controllers;

use App\Models\Flyer;
use App\Repositories\FlyerRepository as FlyerRepository;
use Illuminate\Http\Request;


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
        $flyersArray = $this->flyers->all();

        //Looping through the data
        foreach ($flyersArray as $flyer) {
           
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
    
}
