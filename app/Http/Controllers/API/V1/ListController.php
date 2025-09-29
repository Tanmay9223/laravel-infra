<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\BaseController;
use Illuminate\Http\{ JsonResponse , Request};
use App\Classes\ApiResponseClass;
use App\Interfaces\{
    CountryRepositoryInterface, StateRepositoryInterface, CityRepositoryInterface
};
use App\Http\Resources\{
    CountryResource, StateResource, CityResource
};

class ListController extends BaseController
{
    private CountryRepositoryInterface $countryRepositoryInterface;
    private StateRepositoryInterface $stateRepositoryInterface; 
    private CityRepositoryInterface $cityRepositoryInterface;

    public function __construct(CountryRepositoryInterface $countryRepositoryInterface, StateRepositoryInterface $stateRepositoryInterface, CityRepositoryInterface $cityRepositoryInterface)
    {
        $this->countryRepositoryInterface = $countryRepositoryInterface;
        $this->stateRepositoryInterface = $stateRepositoryInterface;
        $this->cityRepositoryInterface = $cityRepositoryInterface;

    }
    
    public function countryList(): JsonResponse
    {
        $countries = $this->countryRepositoryInterface->getByColumnAll(['status'=> 1]);
        if($countries->isNotEmpty()){
            return ApiResponseClass::sendResponseCode(CountryResource::collection($countries), $this->successStatus, 'Country found.');
        }
        return ApiResponseClass::sendResponseCode([], $this->successStatus,'Country not found.');
    }

    public function stateList($country_id): JsonResponse
    {
        $states = $this->stateRepositoryInterface->getByColumnAll(['status'=> 1,'country_id' => $country_id]);
        if($states->isNotEmpty()){
            return ApiResponseClass::sendResponseCode(StateResource::collection($states), $this->successStatus, 'State found.');
        }
        return ApiResponseClass::sendResponseCode([], $this->successStatus,'State not found.');
    }

    public function cityList($country_id ,$state_id): JsonResponse
    {
        $city = $this->cityRepositoryInterface->getByColumnAll(['status'=> 1,'country_id' => $country_id,'state_id' => $state_id]);
        if($city->isNotEmpty()){

            return ApiResponseClass::sendResponseCode(CityResource::collection($city), $this->successStatus,'City found.');
        }
        return ApiResponseClass::sendResponseCode([], $this->successStatus,'City not found.');
    }


}
