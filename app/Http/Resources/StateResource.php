<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'country_code'=>$this->country_code,
            // 'fips_code' => $this->fips_code,
            // 'iso2' => $this->iso2,
            // 'latitude' => $this->latitude,
            // 'longitude' => $this->longitude,
        ];
        return $data;
    }
}
