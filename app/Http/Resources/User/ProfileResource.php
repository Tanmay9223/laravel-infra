<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'username'  =>      $this->username,
            'first_name'    =>  $this->first_name,
            'last_name' =>      $this->last_name,
            'email'   =>        $this->email,
            'mobile'  =>        $this->mobile,
            'dial_code' =>      $this->dial_code,
            'profile_image' =>  isset($this->avatar) && ($this->avatar != NULL) ? $this->avatar : null,
            'details' =>        $this->userDetail ? [
                                    'country' =>    isset($this->userDetail->countries->name) ? $this->userDetail->countries->name : '',
                                    'state'=>       isset($this->userDetail->state->name) ? $this->userDetail->state->name : '',
                                    'city'=>        isset($this->userDetail->city->name) ? $this->userDetail->city->name : '',
                                    'zipcode' =>    $this->userDetail->zipcode,
                                    'address' =>    $this->userDetail->address,
                                    'gender' =>     $this->userDetail->gender,
                                    'dob' =>        $this->userDetail->dob,
                                    'gender' =>     $this->userDetail->gender,
                             ] : null,
            // 'stage_status' =>   $this->stage_status ,
        ];
    }
}
