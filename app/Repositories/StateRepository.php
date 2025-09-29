<?php

namespace App\Repositories;

use App\Models\
{
    States
};
use App\Interfaces\StateRepositoryInterface;

class StateRepository implements StateRepositoryInterface
{
    
    public function __construct(){
    }
    
    public function getByColumnAll(array $data){
        return States::where($data)->get();
    }
}