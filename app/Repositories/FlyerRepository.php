<?php 

namespace App\Repositories;

use App\Repositories\RepositoryInterface;
use App\Models\Flyer;
use League\Csv\Reader;
use League\Csv\Statement;


class FlyerRepository implements RepositoryInterface {
    
    public function all() {

        $flyerArray = [];
        $records = $this->getRecords();
        
        foreach ($records as $key => $flyerData) {
            $flyer = new Flyer([
                'id' => $flyerData["id"], 
                'title' => $flyerData["title"], 
                'start_date' => $flyerData["start_date"], 
                'end_date' => $flyerData["end_date"], 
                'is_published' => $flyerData["is_published"], 
                'retailer' => $flyerData["retailer"], 
                'category'=> $flyerData["category"]
            ]);
            $flyerArray[] = $flyer;
        }
        return $flyerArray;
    }

    public function find($id) {
        
        $records = $this->getRecords();
        
        $flyerFound = false;
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

                $flyerFound = true;
                break;
            }
        }
        
        if (!$flyerFound) {
            return false;
        }

        if ($flyerFound) {
            return $flyer;
        }
    }

    private function getRecords() {
        $csv = Reader::createFromPath('flyers/flyers_data.csv', 'r');
        $csv->setHeaderOffset(0);
        $stmt = Statement::create();
        $records = $stmt->process($csv);
        return $records;
    }
}