<?php

namespace App\Repositories;

use App\Models\
{
    City
};
use App\Interfaces\CityRepositoryInterface;

class CityRepository implements CityRepositoryInterface
{
    
    public function __construct(){
    }
    
    public function getByColumnAll(array $data){
        return City::where($data)->get();
    }
}