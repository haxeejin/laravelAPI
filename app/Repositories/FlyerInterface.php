<?php 

namespace App\Repositories;

interface FlyerInterface{

    public function all();

    public function find($id);

}