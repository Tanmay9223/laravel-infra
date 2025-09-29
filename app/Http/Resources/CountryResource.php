<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CountryResource extends JsonResource
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
            'short_code' => $this->short_code,
            'dial_code' => $this->dial_code,
            // 'numeric_code' => $this->numeric_code,
            // 'currency' => $this->currency,
            // 'currency_symbol' => $this->currency_symbol,
            // 'latitude' => $this->latitude,
            // 'longitude' => $this->longitude,
        ];
        return $data;
    }
}
