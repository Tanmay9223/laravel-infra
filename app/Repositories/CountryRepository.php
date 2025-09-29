<?php

namespace App\Repositories;

use App\Models\
{
    Country
};
use App\Interfaces\CountryRepositoryInterface;

class CountryRepository implements CountryRepositoryInterface
{
    
    public function __construct(){
    }
    
    public function getByColumnAll(array $data){
        return Country::where($data)->get();
    }
}